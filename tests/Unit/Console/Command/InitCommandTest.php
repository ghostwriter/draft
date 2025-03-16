<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Command;

use Ghostwriter\Draft\Application\NameProvider;
use Ghostwriter\Draft\Application\NamespaceProvider;
use Ghostwriter\Draft\Application\PathProvider;
use Ghostwriter\Draft\Application\Sanitizer;
use Ghostwriter\Draft\Console\Command\BuildCommand;
use Ghostwriter\Draft\Console\Command\InitCommand;
use Ghostwriter\Draft\Console\Command\NewCommand;
use Ghostwriter\Draft\Console\Command\TraceCommand;
use Ghostwriter\Draft\Draft;
use Ghostwriter\Draft\DraftServiceProvider;
use Ghostwriter\Draft\Parser\Node\DraftFileNode;
use Ghostwriter\Draft\Parser\Printer;
use Ghostwriter\Draft\Parser\Visitor\DraftVisitor;
use Mockery;
use PhpParser\Node;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use Tests\Unit\AbstractTestCase;

use function base_path;
use function mb_strlen;

#[CoversClass(Draft::class)]
#[CoversClass(DraftFileNode::class)]
#[CoversClass(DraftServiceProvider::class)]
#[CoversClass(InitCommand::class)]
#[CoversClass(Printer::class)]
#[UsesClass(BuildCommand::class)]
#[UsesClass(DraftVisitor::class)]
#[UsesClass(NewCommand::class)]
#[UsesClass(TraceCommand::class)]
#[UsesClass(NameProvider::class)]
#[UsesClass(PathProvider::class)]
#[UsesClass(Sanitizer::class)]
#[CoversClass(NamespaceProvider::class)]
final class InitCommandTest extends AbstractTestCase
{
    #[DataProvider('initCommandProvider')]
    public function testInitCommandFailed(string $command, array $parameters): void
    {
        $this->filesystem->expects('missing')->once()
            ->with(base_path('draft.php'))
            ->andReturn(false);

        $this->printer->expects('print')->never()
            ->with(Mockery::type(Node::class))
            ->andReturn(Mockery::type('string'));

        $this->artisan($command, $parameters)
            ->expectsOutput('Checking for draft.php file...')
            ->expectsOutput('[✕] draft.php file already exists, use --force to overwrite it!')
            ->doesntExpectOutput('[✓] Created draft.php file')
            ->assertExitCode(1);
    }

    #[DataProvider('initCommandProvider')]
    public function testInitCommandSucceeded(string $command, array $parameters): void
    {
        $this->filesystem->expects('missing')->once()
            ->with(base_path('draft.php'))
            ->andReturn(true);

        $this->filesystem->expects('put')->once()
            ->with(base_path('draft.php'), Mockery::type('string'))
            ->andReturnUsing(static fn (string $path, string $content) => mb_strlen($content));

        $this->printer->expects('print')->once()
            ->with(Mockery::type(Node::class))
            ->andReturn(Mockery::type('string'));

        $this->artisan($command, $parameters)
            ->expectsOutput('Checking for draft.php file...')
            ->expectsOutput('[✓] Created draft.php file')
            ->doesntExpectOutput('[✕] draft.php file already exists, use --force to overwrite it!')
            ->assertExitCode(0);
    }

    #[DataProvider('initForceCommandProvider')]
    public function testInitCommandSucceededUsingForce(string $command, array $parameters): void
    {
        $this->filesystem->expects('missing')->once()
            ->with(base_path('draft.php'))
            ->andReturn(false);

        $this->filesystem->expects('put')->once()
            ->with(base_path('draft.php'), Mockery::type('string'))
            ->andReturnUsing(static fn (string $path, string $content) => mb_strlen($content));

        $this->printer->expects('print')->once()
            ->with(Mockery::type(Node::class))
            ->andReturn(Mockery::type('string'));

        $this->artisan($command, $parameters)
            ->expectsOutput('Checking for draft.php file...')
            ->expectsOutput('[✓] Created draft.php file (force)')
            ->doesntExpectOutput('[✕] draft.php file already exists, use --force to overwrite it!')
            ->assertExitCode(0);
    }
}
