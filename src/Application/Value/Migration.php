<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Value;

use Ghostwriter\Draft\Application\Interface\MigrationInterface;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Override;

final class Migration extends Blueprint implements MigrationInterface
{
    public $table;

    private ?string $foreignKey = null;

    public function __construct(
        private readonly Model $model
    ) {}

    public function getForeignKey(): string
    {
        /** @var string $this->table */
        return $this->foreignKey ??= Str::of($this->table)
            ->singular()
            ->snake()
            ->append('_' . $this->getKeyName())
            ->toString();
    }

    #[Override]
    public function getModel(): Model
    {
        return $this->model;
    }
}
