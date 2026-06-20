<?php

declare(strict_types=1);

namespace Ghostwriter\Draft;

use Ghostwriter\CaseConverter\Interface\CaseConverterInterface;
use Ghostwriter\Container\Attribute\Provider;
use Ghostwriter\Container\Container;
use Ghostwriter\Draft\Application\Definition\ControllerDefinition;
use Ghostwriter\Draft\Application\Definition\EventDefinition;
use Ghostwriter\Draft\Application\Definition\FactoryDefinition;
use Ghostwriter\Draft\Application\Definition\InertiaDefinition;
use Ghostwriter\Draft\Application\Definition\JobDefinition;
use Ghostwriter\Draft\Application\Definition\LivewireDefinition;
use Ghostwriter\Draft\Application\Definition\MailDefinition;
use Ghostwriter\Draft\Application\Definition\MigrationDefinition;
use Ghostwriter\Draft\Application\Definition\ModelDefinition;
use Ghostwriter\Draft\Application\Definition\NotificationDefinition;
use Ghostwriter\Draft\Application\Definition\PolicyDefinition;
use Ghostwriter\Draft\Application\Definition\RouterDefinition;
use Ghostwriter\Draft\Application\Definition\RuleDefinition;
use Ghostwriter\Draft\Application\Definition\SeederDefinition;
use Ghostwriter\Draft\Application\Definition\TestDefinition;
use Ghostwriter\Draft\Application\Definition\ViewDefinition;
use Ghostwriter\Draft\Application\Formatter;
use Ghostwriter\Draft\Application\Interface\DefinitionInterface;
use Ghostwriter\Draft\Container\ServiceProvider;
use Throwable;

use function array_key_exists;

final class Draft
{
    public const array RESOURCE_ACTIONS = ['index', 'create', 'edit', 'show', 'store', 'update', 'destroy'];

    public function __construct(
        private array $controllerDefinitions = [],
        private array $eventDefinitions = [],
        private array $factoryDefinitions = [],
        private array $inertiaDefinitions = [],
        private array $jobDefinitions = [],
        private array $livewireDefinitions = [],
        private array $mailDefinitions = [],
        private array $migrationDefinitions = [],
        private array $modelDefinitions = [],
        private array $notificationDefinitions = [],
        private array $policyDefinitions = [],
        private array $routeDefinitions = [],
        private array $ruleDefinitions = [],
        private array $seederDefinitions = [],
        private array $testsDefinitions = [],
        private array $viewDefinitions = [],
    ) {}

    /** @throws Throwable */
    public static function new(callable $factory): self
    {
        $container = Container::getInstance();

        $draft = $container->get(self::class);

        $container->call($factory);

        $container->reset();

        return $draft;
    }

    /** @throws Throwable */
    public function controller(string $name, callable $factory): void
    {
        $this->call($this->controllerDefinition($name), $factory);
    }

    public function controllerDefinitions(): array
    {
        return $this->controllerDefinitions;
    }

    public function eventDefinitions(): array
    {
        return $this->eventDefinitions;
    }

    public function factoryDefinitions(): array
    {
        return $this->factoryDefinitions;
    }

    /** @throws Throwable */
    public function inertia(string $name, callable $factory): void
    {
        $this->call($this->inertiaDefinition($name), $factory);
    }

    public function inertiaDefinitions(): array
    {
        return $this->inertiaDefinitions;
    }

    public function jobDefinitions(): array
    {
        return $this->jobDefinitions;
    }

    /** @throws Throwable */
    public function livewire(string $name, callable $factory): void
    {
        $this->call($this->livewireDefinition($name), $factory);
    }

    public function livewireDefinitions(): array
    {
        return $this->livewireDefinitions;
    }

    public function mailDefinitions(): array
    {
        return $this->mailDefinitions;
    }

    public function migration(string $model, callable $factory): void
    {
        $this->model($model, static fn (ModelDefinition $modelDefinition) => $modelDefinition->migration($factory));
    }

    public function migrationDefinitions(): array
    {
        return $this->migrationDefinitions;
    }

    /** @throws Throwable */
    public function model(string $name, callable $factory): ModelDefinition
    {
        $modelDefinition = $this->modelDefinition($name);

        //        $modelDefinitionName = $modelDefinition->name(); // User

        //        $this->factoryDefinition($modelDefinitionName); // FactoryDefinition<UserFactory>
        //        $this->seederDefinition($modelDefinitionName); // SeederDefinition<UserSeeder>
        //        $this->policyDefinition($modelDefinitionName); // PolicyDefinition<UserPolicy>
        //        $this->featureTestDefinition($modelDefinitionName); // TestDefinition<Feature\UserTest>
        //        $this->unitTestDefinition($modelDefinitionName); // TestDefinition<Unit\UserTest>

        $this->call($modelDefinition, $factory);

        return $modelDefinition;
    }

    public function modelDefinitions(): array
    {
        return $this->modelDefinitions;
    }

    public function notificationDefinitions(): array
    {
        return $this->notificationDefinitions;
    }

    public function policyDefinitions(): array
    {
        return $this->policyDefinitions;
    }

    public function resourceController(string $name): void
    {
        $pluralName = $this->formatter()->pluralize($name);

        $this->controller(
            $name,
            static fn (
                ControllerDefinition $controllerDefinition,
            )
                => $controllerDefinition->resource($pluralName),
        );
    }

    public function routeDefinitions(): array
    {
        return $this->routeDefinitions;
    }

    public function router(callable $factory): void
    {
        $factory($this->routeDefinitions[RouterDefinition::class] ??= RouterDefinition::new($factory));
    }

    public function ruleDefinitions(): array
    {
        return $this->ruleDefinitions;
    }

    public function seederDefinitions(): array
    {
        return $this->seederDefinitions;
    }

    /**
     * @throws Throwable
     */
    public function test(string $model, callable $factory): void
    {
        $this->model($model, static fn (ModelDefinition $modelDefinition) => $modelDefinition->test($factory));
    }

    public function testsDefinitions(): array
    {
        return $this->testsDefinitions;
    }

    public function viewDefinitions(): array
    {
        return $this->viewDefinitions;
    }

    /**
     * @param callable(DefinitionInterface):void $factory
     *
     * @throws Throwable
     */
    private function call(DefinitionInterface $definition, callable $factory): void
    {
        Container::getInstance()->call($factory, [$definition]);
    }

    private function caseConverter(): CaseConverterInterface
    {
        return Container::getInstance()->get(CaseConverterInterface::class);
    }

    private function controllerDefinition(string $name): ControllerDefinition
    {
        $controllerName = $this->formatter()->formatControllerName($name);

        return $this->controllerDefinitions[$controllerName] ??= ControllerDefinition::new($controllerName);
    }

    private function eventDefinition(string $name): EventDefinition
    {
        $eventName = $this->formatter()->formatEventName($name);

        return $this->eventDefinitions[$eventName] ??= EventDefinition::new($eventName);
    }

    private function factoryDefinition(string $name): FactoryDefinition
    {
        $factoryName = $this->formatter()->formatFactoryName($name);

        return $this->factoryDefinitions[$factoryName] ??= FactoryDefinition::new($factoryName);
    }

    private function featureTestDefinition(string $name): TestDefinition
    {
        $testName = $this->formatter()->formatTestName($name, 'Feature');

        return $this->testsDefinitions[$testName] ??= TestDefinition::new($testName);
    }

    private function formatter(): Formatter
    {
        return Container::getInstance()->get(Formatter::class);
    }

    private function inertiaDefinition(string $name): InertiaDefinition
    {
        $componentName = $this->formatter()->formatComponentName($name);

        if (array_key_exists($componentName, $this->inertiaDefinitions)) {
            return $this->inertiaDefinitions[$componentName];
        }

        $livewireDefinition = Container::getInstance()->build(InertiaDefinition::class, [
            'name' => $componentName,
        ]);

        $livewireDefinition->render($this->caseConverter()->toKebabCase($componentName));

        return $this->inertiaDefinitions[$componentName] = $livewireDefinition;
    }

    private function jobDefinition(string $name): JobDefinition
    {
        $jobName = $this->formatter()->formatJobName($name);

        return $this->jobDefinitions[$jobName] ??= new JobDefinition($jobName);
    }

    private function livewireDefinition(string $name): LivewireDefinition
    {
        $componentName = $this->formatter()->formatComponentName($name);

        if (array_key_exists($componentName, $this->livewireDefinitions)) {
            return $this->livewireDefinitions[$componentName];
        }

        $livewireDefinition = new LivewireDefinition($componentName);

        $livewireDefinition->render($this->caseConverter()->toKebabCase($componentName));

        return $this->livewireDefinitions[$componentName] = $livewireDefinition;
    }

    private function mailDefinition(string $name): MailDefinition
    {
        $mailName = $this->formatter()->formatMailName($name);

        return $this->mailDefinitions[$mailName] ??= new MailDefinition($mailName);
    }

    private function migrationDefinition(string $name): MigrationDefinition
    {
        $migrationName = $this->formatter()->formatMigrationName($name);

        return $this->migrationDefinitions[$migrationName] ??= new MigrationDefinition($migrationName);
    }

    private function modelDefinition(string $name): ModelDefinition
    {
        $modelName = $this->formatter()->formatModelName($name);

        return $this->modelDefinitions[$modelName] ??= new ModelDefinition(
            $modelName,
            $this->formatter()->formatTableName($name),
        );
    }

    private function notificationDefinition(string $name): NotificationDefinition
    {
        $notificationName = $this->formatter()->formatNotificationName($name);

        return $this->notificationDefinitions[$notificationName] ??= new NotificationDefinition($notificationName);
    }

    private function policyDefinition(string $name): PolicyDefinition
    {
        $policyName = $this->formatter()->formatPolicyName($name);

        return $this->policyDefinitions[$policyName] ??= new PolicyDefinition($policyName);
    }

    private function ruleDefinition(string $name): RuleDefinition
    {
        $ruleName = $this->formatter()->formatRuleName($name);

        return $this->ruleDefinitions[$ruleName] ??= new RuleDefinition($ruleName);
    }

    private function seederDefinition(string $name): SeederDefinition
    {
        $seederName = $this->formatter()->formatSeederName($name);

        return $this->seederDefinitions[$seederName] ??= new SeederDefinition($seederName);
    }

    private function toPlural(string $name): string
    {
        return $this->inflector()->pluralize($name);
    }

    private function unitTestDefinition(string $name): TestDefinition
    {
        $testName = $this->formatter()->formatTestName($name, 'Unit');

        return $this->testsDefinitions[$testName] ??= new TestDefinition($testName);
    }

    private function viewDefinition(string $name): ViewDefinition
    {
        $viewName = $this->formatter()->formatViewName($name);

        return $this->viewDefinitions[$viewName] ??= new ViewDefinition($viewName);
    }
}
