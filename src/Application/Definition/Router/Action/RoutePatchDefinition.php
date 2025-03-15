<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Router\Action;

use Ghostwriter\Draft\Application\Interface\DefinitionInterface;

final readonly class RoutePatchDefinition implements DefinitionInterface
{
    public function __construct(
        private string $route,
        private string $handler,
    ) {}
}
