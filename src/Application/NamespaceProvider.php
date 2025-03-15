<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;

use function implode;
use function mb_ltrim;
use function mb_strtolower;
use function mb_trim;
use function str_replace;
use function str_starts_with;

final readonly class NamespaceProvider
{
    private string $rootNamespace;

    public function __construct(
        private Application $application,
        private Repository $repository,
    ) {
        $this->rootNamespace = mb_trim(
            $this->repository->get('draft.namespaces.app', $this->application->getNamespace()),
            '\\'
        );

    }

    public function app(string $name, string ...$namespaces): string
    {
        return $this->format($name, $this->rootNamespace, ...$namespaces);
    }

    public function commands(string $name): string
    {
        return $this->console($name, 'Commands');
    }

    public function components(string $name, string ...$namespaces): string
    {
        return $this->view($name, 'Components', ...$namespaces);
    }

    public function console(string $name, string ...$namespaces): string
    {
        return $this->app($name, 'Console', ...$namespaces);
    }

    public function controllers(string $name): string
    {
        return $this->http($name, 'Controllers');
    }

    public function database(string $name, string ...$namespaces): string
    {
        return $this->format($name, 'Database', ...$namespaces);
    }

    public function events(string $name, string ...$namespaces): string
    {
        return $this->app($name, 'Events', ...$namespaces);
    }

    public function factories(string $name, string ...$namespaces): string
    {
        return $this->database($name, 'Factories', ...$namespaces);
    }

    public function http(string $name, string ...$namespaces): string
    {
        return $this->app($name, 'Http', ...$namespaces);
    }

    public function job(string $name, string ...$namespaces): string
    {
        return $this->app($name, 'Jobs', ...$namespaces);
    }

    public function listener(string $name, string ...$namespaces): string
    {
        return $this->app($name, 'Listeners', ...$namespaces);
    }

    public function livewire(string $name, string ...$namespaces): string
    {
        return $this->app($name, 'Livewire', ...$namespaces);
    }

    public function mail(string $name, string ...$namespaces): string
    {
        return $this->app($name, 'Mail', ...$namespaces);
    }

    public function middleware(string $name, string ...$namespaces): string
    {
        return $this->http($name, 'Middleware', ...$namespaces);
    }

    public function migration(string $name, string ...$namespaces): string
    {
        return $this->database($name, 'Migrations', ...$namespaces);
    }

    public function models(string $name, string ...$namespaces): string
    {
        return $this->app($name, 'Models', ...$namespaces);
    }

    public function notification(string $name, string ...$namespaces): string
    {
        return $this->app($name, 'Notifications', ...$namespaces);
    }

    public function observers(string $name, string ...$namespaces): string
    {
        return $this->app($name, 'Observers', ...$namespaces);
    }

    public function policies(string $name, string ...$namespaces): string
    {
        return $this->app($name, 'Policies', ...$namespaces);
    }

    public function provider(string $name, string ...$namespaces): string
    {
        return $this->app($name, 'Providers', ...$namespaces);
    }

    public function requests(string $name): string
    {
        return $this->http($name, 'Requests');
    }

    public function resources(string $name, string ...$namespaces): string
    {
        return $this->http($name, 'Resources', ...$namespaces);
    }

    public function rule(string $name, string ...$namespaces): string
    {
        return $this->app($name, 'Rules', ...$namespaces);
    }

    public function scopes(string $name, string ...$namespaces): string
    {
        return $this->model($name, 'Scopes', ...$namespaces);
    }

    public function seeders(string $name, string ...$namespaces): string
    {
        return $this->database($name, 'Seeders', ...$namespaces);
    }

    public function tests(string $name, string $variant = 'Unit'): string
    {
        return match (mb_strtolower($variant)) {
            'feature' => $this->format($name, 'Tests', 'Feature'),
            default => $this->format($name, 'Tests', 'Unit'),
        };
    }

    public function view(string $name, string ...$namespaces): string
    {
        return $this->app($name, 'View', ...$namespaces);
    }

    private function format(string $name, string ...$namespaces): string
    {
        $suffix = $this->sanitize($name);

        $prefix = implode('\\', $namespaces) . '\\';

        if (str_starts_with($suffix, $prefix)) {
            return $suffix;
        }

        return $prefix . $suffix;
    }

    private function sanitize(string $name): string
    {
        // replace all slashes with a single backslash
        return str_replace(
            '/',
            '\\',
            // Remove leading slashes and backslashes from the beginning of the string
            mb_ltrim($name, '\\/'),
        );
    }
}
