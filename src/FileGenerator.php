<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Exception;
use Laminas\Code\DeclareStatement;
use Laminas\Code\Generator\FileGenerator as Laminas_FileGenerator;
use Nette\PhpGenerator\Factory as NetteFactory;

class FileGenerator {
    protected ?ClassGenerator $classGenerator = null;
    protected ?string $fileName = null;
    protected bool $declareStrictTypes = true;
    protected bool $inited = false;
    protected bool $formatPHPCode = true;

    public function __construct(?string $fileName, ?ClassGenerator $classGenerator = null) {
        if ($this->inited === true) {
            throw new Exception('__construct already inited!');
        }

        $this->inited = true;
        $this->fileName = $fileName;
        $this->classGenerator = $classGenerator;
    }

    public function setFileName(string $filename): self {
        $this->fileName = $filename;

        return $this;
    }

    public function setClassGenerator(?ClassGenerator $classGenerator = null): self {
        if (! is_null($this->classGenerator)) {
            throw new Exception('classGenerator is already set!');    
        }

        if (is_null($classGenerator)) {
            $classGenerator = new ClassGenerator();
        }

        $this->classGenerator = $classGenerator;

        return $this;
    }

    public function newClassGenerator(
        $name = null,
        $namespaceName = null,
        $flags = null,
        $extends = null,
        array $interfaces = [],
        array $properties = [],
        array $methods = [],
        $docBlock = null
    ) {
        $class = new ClassGenerator(
            $name,
            $namespaceName,
            $flags,
            $extends,
            $interfaces,
            $properties,
            $methods,
            $docBlock
        );

        $this->setClassGenerator($class);

        return $class;
    }

    public function useDeclareStrictTypes(bool $value = true): self {
        $this->declareStrictTypes = $value;

        return $this;
    }

    public function generate(): string {
        $file = new Laminas_FileGenerator();
        $file->setFilename($this->fileName);

        if ($this->declareStrictTypes === true) {
            $file->setDeclares([DeclareStatement::strictTypes(1)]);
        }

        $file->setClass($this->classGenerator);

        $code = $file->generate();

        if ($this->formatPHPCode === true) {
            return $this->formatPhpCode($code);
        }

        return $code;
    }

    protected function formatPhpCode(string $code): string {
        return (new NetteFactory())->fromCode($code)->__toString();
    }

    public function write(): self {
        if ($this->fileName == '' || ! is_writable(dirname($this->fileName))) {
            throw new Exception('This code generator object is not writable.');
        }

        file_put_contents($this->fileName, $this->generate());

        return $this;
    }
}