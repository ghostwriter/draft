<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Action;

final class InertiaActionDefinition
{
    public function __construct(
        private readonly string $name,
        private array $properties = [],
    ) {}

    public function data(string ...$properties): void
    {
        $this->properties = $properties;
    }

    public function fire(string $event, array $data = []): void {}

    public function name(): string
    {
        return $this->name;
    }

    public function render(string $template, array $data = []): void {}
}
