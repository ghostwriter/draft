<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Router;

use Ghostwriter\Draft\Application\Interface\DefinitionInterface;

final readonly class RouteResourceDefinition implements DefinitionInterface
{
    public function __construct(
        private string $name,
    ) {}
}
