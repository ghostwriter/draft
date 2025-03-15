<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application;

use Doctrine\Inflector\Inflector;
use Ghostwriter\CaseConverter\Interface\CaseConverterInterface;

use function mb_strlen;
use function mb_strtolower;
use function mb_substr;
use function sprintf;
use function str_ends_with;

final readonly class Formatter
{
    private const array RESOURCE_ACTIONS = ['index', 'create', 'edit', 'show', 'store', 'update', 'destroy'];

    public function __construct(
        private CaseConverterInterface $caseConverter,
        private Inflector $inflector,
        private Sanitizer $sanitizer
    ) {}

    public function formatActionName(string $model = 'user', string $action = 'index'): string
    {
        $sanitized = $this->sanitizeAction($model);

        //        $pluralized = $this->pluralize(mb_strtolower($sanitized));

        foreach (self::RESOURCE_ACTIONS as $type) {
            if (str_ends_with($sanitized, '.' . $type)) {
                return mb_substr($sanitized, 0, -mb_strlen($type));
            }
        }

        return $model . '.' . $action;
    }

    public function formatComponentName(string $name): string
    {
        return $this->formatModelName($name) . 'Component';
    }

    public function formatControllerName(string $name): string
    {
        return $this->formatModelName($name) . 'Controller';
    }

    public function formatEventName(string $name): string
    {
        return $this->formatModelName($name) . 'Event';
    }

    public function formatFactoryName(string $name): string
    {
        return $this->formatModelName($name) . 'Factory';
    }

    public function formatJobName(string $name): string
    {
        return $this->formatModelName($name) . 'Job';
    }

    public function formatMailName(string $name): string
    {
        return $this->formatModelName($name) . 'Mail';
    }

    public function formatMigrationName(string $table): string
    {
        return sprintf('create_%s_table', $this->caseConverter->toSnakeCase($table));
    }

    public function formatModelName(string $word): string
    {
        $word = $this->sanitize($word);

        return $this->singularize($word);
    }

    public function formatNotificationName(string $name): string
    {
        if (str_ends_with($name, 'Notification')) {
            return $name;
        }

        return $this->formatModelName($name) . 'Notification';
    }

    public function formatPolicyName(string $name): string
    {
        if (str_ends_with($name, 'Policy')) {
            return $name;
        }

        return $this->formatModelName($name) . 'Policy';
    }

    public function formatRouteActionName(string $model = 'post', string $action = 'Index'): string
    {
        return sprintf('%s.%s', $this->pluralize($this->sanitize($model)), $this->sanitizeAction($action));
    }

    public function formatRuleName(string $name): string
    {
        if (str_ends_with($name, 'Rule')) {
            return $name;
        }

        return $this->formatModelName($name) . 'Rule';
    }

    public function formatSeederName(string $name): string
    {
        if (str_ends_with($name, 'Seeder')) {
            return $name;
        }

        return $this->formatModelName($name) . 'Seeder';
    }

    public function formatTableName(string $name): string
    {
        return $this->caseConverter->toSnakeCase($this->pluralize($name));
    }

    public function formatTestName(string $name, string $type): string
    {
        return sprintf('%s\%sTest', $type, $this->formatModelName($name));
    }

    public function formatViewName(string $name): string
    {
        if (str_ends_with($name, 'View')) {
            return $name;
        }

        return $this->formatModelName($name) . 'View';
    }

    public function pluralize(string $word): string
    {
        return $this->inflector->pluralize($word);
    }

    public function singularize(string $word): string
    {
        return $this->inflector->singularize($word);
    }

    private function sanitize(string $word): string
    {
        return $this->sanitizer->sanitize($word);
    }

    private function sanitizeAction(string $word): string
    {
        return $this->sanitizer->sanitizeAction($word);
    }
}
