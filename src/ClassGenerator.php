<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Laminas\Code\Generator\ClassGenerator as Laminas_ClassGenerator;
use Laminas\Code\Generator\TypeGenerator;
use Dflydev\DotAccessData\Data;
use Nette\PhpGenerator\Factory;
use Laminas\Code\Generator\DocBlock\Tag\PropertyTag;
use Exception;

class ClassGenerator extends Laminas_ClassGenerator {
    use Traits\DocBlockerTrait;

    protected array $config = [
        'docblock' => [
            'short_description' => true,
            'access_tag' => true,
            'property_tag' => true
        ],
        'generation' => [
            'alphabetical_order_uses' => true,
            'replace_slashes' => true
        ]
    ];

    protected ?Data $dotAccess = null;

    public function __construct(
        $name = null,
        $namespaceName = null,
        $flags = null,
        $extends = null,
        array $interfaces = [],
        array $properties = [],
        array $methods = [],
        $docBlock = null
    ) {
        parent::__construct($name,$namespaceName,$flags,$extends,$interfaces,$properties,$methods,$docBlock);

        $this->dotAccess = new Data($this->config);
    }

    public function newProperty(?string $name = null, $defaultValue = null, $flags = PropertyGenerator::FLAG_PUBLIC, ?TypeGenerator $type = null): PropertyGenerator {
        $property = new PropertyGenerator($name,$defaultValue,$flags,$type);

        $this->addPropertyFromGenerator($property);

        return $property;
    }

    public function addTypedClassProperty(string $access, string $name, string $desc, string $typeHint, $defaultValue = null, bool $omitDefaultValue = false): PropertyGenerator {
        $access = trim(strtolower($access));

        $typeHintGenerator = new TypeHintGenerator($typeHint);
        
        $flagBits = match($access) {
            'public' => PropertyGenerator::FLAG_PUBLIC,
            'private' => PropertyGenerator::FLAG_PRIVATE,
            'protected' => PropertyGenerator::FLAG_PROTECTED,
            default => PropertyGenerator::FLAG_PUBLIC
        };

        $property = $this->newProperty($name,$defaultValue,$flagBits,$typeHintGenerator->getTypeGenerator());
        $property->addTypeHintGenerator($typeHintGenerator);
        $property->omitDefaultValue($omitDefaultValue);
        
        //should we set a short description?
        if ($this->dotAccess->get('docblock.short_description',true)===true) {
            $property->addDocBlockShortDescription($name);
        }

        $property->addDocBlockLongDescription($desc);
        
        //should we set an access tag?
        if ($this->dotAccess->get('docblock.access_tag',true)===true) {
            $property->addDocBlockTag('access',$access);
        }

        //should we set a property tag?
        if ($this->dotAccess->get('docblock.property_tag',true)===true) {
            $property->getDocBlock()->setTag(new PropertyTag($name,$typeHintGenerator->getVarTag(),$desc));
        }

        return $property;
    }

    public function addClassProperty(string $access, string $name, string $desc, $defaultValue = null, bool $omitDefaultValue = false): PropertyGenerator {
        $access = trim(strtolower($access));

        $flagBits = match($access) {
            'public' => PropertyGenerator::FLAG_PUBLIC,
            'private' => PropertyGenerator::FLAG_PRIVATE,
            'protected' => PropertyGenerator::FLAG_PROTECTED,
            default => PropertyGenerator::FLAG_PUBLIC
        };

        $property = $this->newProperty($name,$defaultValue,$flagBits,null);
        $property->omitDefaultValue($omitDefaultValue);

        //should we set a short description?
        if ($this->dotAccess->get('docblock.short_description',true)===true) {
            $property->addDocBlockShortDescription($name);
        }

        $property->addDocBlockLongDescription($desc);
        
        //should we set an access tag?
        if ($this->dotAccess->get('docblock.access_tag',true)===true) {
            $property->addDocBlockTag('access',$access);
        }

        //should we set a property tag?
        if ($this->dotAccess->get('docblock.property_tag',true)===true) {
            $property->getDocBlock()->setTag(new PropertyTag($name,null,$desc));
        }

        return $property;
    }

    public function newMethod($name = null, array $parameters = [], $flags = MethodGenerator::FLAG_PUBLIC, $body = null, $docBlock = null): MethodGenerator {
        $method = new MethodGenerator($name,$parameters,$flags,$body,$docBlock);

        $this->addMethodFromGenerator($method);

        return $method;
    }

    public function addTypedClassMethod(string $access, string $name, string $desc, string $returnTypeHint): MethodGenerator {
        $access = trim(strtolower($access));

        $typeHintGenerator = new TypeHintGenerator($returnTypeHint);
        
        $flagBits = match($access) {
            'public' => MethodGenerator::FLAG_PUBLIC,
            'private' => MethodGenerator::FLAG_PRIVATE,
            'protected' => MethodGenerator::FLAG_PROTECTED,
            default => MethodGenerator::FLAG_PUBLIC
        };

        $method = $this->newMethod($name,[],$flagBits,null,null);
        $method->addTypeHintGenerator($typeHintGenerator);

        //set the return type
        if (!in_array($name,array('__construct','__destruct'))) {
            $method->setReturnType($typeHintGenerator->getTypeHint());
        }

        //should we set a short description?
        if ($this->dotAccess->get('docblock.short_description',true)===true) {
            $method->addDocBlockShortDescription($name);
        }

        $method->addDocBlockLongDescription($desc);
        
        //should we set an access tag?
        if ($this->dotAccess->get('docblock.access_tag',true)===true) {
            $method->addDocBlockTag('access',$access);
        }

        return $method;
    }

    public function setExtendsClass(string $name): self {
        $this->setExtendedClass($name);

        return $this;
    }

    public function generate(bool $formatCode = true, array $transforms = []) {
        //lets abc order our "use" statments
        if ($this->dotAccess->get('generation.alphabetical_order_uses',true) === true) {
            //this may already be done via Laminas so in the future we may not have to
            $this->abcOrderUseStatements();
        }

        //store and remove all use statements so we can "str_replace" them without "\" markers later
        $uses = $this->getUses();

        $code = parent::generate();

        if ($this->dotAccess->get('generation.replace_slashes',true) === true) {
            //lets "str_replace" all "\" markers (or at least the ones we can)
            $actions = $this->replaceSlashMarkers($code,$uses);
            $code = $actions['code'];
        }

        if ($formatCode===true) {
            $nette = new Factory();
            $class = $nette->fromCode('<?php'.PHP_EOL.$code);

            $code = trim(str_replace('<?php','',$class->__toString())).PHP_EOL;
        }

        //do we have any additional tranformations to do?
        foreach ($transforms as $transform) {
            if (!is_array($transform)) {
                throw new Exception('Transformer must be an array!');
            }

            if (!array_key_exists('str',$transform)) {
                throw new Exception('Transformer does not have a str');
            }

            if (!array_key_exists('replace',$transform)) {
                throw new Exception('Transformer does not have a replacement!');
            }

            $code = str_replace($transform['str'],$transform['replace'],$code);
        }

        return $code;
    }

    protected function replaceSlashMarkers(string $code, array $uses = []): array  {
        $originalUses = $uses;

        $storable = [];

        $uses[] = 'self';
        $uses[] = 'static';
        foreach ($uses as $use) {
            $fqcn = $use;
            $alias = null;
            $class = null;

            //namespace and alias segments
            $segments = explode(' as ',$use);

            if (count($segments) == 2) {
                $fqcn = reset($segments);
                $alias = end($segments);
            }

            //class segments
            $cSegments = explode('\\',$fqcn);
            $class = end($cSegments);

            $find = '\\'.$class;
            $replace = $class;

            $fullStr = 'use '.$fqcn.';';
            if (!is_null($alias)) {
                $fullStr = rtrim($fullStr,';').' as '.$alias.';';
            }

            //replace the "use" statement with nothing
            $code = str_replace($fullStr,'',$code);

            //replace the "\{Class}" with "{Class}"
            $code = str_replace($find,$replace,$code);

            //set our storable unit
            if (!in_array($class,array('self','static'))) {
                $storable[] = $fullStr;
            }
        }

        //do we have a namespace name?
        $ns = $this->getNamespaceName();
        if (strlen($ns)>0) {
            $find = 'namespace '.$ns.';';
            
            //replace the namespace with the namespace + use statements
            $replace = $find.PHP_EOL.PHP_EOL.implode(PHP_EOL,$storable);

            $code = str_replace($find,$replace,$code);
        } else {
            $code = implode(PHP_EOL,$storable).PHP_EOL.$code;
        }

        return array(
            'uses' => $originalUses,
            'replacements' => $uses,
            'code' => $code
        );
    }

    protected function abcOrderUseStatements() {
        $uses = $this->getUses();
        $useStmts = array();
        $classes = array();

        foreach ($uses as $use) {
            $class = $use;
            $alias = null;
            $segments = explode(' as ',$use);

            if (count($segments)==2) {
                $class = reset($segments);
                $alias = end($segments);
            }

            $useStmts[$class] = array(
                'class' => $class,
                'alias' => $alias
            );
            $classes[] = $class;

            $this->removeUse($class);
        }

        $sorted = sort($classes);
        if ($sorted === false) {
            throw new Exception('Sorted function failed!');
        }
        
        foreach ($classes as $sclass) {
            if (!isset($useStmts[$sclass])) {
                throw new Exception('Unable to located sorted class: '.$sclass);
            }

            $use = $useStmts[$sclass];

            $this->addUse($use['class'],$use['alias']);
        }
    }

    public function saveToFile(string $filename, bool $strict = true, array $transforms = []): void {
        $code = $this->generate(true,$transforms);

        if ($strict === true) {
            $code = 'declare(strict_types=1);'.PHP_EOL.PHP_EOL.$code;
        }

        $code = '<?php'.PHP_EOL.PHP_EOL.$code;

        file_put_contents($filename,$code);
    }
}