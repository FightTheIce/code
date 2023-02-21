<?php

declare(strict_types=1);

namespace FightTheIce\Code\Traits;

use Exception;
use FightTheIce\Code\DocBlockGenerator;
use Laminas\Code\Generator\DocBlockGenerator as Laminas_DocBlockGenerator;

trait DocBlockerTrait
{
    /**
     * @param array<\Laminas\Code\Generator\DocBlock\Tag\TagInterface> $tags
     *
     * @return \FightTheIce\Code\DocBlockGenerator
     */
    public function newDocBlockGenerator(
        ?string $shortDescription = null,
        ?string $longDescription = null,
        array $tags = []
    ): DocBlockGenerator {
        $docblock = new DocBlockGenerator(
            $shortDescription,
            $longDescription,
            $tags
        );

        $this->setDocBlock($docblock);

        return $docblock;
    }

    public function setDocBlockShortDescription(string $description): self
    {
        $this->imposeFTIDocBlock()->setShortDescription($description);
        return $this;
    }

    public function setDocBlockLongDescription(string $description): self
    {
        $this->imposeFTIDocBlock()->setLongDescription($description);
        return $this;
    }

    public function imposeFTIDocBlock(): DocBlockGenerator
    {
        if (is_null($this->docBlock)) {
            return $this->newDocBlockGenerator();
        }
        if ($this->docBlock::class === Laminas_DocBlockGenerator::class) {
            //this means the docblock was set outside of FTI-Code so lets
            //impose FTI-Docblock

            $docblock = $this->docBlock;
            $short = $docblock->getShortDescription();
            $long = $docblock->getLongDescription();
            $tags = $docblock->getTags();

            return $this->newDocBlockGenerator($short, $long, $tags);
        }
        return $this->getFTIDocBlock();
    }

    protected function getFTIDocBlock(): DocBlockGenerator
    {
        if ($this->docBlock instanceof DocBlockGenerator) {
            return $this->docBlock;
        }

        throw new Exception('DocBlock is not of type FTI');
    }
}
