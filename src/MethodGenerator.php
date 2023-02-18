<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Laminas\Code\Generator\MethodGenerator as Laminas_MethodGenerator;
use Laminas\Code\Generator\DocBlock\Tag\ParamTag;
use Laminas\Code\Generator\TypeGenerator;

class MethodGenerator extends Laminas_MethodGenerator {
    use Traits\DocBlockerTrait;
    use Traits\TypeHinterTrait;

    /**
     * newParameter
     * 
     * Add a new parameter to the generated method
     *
     * @param string|null $name
     * @param TypeGenerator|null $type
     * @param any $defaultValue
     * @param int|null $position
     * @param bool $passByReference
     * @return ParameterGenerator
     */
    public function newParameter(?string $name = null, ?TypeGenerator $type = null, $defaultValue = null, ?int $position = null, bool $passByReference = false): ParameterGenerator {
        $parameter = new ParameterGenerator($name,$type,$defaultValue,$position,$passByReference);

        $this->setParameter($parameter);

        return $parameter;
    }

    /**
     * addTypedMthodParameter
     * 
     * Add a type hinted parameter to the generated method
     *
     * @param string $type
     * @param string $name
     * @param string $desc
     * @param any $defaultValue
     * @param bool $omitDefaultValue
     * @return ParameterGenerator
     */
    public function addTypedMethodParameter(string $type, string $name, string $desc, $defaultValue = null, $omitDefaultValue = false): ParameterGenerator {
        $typeHintGenerator = new TypeHintGenerator($type);

        $parameter = $this->newParameter($name,$typeHintGenerator->getTypeGenerator(),$defaultValue,null,false);
        $parameter->setTypeHintGenerator($typeHintGenerator);
        $parameter->setDefaultValue($defaultValue);
        $parameter->omitDefaultValue($omitDefaultValue);
        $parameter->setType($typeHintGenerator->getTypeHint());

        $docblock = $this->getDocBlockGenerator();
        $docblock->setTag(new ParamTag($name,$typeHintGenerator->getVarTag(),$desc));
        
        return $parameter;
    }

    /**
     * addMethodParameter
     * 
     * Add a new parameter to the generated method
     *
     * @param string $name
     * @param string $desc
     * @param any $defaultValue
     * @param bool $omitDefaultValue
     * @return ParameterGenerator
     */
    public function addMethodParameter(string $name, string $desc, $defaultValue, $omitDefaultValue = false): ParameterGenerator {
        $parameter = $this->newParameter($name,null,$defaultValue,null,false);
        $parameter->setDefaultValue($defaultValue);
        $parameter->omitDefaultValue($omitDefaultValue);

        $docblock = $this->getDocBlockGenerator();
        $docblock->setTag(new ParamTag($name,'ANY',$desc));

        return $parameter;
    }

    public function codeGenerator(): CodeGenerator {
        return new CodeGenerator($this->getBody(),$this);
    }
}