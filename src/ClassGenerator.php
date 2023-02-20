<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Laminas\Code\Generator\ClassGenerator as Laminas_ClassGenerator;
use Laminas\Code\Generator\TypeGenerator;

class ClassGenerator extends Laminas_ClassGenerator
{
    public function newDocBlockGenerator(string $shortDescription = null, string $longDescription = null, array $tags = []): DocBlockGenerator
    {
        $docblock = new DocBlockGenerator($shortDescription, $longDescription, $tags);

        $this->setDocBlock($docblock);

        return $docblock;
    }

    public function newMethodGenerator(string $name = null, array $parameters = [], int $flags = MethodGenerator::FLAG_PUBLIC, string $body = null, DocBlockGenerator|string $docBlock = null): MethodGenerator
    {
        $method = new MethodGenerator($name, $parameters, $flags, $body, $docBlock);

        $this->addMethodFromGenerator($method);

        return $method;
    }

    public function newPropertyGenerator(?string $name = null, mixed $defaultValue = null, int $flags = PropertyGenerator::FLAG_PUBLIC, ?TypeGenerator $type = null): PropertyGenerator
    {
        $property = new PropertyGenerator($name, $defaultValue, $flags, $type);

        $this->addPropertyFromGenerator($property);

        return $property;
    }
}
