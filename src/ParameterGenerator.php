<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Laminas\Code\Generator\ParameterGenerator as Laminas_ParameterGenerator;

class ParameterGenerator extends Laminas_ParameterGenerator
{
    use Traits\TypeHinterTrait;
}
