# Draft

[![Automation](https://github.com/ghostwriter/draft/actions/workflows/automation.yml/badge.svg)](https://github.com/ghostwriter/draft/actions/workflows/automation.yml)
[![PHP Version](https://badgen.net/packagist/php/ghostwriter/draft?color=777BB4)](https://www.php.net/supported-versions)
[![Packagist Downloads](https://badgen.net/packagist/dt/ghostwriter/draft?color=F28D1A)](https://packagist.org/packages/ghostwriter/draft)
[![PayPal](https://img.shields.io/badge/paypal-@codepoet-0079C1?logo=data%3Aimage%2Fsvg%2Bxml%3Bbase64%2CPHN2ZyB2aWV3Qm94PSIwIDAgMjQgMjQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI%2BPHBhdGggZD0iTTE5LjcxNSA2LjEzM2MuMjQ5LTEuODY2IDAtMy4xMS0uOTk5LTQuMjY2QzE3LjYzNC42MjIgMTUuNzIxIDAgMTMuMzA3IDBINi4yMzVjLS40MTggMC0uOTE2LjQ0NC0xIC44ODlMMi4zMjMgMjAuNjIyYzAgLjM1Ni4yNS44LjY2NS44aDQuMzI4bC0uMjUgMS45NTZjLS4wODQuMzU1LjE2Ni42MjIuNDk4LjYyMmgzLjY2M2MuNDE3IDAgLjgzMi0uMjY3LjkxNS0uNzExdi0uMjY3bC43NDktNC42MjJ2LS4xNzhjLjA4My0uNDQ0LjUtLjguOTE1LS44aC41YzMuNTc4IDAgNi4zMjUtMS41MSA3LjE1Ni01Ljk1NS40MTgtMS44NjcuMjUyLTMuMzc4LS43NDctNC40NDUtLjI1LS4zNTUtLjY2Ni0uNjIyLTEtLjg4OSIgZmlsbD0iIzAwOWNkZSIvPjxwYXRoIGQ9Ik0xOS43MTUgNi4xMzNjLjI0OS0xLjg2NiAwLTMuMTEtLjk5OS00LjI2NkMxNy42MzQuNjIyIDE1LjcyMSAwIDEzLjMwNyAwSDYuMjM1Yy0uNDE4IDAtLjkxNi40NDQtMSAuODg5TDIuMzIzIDIwLjYyMmMwIC4zNTYuMjUuOC42NjUuOGg0LjMyOGwxLjE2NC03LjM3OC0uMDgzLjI2N2MuMDg0LS41MzMuNS0uODg5Ljk5OC0uODg5aDIuMDhjNC4wNzkgMCA3LjI0MS0xLjc3OCA4LjI0LTYuNzU1LS4wODMtLjI2NyAwLS4zNTYgMC0uNTM0IiBmaWxsPSIjMDEyMTY5Ii8%2BPHBhdGggZD0iTTkuNTYzIDYuMTMzYy4wODItLjI2Ni4yNS0uNTMzLjQ5OC0uNzEuMTY2IDAgLjI1LS4wOS40MTYtLjA5aDUuNDk0Yy42NjYgMCAxLjMzLjA5IDEuODMuMTc4LjE2NiAwIC4zMzMgMCAuNDk4LjA4OS4xNjguMDg5LjMzNC4wODkuNDE4LjE3OGguMjVjLjI0OC4wODkuNDk3LjI2Ni43NDguMzU1LjI0OC0xLjg2NiAwLTMuMTEtLjk5OS00LjM1NUMxNy43MTcuNTMzIDE1LjgwNCAwIDEzLjM5IDBINi4yMzVjLS40MTggMC0uOTE2LjM1Ni0xIC44ODlMMi4zMjMgMjAuNjIyYzAgLjM1Ni4yNS44LjY2NS44aDQuMzI4bDEuMTY0LTcuMzc4IDEuMDg0LTcuOTF6IiBmaWxsPSIjMDAzMDg3Ii8%2BPC9zdmc%2B)](https://paypal.me/codepoet)
[![Sponsors via GitHub](https://img.shields.io/github/sponsors/ghostwriter?label=Sponsor+@ghostwriter/draft&logo=GitHub+Sponsors)](https://github.com/sponsors/ghostwriter)

A code generation tool for Laravel developers.

> **Warning**
>
> This project is not finished yet, work in progress.

This tool will write your draft ideas in to functional laravel code.

- Automatically load existing files and database to generate missing files. (e.g. Missing Tests)
- Fully written Tests for each file generated
- Jetstream with Livewire by v1.0

### Automated Features
- Models
- Factories
- Seeders
- Migrations
- Routes
- Unit Tests (90+ code coverage by v1.0)
- Feature Tests (90+ code coverage by v1.0)
- Controllers
  - FormRequest
  - Middleware
  - Gates & Policies
  - ResourceCollections
  - Statements
    - Mails
    - Notifications
    - Jobs
    - Events
    - RenderView


## Installation

You can install the package via composer:

``` bash
composer require ghostwriter/draft --dev
```

## Usage

### Commands

create a draft file.

``` bash
php artisan draft:init
```

build the laravel application using the draft.php file

``` bash
php artisan draft:build
```

generate missing and or incomplete files along with writing feature/unit tests.

``` bash
php artisan draft:generate
```

trace an existing application to build a "draft.php" file.

``` bash
php artisan draft:trace
```

### Draft file: `draft.php`

```php
<?php

// Note: Some if not most of the syntax below may change, I'm cleaning it up and building an API.

<?php

declare(strict_types=1);

use Ghostwriter\Draft\Application\Definition\Action\LivewireActionDefinition;
use Ghostwriter\Draft\Application\Definition\LivewireDefinition;
use Ghostwriter\Draft\Application\Definition\Router\RouteGroupDefinition;
use Ghostwriter\Draft\Application\Definition\RouterDefinition;
use Ghostwriter\Draft\Draft;
use Ghostwriter\Draft\Application\Definition\Action\ControllerActionDefinition;
use Ghostwriter\Draft\Application\Definition\Action\InertiaActionDefinition;
use Ghostwriter\Draft\Application\Definition\ControllerDefinition;
use Ghostwriter\Draft\Application\Definition\InertiaDefinition;
use Ghostwriter\Draft\Application\Definition\MigrationDefinition;
use Ghostwriter\Draft\Application\Definition\ModelDefinition;
use Ghostwriter\Draft\Application\Definition\Test\TestCaseDefinition;
use Ghostwriter\Draft\Application\Definition\TestDefinition;

$resourceActions = ['index', 'create', 'edit', 'show', 'store', 'update', 'destroy'];

return static function (Draft $draft): void {
    $draft->model('Comment', function (ModelDefinition $definition): void {
        $modelDefinition->relationships('author')->hasOne('User');
        $modelDefinition->relationships('post')->hasOne('Post');
    });
    $draft->model('Post', function (ModelDefinition $definition): void {
        $modelDefinition->relationships('author')->hasOne('User');
        $modelDefinition->relationships('comments')->hasMany('Comment');
        $modelDefinition->relationships('tags')->belongsToMany('Tag');
//       $definition->casts();
//       $definition->controller();
//       $definition->fillable();
//       $definition->hidden();
//       $definition->migration();
//       $definition->name();
//       $definition->relationships();
//       $definition->resourceController();
//       $definition->table();
//       $definition->test();

    });
    $draft->migration('Post', function (MigrationDefinition $migrationDefinition): void {
        $migrationDefinition->id();
        $migrationDefinition->foreignId('user_id');
        $migrationDefinition->string('title', 150);
        $migrationDefinition->text('content');
        $migrationDefinition->timestamp('published_at')->nullable();
        $migrationDefinition->timestamps();
        $migrationDefinition->softDeletes();
    });
    $draft->migration('Comment', function (MigrationDefinition $migrationDefinition): void {
        $migrationDefinition->id();
        $migrationDefinition->foreignId('post_id');
        $migrationDefinition->foreignId('user_id');
        $migrationDefinition->text('content');
        $migrationDefinition->timestamp('published_at')->nullable();
        $migrationDefinition->timestamps();
        $migrationDefinition->softDeletes();
    });
    $draft->controller('PostController', function (ControllerDefinition $controllerDefinition): void {
        $controllerDefinition->resource('posts');

        $controllerDefinition->action('posts.hide', function (ControllerActionDefinition $controllerActionDefinition): void {
            $controllerActionDefinition->formRequest('HidePostRequest', [
                'reason' => 'required',
            ]);

            $controllerActionDefinition->validate([
                'reason' => 'required',
            ]);

            $controllerActionDefinition->save('post');
            $controllerActionDefinition->send('PostHiddenNotification', 'post.author', ['post']);
            $controllerActionDefinition->dispatch('HidePost', ['post']);
            $controllerActionDefinition->fire('HidePost', ['post']);
            $controllerActionDefinition->flash('success', 'post.title was successfully hidden.');
            $controllerActionDefinition->render('posts.show', ['post']);
        });
    });

    $draft->router( function (RouterDefinition $routerDefinition): void {
        $routerDefinition->view('/', 'welcome');

        $routerDefinition->group(function (RouteGroupDefinition $routeGroupDefinition) {
            $middleware = ['web', 'auth'];
            
            $name = 'Comment';
            $singular = Str::singular(Str::snake($name)); // comment
            $plural = Str::plural($singular); // comments
            $model = Str::studly($singular); // Comment
            
            $routeGroupDefinition->middleware($middleware)
                                 ->prefix($plural)
                                 ->name($plural . '.');
            
            $namespace = '\\App\\Livewire\\' . $model .'\\';
            
            $create = $namespace . 'Create' . $model;
            $delete = $namespace . 'Delete' . $model;
            $edit = $namespace . 'Edit' . $model;
            $index = $namespace . 'Index' . $model;
            $show = $namespace . 'Show' . $model;
            $store = $namespace . 'Store' . $model;
            $update = $namespace . 'Update' . $model;
            
            $routeGroupDefinition->delete('/{'.$singular.'}', $delete)->name('destroy');
            $routeGroupDefinition->get('/', $index)->name('index');
            $routeGroupDefinition->get('/create', $create)->name('create');
            $routeGroupDefinition->get('/{'.$singular.'}', $show)->name('show');
            $routeGroupDefinition->get('/{'.$singular.'}/edit', $edit)->name('edit');
            $routeGroupDefinition->patch('/{'.$singular.'}', $update)->name('update');
            $routeGroupDefinition->post('/', $store)->name('store');
            $routeGroupDefinition->put('/{'.$singular.'}', $update)->name('update');
        });
        
        $routerDefinition->get('contact', 'ContactController')->name('contact');

        $routerDefinition->name('posts.hide');
        $routerDefinition->resource('posts');

    });

    $draft->test('Post', function (TestDefinition $testDefinition): void {
        $testDefinition->test(
            'user can see all of their own posts',
            function (TestCaseDefinition $testCaseDefinition): void {
                // $testCaseDefinition->name();
            });
    });
    
    $draft->livewire('auth.login', function (LivewireDefinition $livewireDefinition) :void {
        $livewireDefinition->mount('user');
        $livewireDefinition->action('login', function (LivewireActionDefinition $livewireActionDefinition));
    });

    $draft->inertia('auth.logout', function (InertiaDefinition $inertiaDefinition) :void {
        $inertiaDefinition->action('logout', function (InertiaActionDefinition $inertiaActionDefinition) : void {

        });
    });
};
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG.md](./CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email `nathanael.esayeas@protonmail.com` instead of using the issue tracker.

## Support

[[`Become a GitHub Sponsor`](https://github.com/sponsors/ghostwriter)]

## Credits

- [Nathanael Esayeas](https://github.com/ghostwriter)
- [All Contributors](https://github.com/ghostwriter/draft/contributors)

## License

The BSD-3-Clause. Please see [License File](./LICENSE) for more information.
