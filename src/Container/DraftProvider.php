<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Container;

use Doctrine\Inflector\Inflector;
use Ghostwriter\Container\Interface\Service\ExtensionInterface;
use Ghostwriter\Container\Interface\Service\FactoryInterface;
use Ghostwriter\Container\Service\Provider\AbstractProvider;
use Ghostwriter\EventDispatcher\Interface\ListenerProviderInterface;
use PhpParser\Parser;

/**
 * @see DraftProviderTest
 */
final class DraftProvider extends AbstractProvider
{

    /**
     * [alias => service].
     *
     * @var array<class-string,class-string>
     */
    public const array ALIAS = [];

    /**
     * [concrete => [abstract => implementation]].
     *
     * @var array<class-string,array<class-string,class-string>>
     */
    public const array BIND = [];

    /**
     * [service => [extension, ...]].
     *
     * @var array<class-string,list<class-string<ExtensionInterface>>>
     */
    public const array EXTEND = [
        ListenerProviderInterface::class => [Extension\ListenerProviderExtension::class],
    ];

    /**
     * [service => factory].
     *
     * @var array<class-string,class-string<FactoryInterface>>
     */
    public const array FACTORY = [
        Parser::class => Factory\ParserFactory::class,
        Inflector::class => Factory\InflectorFactory::class,
    ];
}
