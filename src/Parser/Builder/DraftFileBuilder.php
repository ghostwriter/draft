<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Builder;

use Ghostwriter\Draft\Application\Interface\DefinitionInterface;
use Ghostwriter\Draft\Parser\Node\DraftFileNode;
use Override;
use PhpParser\Node;

final class DraftFileBuilder extends AbstractDefinitionBuilder
{
    #[Override]
    public function build(DefinitionInterface $definition): Node
    {
        return new DraftFileNode();
    }
}
