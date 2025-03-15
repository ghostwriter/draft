<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser;

use Ghostwriter\Draft\Application\Interface\ControllerInterface;
use Ghostwriter\Draft\Application\Interface\MigrationInterface;
use Ghostwriter\Draft\Application\Interface\UserInterface;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt;

use function array_key_exists;

final class ClassMap
{
    /** @var array<string,ControllerInterface> */
    private array $controllers = [];

    /** @var array<string,bool> */
    private array $factories = [];

    /** @var Stmt */
    private array $files = [];

    private array $map =  [
        'models' => [
            'model' => [
                'controllers' => [],
                'components' => [
                    'blade' => [],
                    'inertia' => [],
                    'livewire' => [],
                ],
                'factories' => [],
                'policies' => [],
                'forms' => [],
                'migrations' => [],
            ],
            'controllers' => [
                'formRequests' => [],
            ],
            'livewire-components' => [
                'formRequests' => [],
            ],
        ],
    ];

    /** @var array<string,MigrationInterface> */
    private array $migrations = [];

    /** @var UserInterface */
    private array $models = [];

    /** @var array<string,bool> */
    private array $seeders = [];

    public function __construct() {}

    public function addClass(string $class, string $path): void
    {
        $map =&$this->map;

        if (! array_key_exists($path, $map)) {
            $map[$path] = [];
        }

        if (! array_key_exists($class, $map)) {
            $map[$class] = [];
        }
    }

    public function addClassConsts(array $constants, string $class, string $path): void
    {
        $this->map[$path][$class]['const'] = $constants;
    }

    public function addClassMethods(array $methods, string $class, string $path): void
    {
        $this->map[$path][$class]['method'] = $methods;
    }

    public function addClassProperties(array $properties, string $class, string $path): void
    {
        $this->map[$path][$class]['property'] = $properties;
    }

    public function addModel(array $info): void
    {
        $columns = [
            'models',
            'controllers',
            'factories',
            'policies',
            'migrations',
            'forms',
            //            'createRequests',
            //            'updateRequests',
        ];

        foreach ($columns as $column) {
            $key = Str::singular($column);
            //            $info[$key] ?? [];
            $this->map['models'][$column][$info[$key]['path']] = $info[$key];
        }

        //        $this->map['models'][$info['model']['name']] = ['path' => $info['model']['path'], 'exists' => $info['model']['exists']];
        //        $this->map['controllers'][$info['controller']['name']] = ['path' => $info['controller']['path'], 'exists' => $info['controller']['exists']];
        //        $this->map['factories'][$info['factory']['name']] = ['path' => $info['factory']['path'], 'exists' => $info['factory']['exists']];
        //        $this->map['policies'][$info['policy']['name']] = ['path' => $info['policy']['path'], 'exists' => $info['policy']['exists']];
        //        $this->map['migrations'][$info['migration']['name']] = ['path' => $info['migration']['path'], 'exists' => $info['migration']['exists']];
        //        $this->map['seeders'][$info['seeder']['name']] = ['path' => $info['seeder']['path'], 'exists' => $info['seeder']['exists']];

        //        $this->map[] = [
        //            'name' =>  $name,
        //            'rootNamespace' =>  $rootNamespace,
        //            'model' =>  $model,
        //            'modelClassPath' =>  $modelClassPath,
        //            'modelClassPathExists' =>  $modelClassPathExists,
        //            'controller' =>  $controller,
        //            'controllerClassPath' =>  $controllerClassPath,
        //            'controllerClassPathExists' =>  $controllerClassPathExists,
        //        ];
    }
}
