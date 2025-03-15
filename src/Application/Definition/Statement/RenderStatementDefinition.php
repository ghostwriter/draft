<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Statement;

use Ghostwriter\Draft\Application\Interface\Definition\StatementDefinitionInterface;

final readonly class RenderStatementDefinition implements StatementDefinitionInterface
{
    public function __construct(
        private string $template,
        private array $parameters = []
    ) {}
}
