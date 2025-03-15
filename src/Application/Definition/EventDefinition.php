<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition;

use Ghostwriter\Draft\Application\Interface\DefinitionInterface;
use Ghostwriter\Draft\Application\Name\EventName;
use Ghostwriter\Draft\Application\Path\EventPath;

final readonly class EventDefinition implements DefinitionInterface
{
    public function __construct(
        private EventName $name,
        private EventPath $path,
    ) {}

    public static function new(string $name): self
    {
        return new self(new EventName($name), new EventPath($name));
    }

    public function name(): EventName
    {
        return $this->name;
    }

    public function path(): EventPath
    {
        return $this->path;
    }
}
