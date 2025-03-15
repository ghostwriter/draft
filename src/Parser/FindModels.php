<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser;

use Generator;
use Ghostwriter\Draft\Parser\Visitor\DraftVisitor;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;

use function array_merge;
use function array_reduce;

final class FindModels
{
    /**
     * @var Stmt
     */
    private array $models = [];

    /**
     * @throws FileNotFoundException
     *
     * @return Generator<string,list<Stmt>>
     *
     */
    public function __invoke(DraftVisitor $draft, Filesystem $filesystem, ClassMap $classMap): Generator
    {
        $nodeTraverser = new NodeTraverser();
        $nameResolver = new NameResolver();
        $nodeTraverser->addVisitor($nameResolver);

        $nodeFinder = new NodeFinder();
        foreach ($filesystem->files($draft->modelPath()) as $file) {
            $path = $file->getRealPath();
            if (false === $path) {
                throw new FileNotFoundException();
            }

            /** @return list<Stmt> $models */
            $models = $nodeTraverser->traverse($draft->parse($filesystem->get($path), $file->getFilename()));
            foreach ($models as $model) {
                /** @var list<Class_> $classes */
                $classes = $nodeFinder->findInstanceOf($model, Class_::class);
                foreach ($classes as $class) {
                    $className = NodeExtractor::getName($class);
                    $classMap->addClass($className, $path);

                    $classConst = $this->models[$path][$className]['const'] = array_reduce(
                        $nodeFinder->findInstanceOf($class, ClassConst::class),
                        static fn (array $carry, ClassConst $classConst): array
                            => array_merge($carry, NodeExtractor::getConsts($classConst)),
                        []
                    );
                    $classMap->addClassConsts($classConst, $className, $path);

                    $classMethod = $this->models[$path][$className]['method'] = array_reduce(
                        $nodeFinder->findInstanceOf($class, ClassMethod::class),
                        static fn (array $carry, ClassMethod $classMethod): array
                            => array_merge(
                                $carry,
                                [
                                    NodeExtractor::getName($classMethod) => NodeExtractor::getParams($classMethod),
                                ]
                            ),
                        []
                    );
                    $classMap->addClassMethods($classMethod, $className, $path);

                    $classProperty = $this->models[$path][$className]['property'] = array_reduce(
                        $nodeFinder->findInstanceOf($class, Property::class),
                        static fn (array $carry, Property $property): array
                            => array_merge(
                                $carry,
                                array_reduce(
                                    $property->props,
                                    static fn (array $carry, PropertyProperty $propertyProperty): array
                                        => array_merge(
                                            $carry,
                                            [
                                                NodeExtractor::getName($propertyProperty)
                                                    => NodeExtractor::getDefault($propertyProperty),
                                            ]
                                        ),
                                    []
                                )
                            ),
                        []
                    );
                    $classMap->addClassProperties($classProperty, $className, $path);
                }
            }
        }

        //        yield from $this->models;
        yield $classMap;
    }

    /**
     * @return list<Node>
     */
    public function getModels(): array
    {
        return $this->models;
    }
}
