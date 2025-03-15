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
use Ghostwriter\Draft\Parser\Visitor\DraftVisitor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\AbstractTestCase;

#[CoversClass(BuildCommand::class)]
#[CoversClass(Draft::class)]
#[CoversClass(DraftFileNode::class)]
#[CoversClass(DraftServiceProvider::class)]
#[CoversClass(DraftVisitor::class)]
#[CoversClass(InitCommand::class)]
#[CoversClass(NameProvider::class)]
#[CoversClass(NamespaceProvider::class)]
#[CoversClass(NewCommand::class)]
#[CoversClass(PathProvider::class)]
#[CoversClass(Sanitizer::class)]
#[CoversClass(TraceCommand::class)]
final class NewCommandTest extends AbstractTestCase
{
    #[DataProvider('newCommandProvider')]
    public function testNewCommand(string $command, array $parameters): void
    {
        //        dump([$command, $parameters]);

        $this->filesystem->shouldIgnoreMissing();

        //        $this->filesystem->expects('exists')->once()
        //            ->with(\Mockery::type('string'))
        //            ->andReturn(true);
        //
        //        $this->filesystem->expects('isDirectory')->once()
        //            ->with(\Mockery::type('string'))
        //            ->andReturn(true);
        //
        //        $this->filesystem->expects('get')->once()
        //            ->with(\Mockery::type('string'))
        //            ->andReturn(true);
        //
        //        $this->filesystem->expects('put')->once()
        //            ->with(\Mockery::type('string'), \Mockery::type('string'))
        //            ->andReturn(1);

        $this->artisan($command, $parameters)
//            ->expectsQuestion(sprintf(
//                'A2 App\\Models\\%s model does not exist. Do you want to generate it?',
//                $parameters['name'],
//            ), 'draft')
//            ->expectsOutput('Creating a new draft file...')
//            ->expectsOutput('[✓] Created draft.php file')
            ->assertExitCode(0);
    }
}
