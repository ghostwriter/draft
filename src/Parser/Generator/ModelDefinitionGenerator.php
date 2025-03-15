<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Generator;

use Ghostwriter\Draft\Application\Interface\DefinitionInterface;
use Ghostwriter\Draft\Parser\Builder\ModelDefinitionBuilder;
use Ghostwriter\Draft\Parser\Printer;

final class ModelDefinitionGenerator implements DefinitionGeneratorInterface
{
    public function __construct(
        private ModelDefinitionBuilder $builder,
        private Printer $printer,
    ) {}

    public function generate(DefinitionInterface $definition): string
    {
        $node = $this->builder->build($definition);

        return $this->printer->prettyPrintFile([$node]);
    }
}
