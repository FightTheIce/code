<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use ArrayObject as SplArrayObject;
use Laminas\Code\Generator\PropertyGenerator as Laminas_PropertyGenerator;
use Laminas\Code\Generator\PropertyValueGenerator;
use Laminas\Stdlib\ArrayObject as StdlibArrayObject;

class PropertyGenerator extends Laminas_PropertyGenerator
{
    use Traits\DocBlockerTrait;
    use Traits\ImposeFtiTrait;

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

    public function hasDocBlock(): bool
    {
        return ! is_null($this->getDocBlock());
    }
}
