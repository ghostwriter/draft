<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Interface\Controller;

use Ghostwriter\Draft\Application\Interface\Definition\StatementDefinitionInterface;
use Ghostwriter\Draft\Application\Interface\ModelInterface;

interface ActionInterface
{
    public function name(): string;

    public function statement(StatementDefinitionInterface $statement): self;

    /**
     * @return iterable<string,StatementDefinitionInterface>
     */
    public function statements(): iterable;

    public function with(string $key, ModelInterface $model): self;

    /**
     * @param iterable<string,ModelInterface> $param
     */
    public function withMany(iterable $param): self;
}
