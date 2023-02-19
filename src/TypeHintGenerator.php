<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Exception;
use Laminas\Code\Generator\TypeGenerator;

class TypeHintGenerator
{
    /**
     * typeHint
     *
     * The original type hint
     *
     * @access protected
     *
     * @property string|null $typeHint The original type hint
     */
    protected ?string $typeHint = null;

    /**
     * baseHint
     *
     * The base hint of the type hint (ie. ?string, would be string)
     *
     * @access protected
     *
     * @property string|null $baseHint The base hint of the type hint (ie. ?string,
     * would be string)
     */
    protected ?string $baseHint;

    /**
     * nullable
     *
     * Can this type hint also be null?
     *
     * @access protected
     *
     * @property bool $nullable Can this type hint also be null?
     */
    protected bool $nullable = false;

    /**
     * inited
     *
     * Has the constructor been inited?
     *
     * @access protected
     *
     * @property bool $inited Has the constructor been inited?
     */
    protected bool $inited = false;

    /**
     * __construct
     *
     * Class construct
     *
     * @access public
     *
     * @param string|null $type The type hint as a string
     */
    public function __construct(?string $type = null)
    {
        if ($this->inited === true) {
            throw new Exception(self::class.'::__construct has already been initialzed!');
        }

        $this->inited = true;

        $type = trim($type);

        if (is_null($type)) {
            return;
        }

        $type = trim($type);
        if (strlen($type) === 0) {
            throw new Exception(self::class.'::__construct expects parameter $type to be a non empty string!');
        }

        $this->typeHint = $type;
        $this->baseHint = $type;
        if (substr($type, 0, 1) === '?') {
            $this->baseHint = substr($type, 1);
            $this->nullable = true;
        }
    }

    /**
     * hasType
     *
     * Used to determine if the provided type hint has a type
     *
     * @access public
     */
    public function hasType(): bool
    {
        return ! is_null($this->typeHint);
    }

    /**
     * getTypeHint
     *
     * Returns the original typehint or null if none was set
     *
     * @access public
     */
    public function getTypeHint(): ?string
    {
        return $this->typeHint;
    }

    /**
     * getBaseType
     *
     * Returns the base type hint (i.e. If ?string then this would return string)
     *
     * @access public
     */
    public function getBaseType(): ?string
    {
        return $this->baseHint;
    }

    /**
     * isNullable
     *
     * Used to determine if this type hint can also be null
     *
     * @access public
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * getVarTag
     *
     * Returns the datatypes used in this type hint, usefull for docblock
     *
     * @access public
     */
    public function getVarTag(): string
    {
        if ($this->hasType() === false) {
            return '';
        }

        $var = $this->getBaseType();

        if ($this->isNullable() === true) {
            $var .= '|null';
        }

        return $var;
    }

    /**
     * getTypeGenerator
     *
     * Returns the Laminas\Code\GeneratorTypeGenerator object or null if one isn't
     * able to be created
     *
     * @access public
     */
    public function getTypeGenerator(): ?TypeGenerator
    {
        if ($this->hasType() === false) {
            return null;
        }

        return TypeGenerator::fromTypeString($this->typeHint);
    }
}
