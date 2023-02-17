<?php

namespace FightTheIce\Code;

use Laminas\Code\Generator\BodyGenerator;
use Exception;
use Laminas\Code\Generator\AbstractGenerator;

class CodeGenerator extends BodyGenerator {
    protected ?AbstractGenerator $abstract = null;

    public function __construct(?string $body = null, ?AbstractGenerator $abstract = null) {
        if (!is_null($body)) {
            $this->appendContents($body);
        }

        if (!is_null($abstract)) {
            $this->setAbstractGenerator($abstract);
        }
    }
    
    public function setAbstractGenerator(AbstractGenerator $abstract) {
        $this->abstract = $abstract;
    }

    public function finish(): AbstractGenerator {
        if (is_null($this->abstract)) {
            throw new Exception('Abstract object not set!');
        }

        $this->abstract->setBody($this->getContent());

        return $this->abstract;
    }

    public function comment(string $comment, string $type = '//'): self {
        $type = strtolower(trim($type));
        if (!in_array($type,array('#','//'))) {
            throw new Exception('Moo Cow!');
        }

        return $this->appendContents($type.$comment);
    }

    public function callParent(string $method,array $parameters = []) {
        $contents = 'parent::'.$method;

        foreach ($parameters as &$param) {
            if (!is_string($param)) {
                throw new Exception(self::class.'::callParent expects param to be a string!');
            }

            $param = '$'.$param;
        }

        $contents.= '('.implode(',',$parameters).');';

       $this->appendContents($contents);

        return $this;
    }

    public function updateProperty(string $name,string $value): self {
        return $this->appendContents('$this->'.$name.' = '.$value);
    }

    public function newVar(string $name, string $value): self {
        return $this->appendContents('$'.$name.' = '.$value.';');
    }

    public function callMethod(string $name, array $parameters = []): self {
        $body = '$this->'.$name.'(';

        foreach ($parameters as $name) {
            $body.= '$'.$name.',';
        }

        $body = rtrim($body,',').');';

        return $this->appendContents($body);
    }

    public function newLine(): self {
        return $this->appendContents('',true);
    }

    protected function appendContents(string $contents, bool $skipTrim = false): self {
        $body = $this->getContent();
        $body.= PHP_EOL.$contents;

        if ($skipTrim===false) {
            $body = trim($body);
        }

        $this->setContent($body);

        return $this;
    }

    public function returnValue(string $value) {
        return $this->appendContents('return '.rtrim($value,';').';');
    }
}