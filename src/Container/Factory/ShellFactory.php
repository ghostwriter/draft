<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Container\Factory;

use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\FactoryInterface;
use Ghostwriter\Shell\Interface\ShellInterface;
use Ghostwriter\Shell\Shell;
use Override;

final readonly class ShellFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(ContainerInterface $container): ShellInterface
    {
        return Shell::new();
    }
}
