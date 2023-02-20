<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Laminas\Code\Generator\ParameterGenerator as Laminas_ParameterGenerator;
use Laminas\Code\Generator\PropertyValueGenerator;

class ParameterGenerator extends Laminas_ParameterGenerator
{
    public function newPropertyValueGenerator($value = null, $type = PropertyValueGenerator::TYPE_AUTO, $outputMode = PropertyValueGenerator::OUTPUT_MULTIPLE_LINE, $constants = null): PropertyValueGenerator
    {
        $value = new PropertyValueGenerator($value, $type, $outputMode, $constants);

        $this->setDefaultValue($value);

        return $value;
    }
}
