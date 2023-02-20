<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Laminas\Code\Generator\ClassGenerator as Laminas_ClassGenerator;
use Laminas\Code\Generator\PropertyGenerator;
use Laminas\Code\Generator\PropertyValueGenerator;
use Laminas\Code\Generator\TypeGenerator;

class ClassGenerator extends Laminas_ClassGenerator
{
    /**
     * @param array<\Laminas\Code\Generator\DocBlock\Tag\TagInterface> $tags
     *
     * @return DocBlockGenerator
     */
    public function newDocBlockGenerator(
        ?string $shortDescription = null,
        ?string $longDescription = null,
        array $tags = []
    ): DocBlockGenerator {
        $docblock = new DocBlockGenerator(
            $shortDescription,
            $longDescription,
            $tags
        );

        $this->setDocBlock($docblock);

        return $docblock;
    }

    /**
     * @param array<ParameterGenerator> $parameters
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
     * @param  PropertyValueGenerator|string|array|null  $defaultValue
     */
    public function newPropertyGenerator(
        ?string $name = null,
        PropertyValueGenerator|string|array|null $defaultValue = null,
        int $flags = PropertyGenerator::FLAG_PUBLIC,
        ?TypeGenerator $type = null
    ): PropertyGenerator {
        $property = new PropertyGenerator($name, $defaultValue, $flags, $type);

        $this->addPropertyFromGenerator($property);

        return $property;
    }

    public function setDocBlockShortDescription(string $desc): self
    {
        $docblock = $this->getDocBlock();
        if (is_null($docblock)) {
            $docblock = new DocBlockGenerator();
        }

        $docblock->setShortDescription($desc);

        return $this;
    }

    public function setDocBlockLongDescription(string $desc): self
    {
        $docblock = $this->getDocBlock();
        if (is_null($docblock)) {
            $docblock = new DocBlockGenerator();
        }

        $docblock->setLongDescription($desc);

        return $this;
    }
}
