<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Laminas\Code\Generator\PropertyGenerator as Laminas_PropertyGenerator;

class PropertyGenerator extends Laminas_PropertyGenerator
{
    use Traits\DocBlockerTrait;
    use Traits\TypeHinterTrait;
}
