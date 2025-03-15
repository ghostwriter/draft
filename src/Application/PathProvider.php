<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application;

use Doctrine\Inflector\Inflector;
use Ghostwriter\CaseConverter\Interface\CaseConverterInterface;

use function app_path;
use function config;
use function is_dir;
use function mb_ltrim;
use function sprintf;
use function str_replace;
use function str_starts_with;

final readonly class PathProvider
{
    private const array SUFFIXES = [
        'Command',
        'Component',
        'Controller',
        'Database',
        'Event',
        'Factory',
        'Form',
        'FormRequest',
        'Inertia',
        'Job',
        'Listener',
        'Livewire',
        'Mail',
        'Migration',
        'Model',
        'Notification',
        'Policy',
        'Request',
        'Route',
        'Rule',
        'Seeder',
        'Test',
        'View',
    ];

    private array $paths;

    public function __construct(
        private CaseConverterInterface $caseConverter,
        private Inflector $inflector,
    ) {
        //        $this->paths = [
        //            'database' => $this->toSnakeCase($this->inflector->pluralize('database')),
        //            'migration' => $this->toSnakeCase($this->inflector->pluralize('migration')),
        //            'model' => $this->toSnakeCase($this->inflector->pluralize('model')),
        //            'seeder' => $this->toSnakeCase($this->inflector->pluralize('seeder')),
        //        ];
    }

    public function getPath(string $name): string
    {
        return sprintf(
            '%s/%s',
            $this->caseConverter->toKebabCase($this->inflector->pluralize($name)),
            $this->caseConverter->toKebabCase($name)
        );
    }

    public function model(string $model): string
    {
        $model = mb_ltrim($model, '\\/');

        $model = str_replace('/', '\\', $model);

        $rootNamespace = $this->rootNamespace();

        if (str_starts_with($model, $rootNamespace)) {
            return $model;
        }

        return is_dir(app_path('Models'))
            ? $rootNamespace . 'Models\\' . $model
            : $rootNamespace . $model;
    }

    private function rootNamespace(): string
    {
        return sprintf('%s\\', config('draft.namespaces.app'));
    }
}
