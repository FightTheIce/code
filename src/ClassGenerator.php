<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Laminas\Code\Generator\ClassGenerator as Laminas_ClassGenerator;
use Laminas\Code\Generator\PropertyGenerator;
use Laminas\Code\Generator\PropertyValueGenerator;
use Laminas\Code\Generator\TypeGenerator;

class ClassGenerator extends Laminas_ClassGenerator
{
    use Traits\DocBlockerTrait;

    /**
     * @param array<\Laminas\Code\Generator\ParameterGenerator> $parameters
     */
    public function newMethodGenerator(
        ?string $name = null,
        array $parameters = [],
        int $flags = MethodGenerator::FLAG_PUBLIC,
        ?string $body = null,
        DocBlockGenerator|string|null $docBlock = null
    ): MethodGenerator {
        $method = new MethodGenerator(
            $name,
            $parameters,
            $flags,
            $body,
            $docBlock
        );

        $this->addMethodFromGenerator($method);

        return $method;
    }

    public function addTypedMethod(
        string $name,
        string $desc,
        string $visibility,
        ?string $returnType = null
    ): MethodGenerator {
        if (is_null($returnType)) {
            $returnType = 'mixed';
        }

        $method = $this->newMethodGenerator($name);
        $method->setVisibility($visibility);
        $method->setDocBlockShortDescription($name);
        $method->setDocBlockLongDescription($desc);
        $method->imposeFTIDocBlock()->newGenericTag('access', $visibility);

        $method->setReturnType($returnType);

        $rTypes = [];
        if (substr($returnType, 0, 1) === '?') {
            $rTypes = [substr($returnType, 1),'null'];
        } else {
            $rTypes = explode('|', $returnType);
        }

        $method->imposeFTIDocBlock()->newReturnTag($rTypes, null);

        return $method;
    }

    /**
     * @param array<mixed>|PropertyValueGenerator|string|null  $defaultValue
     */
    public function newPropertyGenerator(
        ?string $name = null,
        array|PropertyValueGenerator|string|null $defaultValue = null,
        int $flags = PropertyGenerator::FLAG_PUBLIC,
        ?TypeGenerator $type = null
    ): PropertyGenerator {
        $property = new PropertyGenerator($name, $defaultValue, $flags, $type);

        $this->addPropertyFromGenerator($property);

        return $property;
    }
}
