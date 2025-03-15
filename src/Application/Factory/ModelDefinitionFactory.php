<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Factory;

use Ghostwriter\Draft\Application\Definition\ModelDefinition;

final class ModelDefinitionFactory
{
    public function __construct() {}

    public function create(string $name, string $table): ModelDefinition
    {
        return new ModelDefinition($name, $table);
    }
}
