<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Exception;

use Ghostwriter\Draft\Application\Interface\Exception\DraftExceptionInterface;

final class RuntimeException extends \RuntimeException implements DraftExceptionInterface {}
