<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Laminas\Code\Generator\DocBlock\Tag\AuthorTag;
use Laminas\Code\Generator\DocBlock\Tag\GenericTag;
use Laminas\Code\Generator\DocBlock\Tag\LicenseTag;
use Laminas\Code\Generator\DocBlock\Tag\MethodTag;
use Laminas\Code\Generator\DocBlock\Tag\ParamTag;
use Laminas\Code\Generator\DocBlock\Tag\PropertyTag;
use Laminas\Code\Generator\DocBlock\Tag\ReturnTag;
use Laminas\Code\Generator\DocBlock\Tag\ThrowsTag;
use Laminas\Code\Generator\DocBlock\Tag\VarTag;
use Laminas\Code\Generator\DocBlockGenerator as Laminas_DocBlockGenerator;

class DocBlockGenerator extends Laminas_DocBlockGenerator
{
    use Traits\ImposeFtiTrait;

    public function newAuthorTag(
        ?string $authorName = null,
        ?string $authorEmail = null
    ): self {
        $tag = new AuthorTag($authorName, $authorEmail);

        $this->setTag($tag);

        return $this;
    }

    public function newGenericTag(
        ?string $name = null,
        ?string $content = null
    ): self {
        $tag = new GenericTag($name, $content);

        $this->setTag($tag);

        return $this;
    }

    public function newLicenseTag(
        ?string $url = null,
        ?string $licenseName = null
    ): self {
        $tag = new LicenseTag($url, $licenseName);

        $this->setTag($tag);

        return $this;
    }

    /**
     * @param array<string>    $types
     */
    public function newMethodTag(
        ?string $methodName = null,
        array $types = [],
        ?string $description = null,
        bool $isStatic = false
    ): self {
        $tag = new MethodTag($methodName, $types, $description, $isStatic);

        $this->setTag($tag);

        return $this;
    }

    /**
     * @param array<string> $types
     */
    public function newParamTag(
        ?string $variableName = null,
        array $types = [],
        ?string $description = null
    ): self {
        $tag = new ParamTag($variableName, $types, $description);

        $this->setTag($tag);

        return $this;
    }

    /**
     * @param array<string> $types
     */
    public function newPropertyTag(
        ?string $propertyName = null,
        array $types = [],
        ?string $description = null
    ): self {
        $tag = new PropertyTag($propertyName, $types, $description);

        $this->setTag($tag);

        return $this;
    }

    /**
     * @param array<string> $types
     */
    public function newReturnTag(
        array $types = [],
        ?string $description = null
    ): self {
        $tag = new ReturnTag($types, $description);

        $this->setTag($tag);

        return $this;
    }

    /**
     * @param array<string> $types
     */
    public function newThrowsTag(
        array $types = [],
        ?string $description = null
    ): self {
        $tag = new ThrowsTag($types, $description);

        $this->setTag($tag);

        return $this;
    }

    /**
     * @param array<string> $types
     */
    public function newVarTag(
        ?string $variableName = null,
        array $types = [],
        ?string $description = null
    ): self {
        $tag = new VarTag($variableName, $types, $description);

        $this->setTag($tag);

        return $this;
    }
}
