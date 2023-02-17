<?php

namespace FightTheIce\Code;

use Laminas\Code\Generator\TypeGenerator;
use Exception;

class TypeHintGenerator {
    protected ?string $typeHint = null;
    protected ?string $baseHint = null;
    protected bool $nullable = false;
    protected bool $inited = false;

    public function __construct(?string $type = null) {
        if ($this->inited===true) {
            throw new Exception(self::class.'::__construct has already been initialzed!');
        }

        $this->inited = true;

        $type = trim($type);

        if (is_null($type)) {
            return;
        }

        $type = trim($type);
        if (empty($type)) {
            throw new Exception(self::class.'::__construct expects parameter $type to be a non empty string!');
        }

        $this->typeHint = $type;
        $this->baseHint = $type;
        if (substr($type,0,1)=='?') {
            $this->baseHint = substr($type,1);
            $this->nullable = true;
        }
    }

    public function hasType(): bool {
        return !is_null($this->typeHint);
    }

    public function getTypeHint(): ?string {
        return $this->typeHint;
    }

    public function getBaseType(): ?string {
        return $this->baseHint;
    }

    public function isNullable(): bool {
        return $this->nullable;
    }

    public function getVarTag(): string {
        if ($this->hasType()===false) {
            return '';
        }

        $var = $this->getBaseType();

        if ($this->isNullable()===true) {
            $var = 'null|'.$var;
        }

        return $var;
    }

    public function getTypeGenerator(): TypeGenerator {
        return TypeGenerator::fromTypeString($this->typeHint);
    }
}