<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Migration\Relationship;

final class HasOneRelationshipDefinition
{
    public function __construct(
        private string $model,
        private string $foreignKey,
        private string $localKey,
    ) {}
}
