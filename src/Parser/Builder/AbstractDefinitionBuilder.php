<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Builder;

use Ghostwriter\Draft\Application\Interface\DefinitionInterface;
use Override;
use PhpParser\Builder\Namespace_ as NamespaceBuilder;
use PhpParser\BuilderFactory;
use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Stmt\Class_ as ClassStatement;
use PhpParser\Node\Stmt\Declare_ as DeclareStatement;
use PhpParser\Node\Stmt\DeclareDeclare;
use PhpParser\Node\Stmt\Namespace_ as NamespaceStatement;
use PhpParser\Node\Stmt\Property as PropertyStatement;

abstract class AbstractDefinitionBuilder implements DefinitionBuilderInterface
{
    public function __construct(
        protected BuilderFactory $factory = new BuilderFactory(),
    ) {}

    protected function createClass(string $name, callable $callback): ClassStatement
    {
        $class = $this->factory->class($name);

        $callback($class);

        return $class->getNode();
    }

    protected function createDeclareStatement(): DeclareStatement
    {
        static $declareStatement = null;

        return $declareStatement ??= new DeclareStatement([
            new DeclareDeclare(new Identifier('strict_types'), new Int_(1)),
        ]);
    }

    protected function createFile(string $name, callable $callback): ClassStatement
    {
        //        'Post'

        $this->createNamespace(
            'App\\Models',
            fn (NamespaceBuilder $namespace) => $namespace->addStmt($this->createClass($name, $callback))
        );
        $class = $this->factory->class($name);

        $callback($class);

        return $class->getNode();
    }

    protected function createNamespace(string $namespace, callable $callback): NamespaceStatement
    {
        $namespace = $this->factory->namespace($namespace);

        $namespace->addStmt($this->createDeclareStatement());

        $callback($namespace);

        return $namespace->getNode();
    }

    protected function createProperty(string $name, callable $callback): PropertyStatement
    {
        $property = $this->factory->property($name);

        $callback($property);

        return $property->getNode();
    }

    #[Override]
    abstract public function build(DefinitionInterface $definition): Node;
}
