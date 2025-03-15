<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Visitor;

use PhpParser\NodeVisitor;

final class FormRequestVisitor implements NodeVisitor
{
    use NodeVisitorTrait;
}
