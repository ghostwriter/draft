<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Builder;

use Ghostwriter\Draft\Application\Definition\Action\ControllerActionDefinition;
use Ghostwriter\Draft\Application\Definition\ControllerDefinition;
use Ghostwriter\Draft\Application\Definition\DraftFileDefinition;
use Ghostwriter\Draft\Application\Definition\MigrationDefinition;
use Ghostwriter\Draft\Application\Definition\ModelDefinition;
use Ghostwriter\Draft\Application\Definition\RouterDefinition;
use Ghostwriter\Draft\Application\Definition\TestDefinition;
use Ghostwriter\Draft\Application\Interface\DefinitionInterface;
use Ghostwriter\Draft\Draft;
use Ghostwriter\Draft\Parser\Node\DraftFileNode;
use Ghostwriter\Draft\Parser\Node\FileNode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use PhpParser\Builder\Namespace_ as NamespaceBuilder;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall as MethodCallExpression;
use PhpParser\Node\Expr\StaticCall as StaticCallExpression;
use PhpParser\Node\Expr\Variable as VariableExpression;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Block as BlockStatement;
use PhpParser\Node\Stmt\Class_ as ClassStatement;
use PhpParser\Node\Stmt\Declare_ as DeclareStatement;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Expression as ExpressionStatement;
use PhpParser\Node\Stmt\Namespace_ as NamespaceStatement;
use PhpParser\Node\Stmt\Nop as NopStatement;
use PhpParser\Node\Stmt\Return_ as ReturnStatement;
use PhpParser\Node\Stmt\Use_ as UseStatement;
use PhpParser\Node\UseItem;
use Tests\Unit\AbstractTestCase;

use function array_map;
use function sprintf;

final class Builder extends AbstractDefinitionBuilder
{
    public function build(DefinitionInterface $definition): Node
    {
        return match ($definition::class) {
            DraftFileDefinition::class => $this->buildDraftFileNode($definition),
            ModelDefinition::class => $this->buildModelNode($definition),
            MigrationDefinition::class => $this->buildMigrationNode($definition),
            ControllerDefinition::class => $this->buildControllerNode($definition),
            RouterDefinition::class => $this->buildRouterNode($definition),
            TestDefinition::class => $this->buildTestNode($definition),
            default => throw new InvalidArgumentException(sprintf('Unknown Definition: %s', $definition::class)),
        };
    }

    private function buildControllerNode(ControllerDefinition $definition): ClassStatement
    {
        $classBuilder = $this->factory
            ->class($definition->name())
            ->addStmt($this->factory->use('App\\Http\\Controllers\\Controller'))
            ->extend('Controller');

        foreach ($definition->actions() as $action) {
            if (! $action instanceof ControllerActionDefinition) {
                continue;
            }

            $method = $this->factory
                ->method($action->name())
                ->makePublic();

            $classBuilder->addStmt($method);
        }

        return $classBuilder->getNode();
    }

    private function buildDraftFileNode(DraftFileDefinition $definition): Node
    {
        return new DraftFileNode();
    }

    private function buildFileNode(array $uses = [], array $namespaces = []): Node
    {
        $uses[UseStatement::TYPE_NORMAL][Draft::class] = null;
        $uses[UseStatement::TYPE_FUNCTION] ??= [];
        $uses[UseStatement::TYPE_CONSTANT] ??= [];

        return new FileNode($uses, $namespaces);

        return new NamespaceStatement(
            new Name('App\\Draft'),
            [
                new DeclareStatement([new DeclareDeclare(new Identifier('strict_types'), new Int_(1))]),
                new NopStatement(),
            ]
        );

        return [
            new DeclareStatement([new DeclareDeclare(new Identifier('strict_types'), new Int_(1))]),
            new NopStatement(),
            ...[
                ...array_map(
                    static fn (string $use) => new UseStatement([new UseItem(new Name($use))]),
                    $uses['class'] ?? []
                ),
                ...array_map(
                    static fn (string $use) => new UseStatement([new UseItem(new Name($use))]),
                    $uses['function'] ?? []
                ),
                ...array_map(
                    static fn (string $use) => new UseStatement([new UseItem(new Name($use))]),
                    $uses['constant'] ?? []
                ),
            ],
            new UseStatement([new UseItem(new Name(Draft::class))]),
            new NopStatement(),
            //            new Return_(new Closure([
            //                'attrGroups' => [],
            //                'static' => true,
            //                'byRef' => false,
            //                'params' => [new Param(new Variable('draft'), null, new Name('Draft'))],
            //                'uses' => [],
            //                'returnType' => new Identifier('void'),
            //                'stmts' => [new Nop()],
            //            ])),
        ];
    }

    private function buildMigrationNode(MigrationDefinition $definition): Node
    {
        // declare(strict_types=1);
        //
        // use Illuminate\Database\Migrations\Migration;
        // use Illuminate\Database\Schema\Blueprint;
        // use Illuminate\Support\Facades\Schema;
        //
        // return new class() extends Migration {
        //    /**
        //     * Reverse the migrations.
        //     */
        //    public function down(): void
        //    {
        //        Schema::dropIfExists('failed_jobs');
        //    }
        //
        //    /**
        //     * Run the migrations.
        //     */
        //    public function up(): void
        //    {
        //        Schema::create('failed_jobs', static function (Blueprint $table): void {
        //            $table->id();
        //            $table->string('uuid')->unique();
        //            $table->text('connection');
        //            $table->text('queue');
        //            $table->longText('payload');
        //            $table->longText('exception');
        //            $table->timestamp('failed_at')->useCurrent();
        //        });
        //    }
        // };

        $classBuilder = $this->factory
            ->class()
            ->addStmt($this->factory->use(Migration::class))
            ->addStmt($this->factory->use(Schema::class))
            ->extend('Migration')
            ->addStmts(
                [
                    $this->factory
                        ->method('down')
                        ->makePublic()
                        ->addStmt(
                            $this->factory
                                ->methodCall(
                                    $this->factory->var('Schema'),
                                    'dropIfExists',
                                    $this->factory
                                        ->args([$this->factory->val($definition->table())]),
                                ),
                        ),
                    $this->factory
                        ->method('up')
                        ->makePublic()
                        ->addStmt(
                            new ExpressionStatement(
                                new StaticCallExpression(
                                    new VariableExpression('Schema'),
                                    'create',
                                    [new Arg(new String_($definition->table()))],
                                    //                                new VariableExpression('$table'),
                                ),
                            ),
                        ), ]
            );

        return $this->createNamespace(
            '',
            fn (NamespaceBuilder $namespaceBuilder) => $namespaceBuilder
                ->addStmt($this->createDeclareStatement())
                ->addStmt($this->buildNopStatement())
                ->addStmt($classBuilder->getNode())
        );
    }

    private function buildModelNode(ModelDefinition $definition): FileNode
    {
        $declareStatement = $this->createDeclareStatement();

        $uses = [
            UseStatement::TYPE_NORMAL => [
                Model::class => null,
            ],
            UseStatement::TYPE_FUNCTION => [],
            UseStatement::TYPE_CONSTANT => [],
        ];

        $modelNamespace = 'App\\Models';

        $namespaces = [
            $this->createNamespace(
                $modelNamespace,
                function (NamespaceStatement $namespace) use ($definition): void {
                    $namespace->addStmt();

                    $classBuilder = $this->factory
                        ->class($definition->name())
                        ->extend(Model::class)
                        ->addStmt(
                            $this->factory->property('table')
                                ->makeProtected()
                                ->setDefault($definition->table())
                        );
                }
            ),
            new NamespaceStatement(
                new Name($modelNamespace),
                [$this->createDeclareStatement(), $this->buildNopStatement()]
            ),
        ];

        //        $classBuilder = $this->factory
        //            ->class($definition->name())
        //            ->extend(Model::class)
        //            ->addStmt($this->factory->property('table')->makeProtected()->setDefault($definition->table()));
        //
        //        foreach ($definition->relationships('') as $relationship) {
        //            $method = $this->factory
        //                ->method($relationship['name'])
        //                ->makePublic()
        //                ->addStmt(
        //                    new ReturnStatement(
        //                        new MethodCall(
        //                            new Variable('this'),
        //                            $relationship['type'],
        //                            [new Arg(new String_($relationship['target']))],
        //                        ),
        //                    ),
        //                );
        //            $classBuilder->addStmt($method);
        //        }

        return $this->buildFileNode($uses, $namespaces);
    }

    private function buildNopStatement(): NopStatement
    {
        static $nopStatement;

        return $nopStatement ??= new NopStatement();
    }

    private function buildRouterNode(RouterDefinition $definition): BlockStatement
    {
        $router = new VariableExpression('router');

        $calls = [];
        foreach ($definition->routes() as $route) {
            $calls[] = new ExpressionStatement(
                new MethodCallExpression(
                    $router,
                    $route['method'],
                    [new Arg(new String_($route['uri'])), new Arg(new String_($route['action']))],
                ),
            );
        }

        return new BlockStatement($calls);
    }

    private function buildTestNode(TestDefinition $definition): ClassStatement
    {
        $classBuilder = $this->factory
            ->class($definition->name() . 'Test')
            ->addStmt($this->factory->use(AbstractTestCase::class))
            ->extend('AbstractTestCase');

        foreach ($definition->testcases() as $testcase) {
            $method = $this->factory
                ->method('test_' . $testcase->name())
                ->makePublic()
                ->addStmt(
                    new ExpressionStatement(
                        new MethodCallExpression(
                            new VariableExpression('self'),
                            'assertTrue',
                            [new Arg(new Node\Scalar\LNumber(1))],
                        ),
                    ),
                );

            $classBuilder->addStmt($method);
        }

        return $classBuilder->getNode();
    }
}
