<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Statement;

use Ghostwriter\Draft\Application\Interface\Definition\StatementDefinitionInterface;

final class QueryStatementDefinition implements StatementDefinitionInterface
{
    public function __construct(
        private readonly array $query = []
    ) {}
}
