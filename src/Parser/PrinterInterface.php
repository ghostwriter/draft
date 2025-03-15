<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser;

use PhpParser\Node;

interface PrinterInterface
{
    public function print(Node $node): string;
}
