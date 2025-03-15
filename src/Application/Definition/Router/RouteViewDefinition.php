<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Router;

use Ghostwriter\Draft\Application\Interface\DefinitionInterface;

final readonly class RouteViewDefinition implements DefinitionInterface
{
    public function __construct(
        private string $route,
        private string $template,
    ) {}

    public static function new(string $route, string $template): self
    {
        return new self($route, $template);
    }

    public function name(): string
    {
        return $this->name;
    }
    //
    //    public function get(string $string, callable $factory): RouteInterface
    //    {
    //        return $factory($this->name, $string);
    //    }
}
