<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use ArrayObject as SplArrayObject;
use Laminas\Code\Generator\PropertyGenerator as Laminas_PropertyGenerator;
use Laminas\Code\Generator\PropertyValueGenerator;
use Laminas\Stdlib\ArrayObject as StdlibArrayObject;

class PropertyGenerator extends Laminas_PropertyGenerator
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

    /**
     * @param mixed                                 $value
     * @param string                                $type
     * @param PropertyValueGenerator::OUTPUT_*      $outputMode
     * @param SplArrayObject|StdlibArrayObject|null $constants
     */
    public function newPropertyValueGenerator(
        $value = null,
        $type = PropertyValueGenerator::TYPE_AUTO,
        $outputMode = PropertyValueGenerator::OUTPUT_MULTIPLE_LINE,
        $constants = null
    ): PropertyValueGenerator {
        $value = new PropertyValueGenerator(
            $value,
            $type,
            $outputMode,
            $constants
        );

        $this->setDefaultValue($value);

        return $value;
    }
}
