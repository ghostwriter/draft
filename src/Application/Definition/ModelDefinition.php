<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition;

use Ghostwriter\Container\Container;
use Ghostwriter\Draft\Application\Definition\Migration\RelationshipDefinition;
use Ghostwriter\Draft\Application\Formatter;
use Ghostwriter\Draft\Application\Interface\DefinitionInterface;
use RuntimeException;

use function mb_strtolower;

final class ModelDefinition implements DefinitionInterface
{
    public function __construct(
        private readonly string $name,
        private readonly string $table,
        private array $relationships = [],
        private ?TestDefinition $testDefinition = null,
        private ?ControllerDefinition $controllerDefinition = null,
        private ?MigrationDefinition $migrationDefinition = null,
    ) {}

    public function casts(array $array): void {}

    public function controller(callable $factory): void
    {
        if ($this->controllerDefinition instanceof ControllerDefinition) {
            throw new RuntimeException('Controller already defined');
        }

        $this->controllerDefinition = new ControllerDefinition($this->formatControllerName($this->name));

        Container::getInstance()->call($factory, [$this->controllerDefinition]);
    }

    public function fillable(array $array): void {}

    public function hidden(array $array): void {}

    //    public function __call(string $name, array $arguments): self
    //    {
    //        $firstArgument = $arguments[0] ?? $name;
    //
    //        $this->fields[$firstArgument] = [$name, $arguments];
    //
    //        return $this;
    //    }

    //    public function fields(): array
    //    {
    //        return $this->fields;
    //    }

    public function migration(callable $callback, ?string $table = null): void
    {
        if ($this->migrationDefinition instanceof MigrationDefinition) {
            throw new RuntimeException('Migration already defined');
        }

        $this->migrationDefinition = new MigrationDefinition($this->formatMigrationName($table ?? $this->table));

        Container::getInstance()->call($callback, [$this->migrationDefinition]);
    }

    //    public function addFactory(FactoryDefinition $factoryDefinition): void
    //    {
    //        $this->draft->factoryDefinitions[$factoryDefinition->name()] = $factoryDefinition;
    //    }

    public function name(): string
    {
        return $this->name;
    }

    public function namespace(): string
    {
        return 'App\\Models';
    }

    public function relationships(string $method): RelationshipDefinition
    {

        return $this->relationships[$method] ??= new RelationshipDefinition(
            $method,
            $this->name,
            mb_strtolower($this->name) . 's',
        );
    }

    public function resourceController(): void
    {
        $this->controller(
            fn (
                ControllerDefinition $controllerDefinition,
            )
                => $controllerDefinition->resource($this->name),
        );

        //        $this->controller(
        //            $name,
        //            static fn (
        //                ControllerDefinition $controllerDefinition,
        //            ) => $controllerDefinition->resource($pluralName),
        //        );

        //        $this->draft->resourceController($this->name());
        //        $this->draft->controller($this->name, static function (ControllerDefinition $controllerDefinition): void {
        //            $controllerDefinition->resource($this->name);
        //        });
    }

    //    public function hasOne(string $model, ?string $foreignKey = null, string $localKey = 'id'): self
    //    {
    //        $this->relationships('hasOne')->hasOne($model, $foreignKey, $localKey);
    //
    //        return $this;
    //    }

    public function table(): string
    {
        return Container::getInstance()->get(Formatter::class)->formatTableName($this->name);

        return mb_strtolower($this->name) . 's';
    }

    public function test(callable $factory): void
    {
        if ($this->testDefinition instanceof TestDefinition) {
            throw new RuntimeException('Test already defined');
        }

        $this->testDefinition = new TestDefinition($this->name);

        Container::getInstance()->call($factory, [$this->testDefinition]);
    }

    private function formatControllerName(string $name): string
    {
        return Container::getInstance()->get(Formatter::class)->formatControllerName($name);
    }

    private function formatMigrationName(string $table): string
    {
        return Container::getInstance()->get(Formatter::class)->formatMigrationName($table);
    }
}
