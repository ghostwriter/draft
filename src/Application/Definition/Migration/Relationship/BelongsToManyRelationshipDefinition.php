<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Migration\Relationship;

final readonly class BelongsToManyRelationshipDefinition
{
    public function __construct(
        public string $model,
        public string $table,
        public string $localKey,
        public string $foreignKey,
    ) {}
}
