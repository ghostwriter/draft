<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Interface;

interface MigrationInterface
{
    public function getModel(): ModelInterface;
}
