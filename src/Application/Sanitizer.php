<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application;

use Doctrine\Inflector\Inflector;
use Ghostwriter\CaseConverter\Interface\CaseConverterInterface;

use function array_reduce;
use function in_array;
use function mb_strlen;
use function mb_strtolower;
use function mb_substr;
use function str_ends_with;

final readonly class Sanitizer
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

    private const array ACTIONS = ['index', 'create', 'edit', 'show', 'store', 'update', 'destroy'];

    public function __construct(
        private CaseConverterInterface $caseConverter,
        private Inflector $inflector,
    ) {}

    public function sanitize(string $word): string
    {
        return array_reduce(
            self::SUFFIXES,
            static fn (string $sanitized, string $suffix) => match (true) {
                str_ends_with($sanitized, $suffix) => mb_substr($sanitized, 0, -mb_strlen($suffix)),
                default => $sanitized,
            },
            $word,
        );
    }

    /**
     * Sanitize the action name to ensure it is a valid action.
     *
     * @param string $action the action name to sanitize
     *
     * @return string the sanitized action name
     */
    public function sanitizeAction(string $action = 'index'): string
    {
        if (in_array($action, self::ACTIONS, true)) {
            return $action;
        }

        return $this->caseConverter->toSnakeCase($action);
    }

    public function sanitizeActions(string $model = 'user'): string
    {
        return array_reduce(
            self::ACTIONS,
            static fn (string $sanitized, string $type) => match (true) {
                str_ends_with($sanitized, '.' . $type) => mb_substr($sanitized, 0, -mb_strlen($type)),
                default => $sanitized,
            },
            mb_strtolower($model),
        );

        return array_reduce(
            self::ACTIONS,
            static fn (string $sanitized, string $type) => match (true) {
                str_ends_with($sanitized, '.' . $type) => mb_substr($sanitized, 0, -mb_strlen($type)),
                default => $sanitized,
            },
            $plural,
        );
    }
}
