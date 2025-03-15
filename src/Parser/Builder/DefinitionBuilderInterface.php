<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Builder;

use Ghostwriter\Draft\Application\Interface\DefinitionInterface;
use PhpParser\Node;

interface DefinitionBuilderInterface
{
    public function build(DefinitionInterface $definition): Node;
}
