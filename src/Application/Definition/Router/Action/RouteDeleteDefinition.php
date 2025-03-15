<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Router\Action;

use Ghostwriter\Draft\Application\Interface\DefinitionInterface;

final class RouteDeleteDefinition implements DefinitionInterface
{
    public function __construct(
        private string $route,
        private string $handler,
    ) {}
}

// Router::can('create.posts')
