<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Laminas\Code\Generator\ClassGenerator as Laminas_ClassGenerator;
use Laminas\Code\Generator\TypeGenerator;
use Dflydev\DotAccessData\Data;
use Nette\PhpGenerator\Factory;

class ClassGenerator extends Laminas_ClassGenerator {
    use Traits\DocBlockerTrait;

    protected array $config = [
        'docblock' => [
            'short_description' => true,
            'access_tag' => true
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
        $property->setTypeHintGenerator($typeHintGenerator);
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

        return $property;
    }

    public function newMethod($name = null, array $parameters = [], $flags = MethodGenerator::FLAG_PUBLIC, $body = null, $docBlock = null): MethodGenerator {
        $method = new MethodGenerator($name,$parameters,$flags,$body,$docBlock);

        $this->addMethodFromGenerator($method);

        return $method;
    }

    public function addClassMethod(string $access, string $name, string $desc, string $returnTypeHint): MethodGenerator {
        $access = trim(strtolower($access));

        $typeHintGenerator = new TypeHintGenerator($returnTypeHint);
        
        $flagBits = match($access) {
            'public' => MethodGenerator::FLAG_PUBLIC,
            'private' => MethodGenerator::FLAG_PRIVATE,
            'protected' => MethodGenerator::FLAG_PROTECTED,
            default => MethodGenerator::FLAG_PUBLIC
        };

        $method = $this->newMethod($name,[],$flagBits,null,null);
        $method->setTypeHintGenerator($typeHintGenerator);

        //set the return type
        if (!in_array($name,array('__construct','__destruct'))) {
            $method->setReturnType($typeHintGenerator->getTypeGenerator());
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

    public function generate(bool $formatCode = true) {
        $code = parent::generate();

        if ($formatCode===true) {
            $nette = new Factory();
            $class = $nette->fromCode('<?php'.PHP_EOL.$code);

            $code = trim(str_replace('<?php','',$class->__toString()));
        }

        return $code;
    }

    public function saveToFile(string $filename): void {
        file_put_contents($filename,'<?php'.PHP_EOL.PHP_EOL.$this->generate());
    }
}