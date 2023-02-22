<?php

declare(strict_types=1);

namespace FightTheIce\Code;

class Utils
{
    /**
     * resolveUses
     *
     * @param ClassGenerator $class
     *
     * @return array<int, array{class: string, alias: string|null, resolved: string}>
     */
    public static function resolveUses(
        ClassGenerator $class
    ): array {
        $uses = $class->getUses();
        $return = [];
        foreach ($uses as $use) {
            $segments = explode(' as ', $use);
            $tmp = [];
            $tmp['class'] = reset($segments);
            if (count($segments) === 2) {
                $tmp['alias'] = end($segments);
                $tmp['resolved'] = $tmp['alias'];
            } else {
                $classSegments = explode('\\', $tmp['class']);
                $tmp['alias'] = null;
                $tmp['resolved'] = end($classSegments);
            }

            $return[] = $tmp;
        }

        return $return;
    }

    /**
     * @return non-empty-array<int, string>
     */
    public static function createTypesArray(string $typeHint): array
    {
        $types = [];

        if (substr($typeHint, 0, 1) === '?') {
            $types[] = substr($typeHint, 1);
            $types[] = 'null';
        } else {
            $types = explode('|', $typeHint);
        }

        return $types;
    }

    /**
     * @return non-empty-array<int, string>
     */
    public static function createTypesArrayResolvedUses(
        string $typeHint,
        ClassGenerator $class
    ): array {
        $types = self::createTypesArray($typeHint);
        $resolved = self::resolveUses($class);
        $reference = [];
        $cResolved = count($resolved);
        for ($a = 0; $a < $cResolved; $a++) {
            $res = $resolved[$a];
            $reference[$res['class']] = $a;
        }

        $checkNamespace = $class->hasNamespaceName();
        $namespace = '|';
        $nsChar = 0;
        if ($checkNamespace === true) {
            /** @psalm-suppress PossiblyNullOperand */
            $namespace = $class->getNamespaceName().'\\';
            $nsChar = strlen($namespace);
        }

        foreach ($types as &$type) {
            if (array_key_exists($type, $reference)) {
                //update type to our resolved name
                $type = $resolved[$reference[$type]];
                continue;
            }

            if ($checkNamespace === true) {
                if (substr($type, 0, $nsChar) === $namespace) {
                    $segments = explode($namespace, $type);
                    array_shift($segments);
                    $type = implode('\\', $segments);
                }
            }
        }

        return $types;
    }
}
