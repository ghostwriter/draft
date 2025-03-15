<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application;

use Doctrine\Inflector\Inflector;
use Ghostwriter\CaseConverter\Interface\CaseConverterInterface;
use Ghostwriter\Draft\Application\Interface\NameProviderInterface;
use Str;

use function app;
use function count;
use function explode;
use function sprintf;

final readonly class NameProvider implements NameProviderInterface
{
    public function __construct(
        private CaseConverterInterface $caseConverter,
        private Inflector $inflector,
        private Sanitizer $sanitizer,
    ) {}

    public function _caseConverter(): CaseConverterInterface
    {
        return $this->caseConverter;
    }

    public function _inflector(): Inflector
    {
        return $this->inflector;
    }

    public function component(string $name): string
    {
        return $this->suffix($this->sanitize($name), 'Component');
    }

    public function controller(string $name): string
    {
        return $this->suffix($this->sanitize($name), 'Controller');
    }

    public function database(string $name): string
    {
        return $this->toSnakeCase($this->pluralize($name));
    }

    public function event(string $name): string
    {
        return $this->suffix($this->sanitize($name), 'Event');
    }

    public function factory(string $name): string
    {
        return $this->suffix($this->sanitize($name), 'Factory');
    }

    public function formRequest(string $name): string
    {
        return $this->suffix($this->sanitize($name), 'Request');
    }

    public function job(string $name): string
    {
        return $this->suffix($this->sanitize($name), 'Job');
    }

    public function listener(string $name): string
    {
        return $this->suffix($this->sanitize($name), 'Listener');
    }

    public function mail(string $name): string
    {
        return $this->suffix($this->sanitize($name), 'Mail');
    }

    public function migration(string $name): string
    {
        return sprintf('create_%s_table', $this->table($name));
    }

    public function model(string $word): string
    {
        return $this->toPascalCase($this->sanitize($word));
    }

    public function notification(string $name): string
    {
        return $this->suffix($this->sanitize($name), 'Notification');
    }

    public function policy(string $name): string
    {
        return $this->suffix($this->sanitize($name), 'Policy');
    }

    public function rootNamespace(): string
    {
        return sprintf('%s\\', app()->getNamespace());
    }

    public function route(string $name): string
    {
        return $this->suffix($this->sanitize($name), 'route');
    }

    public function rule(string $name): string
    {
        return $this->suffix($this->sanitize($name), 'Rule');
    }

    public function seeder(string $name): string
    {
        return $this->suffix($this->sanitize($name), 'Seeder');
    }

    public function table(string $name): string
    {
        return $this->toSnakeCase($this->pluralize($name));
    }

    public function test(string $name): string
    {
        return $this->suffix($this->sanitize($name), 'Test');
    }

    public function view(string $name): string
    {
        $parts = explode('.', $name);

        if (count($parts) === 2) {
            return Str::of($this->sanitize($parts[0]))
                ->plural()
                ->lower()
                ->append('.' . $parts[1])
                ->toString();
        }

        return Str::of($this->sanitize($name))
            ->plural()
            ->lower()
            ->toString();
    }

    // 'Component',
    // 'Controller',
    // 'Database',
    // 'Event',
    // 'Factory',
    // 'FormRequest',
    // 'Inertia',
    // 'Job',
    // 'Listener',
    // 'Livewire',
    // 'Mail',
    // 'Migration',
    // 'Model',
    // 'Notification',
    // 'Policy',
    // 'Route',
    // 'Rule',
    // 'Seeder',
    // 'Test',
    // 'View',
    private function camelize(string $word): string
    {
        return $this->inflector->camelize($word);
    }

    private function classify(string $word): string
    {
        return $this->inflector->classify($word);
    }

    private function pluralize(string $word): string
    {
        return $this->inflector->pluralize($word);
    }

    private function sanitize(string $word): string
    {
        return $this->sanitizer->sanitize($word);
    }

    private function singularize(string $word): string
    {
        return $this->inflector->singularize($word);
    }

    private function suffix(string $prefix, string $suffix): string
    {
        return $this->caseConverter->toPascalCase($prefix . '_' . $suffix);
    }

    private function toPascalCase(string $text): string
    {
        return $this->caseConverter->toPascalCase($text);
    }

    private function toSnakeCase(string $text): string
    {
        return $this->caseConverter->toSnakeCase($text);
    }
}
