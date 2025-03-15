<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Visitor;

use Override;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitor;

final class MigrationVisitor implements NodeVisitor
{
    use NodeVisitorTrait;

    private ?Class_ $class = null;

    #[Override]
    public function enterNode(Node $node): mixed
    {
        if (! $node instanceof Class_) {
            return null;
        }

        $this->class = $node;

        return null;
    }
}
