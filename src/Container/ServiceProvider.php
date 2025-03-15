<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Container;

use Doctrine\Inflector\Inflector;
use Ghostwriter\CaseConverter\CaseConverter;
use Ghostwriter\CaseConverter\Interface\CaseConverterInterface;
use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\FactoryInterface;
use Ghostwriter\Container\Interface\ServiceProviderInterface;
use Ghostwriter\Draft\Container\Factory\InflectorFactory;
use Ghostwriter\Draft\Container\Factory\ParserFactory;
use Ghostwriter\Draft\Container\Factory\ShellFactory;
use Ghostwriter\EventDispatcher\Container\ServiceProvider as EventServiceProvider;
use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Ghostwriter\Json\Interface\JsonInterface;
use Ghostwriter\Json\Json;
use Ghostwriter\Shell\Interface\ShellInterface;
use Ghostwriter\Shell\Shell;
use Override;
use PhpParser\Parser;
use Throwable;

final readonly class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @var array<class-string,class-string>
     */
    private const array ALIAS = [
        CaseConverter::class => CaseConverterInterface::class,
        Filesystem::class => FilesystemInterface::class,
        Json::class => JsonInterface::class,
        Shell::class => ShellInterface::class,
    ];

    /**
     * @var array<class-string,class-string<FactoryInterface>>
     */
    private const array FACTORY = [
        Inflector::class => InflectorFactory::class,
        Parser::class => ParserFactory::class,
        Shell::class => ShellFactory::class,
    ];

    /**
     * @var list<class-string<ServiceProviderInterface>>
     */
    private const array PROVIDER = [EventServiceProvider::class];

    /**
     * @throws Throwable
     */
    #[Override]
    public function __invoke(ContainerInterface $container): void
    {
        foreach (self::ALIAS as $service => $alias) {
            $container->alias($service, $alias);
        }

        foreach (self::FACTORY as $class => $factory) {
            $container->factory($class, $factory);
        }

        foreach (self::PROVIDER as $provider) {
            $container->provide($provider);
        }
    }
}
