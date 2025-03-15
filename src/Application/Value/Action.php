<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Value;

use Closure;
use Ghostwriter\Draft\Application\Definition\Statement\DispatchStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\FireStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\QueryStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\RenderStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\SendStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\SessionStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\ValidateStatementDefinition;
use Ghostwriter\Draft\Application\Exception\RuntimeException;
use Ghostwriter\Draft\Application\Interface\Controller\ActionInterface;
use Ghostwriter\Draft\Application\Interface\Definition\StatementDefinitionInterface;
use Ghostwriter\Draft\Application\Interface\DraftInterface;
use Ghostwriter\Draft\Application\Interface\ModelInterface;
use Ghostwriter\Draft\Application\Interface\UserInterface;
use Override;

use function array_key_exists;
use function sprintf;

final class Action implements ActionInterface
{
    /** @var iterable<string,StatementDefinitionInterface> */
    private iterable $statements = [];

    /**
     * @param Closure(ActionInterface):void $factory
     */
    public function __construct(
        private readonly string $name,
        private readonly Closure $factory,
        private readonly DraftInterface $draft
    ) {
        ($this->factory)($this);
    }

    /**
     * @param Closure(DispatchStatementDefinition): DispatchStatementDefinition $fn
     */
    public function dispatchJob(string $jobName, Closure $fn): self
    {
        $this->statement($fn(new DispatchStatementDefinition($jobName)));

        return $this;
    }

    /**
     * @param Closure(FireStatementDefinition): FireStatementDefinition $fn
     */
    public function fire(string $eventName, Closure $fn): self
    {
        $this->statement($fn(new FireStatementDefinition($eventName)));

        return $this;
    }

    #[Override]
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @param Closure(QueryStatementDefinition): QueryStatementDefinition $fn
     */
    public function query(string $query, Closure $fn): self
    {
        $this->statement($fn(new QueryStatementDefinition($query)));

        return $this;
    }

    /**
     * @param Closure(RenderStatementDefinition): RenderStatementDefinition $fn
     */
    public function render(string $view, Closure $fn): self
    {
        $this->statement($fn(new RenderStatementDefinition($view)));

        return $this;
    }

    /**
     * @param Closure(SendStatementDefinition): SendStatementDefinition $fn
     */
    public function send(string $mailable, Closure $fn): self
    {
        $this->statement($fn(new SendStatementDefinition($mailable)));

        return $this;
    }

    /**
     * @param Closure(SessionStatementDefinition): SessionStatementDefinition $fn
     */
    public function session(string $session, Closure $fn): self
    {
        $this->statement($fn(new SessionStatementDefinition($session)));

        return $this;
    }

    #[Override]
    public function statement(StatementDefinitionInterface $statement): ActionInterface
    {
        $statementId = $statement->getId();
        if (array_key_exists($statementId, $this->statements)) {
            throw new RuntimeException(sprintf('Statement "%s" already exists.', $statementId));
        }

        $this->statements[$statementId] = $statement;

        return $this;
    }

    #[Override]
    public function statements(): iterable
    {
        yield from $this->statements;
    }

    public function user(): UserInterface
    {
        return $this->draft->user();
    }

    /**
     * @param Closure(ValidateStatementDefinition): ValidateStatementDefinition $fn
     */
    public function validate(string $invariant, Closure $fn): self
    {
        $this->statement($fn(new ValidateStatementDefinition($invariant)));

        return $this;
    }

    #[Override]
    public function with(string $key, ModelInterface $model): ActionInterface
    {
        return $this;
    }

    #[Override]
    public function withMany(iterable $param): ActionInterface
    {
        return $this;
    }
}
