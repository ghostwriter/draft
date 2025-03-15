<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Generator;

use Ghostwriter\Draft\Application\Interface\DefinitionInterface;

interface DefinitionGeneratorInterface
{
    public function generate(DefinitionInterface $definition): string;
}
