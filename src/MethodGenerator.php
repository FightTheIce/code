<?php

declare(strict_types=1);

namespace FightTheIce\Code;

use Exception;
use Laminas\Code\Generator\MethodGenerator as Laminas_MethodGenerator;
use Laminas\Code\Generator\ParameterGenerator;
use Laminas\Code\Generator\PromotedParameterGenerator;
use Laminas\Code\Reflection\ClassReflection;

class MethodGenerator extends Laminas_MethodGenerator
{
    use Traits\DocBlockerTrait;

    public function newParameterGenerator(
        ?string $name = null,
        ?string $type = null,
        mixed $defaultValue = null,
        ?int $position = null,
        bool $passByReference = false
    ): ParameterGenerator {
        $parameter = new ParameterGenerator(
            $name,
            $type,
            $defaultValue,
            $position,
            $passByReference
        );

        $this->setParameter($parameter);

        return $parameter;
    }

    public function addTypedParameter(
        string $name,
        string $desc,
        string $type,
        mixed $defaultValue = null,
        bool $omitDefaultValue = false
    ): ParameterGenerator {
        $parameter = $this->newParameterGenerator($name, $type, $defaultValue);
        $parameter->setDefaultValue($defaultValue);
        $parameter->omitDefaultValue($omitDefaultValue);

        $dTypes = [];
        if (substr($type, 0, 1) === '?') {
            $dTypes[] = substr($type, 1);
            $dTypes[] = 'null';
        } else {
            $dTypes = explode('|', $type);
        }

        $this->imposeFTIDocBlock()->newParamTag($name, $dTypes, $desc);

        return $parameter;
    }

    /**
     * @psalm-param non-empty-string $name
     * @psalm-param ?non-empty-string $type
     * @psalm-param PromotedParameterGenerator::VISIBILITY_* $visibility
     */
    public function newPromotedParameterGenerator(
        string $name,
        ?string $type = null,
        string $visibility = PromotedParameterGenerator::VISIBILITY_PUBLIC,
        ?int $position = null,
        bool $passByReference = false
    ): PromotedParameterGenerator {
        if ($this->getName() !== '__construct') {
            throw new Exception(
                'Promotions may only be generated on the construct method!'
            );
        }

        $parameter = new PromotedParameterGenerator(
            $name,
            $type,
            $visibility,
            $position,
            $passByReference
        );

        $this->setParameter($parameter);

        return $parameter;
    }

    public function importSource(string $class, ?string $method = null): self
    {
        $method = is_null($method) ? $this->getName() : $method;

        /**
         * @psalm-suppress ArgumentTypeCoercion
         */
        $reflect = new ClassReflection($class); //@phpstan-ignore-line

        if ($reflect->hasMethod($method) === false) {
            throw new Exception('Reflection error');
        }

        $body = $reflect->getMethod($method)->getBody();
        $lines = preg_split('/\R/', $body);

        if (! $lines) {
            throw new Exception('Method body');
        }

        foreach ($lines as &$line) {
            $segments = explode('    ', $line);

            if (count($segments) > 2) {
                array_shift($segments);
                array_shift($segments);
                $line = implode('    ', $segments);
            }
        }

        $this->setBody(trim(implode(PHP_EOL, $lines)));

        return $this;
    }
}
