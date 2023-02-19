<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Laminas\Code\Generator\DocBlock\Tag\ParamTag;
use Laminas\Code\Generator\MethodGenerator as Laminas_MethodGenerator;
use Laminas\Code\Generator\TypeGenerator;
use Laminas\Code\Reflection\ClassReflection;
use Exception;

class MethodGenerator extends Laminas_MethodGenerator
{
    use Traits\DocBlockerTrait;
    use Traits\TypeHinterTrait;

    /**
     * newParameter
     *
     * Add a new parameter to the generated method
     */
    public function newParameter(?string $name = null, ?TypeGenerator $type = null, mixed $defaultValue = null, ?int $position = null, bool $passByReference = false): ParameterGenerator
    {
        $parameter = new ParameterGenerator($name, $type, $defaultValue, $position, $passByReference);

        $this->setParameter($parameter);

        return $parameter;
    }

    /**
     * addTypedMthodParameter
     *
     * Add a type hinted parameter to the generated method
     */
    public function addTypedMethodParameter(string $type, string $name, string $desc, mixed $defaultValue = null, bool $omitDefaultValue = false): ParameterGenerator
    {
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
     * Add a new parameter to the generated method
     */
    public function addMethodParameter(string $name, string $desc, mixed $defaultValue, bool $omitDefaultValue = false): ParameterGenerator
    {
        $parameter = $this->newParameter($name, null, $defaultValue, null, false);
        $parameter->setDefaultValue($defaultValue);
        $parameter->omitDefaultValue($omitDefaultValue);

        $docblock = $this->getDocBlockGenerator();
        $docblock->setTag(new ParamTag($name, 'ANY', $desc));

        return $parameter;
    }

    public function codeGenerator(): CodeGenerator
    {
        return new CodeGenerator($this->getBody(), $this);
    }

    public function importMethodBodyFromReflection(string $class, string $method, bool $bestFixIndentation = true) {
        $reflection = new ClassReflection($class);

        if ($reflection->hasMethod($method) === false) {
            throw new Exception($class.' does not have a method called: '.$method.'!');
        }

        $method = $reflection->getMethod($method);

        $body = $method->getBody();

        //replace "\t" with 4 spaces
        $body = str_replace("\t",'    ',$body);
        $lines = preg_split("/\R/", $body); 
        
        //count the lines
        $cLines = count($lines);

        $eCount = 0;

        for ($a=0; $a<$cLines; $a++) {
            if (empty($lines[$a])) {
                continue;
            }

            $segments = explode(' ',$lines[$a]);
            foreach ($segments as $seg) {
                if (empty($seg)) {
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
    }
}
