<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Interface\Controller;

interface FormRequestInterface
{
    public function rules(): array;
}
