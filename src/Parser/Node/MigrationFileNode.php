<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Node;

use Ghostwriter\Draft\Draft;
use Override;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Declare_;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UseItem;
use PhpParser\NodeAbstract;

final class MigrationFileNode extends NodeAbstract
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
        return 'MigrationFileNode';
    }
}
