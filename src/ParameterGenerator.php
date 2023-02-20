<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Laminas\Code\Generator\ParameterGenerator as Laminas_ParameterGenerator;
use Laminas\Code\Generator\PropertyValueGenerator;
use SplArrayObject;
use StdlibArrayObject;

class ParameterGenerator extends Laminas_ParameterGenerator
{
    public function newPropertyValueGenerator(
        mixed $value = null,
        string $type = PropertyValueGenerator::TYPE_AUTO,
        string $outputMode = PropertyValueGenerator::OUTPUT_MULTIPLE_LINE,
        null|SplArrayObject|StdlibArrayObject $constants = null
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
