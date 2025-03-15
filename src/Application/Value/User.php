<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Value;

use Closure;
use Ghostwriter\Draft\Application\Interface\MigrationInterface;
use Ghostwriter\Draft\Application\Interface\UserInterface;
use Illuminate\Contracts\Auth\Authenticatable as UserModel;
use Illuminate\Support\Str;
use Override;
use ReflectionClass;

use function basename;

final class User implements UserInterface
{
    private ?string $name = null;

    private ?string $namespace = null;

    private ?string $table = null;

    public function __construct(
        private readonly UserModel $userModel
    ) {}

    #[Override]
    public function migration(): MigrationInterface
    {
        return new Migration($this);
    }

    #[Override]
    public function name(): string
    {
        return $this->name ??= basename(self::class);
    }

    #[Override]
    public function namespace(): string
    {
        return $this->namespace ??= (new ReflectionClass($this->userModel))->getNamespaceName();
    }

    #[Override]
    public function table(): string
    {
        return $this->table ??= Str::of($this->name())->plural()->lower()->toString();
    }

    #[Override]
    public function withMigration(?Closure $factory = null): void {}
}
