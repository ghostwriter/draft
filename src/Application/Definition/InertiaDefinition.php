<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition;

use Ghostwriter\Container\Container;
use Ghostwriter\Draft\Application\Definition\Action\InertiaActionDefinition;
use Ghostwriter\Draft\Application\Interface\DefinitionInterface;

final class InertiaDefinition implements DefinitionInterface
{
    public function __construct(
        private readonly string $name,
        private array $actions = [],
    ) {}

    public function action(string $name, callable $callback): void
    {
        $actionDefinition = $this->actionDefinition($name);

        Container::getInstance()->call($callback, [$actionDefinition, $this]);
    }

    public function actionDefinition(string $name): InertiaActionDefinition
    {
        return $this->actions[$name] ??= new InertiaActionDefinition($name);
    }

    public function mount(string ...$properties): void
    {
        $this->action(
            'mount',
            static fn (InertiaActionDefinition $inertiaActionDefinition)
                => $inertiaActionDefinition->data(...$properties),
        );
    }

    public function render(string $template): void
    {
        $this->action(
            'render',
            static fn (InertiaActionDefinition $inertiaActionDefinition)
                => $inertiaActionDefinition->render($template),
        );
    }
}
