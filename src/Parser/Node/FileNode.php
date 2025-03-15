<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser\Node;

use Ghostwriter\Draft\Draft;
use InvalidArgumentException;
use PhpParser\Node\Expr\Closure as ClosureExpression;
use PhpParser\Node\Expr\Variable as VariableExpression;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_ as ClassStatement;
use PhpParser\Node\Stmt\Namespace_ as NamespaceStatement;
use PhpParser\Node\Stmt\Nop as NopStatement;
use PhpParser\Node\Stmt\Return_ as ReturnStatement;
use PhpParser\Node\Stmt\Use_ as UseStatement;
use PhpParser\Node\UseItem;

use function is_array;
use function is_string;

final class FileNode extends AbstractNode
{
    private array $namespaces = [];

    private array $uses;
    //    private array $classes = [];
    //    private array $functions = [];

    /**
     * @param array<string,array<string,null|string>> $uses       Array of use statements
     *                                                            {
     *                                                            Use_::TYPE_NORMAL => ['ClassName'=> 'alias'],
     *                                                            Use_::TYPE_FUNCTION => ['functionName'=> null],
     *                                                            Use_::TYPE_CONSTANT => ['ConstName'=> 'alias'],
     *                                                            }
     * @param array<string, mixed>                    $namespaces Array of namespace statements
     * @param array<string, mixed>                    $attributes Array of attributes
     */
    public function __construct(array $uses = [], array $namespaces = [], array $attributes = [])
    {
        $uses[UseStatement::TYPE_NORMAL][Draft::class] = null; // Ensure Draft is always included

        $this->uses = $this->getUses($uses);
        $this->namespaces = $this->getNamespaces($namespaces);
        $this->stmts = [
            new ReturnStatement(new ClosureExpression([
                'attrGroups' => [],
                'static' => true,
                'byRef' => false,
                'params' => [new Param(new VariableExpression('draft'), null, new Name('Draft'))],
                'uses' => [],
                'returnType' => new Identifier('void'),
                'stmts' => [new NopStatement()],
            ])),
        ];
        parent::__construct($attributes);
    }

    private function getNamespaces(array $namespaces): array
    {
        if ([] === $namespaces) {
            return [];
        }

        $nodes = [];

        foreach ($namespaces as $namespace => $classLikes) {
            if (! is_string($namespace) || ! is_array($classLikes)) {
                throw new InvalidArgumentException('Invalid namespace provided');
            }

            $namespaceNode = new NamespaceStatement(new Name($namespace));

            foreach ($classLikes as $classLike) {
                if (! is_string($classLike)) {
                    throw new InvalidArgumentException('Invalid class like provided');
                }

                $namespaceNode->stmts[] = new ClassStatement($classLike);
            }

            $nodes[] = $namespaceNode;
        }

        return $nodes;
    }

    /**
     * @param array<int,array<string,null|string>> $uses Array of use statements
     *
     * @return UseStatement[]
     */
    private function getUses(array $uses): array
    {
        if ([] === $uses) {
            return [];
        }

        $nodes = [];

        foreach ($uses as $type => $use) {
            $type = match ($type) {
                UseStatement::TYPE_NORMAL,
                UseStatement::TYPE_FUNCTION,
                UseStatement::TYPE_CONSTANT => $type,
                default => throw new InvalidArgumentException('Invalid use type provided'),
            };

            if (! is_array($use)) {
                throw new InvalidArgumentException('Invalid use statement provided');
            }

            foreach ($use as $name => $alias) {
                if (! is_string($name) || (! is_string($alias) && null !== $alias)) {
                    throw new InvalidArgumentException('Invalid use statement provided');
                }

                $nodes[] = new UseStatement([new UseItem(new Name($name), $alias, $type)]);
            }
        }

        return $nodes;
    }
}
