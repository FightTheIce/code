<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Laminas\Code\Generator\MethodGenerator as Laminas_MethodGenerator;
use Laminas\Code\Generator\PromotedParameterGenerator;

class MethodGenerator extends Laminas_MethodGenerator
{
    public function newDocBlockGenerator(?string $shortDescription = null, ?string $longDescription = null, array $tags = []): DocBlockGenerator
    {
        $docblock = new DocBlockGenerator($shortDescription, $longDescription, $tags);

        $this->setDocBlock($docblock);

        return $docblock;
    }

    public function newParameterGenerator(?string $name = null, ?string $type = null, mixed $defaultValue = null, int $position = null, bool $passByReference = null): ParameterGenerator
    {
        $parameter = new ParameterGenerator($name, $type, $defaultValue, $position, $passByReference);

        $this->setParameter($parameter);

        return $parameter;
    }

    public function newPromotedParameterGenerator(string $name, ?string $type = null, string $visibility = PromotedParameterGenerator::VISIBILITY_PUBLIC, ?int $position = null, bool $passByReference = false): PromotedParameterGenerator
    {
        $parameter = new PromotedParameterGenerator($name, $type, $visibility, $position, $passByReference);

        $this->setParameter($parameter);

        return $parameter;
    }
}
