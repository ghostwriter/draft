<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser;

use Ghostwriter\Draft\Parser\Node\DraftFileNode;
use Override;
use PhpParser\Node;
use PhpParser\PrettyPrinter\Standard;

final class Printer extends Standard implements PrinterInterface
{
    /**
     * Pretty prints a given node.
     *
     * @param Node $node the node to pretty print
     *
     * @return string the pretty printed representation of the node
     */
    #[Override]
    public function print(Node $node): string
    {
        return $this->prettyPrintFile([$node]);
    }

    protected function pDraftFileNode(DraftFileNode $node): string
    {
        return $this->prettyPrintFile($node->getStmts());
    }
}
