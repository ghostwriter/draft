<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Value\Path;

use function config;

final readonly class ModelPath implements PathInterface
{
    public function __construct(
        public string $path,
    ) {}

    public static function new(): self
    {
        return new self(config('draft.paths.models'));
    }
}
