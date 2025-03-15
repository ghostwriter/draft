<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Statement;

use Ghostwriter\Draft\Application\Interface\Definition\StatementDefinitionInterface;

final readonly class ValidateStatementDefinition implements StatementDefinitionInterface
{
    public function __construct(
        private string $name,
        private array $rules = []
    ) {}
}
