<?php

declare(strict_types=1);

namespace Tests\Unit;

use Ghostwriter\Draft\Application\Definition\ControllerDefinition;
use Ghostwriter\Draft\Application\Definition\ModelDefinition;
use Ghostwriter\Draft\Application\Formatter;
use Ghostwriter\Draft\Application\NameProvider;
use Ghostwriter\Draft\Application\PathProvider;
use Ghostwriter\Draft\Application\Sanitizer;
use Ghostwriter\Draft\Console\Command\BuildCommand;
use Ghostwriter\Draft\Console\Command\InitCommand;
use Ghostwriter\Draft\Console\Command\NewCommand;
use Ghostwriter\Draft\Console\Command\TraceCommand;
use Ghostwriter\Draft\Container\Factory\InflectorFactory;
use Ghostwriter\Draft\Container\Factory\ParserFactory;
use Ghostwriter\Draft\Container\ServiceProvider;
use Ghostwriter\Draft\Draft;
use Ghostwriter\Draft\Illuminate\Support\ServiceProvider\DraftServiceProvider;
use Ghostwriter\Draft\Parser\ClassMap;
use Ghostwriter\Draft\Parser\FindControllers;
use Ghostwriter\Draft\Parser\FindModels;
use Ghostwriter\Draft\Parser\Node\DraftFileNode;
use Ghostwriter\Draft\Parser\Printer;
use Ghostwriter\Draft\Parser\Visitor\DraftVisitor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(BuildCommand::class)]
#[CoversClass(ClassMap::class)]
#[CoversClass(ControllerDefinition::class)]
#[CoversClass(Draft::class)]
#[CoversClass(DraftServiceProvider::class)]
#[CoversClass(DraftVisitor::class)]
#[CoversClass(FindControllers::class)]
#[CoversClass(FindModels::class)]
#[CoversClass(Formatter::class)]
#[CoversClass(InitCommand::class)]
#[CoversClass(ModelDefinition::class)]
#[CoversClass(NewCommand::class)]
#[CoversClass(ParserFactory::class)]
#[CoversClass(TraceCommand::class)]
#[CoversClass(DraftFileNode::class)]
#[CoversClass(Printer::class)]
#[CoversClass(InflectorFactory::class)]
#[CoversClass(Sanitizer::class)]
#[CoversClass(NameProvider::class)]
#[CoversClass(PathProvider::class)]
#[CoversClass(NamespaceProvider::class)]
final class DraftTest extends AbstractTestCase
{
    public function assertDraftHasControllerDefinitions(Draft $draft): void
    {
        $modelDefinitions = $draft->controllerDefinitions();

        self::assertNotEmpty($modelDefinitions);

        self::assertContainsOnlyInstancesOf(ControllerDefinition::class, $modelDefinitions);
    }

    public function assertDraftHasModelDefinitions(Draft $draft): void
    {
        $modelDefinitions = $draft->modelDefinitions();

        self::assertNotEmpty($modelDefinitions);

        self::assertContainsOnlyInstancesOf(ModelDefinition::class, $modelDefinitions);
    }

    public function assertDraftIsEmpty(Draft $draft): void
    {
        self::assertEmpty($draft->controllerDefinitions());
        self::assertEmpty($draft->eventDefinitions());
        self::assertEmpty($draft->factoryDefinitions());
        self::assertEmpty($draft->inertiaDefinitions());
        self::assertEmpty($draft->jobDefinitions());
        self::assertEmpty($draft->livewireDefinitions());
        self::assertEmpty($draft->mailDefinitions());
        self::assertEmpty($draft->migrationDefinitions());
        self::assertEmpty($draft->modelDefinitions());
        self::assertEmpty($draft->notificationDefinitions());
        self::assertEmpty($draft->policyDefinitions());
        self::assertEmpty($draft->routeDefinitions());
        self::assertEmpty($draft->ruleDefinitions());
        self::assertEmpty($draft->seederDefinitions());
        self::assertEmpty($draft->testsDefinitions());
        self::assertEmpty($draft->viewDefinitions());
    }

    public function testCommand(): void
    {

        //        $filesystem->expets('test');
        //        $this->artisan(InitCommand::class)->assertSuccessful();
        $this->artisan(TraceCommand::class, [])->assertSuccessful();

        //        $this->artisan(BuildCommand::class, [])->assertSuccessful();
        //        $this->artisan(NewCommand::class)
        //            ->expectsQuestion('What should the model be named?', 'Post')
        //            ->assertSuccessful();
    }

    public function testDraftControllersIsEmpty(): void
    {
        self::assertEmpty($this->draftVisitor->controllers());
    }

    public function testDraftFactoriesIsEmpty(): void
    {
        self::assertEmpty($this->draftVisitor->factories());
    }

    public function testDraftHasControllerDefinitions(): void
    {
        $draft = Draft::new(static fn (Draft $draft) =>$draft->controller('Post', static fn () => null));

        $this->assertDraftHasControllerDefinitions($draft);
    }

    public function testDraftHasModelDefinitions(): void
    {
        $draft = Draft::new(static fn (Draft $draft) =>$draft->model('Post', static fn () => null));

        $this->assertDraftHasModelDefinitions($draft);
    }

    public function testDraftIsEmpty(): void
    {
        $draft = new Draft();

        $this->assertDraftIsEmpty($draft);
    }

    public function testDraftModelsIsNeverEmpty(): void
    {
        self::assertCount(1, $this->draftVisitor->models());
        self::assertNotEmpty($this->draftVisitor->models());
    }

    public function testDraftSeedersIsEmpty(): void
    {
        self::assertEmpty($this->draftVisitor->seeders());
    }
}
