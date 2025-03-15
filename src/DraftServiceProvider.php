<?php

declare(strict_types=1);

namespace Ghostwriter\Draft;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;
use Ghostwriter\CaseConverter\CaseConverter;
use Ghostwriter\CaseConverter\Interface\CaseConverterInterface;
use Ghostwriter\Container\Container;
use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Draft\Console\Command\BuildCommand;
use Ghostwriter\Draft\Console\Command\InitCommand;
use Ghostwriter\Draft\Console\Command\NewCommand;
use Ghostwriter\Draft\Console\Command\TraceCommand;
// use Illuminate\Container\Container;
use Ghostwriter\Draft\Parser\Printer;
use Ghostwriter\Draft\Parser\PrinterInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Override;
use PhpParser\Parser;
use PhpParser\ParserFactory;

use const DIRECTORY_SEPARATOR;

use function config_path;
use function dirname;

final class DraftServiceProvider extends ServiceProvider
{
    private const array COMMANDS = [
        BuildCommand::class,
        InitCommand::class,
        NewCommand::class,
        TraceCommand::class,
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'draft');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'draft');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/draft.php' => config_path('draft.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/draft'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/draft'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/draft'),
            ], 'lang');*/

            // Registering package commands.
            $this->commands(self::COMMANDS);

            $this->optimizes(optimize: 'package:optimize', clear: 'package:clear-optimizations');
        }

        AboutCommand::add('My Package', static fn () => [
            'Version' => '1.0.0',
        ]);

        Model::shouldBeStrict();
    }

    /**
     * Register the application services.
     */
    #[Override]
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'draft.php',
            'draft'
        );

        $application = $this->app;
        $application->bind(CaseConverterInterface::class, CaseConverter::class);
        $application->bind(PrinterInterface::class, Printer::class);
        $application->singleton(Inflector::class, static fn (): Inflector => InflectorFactory::create()->build());
        $application->singleton(ContainerInterface::class, static fn (): Container => Container::getInstance());
        $application->singleton(
            Parser::class,
            static fn (): Parser => $application->get(ParserFactory::class)->createForNewestSupportedVersion(),
        );
    }
}
