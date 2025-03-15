<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition;

use Ghostwriter\Container\Container;
use Ghostwriter\Draft\Application\Definition\Action\LivewireActionDefinition;
use Ghostwriter\Draft\Application\Interface\DefinitionInterface;

final class LivewireDefinition implements DefinitionInterface
{
    public function __construct(
        private readonly string $name,
        private array $actions = [],
    ) {}

    public function action(string $name, callable $callback): void
    {
        $actionDefinition = $this->actions[$name] ??= new LivewireActionDefinition($name);
        // $this->actionDefinition($name);

        Container::getInstance()->call($callback, [$actionDefinition, $this]);
    }

    //    public function actionDefinition(string $name): LivewireActionDefinition
    //    {
    //        return
    //
    //        //        $livewireDefinition->action('update', static function (LivewireActionDefinition $livewireActionDefinition): void {
    //        //            $livewireActionDefinition->fire('ProfileUpdated', ['user']);
    //        //        });
    //        //
    //        //        $livewireDefinition->actionDefinition('update')->fire('ProfileUpdated', ['user']);
    //    }

    public function actions(): array
    {
        return $this->actions;
    }

    public function mount(string ...$properties): void
    {
        $this->action(
            'mount',
            static fn (
                LivewireActionDefinition $livewireActionDefinition
            ) => $livewireActionDefinition->data(...$properties)
        );
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
                LivewireActionDefinition $livewireActionDefinition
            ) => $livewireActionDefinition->render($template, $parameters)
        );
    }
}
