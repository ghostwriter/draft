<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Node;

use Ghostwriter\Draft\Draft;
use Override;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;
use PhpParser\NodeAbstract;

use function mb_strrpos;
use function mb_substr;

final class DraftFileNode extends NodeAbstract
{
    /** @var Stmt[] */
    public array $stmts = [];

    /**
     * @param array<string, mixed> $attributes Array of attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->stmts = [
            new Declare_([new DeclareDeclare(new Identifier('strict_types'), new Int_(1))]),
            new Nop(),
            new Use_([new UseItem(new Name(Draft::class))]),
            new Nop(),
            new Return_(new Closure([
                'attrGroups' => [],
                'static' => true,
                'byRef' => false,
                'params' => [new Param(new Variable('draft'), null, new Name('Draft'))],
                'uses' => [],
                'returnType' => new Identifier('void'),
                'stmts' => [new Nop()],
            ])),
        ];
        parent::__construct($attributes);
    }

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
