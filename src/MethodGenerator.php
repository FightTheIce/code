<?php

namespace FightTheIce\Code;

use Laminas\Code\Generator\MethodGenerator as Laminas_MethodGenerator;
use FightTheIce\Code\TypeHintGenerator;
use Laminas\Code\Generator\DocBlock\Tag\ParamTag;

class MethodGenerator extends Laminas_MethodGenerator {
    use Traits\DocBlockerTrait;
    use Traits\TypeHinterTrait;

    public function newParameter($name = null, $type = null, $defaultValue = null, $position = null, $passByReference = false): ParameterGenerator {
        $parameter = new ParameterGenerator($name,$type,$defaultValue,$position,$passByReference);

        $this->setParameter($parameter);

        return $parameter;
    }

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