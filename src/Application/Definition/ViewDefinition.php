<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition;

use Ghostwriter\Draft\Application\Interface\DefinitionInterface;

final readonly class ViewDefinition implements DefinitionInterface
{
    public function __construct(
        private string $name,
    ) {}

    public function name(): string
    {
        return $this->name;
    }
}
