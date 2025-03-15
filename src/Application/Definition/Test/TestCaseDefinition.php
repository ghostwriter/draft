<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Test;

final class TestCaseDefinition
{
    public function __construct(
        private readonly string $name,
        private array $properties = [],
    ) {}

    public function __call(string $name, array $arguments): void
    {
        $this->properties[$name][] = $arguments;
    }

    public function name(): string
    {
        return $this->name;
    }

    //    public function get(string $name): void
    //    {
    //        $this->properties[$name] = [__FUNCTION__, $name];
    //    }
    //
    //    public function assertStatus(int $int) {}
}
