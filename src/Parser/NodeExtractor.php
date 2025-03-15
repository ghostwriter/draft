<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser;

use PhpParser\Node\Const_;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\PropertyProperty;

use function array_merge;
use function array_reduce;
use function dd;
use function is_string;

final class NodeExtractor
{
    public static function debug(mixed ...$arguments): mixed
    {
        // This is a placeholder for debugging purposes.
        dd($arguments);

        return $arguments; // Return null to avoid breaking the flow
    }

    public static function getClass(mixed $node): mixed
    {
        $node = $node->class;

        return match (true) {
            null === $node => null,
            $node instanceof FullyQualified => $node->toString(),
            default => self::debug($node),
        };
    }

    public static function getConsts(ClassConst $classConst): array
    {
        return array_reduce(
            $classConst->consts,
            static function (array $carry, Const_ $const) {
                /** @var ?string $nodeKey */
                $nodeKey = self::getName($const);
                /** @var mixed $nodeValue */
                $nodeValue = self::getValue($const);

                if (null === $nodeKey) {
                    $carry[] = $nodeValue;

                    return $carry;
                }

                $carry[$nodeKey] = $nodeValue;

                return $carry;
            },
            []
        );
    }

    public static function getDefault(Param|PropertyProperty $node): mixed
    {
        /** @var mixed $node */
        $node = $node->default;

        return match (true) {
            default => self::debug($node),
            null === $node => null,
            $node instanceof Array_ => self::getItems($node),
            $node instanceof ConstFetch => self::getName($node),
        };
    }

    public static function getItems(Array_ $array): mixed
    {
        return array_reduce(
            $array->items,
            static function (array $carry, ArrayItem $arrayItem) {
                /** @var ?string $nodeKey */
                $nodeKey = self::getKey($arrayItem);

                /** @var mixed $nodeValue */
                $nodeValue = self::getValue($arrayItem);

                if (null === $nodeKey) {
                    $carry[] = $nodeValue;

                    return $carry;
                }

                $carry[$nodeKey] = $nodeValue;

                return $carry;
            },
            []
        );
    }

    public static function getKey(ArrayItem $arrayItem): mixed
    {
        $arrayItem = $arrayItem->key;

        return match (true) {
            null === $arrayItem => null,
            $arrayItem instanceof String_ => self::getValue($arrayItem),
            default => self::debug($arrayItem),
        };
    }

    public static function getName(
        Class_|ClassConstFetch|ClassMethod|Const_|ConstFetch|Identifier|PropertyProperty|Variable $node
    ): ?string {
        $node = $node->name;

        return match (true) {
            null === $node => null,
            is_string($node) => $node,
            $node instanceof Identifier => self::getName($node),
            $node instanceof Name => $node->toString(),
            default => self::debug($node),
        };
    }

    public static function getParams(ClassMethod $classMethod): array
    {
        return array_reduce(
            $classMethod->params,
            static fn (array $carry, Param $param): array
                => array_merge($carry, [
                    self::getVar($param) => self::getDefault($param),
                ]),
            []
        );
    }

    public static function getProps(ClassMethod $classMethod): array
    {
        return array_reduce(
            $classMethod->props,
            static fn (array $carry, Param $param): array
            => array_merge($carry, [
                self::getVar($param) => self::getDefault($param),
            ]),
            []
        );
    }

    public static function getValue(ArrayItem|ClassConstFetch|Const_|ConstFetch|String_ $node): mixed
    {
        /** @var mixed $node */
        $node = $node->value;

        return match (true) {
            $node instanceof ClassConstFetch => self::getClass($node) . '::' . self::getName($node),
            $node instanceof ConstFetch => self::getName($node),
            $node instanceof String_ => self::getValue($node),
            default => self::debug($node),
            is_string($node) => $node,
        };
    }

    public static function getVar(Param $param): mixed
    {
        $param = $param->var;

        return match (true) {
            default => self::debug($param),
            $param instanceof Variable => self::getName($param),
        };
    }
}
