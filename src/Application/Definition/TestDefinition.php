<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition;

use Ghostwriter\Draft\Application\Definition\Test\TestCaseDefinition;
use RuntimeException;

use function array_key_exists;
use function sprintf;

final class TestDefinition
{
    public function __construct(
        private readonly string $name,
        private array $testcases = [],
    ) {}

    public static function new(string $name): self
    {
        return new self($name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function test(string $name, callable $factory): void
    {
        if (array_key_exists($name, $this->testcases)) {
            throw new RuntimeException(sprintf('Test %s already defined', $name));
        }

        $testCaseDefinition = new TestCaseDefinition($name);

        $factory($testCaseDefinition);

        $this->testcases[$name] = $testCaseDefinition;
    }

    /**
     * @return list<TestCaseDefinition>
     */
    public function testcases(): array
    {
        return $this->testcases;
    }
}
