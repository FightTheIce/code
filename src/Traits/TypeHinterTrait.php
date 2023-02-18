<?php

declare(strict_types=1);

namespace FightTheIce\Code\Traits;

use FightTheIce\Code\TypeHintGenerator;

trait TypeHinterTrait
{
    protected ?TypeHintGenerator $typeHintGenerator = null;

    public function addTypeHintGenerator(TypeHintGenerator $typeHint): self
    {
        $this->typeHintGenerator = $typeHint;

        return $this;
    }

    public function getTypeHintGenerator(): TypeHintGenerator
    {
        return $this->typeHintGenerator;
    }
}
