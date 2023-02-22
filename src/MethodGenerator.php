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
    use Traits\ImposeFtiTrait;

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

        $this->imposeFTIDocBlock()->newParamTag(
            $name,
            Utils::createTypesArray($type),
            $desc
        );

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

    public function importSource(
        ClassGenerator|string $class,
        ?string $method = null
    ): self {
        if ($class instanceof ClassGenerator) {
            $name = $class->getName();
            $namespace = $class->getNamespaceName();
            if (! is_null($namespace)) {
                $name = $namespace.'\\'.$name;
            }

            $class = $name;
        }

        if (is_null($method)) {
            $method = $this->getName();
        }

        /**
         * @psalm-suppress ArgumentTypeCoercion
         */
        $reflect = new ClassReflection($class); //@phpstan-ignore-line

        if ($reflect->hasMethod($method) === false) {
            throw new Exception('Reflection has no method!');
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
