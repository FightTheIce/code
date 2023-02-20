<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Exception;
use Laminas\Code\Generator\MethodGenerator as Laminas_MethodGenerator;
use Laminas\Code\Generator\PromotedParameterGenerator;
use Laminas\Code\Generator\ParameterGenerator;

class MethodGenerator extends Laminas_MethodGenerator
{
    /**
     * @param array<\Laminas\Code\Generator\DocBlock\Tag\TagInterface> $tags
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

    public function newParameterGenerator(
        ?string $name = null,
        ?string $type = null,
        mixed $defaultValue = null,
        ?int $position = null,
        bool $passByReference = false
    ): ParameterGenerator {
        $parameter = new ParameterGenerator(
            $name,
            $type,
            $defaultValue,
            $position,
            $passByReference
        );

        $this->setParameter($parameter);

        return $parameter;
    }

    /**
     * @psalm-param non-empty-string $name
     * @psalm-param ?non-empty-string $type
     * @psalm-param PromotedParameterGenerator::VISIBILITY_* $visibility
     */
    public function newPromotedParameterGenerator(
        string $name,
        ?string $type = null,
        string $visibility = PromotedParameterGenerator::VISIBILITY_PUBLIC,
        ?int $position = null,
        bool $passByReference = false
    ): PromotedParameterGenerator {
        if ($this->getName() !== '__construct') {
            throw new Exception(
                'Promotions may only be generated on the construct method!'
            );
        }

        if (strlen($name) <= 0) {
            throw new Exception('name must not be empty!');
        }

        $parameter = new PromotedParameterGenerator(
            $name,
            $type,
            $visibility,
            $position,
            $passByReference
        );

        $this->setParameter($parameter);

        return $parameter;
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
