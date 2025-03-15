<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition;

use Ghostwriter\Container\Container;
use Ghostwriter\Draft\Application\Definition\Action\ControllerActionDefinition;
use Ghostwriter\Draft\Application\Interface\DefinitionInterface;
use Ghostwriter\Draft\Draft;

use function array_diff;

final class ControllerDefinition implements DefinitionInterface
{
    public function __construct(
        private readonly string $name,
        private array $actions = [],
        private array $middlewares = [],
    ) {}

    public static function new(string $name): self
    {
        return new self($name);
    }

    public function action(string $name, callable $callback): void
    {
        $actionDefinition = $this->actions[$name] ??= new ControllerActionDefinition($name);

        Container::getInstance()->call($callback, [$actionDefinition]);

        $this->actions[$name] = $actionDefinition;
    }

    public function actions(): array
    {
        return $this->actions;
    }

    public function create(callable $callback): void
    {
        $this->action($this->name . '.create', $callback);
    }

    public function destroy(callable $callback): void
    {
        $this->action($this->name . '.destroy', $callback);
    }

    public function edit(callable $callback): void
    {
        $this->action($this->name . '.edit', $callback);
    }

    public function index(callable $callback): void
    {
        $this->action($this->name . '.index', $callback);
    }

    public function middlewares(): array
    {
        return $this->middlewares;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function render(string $template, array $parameters = []): void
    {
        $this->action(
            'render',
            static fn (
                ControllerActionDefinition $controllerActionDefinition
            ) => $controllerActionDefinition->render($template, $parameters)
        );
    }

    public function resource(string $name, array $except = []): void
    {
        foreach (array_diff(Draft::RESOURCE_ACTIONS, $except) as $action) {
            $this->{$action}(static fn () => true);
        }
    }

    public function show(callable $callback): void
    {
        $this->action($this->name . '.show', $callback);
    }

    public function store(callable $callback): void
    {
        $this->action($this->name . '.store', $callback);
    }

    public function update(callable $callback): void
    {
        $this->action($this->name . '.update', $callback);
    }

    private function route(string $name, $action): void {}
}
