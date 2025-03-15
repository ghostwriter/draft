<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Builder;

use Ghostwriter\Draft\Application\Definition\ModelDefinition;
use Ghostwriter\Draft\Application\Interface\DefinitionInterface;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Override;
use PhpParser\Builder\Class_ as ClassBuilder;
use PhpParser\Builder\Namespace_ as NamespaceBuilder;
use PhpParser\Builder\Property as PropertyBuilder;
use PhpParser\Node;

final class ModelDefinitionBuilder extends AbstractDefinitionBuilder
{
    #[Override]
    public function build(DefinitionInterface $definition): Node
    {
        if (! $definition instanceof ModelDefinition) {
            throw new InvalidArgumentException('Invalid definition type');
        }

        return $this->createNamespace(
            $definition->namespace(),
            fn (NamespaceBuilder $namespace)
                => $namespace->addStmt(
                    $this->createClass(
                        $definition->name(),
                        fn (ClassBuilder $class)
                        => $class->extend(Model::class)->addStmt(
                            $this->createProperty(
                                'table',
                                static fn (PropertyBuilder $property)
                                => $property->makeProtected()->makeStatic()->setDefault($definition->table()),
                            ),
                        ),
                    ),
                ),
        );
    }
}
