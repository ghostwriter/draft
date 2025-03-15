<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Value;

use Closure;
use Ghostwriter\Draft\Application\Interface\MigrationInterface;
use Ghostwriter\Draft\Application\Interface\ModelInterface;
use Illuminate\Database\Eloquent\Model as IlluminateModel;
use Illuminate\Support\Str;
use Override;

final class Model implements ModelInterface
{
    private ?IlluminateModel $illuminateModel = null;

    private ?MigrationInterface $migration = null;

    private ?string $table = null;

    public function __construct(
        private readonly string $name
    ) {}

    public function controller(): string
    {
        return '';
    }

    //
    //    public function getForeignKey(): string
    //    {
    //        /** @var self->table */
    //        return Str::of($this->table)
    //            ->singular()
    //            ->snake()
    //            ->append('_' . $this->getKeyName())
    //            ->toString();
    //    }

    #[Override]
    public function migration(): MigrationInterface
    {
        return $this->migration ??= new Migration($this);
    }

    public function model(): IlluminateModel
    {
        return $this->illuminateModel ??= new class() extends IlluminateModel {};
    }

    #[Override]
    public function name(): string
    {
        return $this->name;
    }

    #[Override]
    public function namespace(): string
    {
        return '';
    }

    #[Override]
    public function table(): string
    {
        return $this->table ??= Str::of($this->name)->plural()->lower()->toString();
    }

    #[Override]
    public function withMigration(?Closure $factory = null): void
    {
        if (! $factory instanceof Closure) {
            return;
        }

        $migration = $factory($this, $this->migration());
        if ($migration instanceof MigrationInterface) {
            $this->migration = $migration;
        }
    }
}
