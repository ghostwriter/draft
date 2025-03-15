<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition;

use Ghostwriter\Draft\Application\Definition\Router\Action\RouteDeleteDefinition;
use Ghostwriter\Draft\Application\Definition\Router\Action\RouteGetDefinition;
use Ghostwriter\Draft\Application\Definition\Router\Action\RoutePostDefinition;
use Ghostwriter\Draft\Application\Definition\Router\Action\RoutePutDefinition;
use Ghostwriter\Draft\Application\Definition\Router\RouteGroupDefinition;
use Ghostwriter\Draft\Application\Definition\Router\RouteViewDefinition;
use Ghostwriter\Draft\Application\Exception\RuntimeException;
use Ghostwriter\Draft\Application\Interface\DefinitionInterface;
use Str;

use function array_diff;
use function array_key_exists;
use function array_merge;
use function implode;
use function sprintf;

final class RouterDefinition implements DefinitionInterface
{
    public function __construct(
        private string $name = '',
        private string $prefix = '',
        private array $middlewares = [],
        private array $routes = [],
        private array $routerGroupDefinitions = [],
    ) {}

    public static function new(): self
    {
        return new self();
    }

    public function apiResource($name, $controller, array $options = [])
    {
        $only = ['index', 'show', 'store', 'update', 'destroy'];

        if (isset($options['except'])) {
            $only = array_diff($only, (array) $options['except']);
        }

        return $this->resource($name, $controller, array_merge([
            'only' => $only,
        ], $options));
    }

    public function apiSingleton($name, $controller, array $options = [])
    {
        $only = ['store', 'show', 'update', 'destroy'];

        if (isset($options['except'])) {
            $only = array_diff($only, (array) $options['except']);
        }

        return $this->singleton($name, $controller, array_merge([
            'only' => $only,
        ], $options));
    }

    public function delete(string $route, string $handler): self
    {
        $this->throwIfDuplicateRouteFound($route, 'DELETE');

        $this->routes[$route] = RouteDeleteDefinition::new($route, $handler);

        return $this;
    }

    public function get(string $route, string $handler): self
    {
        foreach (['GET', 'HEAD'] as $method) {
            $this->throwIfDuplicateRouteFound($route, $method);

            $this->routes[$route] = RouteGetDefinition::new($route, $method);

        }

        return $this;
    }

    public function group(callable $factory): self
    {
        $routerGroupDefinition = RouteGroupDefinition::new($this->name, $this->prefix, $this->middlewares);

        $factory($routerGroupDefinition);

        $this->routerGroupDefinitions[] = $routerGroupDefinition;

        return $this;
    }

    public function middleware(string ...$middlewares): self
    {
        foreach ($middlewares as $middleware) {
            $this->middlewares[$middleware] = $middleware;
        }

        return $this;
    }

    public function name(string $name, string $suffix = ''): self
    {

        $this->name = $name . $suffix;

        return $this;
    }

    public function patch(string $route, string $handler): self
    {
        $this->throwIfDuplicateRouteFound($route, 'PATCH');

        $this->routes[$route] = RoutePutDefinition::new($route, $handler);

        return $this;
    }

    public function post(string $route, string $handler): self
    {
        $this->throwIfDuplicateRouteFound($route, 'POST');

        $this->routes[$route] = RoutePostDefinition::new($route, $handler);

        return $this;
    }

    public function prefix(string $name): self
    {
        $this->prefix = $name;

        return $this;
    }

    public function put(string $route, string $handler): self
    {
        $this->throwIfDuplicateRouteFound($route, 'PUT');

        $this->routes[$route] = RoutePutDefinition::new($route, $handler);

        return $this;
    }

    public function resource(string $name, array $except = []): self
    {
        $only = ['index', 'create', 'show', 'edit', 'store', 'update', 'destroy'];

        $singular = Str::singular($name);
        $plural = Str::plural($name);

        $model = Str::studly($singular);
        $controller = $model . 'Controller';

        $this->get($plural, $controller . '.index');
        $this->get($plural . '/create', $controller . '.create');
        $this->get($plural . '/{' . $singular . '}', $controller . '.show');
        $this->get($plural . '/{' . $singular . '}/edit', $controller . '.edit');
        $this->post($plural, $controller . '.store');
        $this->put($plural . '/{' . $singular . '}', $controller . '.update');
        $this->delete($plural . '/{' . $singular . '}', $controller . '.destroy');

        foreach (
            array_diff(array_diff($except, $except), $only) as $method) {

            match ($method) {
                'index' => $this->get($plural, $controller . '.index'),
                'destroy' => $this->delete($name . '/{id}', $name . '.destroy'),
                'store' => 0,
                'update' => 0,
                'edit' => 0,
                'show' => 0,
                'create' => 0,
                default => throw new RuntimeException(sprintf(
                    'Unsupported method %s, available: %s',
                    $method,
                    implode(', ', $only)
                ))
            };
        }

        $this->get($name, $name . '.index');
        $this->get($name . '/{id}', $name . '.show');
        $this->post($name, $name . '.store');
        $this->put($name . '/{id}', $name . '.update');

        return $this;
    }

    public function routes(): array
    {
        return $this->routes;
    }

    public function throwIfDuplicateRouteFound(string $route, string $type): void
    {
        if (! array_key_exists($route, $this->routes)) {
            return;
        }

        if (! array_key_exists($type, $this->routes[$route])) {
            return;
        }

        throw new RuntimeException(sprintf('Duplicate route found: %s with type: %s', $route, $type));
    }

    public function view(string $route, string $template): self
    {
        $this->throwIfDuplicateRouteFound($route, __FUNCTION__);

        $this->routes[$route] = RouteViewDefinition::new($route, $template);

        return $this;
    }
}
