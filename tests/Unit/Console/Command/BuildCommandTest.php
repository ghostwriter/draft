<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Command;

use Ghostwriter\Draft\Console\Command\BuildCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(BuildCommand::class)]
final class BuildCommandTest extends TestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
