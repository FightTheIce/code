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
    use Traits\ImposeFtiTrait;

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

    /**
     * addTypedMethod
     *
     * A typed (return type) method to the generated class
     *
     * @access public
     *
     * @param string $name Method name
     * @param string $desc Method description
     * @param string $visibility Method visbility (public, private, protected)
     * @param string $returnType
     *
     * @return \FightTheIce\Code\MethodGenerator
     */
    public function addTypedMethod(
        string $name,
        string $desc,
        string $visibility,
        string $returnType
    ): MethodGenerator {
        $method = $this->newMethodGenerator($name);
        $method->setVisibility($visibility);
        $method->setReturnType($returnType);
        $method->setDocBlockShortDescription($name);
        $method->setDocBlockLongDescription($desc);

        $method->imposeFTIDocBlock()
            ->newGenericTag('access', $visibility)
            ->newReturnTag(
                Utils::createTypesArrayResolvedUses(
                    $returnType,
                    $this
                ),
                null
            );

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

    public function hasNamespaceName(): bool
    {
        return ! is_null($this->getDocBlock());
    }
}
