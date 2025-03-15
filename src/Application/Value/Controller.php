<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Application\Value;

use Closure;
use Ghostwriter\Draft\Application\Exception\RuntimeException;
use Ghostwriter\Draft\Application\Interface\Controller\ActionInterface;
use Ghostwriter\Draft\Application\Interface\ControllerInterface;
use Ghostwriter\Draft\Application\Interface\ModelInterface;
use Ghostwriter\Draft\Application\Interface\UserInterface;
use Illuminate\Routing\Controller as IlluminateController;
use Illuminate\Routing\Controllers\Middleware;
use Override;

use function array_key_exists;
use function sprintf;

final class Controller extends IlluminateController implements ControllerInterface
{
    public $draft;

    /** @var array<string,ActionInterface> */
    private array $actions = [];

    private bool $apiResource = false;

    private bool $apiResourceCollection = false;

    private bool $invokable = false;

    /** @var array<string,Middleware> */
    private array $middlewares = [];

    /** @var array<string,Model> */
    private array $models = [];

    private bool $resource = false;

    private ?UserInterface $user = null;

    public function __construct(
        private ModelInterface $model
    ) {
        //        'index' => 'viewAny',
        //            'create' => 'create',
        //            'store' => 'create',

        //            'show' => 'view',
        //            'edit' => 'update',
        //            'update' => 'update',
        //            'destroy' => 'delete',
        //        return $controller;
    }

    /**
     * @param Closure(ActionInterface):void $factory
     */
    #[Override]
    public function action(string $name, ?Closure $factory = null): void
    {
        if (array_key_exists($name, $this->actions)) {
            throw new RuntimeException(sprintf('Action "%s" already exists.', $name));
        }

        $this->actions[$name] = new Action($name, $factory);
    }

    #[Override]
    public function actions(): iterable
    {
        yield from $this->actions;
    }

    public function apiResource(): void
    {
        $this->apiResource = true;
    }

    public function apiResourceCollection(): void
    {
        $this->apiResourceCollection = true;
    }

    #[Override]
    public function getModel(): Model
    {
        return $this->model;
    }

    public function invokable(): void
    {
        $this->invokable = true;
    }

    public function isApiResource(): bool
    {
        return $this->apiResource;
    }

    public function isApiResourceCollection(): bool
    {
        return $this->apiResourceCollection;
    }

    public function isInvokable(): bool
    {
        return $this->invokable;
    }

    public function isResource(): bool
    {
        return $this->resource;
    }

    #[Override]
    public function model(string $name): ModelInterface
    {
        return $this->draft->model($name);
    }

    #[Override]
    public function models(): iterable
    {
        yield from $this->models;
    }

    public function resource(): void
    {
        $this->resource = true;
    }

    #[Override]
    public function user(): UserInterface
    {
        $user = $this->user;
        if ($user instanceof UserInterface) {
            return $user;
        }

        throw new RuntimeException('No user was provided.');
    }

    //    public function route(Route $route): void
    //    {
    //        $this->router->controller($this::class);
    //        //        Route::resource('photos', PhotoController::class);
    //        //        Route::resources([
    //        //            'photos' => PhotoController::class,
    //        //            'posts' => PostController::class,
    //        //        ]);
    //    }
    #[Override]
    public function withUser(UserInterface $user): self
    {
        $currentUser = $this->user;
        if ($currentUser instanceof UserInterface && $user === $currentUser) {
            return $this;
        }

        $copy = clone $this;
        $copy->user = $user;

        return $copy;
    }
}
