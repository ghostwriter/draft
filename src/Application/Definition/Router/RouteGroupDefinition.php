<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Router;

use Ghostwriter\Draft\Application\Definition\Router\Action\RouteAnyDefinition;
use Ghostwriter\Draft\Application\Definition\Router\Action\RouteDeleteDefinition;
use Ghostwriter\Draft\Application\Definition\Router\Action\RouteGetDefinition;
use Ghostwriter\Draft\Application\Definition\Router\Action\RoutePatchDefinition;
use Ghostwriter\Draft\Application\Definition\Router\Action\RoutePostDefinition;
use Ghostwriter\Draft\Application\Definition\Router\Action\RoutePutDefinition;
use Ghostwriter\Draft\Application\Interface\DefinitionInterface;

final class RouteGroupDefinition implements DefinitionInterface
{
    private array $names = [];

    private array $redirects = [];

    private array $routes = [];

    public function __construct(
        private string $name,
        private string $prefix,
        private array $middlewares = [],
    ) {}

    public static function new(string $name, string $prefix, array $middlewares): self
    {
        return new self($name, $prefix, $middlewares);
    }

    public function any(string $route, string $handler): self
    {
        $this->routes[$route][$handler] ??= new RouteAnyDefinition($route, $handler);

        return $this;
    }

    public function delete(string $route, string $handler): self
    {
        $this->routes[$route][$handler] ??= new RouteDeleteDefinition($route, $handler);

        return $this;
    }

    public function fallback(string $action): self
    {
        $this->routes[$action][$action] ??= new RouteDeleteDefinition($route, $handler);

        return $this;
    }

    public function get(string $route, string $handler): self
    {
        $this->routes[$route][$handler] = new RouteGetDefinition($route, $handler);

        return $this;
    }

    public function name(string $name): self
    {
        $this->name = $name;

        //        $routeName = $this->names[$name] ??= new RouteName($name);;
        //
        //        $this->routes[\array_key_last($this->routes)] = $routeName;

        return $this;
    }

    public function patch(string $route, string $handler): self
    {
        $this->routes[$route][$handler] ??= new RoutePatchDefinition($route, $handler);

        return $this;
    }

    public function post(string $route, string $handler): self
    {
        $this->routes[$route][$handler] ??= new RoutePostDefinition($route, $handler);

        return $this;
    }

    public function prefix(string $name): self
    {
        $this->prefix = $name;
        //        $routeName = $this->names[$name] ??= new RouteName($name);;
        //
        //        $this->routes[\array_key_last($this->routes)] = $routeName;

        return $this;
    }

    public function put(string $route, string $handler): self
    {
        $this->routes[$route][$handler] ??= new RoutePutDefinition($route, $handler);

        return $this;
    }

    public function redirect(string $fromRoute, string $toRoute): self
    {
        $this->redirects[$fromRoute][$toRoute] = new RouteRedirectDefinition($fromRoute, $toRoute);

        return $this;
    }

    public function resource(string $name): self
    {
        $this->routes[$name] = new RouteResourceDefinition($name);

        return $this;
    }

    public function view(string $route, string $view): self
    {
        $this->routes[$route][$view] ??= new RouteViewDefinition($route, $view);

        return $this;
    }
}
