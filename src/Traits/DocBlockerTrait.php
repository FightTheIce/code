<?php

declare(strict_types=1);

namespace FightTheIce\Code\Traits;

use Laminas\Code\Generator\DocBlock\Tag\GenericTag;
use Laminas\Code\Generator\DocBlockGenerator;

trait DocBlockerTrait
{
    public function getDocBlockGenerator(): DocBlockGenerator
    {
        //we should check for the method "getDocBlock"

        $docblock = $this->getDocBlock();
        if (is_null($docblock)) {
            $docblock = new DocBlockGenerator();

            //we should check for the method "setDocBlock"
            $this->setDocBlock($docblock);
        }

        return $docblock;
    }

    public function setDocBlockShortDescription(string $desc): self
    {
        $this->getDocBlockGenerator()->setShortDescription($desc);

        return $this;
    }

    public function setDocBlockLongDescription(string $desc): self
    {
        $this->getDocBlockGenerator()->setLongDescription($desc);

        return $this;
    }

    public function setDocBlockTag(string $tag, string $value): self
    {
        $this->getDocBlockGenerator()->setTag(new GenericTag(
            $tag,
            $value
        ));

        return $this;
    }
}
