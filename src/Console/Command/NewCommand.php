<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Console\Command;

use Ghostwriter\Draft\Application\NameProvider;
use Ghostwriter\Draft\Application\NamespaceProvider;
use Ghostwriter\Draft\Application\PathProvider;
use Ghostwriter\Draft\Parser\ClassMap;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Console\MigrationGeneratorCommand;
use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Foundation\Console\PolicyMakeCommand;
use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Foundation\Console\TestMakeCommand;
use Illuminate\Foundation\Console\ViewMakeCommand;
use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Livewire\Features\SupportConsoleCommands\Commands\FormCommand;
use Livewire\Features\SupportConsoleCommands\Commands\MakeCommand;
use Override;
use ReflectionClass;

use const DIRECTORY_SEPARATOR;

use function app;
use function array_map;
use function base_path;
use function class_basename;
use function config;
use function dd;
use function dirname;
use function dump;
use function implode;
use function preg_match;
use function realpath;
use function sort;
use function sprintf;
use function str_replace;

final class NewCommand extends GeneratorCommand
{
    protected $description = 'Draft a new model.';

    protected $signature = 'draft:new {name}';

    protected $type = 'Draft';

    public function __construct(
        Filesystem $files,
        private readonly NamespaceProvider $namespaceProvider,
        private readonly NameProvider $nameProvider,
        private readonly PathProvider $pathProvider,
    ) {
        parent::__construct($files);
    }

    /**
     * @return array{name: string, namespace: string, path: string, realpath: false|string, exists: bool}
     */
    public function classMap1(string $name): array
    {
        return [
            'name' => class_basename($name),
            'namespace' => $name,
            'path' => $this->getPath($name),
            'realpath' => realpath(dirname($this->getPath($name))),
            'exists' => $this->alreadyExists($name),
        ];
    }

    #[Override]
    public function handle(): int
    {
        //        $name = Str::studly(Str::singular($this->getNameInput()));
        //
        //        $model = $this->qualifyModel($name);
        //
        //        $this->createModel($name, $model);
        //
        //        $this->createLivewire($name);
        //        // $this->createController($name, $model);
        //
        //        $this->info(sprintf('[#BLM!] Draft files for %s model created successfully', $model));
        //
        //        return self::SUCCESS;

        $name = $this->getNameInput();
        $model = $this->qualifyModel($name);
        $modelName = $this->nameProvider->model($name);
        $modelPath = $this->pathProvider->model($name);
        $rootNamespace = $this->rootNamespace();

        $components = array_map(static fn (string $value) => $name . '\\' . $value . 'Handler', [
            'Create',
            'Index',
            'Edit',
            'Show',
            //            'Read',
            'Store',
            'Update',
            'Delete',
            //            'Manage',
        ]);
        sort($components);
        dump([
            'model' => [$name, $rootNamespace, $model, $this->getPath($model)],
            $this->qualifyClass($model),
            $this->qualifyModel($name),
            $this->getPath($name),
            $this->possibleModels(),
            $components,
            config('draft'),
            $this->createControllerResource($modelName),
        ]);
        //        $this->
        //        $name = class_basename($this->argument('name'));

        //        dump([
        //            $name,
        //            $components,
        //            array_map(
        //                fn (string $file): int
        //                => $this->call(FormCommand::class, [
        //                    'name' => $file,
        //                    '--force' => true,
        //                ]),
        //                $components
        //            ),
        //            array_map(
        //                fn (string $file): int
        //                => $this->call(ModelMakeCommand::class, [
        //                    'name' => $file,
        //                    '--factory' => true,
        //                    '--force' => true,
        //                    '--migration' => true,
        //                    '--policy' => true,
        //                    '--test' => true,
        //                ]),
        //                $components
        //            ),
        //        ]);

        //        dump([
        //            array_map(
        //                fn (string $file): int
        //                => $this->call(MakeCommand::class, [
        //                    'name' => $file,
        //                    '--test' => true,
        //                    '--force' => true,
        //                ]),
        //                $components
        //            ),
        //        ]);

        return self::SUCCESS;

        $info = $this->classMap($name);

        $rootNamespace = $this->rootNamespace();

        //        $namespaces = [
        //            'controllers' =>$rootNamespace.'\Http\Controllers',
        //            'events' =>$rootNamespace.'\Events',
        //            'mail' =>$rootNamespace.'\Mail',
        //        ];
        //
        //        $class = $this->qualifyClass($name);
        //        $controller = ;
        //        $path = $this->getPath($name);
        //        $model = $this->buildClass($name);

        $classMap = new ClassMap();
        $classMap->addModel($info);
        //        dd([
        //            app(),
        //            $classMap,
        //            'app/Models/Ice.php',
        //            'database/factories/IceFactory.php',
        //            'database/migrations/2023_01_04_032916_create_ices_table.php',
        //            'app/Policies/IcePolicy.php',
        //        ]);

        $this->info(sprintf('Drafting a new %s model.', $name));

        // Laravel
        // make:model name {--controller} {--resource} {--force} {--factory} {--migration} {--test}
        $this->call(ModelMakeCommand::class, [
            'name' => $name,
            '--factory' => true,
            '--force' => true,
            '--migration' => true,
            '--policy' => true,
            '--test' => true,
            // 'controller' => true, # using Livewire instead
            // 'resource' =>true, # using Livewire instead
            // '--seed' => true, # May not use it (Use the Default [DatabaseSeeder] to "Build a Story".)
        ]);

        // Livewire
        // livewire:make name {--force} {--inline} {--test} {--stub=}
        //      - Livewire allows us to use our own stubs, so create one for each "Action" component.
        // Todo: Admins & Owners or Role based.
        array_map(
            fn (string $file): int => $this->call(MakeCommand::class, [
                'name' => $file,
                '--test' => true,
                '--force' => true,
            ]),
            array_map(static fn (string $value) => Str::of($name)
                ->prepend($value)
                ->toString(), ['Create', 'Index', 'Edit', 'Show', 'Read', 'Store', 'Update', 'Delete', 'Manage']),
            [Str::of($name)->prepend()->pluralStudly()->toString()]
        );

        return self::SUCCESS;
    }

    #[Override]
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace; // . '\Foo';
    }

    #[Override]
    protected function getStub(): void
    {
        //        return base_path('stubs');
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param string $model
     *
     * @throws InvalidArgumentException
     *
     * @return string
     *
     */
    protected function parseModel(string $model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }

    private function callArtisan(object|string $command, array $arguments = []): int
    {
        return $this->call($command, $arguments);

        return $this->callSilent($command, $arguments);
    }

    private function classMap(string $name): array
    {
        // dd(app()->getNamespace());
        $model = $this->qualifyModel($name);
        $controller = $this->qualifyClass(sprintf('Http\Controllers\%sController', $name));
        $seeder = $this->qualifyClass(sprintf('Http\Controllers\%sController', $name));
        $factory = $this->qualifyClass(
            Str::of($name)->replaceFirst($this->rootNamespace(), '')
                ->start('Database\\Factories\\')
                ->finish('Factory')
        );
        $policy = $this->qualifyClass(sprintf('Policies\%sPolicy', $name));
        $migration = $this->qualifyClass(sprintf('Database\Migrations\create_%s_table', $name));

        $createFormRequest = $this->qualifyClass(sprintf('Http\Requests\Create%sRequest', $name));
        $updateFormRequest = $this->qualifyClass(sprintf('Http\Requests\Update%sRequest', $name));
        //        $this->call('make:seeder', [
        //            'name' => "{$seeder}Seeder",
        //        ]);
        // array_map(fn($i)=> $this->qualifyClass(sprintf('Http\Controllers\%sController', $name)),[])
        //        $info = ($name);

        return [
            'model' => $this->classMap1($model),
            'controller' => $this->classMap1($controller),
            'seeder' => $this->classMap1($seeder),
            'factory' => $this->classMap1($factory),
            'policy' => $this->classMap1($policy),
            'migration' => $this->classMap1($migration),
            'form' => [
                'path' => dirname($this->getPath($name)),
                $this->classMap1($createFormRequest),
                $this->classMap1($updateFormRequest),
            ],
            //            'createRequest' => $this->classMap1($createFormRequest),
            //            'updateRequest' => $this->classMap1($updateFormRequest),
            //            'formRequest' => $this->classMap1($formRequest),
            //            'exists' => $this->alreadyExists($name),
            //            'path' => $this->getPath($name),
        ];
    }

    private function createController(string $name, string $model): void
    {
        $this->callArtisan(ControllerMakeCommand::class, [
            'name' => $name . 'Controller',
            '--model' => $model,
            '--requests' => true,
            '--resource' => true,
            '--test' => true,
            '--force' => true,
        ]);

        $this->createFormRequests($name);
    }

    private function createControllerResource(string $model): int
    {
        $controller = $this->nameProvider->controller($model);

        $result = $this->callArtisan(ControllerMakeCommand::class, [
            'name' => $controller,
            '--model' => $model,
            '--requests' => true,
            '--resource' => true,
            '--test' => true,
            // '--force' => true,
        ]);

        $this->createPolicy($model, $model);

        $plural = $this->nameProvider->model($model);

        array_map(
            fn (string $action): int => $this->createView($plural, $action),
            ['create', 'edit', 'index', 'show']
        );

        return $result;
    }

    private function createFactory(string $name, string $model): void
    {
        $this->callArtisan(FactoryMakeCommand::class, [
            'name' => $name . 'Factory',
            '--model' => $model,
        ]);
    }

    private function createFormRequests(string $name): void
    {
        array_map(
            fn (string $action): int => $this->callArtisan(RequestMakeCommand::class, [
                'name' => $action . $name . 'Request',
                // '--force' => true,
            ]),
            ['Store', 'Update']
        );
    }

    private function createLivewire(string $name): void
    {
        // create forms for store and update requests
        array_map(
            fn (string $action): int
                => $this->callArtisan(FormCommand::class, [
                    'name' => $name . '\\' . $action . $name . 'Form',
                    // '--force' => true,
                ]),
            ['Store', 'Update']
        );

        // create 7 livewire components for each crud operations
        array_map(
            fn (string $component): int => $this->callArtisan(MakeCommand::class, [
                'name' => $name . '.' . $component,
                '--test' => true,
                // '--force' => true,
            ]),
            array_map(static fn (string $action) => $action . $name, [
                'Create',
                'Delete',
                'Edit',
                'Index',
                'Show',
                'Store',
                'Update',
            ])
        );
    }

    private function createMigration(string $name): void
    {
        $table = Str::snake(Str::pluralStudly($name));

        $migrationCommand = new class($table, $this->files) extends MigrationGeneratorCommand {
            public function __construct(
                private readonly string $tableName,
                Filesystem $files
            ) {
                parent::__construct($files);
            }

            protected function migrationTableName(): string
            {
                return $this->tableName;
            }

            protected function migrationStubFile(): string
            {
                $stub = base_path('stubs/migration.create.stub');

                if ($this->files->exists($stub)) {
                    return $stub;
                }

                return implode(DIRECTORY_SEPARATOR, [
                    dirname((new ReflectionClass(Migration::class))->getFileName()),
                    'stubs',
                    'migration.update.stub', // 'migration.create.stub',
                ]);
            }

            /**
             * Replace the placeholders in the generated migration file.
             *
             * @param string $path
             * @param string $table
             */
            protected function replaceMigrationPlaceholders(string $path, string $table): void
            {
                $this->files->put(
                    $path,
                    str_replace(
                        ['{{table}}', '{{ table }}'],
                        $table,
                        $this->files->get($this->migrationStubFile()),
                    )
                );
            }
        };

        $this->callArtisan($migrationCommand);
    }

    private function createModel(string $name, string $model): void
    {
        $this->callArtisan(ModelMakeCommand::class, [
            'name' => $model,
            '--test' => true,
        ]);
        $this->createFactory($name, $model);
        $this->createMigration($name);
        $this->createPolicy($name, $model);
        $this->createSeeder($name);
        $this->createTest($name);
    }

    private function createPolicy(string $policy, string $model): void
    {
        $this->callArtisan(PolicyMakeCommand::class, [
            'name' => $policy . 'Policy',
            '--model' => $model,
        ]);
    }

    private function createSeeder(string $name): void
    {
        $this->callArtisan(SeederMakeCommand::class, [
            'name' => $name . 'Seeder',
        ]);
    }

    private function createTest(string $name): void
    {
        $test = $name . 'Test';

        $this->callArtisan(TestMakeCommand::class, [
            'name' => $test,
        ]);

        $this->callArtisan(TestMakeCommand::class, [
            'name' => $test,
            '--unit' => true,
        ]);
    }

    private function createView(string $name): int
    {
        return $this->callArtisan(ViewMakeCommand::class, [
            'name' => $this->nameProvider->view($name),
            '--phpunit' => true,
            '--test' => true,
            '--force' => true,
        ]);
    }

    // callSilent
}
