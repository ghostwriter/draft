<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Visitor;

use Override;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitor;

final class NotificationVisitor implements NodeVisitor
{
    use NodeVisitorTrait;

    private ?Class_ $class = null;

    private ?Identifier $identifier = null;

    #[Override]
    public function enterNode(Node $node): null|int|Node
    {
        if (! $node instanceof Class_) {
            return self::dontTraverseChildren();
        }

        $this->class = $node;

        $nodeName = $node->name;
        if (null === $nodeName) {
            $node->name = new Identifier('Untitled');

            return $node;
        }

        $this->identifier = $nodeName;

        return null;
    }
}
