<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Container\Factory;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory as DoctrineInflectorFactory;
use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\FactoryInterface;
use Override;

/**
 * @implements FactoryInterface<Inflector>
 */
final readonly class InflectorFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(ContainerInterface $container): Inflector
    {
        return DoctrineInflectorFactory::create()->build();
    }
}
