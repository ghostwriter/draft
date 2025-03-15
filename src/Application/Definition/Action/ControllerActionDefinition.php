<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Definition\Action;

use Ghostwriter\Draft\Application\Definition\Statement\DispatchStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\FindStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\FireStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\FlashStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\FormRequestStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\QueryStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\RedirectStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\RenderStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\SaveStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\SendStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\ValidateStatementDefinition;

final class ControllerActionDefinition
{
    public function __construct(
        private readonly string $name,
        private array $statements = [],
    ) {}

    public function dispatch(string $job, array $parameters = []): self
    {
        $this->statements[] = new DispatchStatementDefinition($job, $parameters);

        return $this;
    }

    public function find(string $name): self
    {
        $this->statements[] = new FindStatementDefinition($name);

        return $this;
    }

    public function fire(string $event, array $parameters = []): self
    {
        $this->statements[] = new FireStatementDefinition($event, $parameters);

        return $this;
    }

    public function flash(string $type, string $message): self
    {
        $this->statements[] = new FlashStatementDefinition($type, $message);

        return $this;
    }

    public function formRequest(string $name, array $rules): self
    {
        $this->statements[] = new FormRequestStatementDefinition($name, $rules);

        return $this;
    }

    //    public function modelDefinition(string $name): ModelDefinition
    //    {
    // //        return $this->[$name]
    //    }

    public function name(): string
    {
        return $this->name;
    }

    public function query(array $parameters = [
        'all' => true,
    ]): self
    {
        $this->statements[] = new QueryStatementDefinition($parameters);

        return $this;
    }

    public function redirect(string $route): self
    {
        $this->statements[] = new RedirectStatementDefinition($route);

        return $this;
    }

    public function render(string $template, array $parameters = []): self
    {
        $this->statements[] = new RenderStatementDefinition($template, $parameters);

        return $this;
    }

    public function save(string $name): self
    {
        $this->statements[] = new SaveStatementDefinition($name);

        return $this;
    }

    public function send(string $notification, string $recipient, array $data): self
    {
        $this->statements[] = new SendStatementDefinition($notification, $recipient, $data);

        return $this;
    }

    public function validate(array $rules): self
    {
        $this->statements[] = new ValidateStatementDefinition($this->name, $rules);

        return $this;
    }
}
