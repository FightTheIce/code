<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Dflydev\DotAccessData\Data;
use Exception;
use Laminas\Code\Generator\ClassGenerator as Laminas_ClassGenerator;
use Laminas\Code\Generator\DocBlock\Tag\PropertyTag;
use Laminas\Code\Generator\TypeGenerator;
use Nette\PhpGenerator\Factory;

class ClassGenerator extends Laminas_ClassGenerator
{
	use Traits\DocBlockerTrait;

	/**
	 * config
	 *
	 * Configuration of class generator
	 *
	 * @access protected
	 * @property array $config Configuration of class generator
	 */
	protected array $config = [
	    'docblock' => [
	        'short_description' => true,
	        'access_tag' => true,
	        'property_tag' => true,
	    ],
	    'generation' => [
	        'alphabetical_order_uses' => true,
	        'replace_slashes' => true,
	    ],
	];

	/**
	 * dotAccess
	 *
	 * Dot Access easy array crawler
	 *
	 * @access protected
	 * @property Data|null $dotAccess Dot Access easy array crawler
	 */
	protected ?Data $dotAccess = null;


	/**
	 * __construct
	 *
	 * Class Construct
	 *
	 * @access public
	 * @return void
	 * @param mixed $name Class Name
	 * @param mixed $namespaceName The namespace this class belongs to
	 * @param mixed $flags Class Flags (ie. final, readonly, abstract)
	 * @param mixed $extends Class extends what a parent class
	 * @param array $interfaces Class implements what?
	 * @param array $properties Class properties
	 * @param array $methods Class methods
	 * @param mixed $docBlock Docblock
	 */
	public function __construct(
		mixed $name = null,
		mixed $namespaceName = null,
		mixed $flags = null,
		mixed $extends = null,
		array $interfaces = [],
		array $properties = [],
		array $methods = [],
		mixed $docBlock = null,
	) {
		parent::__construct($name,$namespaceName,$flags,$extends,$interfaces,$properties,$methods,$docBlock);

		$this->dotAccess = new Data($this->config);
	}


	/**
	 * newProperty
	 *
	 * Add a new property to the generated class
	 *
	 * @access public
	 * @return PropertyGenerator
	 * @param string|null $name Property Name
	 * @param mixed $defaultValue The default value of the parameter
	 * @param int $flags Property flags
	 * @param TypeGenerator|null $type Type hint
	 */
	public function newProperty(
		?string $name = null,
		mixed $defaultValue = null,
		int $flags = 16,
		?TypeGenerator $type = null,
	): PropertyGenerator {
		$property = new PropertyGenerator($name,$defaultValue,$flags,$type);

		$this->addPropertyFromGenerator($property);

		return $property;
	}


	/**
	 * addTypedClassProperty
	 *
	 * Add a new typed property to the generated class
	 *
	 * @access public
	 * @return PropertyGenerator
	 * @param string $access The access level of the property
	 * @param string $name The name of the property
	 * @param string $desc The description of the property
	 * @param string $typeHint The type hint of the property
	 * @param mixed $defaultValue The default value of the property
	 * @param bool $omitDefaultValue Should we omit the default value?
	 */
	public function addTypedClassProperty(
		string $access,
		string $name,
		string $desc,
		string $typeHint,
		mixed $defaultValue = null,
		bool $omitDefaultValue = false,
	): PropertyGenerator {
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


	/**
	 * addClassProperty
	 *
	 * Add a property to the generated class
	 *
	 * @access public
	 * @return PropertyGenerator
	 * @param string $access The access level of the property
	 * @param string $name The name of the property
	 * @param string $desc The description of the property
	 * @param mixed $defaultValue The default value of the property
	 * @param bool $omitDefaultValue Should we omit the default value?
	 */
	public function addClassProperty(
		string $access,
		string $name,
		string $desc,
		mixed $defaultValue = null,
		bool $omitDefaultValue = false,
	): PropertyGenerator {
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


	/**
	 * newMethod
	 *
	 * Add a new method to the generated class
	 *
	 * @access public
	 * @return MethodGenerator
	 * @param mixed $name The name of the method
	 * @param array $parameters Parameters of the method
	 * @param int $flags Method flags
	 * @param mixed $body The method body
	 * @param mixed $docBlock The docblock object
	 */
	public function newMethod(
		mixed $name = null,
		array $parameters = [],
		int $flags = 16,
		mixed $body = null,
		mixed $docBlock = null,
	): MethodGenerator {
		$method = new MethodGenerator($name,$parameters,$flags,$body,$docBlock);

		$this->addMethodFromGenerator($method);

		return $method;
	}


	/**
	 * addTypedClassMethod
	 *
	 * Add a method with a return type
	 *
	 * @access public
	 * @return MethodGenerator
	 * @param string $access The method access level
	 * @param string $name Method name
	 * @param string $desc The method description
	 * @param string $returnTypeHint The return type of the method
	 */
	public function addTypedClassMethod(
		string $access,
		string $name,
		string $desc,
		string $returnTypeHint,
	): MethodGenerator {
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

		//should we set a return tag?
		if ($this->dotAccess->get('docblock.return_tag',true) === true) {
		    $method->addDocBlockTag('return',$typeHintGenerator->getVarTag());
		}

		return $method;
	}


	/**
	 * setExtendsClass
	 *
	 * Sets the extended class name
	 *
	 * @access public
	 * @return self
	 * @param string $name The extended class name
	 */
	public function setExtendsClass(string $name): self
	{
		$this->setExtendedClass($name);

		return $this;
	}


	/**
	 * generate
	 *
	 * Generate the code
	 *
	 * @access public
	 * @return string
	 * @param bool $formatCode Should we PSR12 format the code?
	 * @param array $transforms Additional string replacements that should occur
	 */
	public function generate(bool $formatCode = true, array $transforms = []): string
	{
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
		    $workAround = '<';
		    $workAround.= '?';
		    $workAround.= 'php';

		    $nette = new Factory();
		    $class = $nette->fromCode($workAround.PHP_EOL.$code);

		    $code = trim(str_replace($workAround,'',$class->__toString())).PHP_EOL;

		    //$code = trim(str_replace($workAround,'',$code));
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


	/**
	 * replaceSlashMarkers
	 *
	 * Replace "\" markers
	 *
	 * @access protected
	 * @return array
	 * @param string $code The generated code
	 * @param array $uses The uses/imports of this class
	 */
	protected function replaceSlashMarkers(string $code, array $uses = []): array
	{
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


	/**
	 * abcOrderUseStatements
	 *
	 * ABC order use statements
	 *
	 * @access protected
	 * @return void
	 */
	protected function abcOrderUseStatements(): void
	{
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


	/**
	 * saveToFile
	 *
	 * Save the generated class to a file
	 *
	 * @access public
	 * @return void
	 * @param string $filename The filename to save the generated class to
	 * @param bool $strict Should use add a strict declaration
	 * @param array $transforms Additional transformations
	 */
	public function saveToFile(string $filename, bool $strict = true, array $transforms = []): void
	{
		$code = $this->generate(true,$transforms);

		if ($strict === true) {
		    $code = 'declare(strict_types=1);'.PHP_EOL.PHP_EOL.$code;
		}

		$code = '<?php'.PHP_EOL.PHP_EOL.$code;

		file_put_contents($filename,$code);
	}
}
