<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Node;

use PhpParser\Node\Stmt;
use PhpParser\NodeAbstract;

use function mb_strrpos;
use function mb_substr;

abstract class AbstractNode extends NodeAbstract
{
    /** @var Stmt[] */
    public array $stmts = [];

    /** @return Stmt[] */
    public function getStmts(): array
    {
        return $this->stmts;
    }

    #[Override]
    public function getSubNodeNames(): array
    {
        return ['stmts'];
    }

    #[Override]
    public function getType(): string
    {
        $last = mb_strrpos(self::class, '\\');

        if (false === $last) {
            // If the last occurrence is false,
            // it means that there is no backslash in the class name
            return self::class;
        }

        /** @var non-empty-string */
        return mb_substr(self::class, 1 + $last);
    }
}
