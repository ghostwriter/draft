<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Action;

final class ResourceActionDefinition
{
    public function __construct(
        private readonly string $name,
        private array $except = [],
    ) {}
}
