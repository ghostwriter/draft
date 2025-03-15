<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Action;

use Ghostwriter\Draft\Application\Definition\ModelDefinition;

final class LivewireActionDefinition
{
    public function __construct(
        private readonly string $name,
        private array $properties = [],
    ) {}

    public function data(string ...$properties): self
    {
        foreach ($properties as $property) {
            $this->properties[$property] = $property;
        }

        return $this;
    }

    public function fire(string $event, array $data): self
    {
        return $this;
    }

    public function flash(string $type, string $message): self
    {
        return $this;
    }

    public function modelDefinition(string $name): ?ModelDefinition
    {
        return $this->modelDefinition;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function properties(): array
    {
        return $this->properties;
    }

    public function query(string $name): self
    {
        return $this;
    }

    public function redirect(string $route): self
    {
        return $this;
    }

    public function render(string $view, array $data): self
    {
        return $this;
    }

    public function save(string $name): self
    {
        return $this;
    }

    public function send(string $notification, string $recipient, array $data): self
    {
        return $this;
    }

    public function useFormRequest(string $name): self
    {
        return $this;
    }
}
