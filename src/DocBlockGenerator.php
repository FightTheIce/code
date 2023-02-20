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
    public function newAuthorTag(mixed $authorName = null, mixed $authorEmail = null): AuthorTag
    {
        $tag = new AuthorTag($authorName, $authorEmail);

        $this->setTag($tag);

        return $tag;
    }

    public function newGenericTag(mixed $name = null, mixed $content = null): GenericTag
    {
        $tag = new GenericTag($name, $content);

        $this->setTag($tag);

        return $tag;
    }

    public function newLicenseTag(mixed $url = null, mixed $licenseName = null): LicenseTag
    {
        $tag = new LicenseTag($url, $licenseName);

        $this->setTag($tag);

        return $tag;
    }

    public function newMethodTag(mixed $methodName = null, array $types = [], mixed $description = null, bool $isStatic = false): MethodTag
    {
        $tag = new MethodTag($methodName, $types, $description, $isStatic);

        $this->setTag($tag);

        return $tag;
    }

    public function newParamTag(mixed $variableName = null, array $types = [], mixed $description = null): ParamTag
    {
        $tag = new ParamTag($variableName, $types, $description);

        $this->setTag($tag);

        return $tag;
    }

    public function newPropertyTag(mixed $propertyName = null, array $types = [], mixed $description = null): PropertyTag
    {
        $tag = new PropertyTag($propertyName, $types, $description);

        $this->setTag($tag);

        return $tag;
    }

    public function newReturnTag(array $types = [], mixed $description = null): ReturnTag
    {
        $tag = new ReturnTag($types, $description);

        $this->setTag($tag);

        return $tag;
    }

    public function newThrowsTag(array $types = [], mixed $description = null): ThrowsTag
    {
        $tag = new ThrowsTag($types, $description);

        $this->setTag($tag);

        return $tag;
    }

    public function newVarTag(?string $variableName = null, array $types = [], ?string $description = null): VarTag
    {
        $tag = new VarTag($variableName, $types, $description);

        $this->setTag($tag);

        return $tag;
    }
}
