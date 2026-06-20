<?php

declare(strict_types=1);

namespace Tests\Unit;

use Generator;
use Ghostwriter\Draft\Console\Command\BuildCommand;
use Ghostwriter\Draft\Console\Command\InitCommand;
use Ghostwriter\Draft\Console\Command\NewCommand;
use Ghostwriter\Draft\Console\Command\TraceCommand;
use Ghostwriter\Draft\Draft;
use Ghostwriter\Draft\Illuminate\Support\ServiceProvider\DraftServiceProvider;
use Ghostwriter\Draft\Parser\PrinterInterface;
use Ghostwriter\Draft\Parser\Visitor\DraftVisitor;
use Illuminate\Cache\Console\CacheTableCommand;
use Illuminate\Config\Repository;
use Illuminate\Database\Console\TableCommand as DatabaseTableCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\Concerns\InteractsWithAuthentication;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDeprecationHandling;
use Illuminate\Foundation\Testing\Concerns\InteractsWithExceptionHandling;
use Illuminate\Foundation\Testing\Concerns\InteractsWithRedis;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTestCaseLifecycle;
use Illuminate\Foundation\Testing\Concerns\InteractsWithTime;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithConsoleEvents;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\Console\NotificationTableCommand;
use Illuminate\Queue\Console\BatchesTableCommand;
use Illuminate\Queue\Console\FailedTableCommand;
use Illuminate\Queue\Console\TableCommand as QueueTableCommand;
use Illuminate\Routing\Router;
use Illuminate\Session\Console\SessionTableCommand;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use Mockery;
use Mockery\MockInterface;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\TestCase;
use Override;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;
use ReflectionMethod;
use ReflectionParameter;

use function array_keys;
use function array_map;
use function assert;
use function class_basename;
use function database_path;
use function end;
use function implode;
use function is_array;
use function is_bool;
use function sprintf;
use function tap;

// #[WithMigration('laravel', 'cache', 'queue', 'session', 'notifications')]
abstract class AbstractTestCase extends TestCase
{
    public const array CMD = [
        BatchesTableCommand::class,
        DatabaseTableCommand::class,
        CacheTableCommand::class,
        FailedTableCommand::class,
        NotificationTableCommand::class,
        QueueTableCommand::class,
        SessionTableCommand::class,
    ];
    //    use DatabaseTransactions;
    //    use InteractsWithAuthentication;
    //    use InteractsWithConsole;
    //    use InteractsWithContainer;
    //    use InteractsWithDatabase;
    //    use InteractsWithDeprecationHandling;
    //    use InteractsWithExceptionHandling;
    //    use InteractsWithRedis;
    //    use InteractsWithSession;
    //    use InteractsWithTestCaseLifecycle;
    //    use InteractsWithTime;
    //    use InteractsWithViews;
    //    use MakesHttpRequests;
    //    use RefreshDatabase;
    //        use WithConsoleEvents;
    //    use WithFaker;

    protected Draft $draft;

    protected DraftVisitor $draftVisitor;

    protected $enablesPackageDiscoveries = true;

    protected (Filesystem&MockInterface)|(Filesystem&MockObject) $filesystem;

    protected MockInterface&PrinterInterface $printer;

    #[Override]
    final protected function setUp(): void
    {
        parent::setUp();
        $this->afterApplicationRefreshed(static function (): void {
            // Code before application created.
        });

        $this->afterApplicationCreated(function (): void {
            // Code after application created.
            $this->draft = $this->app->get(Draft::class);
            $this->draftVisitor = $this->app->get(DraftVisitor::class);

            $this->filesystem = self::draftMock(Filesystem::class);
            $this->printer = self::draftMock(PrinterInterface::class);
        });

        $this->beforeApplicationDestroyed(static function (): void {
            // Code before application destroyed.
        });

    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     */
    #[Override]
    public function defineEnvironment($app): void
    {
        tap($app['config'], static function (Repository $config): void {
            //            $config->set([
            //                'database.connections.testbench' => [
            //                    'driver'   => 'sqlite',
            //                    'database' => ':memory:',
            //                    'prefix'   => ''
            //                ],
            //                'queue.batching.database' => 'testbench',
            //                'queue.failed.database' => 'testbench',
            //            ]);
        });
    }

    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom(database_path('database/migrations'));
    }

    /**
     * Define routes setup.
     *
     * @param Router $router
     */
    #[Override]
    protected function defineRoutes($router): void
    {
        // Define routes.
        $router->get('testing', static fn () => 'test');
    }

    /**
     * Get package providers.
     *
     * @param Application $app
     *
     * @return array<int, class-string<ServiceProvider>>
     */
    #[Override]
    protected function getPackageProviders($app)
    {
        return [DraftServiceProvider::class, ...parent::getPackageProviders($app)];
    }

    /**
     * Get package providers.
     *
     * @param Application $app
     *
     * @return array<class-string<ServiceProvider>, class-string<ServiceProvider>>
     */
    #[Override]
    protected function overrideApplicationBindings($app)
    {
        return [
            // 'Illuminate\View\ViewServiceProvider' => 'Acme\ViewServiceProvider',
            ...parent::overrideApplicationBindings($app),
        ];
    }

    public static function buildCommandProvider(): Generator
    {
        foreach (['draft:build', BuildCommand::class] as $command) {
            yield $command => [$command];
        }
    }

    public static function commandProvider(): Generator
    {
        yield from self::initCommandProvider();
        yield from self::initForceCommandProvider();
        yield from self::buildCommandProvider();
        yield from self::newCommandProvider();
        yield from self::traceCommandProvider();
    }

    /**
     * @param list<string>                       $commands
     * @param array<string,array<string,scalar>> $arguments
     *
     * @return Generator
     */
    public static function draftCommandProvider(array $commands, array $arguments = []): Generator
    {
        if ([] === $commands) {
            throw new InvalidArgumentException('Commands array cannot be empty.');
        }

        foreach ($commands as $command) {
            $name = class_basename($command);

            if ([] === $arguments) {
                yield $name => [$command, []];

                continue;
            }

            foreach ($arguments as $parameters) {
                yield self::getCommandAsString($command, $parameters) => [$command, $parameters];
            }
        }
    }

    /**
     * @param array<string,scalar> $parameters
     *
     * @return non-empty-string|string
     */
    public static function getCommandAsString(string $command, array $parameters): string
    {
        return sprintf('%s %s', class_basename($command), implode(
            ' ',
            array_map(
                static fn (string $key, mixed $value) => match (true) {
                    is_bool($value) => match (true) {
                        $value => $key,
                        default => ''
                    },
                    is_array($value) => implode(',', $value),
                    default => sprintf('%s=%s', $key, $value)
                },
                array_keys($parameters),
                $parameters
            )
        ));
    }

    public static function initCommandProvider(): Generator
    {
        yield from self::draftCommandProvider(['draft:init', InitCommand::class]);
    }

    public static function initForceCommandProvider(): Generator
    {
        yield from self::draftCommandProvider(['draft:init', InitCommand::class], [
            [
                '--force' => true,
            ],
        ]);
    }

    public static function newCommandProvider(): Generator
    {
        yield from self::draftCommandProvider(
            ['draft:new', NewCommand::class],
            [
                [
                    'name' => 'Post',
                ],
                [
                    'name' => 'Comment',
                ],
                [
                    'name' => 'User',
                ],
            ]
        );
    }

    public static function traceCommandProvider(): Generator
    {
        yield from self::draftCommandProvider(['draft:trace', TraceCommand::class]);
    }

    /**
     * @template TMock of object
     *
     * @param class-string<TMock>                $class
     * @param callable(MockInterface&TMock):void $callback
     *
     * @return MockInterface&TMock
     */
    final protected static function draftMock(string $class, ?callable $callback = null): MockInterface
    {
        $mock = Mockery::mock($class);

        if (null !== $callback) {
            $callback($mock);
        }

        Facade::getFacadeApplication()->instance($class, $mock);

        return $mock;
    }

    private static function isVariadic(string $class, string $method): bool
    {
        try {
            $reflection = new ReflectionMethod($class, $method);
        } catch (ReflectionException $e) {
            // Handle the exception if the method does not exist
            return false;
        }

        $parameters = $reflection->getParameters();

        $lastParameter = end($parameters);

        assert($lastParameter instanceof ReflectionParameter);

        return $lastParameter->isVariadic();
    }
}
