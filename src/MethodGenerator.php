<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Laminas\Code\Generator\MethodGenerator as Laminas_MethodGenerator;
use Laminas\Code\Generator\PromotedParameterGenerator;
use Exception;

class MethodGenerator extends Laminas_MethodGenerator
{
    public function newDocBlockGenerator(?string $shortDescription = null, ?string $longDescription = null, array $tags = []): DocBlockGenerator
    {
        $docblock = new DocBlockGenerator($shortDescription, $longDescription, $tags);

        $this->setDocBlock($docblock);

        return $docblock;
    }

    public function newParameterGenerator(?string $name = null, ?string $type = null, mixed $defaultValue = null, ?int $position = null, ?bool $passByReference = false): ParameterGenerator
    {
        $parameter = new ParameterGenerator($name, $type, $defaultValue, $position, $passByReference);

        $this->setParameter($parameter);

        return $parameter;
    }

    public function newPromotedParameterGenerator(string $name, ?string $type = null, string $visibility = PromotedParameterGenerator::VISIBILITY_PUBLIC, ?int $position = null, bool $passByReference = false): PromotedParameterGenerator
    {
        if ($this->getName() !== '__construct') {
            throw new Exception('Promoted parameters may only be generated on the __construct method!');
        }

        $parameter = new PromotedParameterGenerator($name, $type, $visibility, $position, $passByReference);

        $this->setParameter($parameter);

        return $parameter;
    }

    public function setDocBlockShortDescription(string $desc): self {
        $docblock = $this->getDocBlock();
        if (is_null($docblock)) {
            $docblock = new DocBlockGenerator();
        }

        $docblock->setShortDescription($desc);

        return $this;
    }

    public function setDocBlockLongDescription(string $desc): self {
        $docblock = $this->getDocBlock();
        if (is_null($docblock)) {
            $docblock = new DocBlockGenerator();
        }

        $docblock->setLongDescription($desc);

        return $this;
    }
}
