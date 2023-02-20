<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Exception;
use Laminas\Code\Generator\DocBlock\Tag\ParamTag;
use Laminas\Code\Generator\MethodGenerator as Laminas_MethodGenerator;
use Laminas\Code\Generator\TypeGenerator;
use Laminas\Code\Reflection\ClassReflection;

class MethodGenerator extends Laminas_MethodGenerator
{
	use Traits\DocBlockerTrait;
	use Traits\TypeHinterTrait;

	/**
	 * newParameter
	 *
	 * Add a new parameter to the generated method
	 *
	 * @access public
	 * @param string|null $name The name of the parameter
	 * @param TypeGenerator|null $type Type Hint
	 * @param mixed $defaultValue The params default value
	 * @param int|null $position Parameter position
	 * @param bool $passByReference Should we pass this parameter by reference
	 */
	public function newParameter(
		?string $name = null,
		?TypeGenerator $type = null,
		mixed $defaultValue = null,
		?int $position = null,
		bool $passByReference = false,
	): ParameterGenerator {
		$parameter = new ParameterGenerator($name, $type, $defaultValue, $position, $passByReference);

		$this->setParameter($parameter);

		return $parameter;
	}


	/**
	 * addTypedMethodParameter
	 *
	 * Add a type parameter to the generated method
	 *
	 * @access public
	 * @param string $type The type hint of the parameter
	 * @param string $name The parameter name
	 * @param string $desc The parameter description
	 * @param mixed $defaultValue The params default value
	 * @param bool $omitDefaultValue Should we omit the default value
	 */
	public function addTypedMethodParameter(
		string $type,
		string $name,
		string $desc,
		mixed $defaultValue = null,
		bool $omitDefaultValue = false,
	): ParameterGenerator {
		$typeHintGenerator = new TypeHintGenerator($type);

		$parameter = $this->newParameter($name, $typeHintGenerator->getTypeGenerator(), $defaultValue, null, false);
		$parameter->addTypeHintGenerator($typeHintGenerator);
		$parameter->setDefaultValue($defaultValue);
		$parameter->omitDefaultValue($omitDefaultValue);
		$parameter->setType($typeHintGenerator->getTypeHint());

		$docblock = $this->getDocBlockGenerator();
		$docblock->setTag(new ParamTag($name, $typeHintGenerator->getVarTag(), $desc));

		return $parameter;
	}


	/**
	 * addMethodParameter
	 *
	 * Add a parameter (untyped) to the generated method
	 *
	 * @access public
	 * @param string $name The name of the parameter
	 * @param string $desc The description of the parameter
	 * @param mixed $defaultValue The params default value
	 * @param bool $omitDefaultValue Should we omit the default value
	 */
	public function addMethodParameter(
		string $name,
		string $desc,
		mixed $defaultValue = null,
		bool $omitDefaultValue = false,
	): ParameterGenerator {
		$parameter = $this->newParameter($name, null, $defaultValue, null, false);
		$parameter->setDefaultValue($defaultValue);
		$parameter->omitDefaultValue($omitDefaultValue);

		$docblock = $this->getDocBlockGenerator();
		$docblock->setTag(new ParamTag($name, 'mixed', $desc));

		return $parameter;
	}


	/**
	 * importMethodBodyFromReflection
	 *
	 * Import Method Body from a class and method via reflection
	 *
	 * @access public
	 * @param string $class Classname
	 * @param string $method The method of the class to import
	 * @param bool $bestFixIndentation Should we attempt to fix the indentation
	 */
	public function importMethodBodyFromReflection(string $class, string $method, bool $bestFixIndentation = true): self
	{
		$reflection = new ClassReflection($class);

		if ($reflection->hasMethod($method) === false) {
		    throw new Exception($class.' does not have a method called: '.$method.'!');
		}

		$method = $reflection->getMethod($method);

		$body = $method->getBody();

		if ($bestFixIndentation === true) {
		    //replace "\t" with 4 spaces
		    $body = str_replace("\t",'    ',$body);
		    $lines = preg_split("/\R/", $body);

		    //count the lines
		    $cLines = count($lines);

		    $eCount = 0;

		    for ($a=0; $a<$cLines; $a++) {
		$line = trim($lines[$a]);

		if (strlen($line) === 0) {
		    continue;
		}

		$segments = explode(' ',$lines[$a]);
		foreach ($segments as $seg) {
		    $segTest = trim($seg);
		    if (strlen($segTest) === 0) {
		$eCount++;
		    } else {
		break;
		    }
		}

		break;
		    }

		    foreach ($lines as &$line) {
		$find = str_repeat(' ',$eCount);
		$line = str_replace($find,'',$line);
		    }

		    $this->setBody(implode(PHP_EOL,$lines));
		} else {
		    $this->setBody($body);
		}

		return $this;
	}
}
