<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Container\Factory;

use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\FactoryInterface;
use Override;
use PhpParser\Parser;
use PhpParser\ParserFactory as PhpParserFactory;

final readonly class ParserFactory implements FactoryInterface
{
    #[Override]
    public function __invoke(ContainerInterface $container): Parser
    {
        return $container->get(PhpParserFactory::class)->createForNewestSupportedVersion();
    }
}
