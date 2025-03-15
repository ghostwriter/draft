<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Parser;

use Generator;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;

use function assert;
use function base_path;

final class FindControllers
{
    /**
     * @var array<string,list<string>>
     */
    private array $controllers = [];

    /**
     * @throws FileNotFoundException
     *
     * @return Generator<string,list<Stmt>>
     */
    public function __invoke(DraftVisitor $draft, Filesystem $filesystem, ClassMap $classMap): Generator
    {
        $nodeTraverser = new NodeTraverser();
        $nameResolver = new NameResolver();
        $nodeTraverser->addVisitor($nameResolver);

        $nodeFinder = new NodeFinder();
        foreach ($filesystem->files(self::controllerPath()) as $controller) {
            $path = $controller->getRealPath();
            if (false === $path) {
                throw new FileNotFoundException();
            }

            $nodes = $draft->parse($filesystem->get($path), $controller->getFilename());

            /** @var list<Class_> $classes */
            $classes = $nodeFinder->findInstanceOf($nodes, Class_::class);
            foreach ($classes as $class) {
                $className = NodeExtractor::getName($class);
                $classMap->addClass($className, $path);

                /** @var list<ClassConst> $constants */
                $constants = $nodeFinder->findInstanceOf($class, ClassConst::class);
                foreach ($constants as $constant) {
                    $constant = NodeExtractor::getConsts($constant);
                    /** @var array<string,list<string>> $current */
                    $current =&$this->controllers[$path][$className]['constant'];
                    $current[] = $constant;
                }

                /** @var list<ClassMethod> $methods */
                $methods = $nodeFinder->findInstanceOf($class, ClassMethod::class);
                foreach ($methods as $method) {
                    $methodName = NodeExtractor::getName($method);
                    $methodParams = NodeExtractor::getParams($method);

                    assert(null !== $methodName);

                    $this->controllers[$path][$className]['methods'] = [
                        $methodName => $methodParams,
                    ];
                }

                //                $this->controllers[$path][$className][] = true;
            }

            //            yield $path => $draft->parse($filesystem->get($path), $controller->getFilename());
            //            $this->controllers[$path] = $ast;
            //             [$className]['constant'][sprintf('%s::%s', $className, $constName)]
            //                 = $constValue;
        }

        yield $classMap;
        //        yield from $this->controllers;
    }

    /**
     * @return list<string>
     */
    public function getControllers(): array
    {
        return $this->controllers;
    }

    public static function controllerPath(): string
    {
        return base_path('app/Http/Controllers');
    }
}
