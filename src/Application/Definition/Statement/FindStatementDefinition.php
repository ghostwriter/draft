<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Statement;

use Ghostwriter\Draft\Application\Interface\Definition\StatementDefinitionInterface;

final class FindStatementDefinition implements StatementDefinitionInterface
{
    public function __construct(
        private string $model,
        private array $query = []
    ) {}
}
