<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Command;

use Ghostwriter\Draft\Console\Command\TraceCommand;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TraceCommand::class)]
final class TraceCommandTest extends TestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
