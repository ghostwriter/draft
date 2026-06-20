<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Container\Extension;

use Ghostwriter\Container\Interface\Service\ExtensionInterface;
use Ghostwriter\EventDispatcher\Container\AbstractListenerProviderExtension;
use Ghostwriter\EventDispatcher\Interface\ListenerProviderInterface;

/**
 * @see ListenerProviderExtensionTest
 * @implements ExtensionInterface<ListenerProviderInterface>
 */
final readonly class ListenerProviderExtension extends AbstractListenerProviderExtension
{
    /** @var array<'object'|class-string,list<class-string>> */
    public const array LISTENERS = [
        'object' => [],
    ];
}
