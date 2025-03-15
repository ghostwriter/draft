<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Migration;

use Ghostwriter\Draft\Application\Definition\Migration\Relationship\BelongsToManyRelationshipDefinition;
use Ghostwriter\Draft\Application\Definition\Migration\Relationship\BelongsToRelationshipDefinition;
use Ghostwriter\Draft\Application\Definition\Migration\Relationship\HasManyRelationshipDefinition;
use Ghostwriter\Draft\Application\Definition\Migration\Relationship\HasOneRelationshipDefinition;
use Ghostwriter\Draft\Application\Interface\DefinitionInterface;

use function implode;
use function mb_strtolower;
use function sort;

final class RelationshipDefinition implements DefinitionInterface
{
    public function __construct(
        private readonly string $method,
        private readonly string $model,
        private readonly string $table,
        private array $definition = [],
    ) {}

    public function belongsTo(string $model, ?string $foreignKey = null, string $ownerKey = 'id'): self
    {
        $this->definition[] = new BelongsToRelationshipDefinition(
            $model,
            $foreignKey ?? $this->method . '_id',
            $ownerKey ?? 'id',
        );

        return $this;
    }

    public function belongsToMany(
        string $model, // Role
        ?string $table = null, // role_user
        ?string $localKey = null, // user_id
        ?string $foreignKey = null, // role_id
    ): self {
        $models = [$model, $this->model];

        sort($models);

        $this->definition[] = new BelongsToManyRelationshipDefinition(
            $model,
            $table ?? mb_strtolower(implode('_', $models)),
            $localKey ?? mb_strtolower($this->model . '_id'),
            $foreignKey ?? mb_strtolower($model . '_id'),
        );

        return $this;
    }

    public function hasMany(string $model, ?string $foreignKey = null, string $localKey = 'id'): self
    {
        $this->definition[] = new HasManyRelationshipDefinition(
            $model,
            $foreignKey ?? mb_strtolower($this->model . '_id'),
            $localKey,
        );

        return $this;
    }

    public function hasOne(string $model, ?string $foreignKey = null, string $localKey = 'id'): self
    {
        $this->definition[] = new HasOneRelationshipDefinition(
            $model,
            $foreignKey ?? mb_strtolower($model . '_id'),
            $localKey,
        );

        return $this;
    }
}
