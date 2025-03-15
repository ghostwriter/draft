<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Builder;

use Ghostwriter\Draft\Application\Interface\DefinitionInterface;
use Ghostwriter\Draft\Parser\Generator\DefinitionGeneratorInterface;
use Ghostwriter\Draft\Parser\Printer;

final class Generator implements DefinitionGeneratorInterface
{
    public function __construct(
        private Builder $builder,
        private Printer $printer,
    ) {}

    /**
     * Generates a string representation of the given definition.
     *
     * @param DefinitionInterface $definition the definition to generate
     *
     * @return string the generated string representation of the definition
     */
    public function generate(DefinitionInterface $definition): string
    {
        $node = $this->builder->build($definition);

        return $this->printer->prettyPrintFile([$node]);
    }
}
