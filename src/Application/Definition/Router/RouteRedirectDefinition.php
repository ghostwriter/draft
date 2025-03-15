<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Router;

use Ghostwriter\Draft\Application\Interface\DefinitionInterface;

final readonly class RouteRedirectDefinition implements DefinitionInterface
{
    public function __construct(
        private string $fromRoute,
        private string $toRoute,
    ) {}
}
