<?php

declare(strict_types=1);

require dirname(__DIR__,2) . '/vendor/autoload.php';

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Models\User;
use Carbon\Carbon;
use Ghostwriter\Draft\Application\Definition\ControllerDefinition;
use Ghostwriter\Draft\Application\Definition\MigrationDefinition;
use Ghostwriter\Draft\Application\Definition\ModelDefinition;
use Ghostwriter\Draft\Application\Definition\RouteDefinition;
use Ghostwriter\Draft\Application\Definition\Router\RouteGroupDefinition;
use Ghostwriter\Draft\Application\Definition\RouterDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\DispatchStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\FireStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\QueryStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\RenderStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\SessionStatementDefinition;
use Ghostwriter\Draft\Application\Definition\Statement\ValidateStatementDefinition;
use Ghostwriter\Draft\Application\Value\Action;
use Ghostwriter\Draft\Application\Value\Controller;
use Ghostwriter\Draft\Application\Value\Migration;
use Ghostwriter\Draft\Application\Value\Model;
use Ghostwriter\Draft\Application\Value\Router;
use Ghostwriter\Draft\Draft;
use Illuminate\Support\Str;

return static function (Draft $draft): void {
    $draft->router(static function (RouterDefinition $routerDefinition): void {
        $middleware = [];

        $name = 'article';

        $middleware =  array_unique(array_merge($middleware, ['web', 'verified']));
        $singular = Str::singular(Str::snake($name)); // article
        $plural = Str::plural($singular); // articles
        $name = Str::studly($singular); // Article


        $routerDefinition->middleware()->group(function (RouteGroupDefinition $routerGroupDefinition) use ($singular, $plural, $name): void {
            $routerGroupDefinition->prefix($plural)->name($plural, '.');

            $namespace = '\\App\\Livewire\\' . $name .'\\';
            $create = $namespace . 'Create' . $name;
            $delete = $namespace . 'Delete' . $name;
            $edit = $namespace . 'Edit' . $name;
            $index = $namespace . 'Index' . $name;
            $show = $namespace . 'Show' . $name;
            $store = $namespace . 'Store' . $name;
            $update = $namespace . 'Update' . $name;

            $routerGroupDefinition->delete('/{'.$singular.'}', $delete)->name('destroy');
            $routerGroupDefinition->get('/', $index)->name('index');
            $routerGroupDefinition->get('/create', $create)->name('create');
            $routerGroupDefinition->get('/{'.$singular.'}', $show)->name('show');
            $routerGroupDefinition->get('/{'.$singular.'}/edit', $edit)->name('edit');
            $routerGroupDefinition->patch('/{'.$singular.'}', $update)->name('update');
            $routerGroupDefinition->post('/', $store)->name('store');
            $routerGroupDefinition->put('/{'.$singular.'}', $update)->name('update');

            $routerGroupDefinition->resource('update');
            $routerGroupDefinition->any('update', 'update');
            $routerGroupDefinition->fallback('update', 'update');
//              Route::fallback()
//                Route::view('nds', 'sdsd');
        });
        $routerDefinition->view('/', 'welcome');
        // $action
        // :
        // :
        //Route any(string $uri, array|Closure|null|string $action = null)
        //Routedelete(string $uri, array|Closure|null|string $action = null)
        //Routeget(string $uri, array|Closure|null|string $action = null)
        //Routeoptions(string $uri, array|Closure|null|string $action = null)
        //Routepatch(string $uri, array|Closure|null|string $action = null)
        //Routepost(string $uri, array|Closure|null|string $action = null)
        //Routeput(string $uri, array|Closure|null|string $action = null)

        //RouteRegistrar as(string $value)
        //RouteRegistrar can(string|UnitEnum $ability, array|string $models = [])
        //RouteRegistrar controller(string $controller)
        //RouteRegistrar domain(BackedEnum|string $value)
        //RouteRegistrar middleware(array|null|string $middleware)
        //RouteRegistrar missing(Closure $missing)
        //RouteRegistrar name(BackedEnum|string $value)
        //RouteRegistrar namespace(null|string $value)
        //RouteRegistrar prefix(string $prefix)
        //RouteRegistrar scopeBindings()
        //RouteRegistrar where(array $where)
        //RouteRegistrar withoutMiddleware(array|string $middleware)
        //RouteRegistrar withoutScopedBindings()

        \Illuminate\Routing\Router::get('/','Ghostwriter\Draft\Application\Value\Controller@action');

        $routerDefinition->get('/', 'HomeController')->name('home');
        $routerDefinition->get('/contact','ContactComponent')->name('contact');
        $routerDefinition->post('/contact','ContactComponent')->name('contact');
        $routerDefinition->resource('posts')->name('contact');

        $routerDefinition->middleware('auth', 'verified')
                         ->group(function (RouteGroupDefinition $routerGroupDefinition) {
                             $routerGroupDefinition->view('dashboard', 'dashboard')->name('dashboard');
                         });

        $routerDefinition->middleware('auth')
                         ->group(function (RouteGroupDefinition $routerGroupDefinition) {
                             $routerGroupDefinition->redirect('settings', 'settings/profile');
                             $routerGroupDefinition->get('settings/profile', Profile::class)->name('settings.profile');
                             $routerGroupDefinition->get('settings/password', Password::class)->name('settings.password');
                             $routerGroupDefinition->get('settings/appearance', Appearance::class)->name('settings.appearance');
                         });
    });
    $draft->router(static function(RouterDefinition $routerDefinition){
        $routerDefinition->middleware('web')->group(function (){

        });});

    $user = $draft->model('User', static function (ModelDefinition $modelDefinition): void {
        // automatically generates the migrations

        $modelDefinition->fillable(['name', 'email', 'password', 'api_key', 'ssn', 'published_at']);
        $modelDefinition->casts(['published_at'=> Carbon::class]);
        $modelDefinition->hidden(['password', 'api_key', 'ssn']);
    });
    $draft->controller('User', static function (ControllerDefinition $controller, Router $router): void {
//            dump([$model, $controller, $controller->middlewares()]);
//            $router->apiResource()
    });
    $draft->migration('User', static function (Model $model, MigrationDefinition $table): void {
        $table->id();
        $table->string('name');
        $table->string('email');
        $table->string('password');
        $table->string('ssn');
        $table->timestamps();
    });

    $draft->model('Flight', static function (ModelDefinition $flight): void {
        $flight->casts([
            'published_at'=> Carbon::class,
        ]);
        $flight->fillable(['name', 'airline', 'published_at']);
    });

    $draft->controller(
        'Flight',
        static function (Model $flight, Controller $controller, Router $router) use ($user): Controller {
            $controller->model($user->name());
            $controller->action(
                'index',
                static function (Action $action) use ($flight, $user): Action {
                    $action->dispatchJob(
                        'BookFlight',
                        static fn (DispatchStatementDefinition $dispatchStatement): DispatchStatementDefinition => $dispatchStatement
                    )->withMany([
                        'users' => $user,
                        'flights' => $flight,
                    ]);
                    $action->fire(
                        'FlightBooked',
                        static fn (FireStatementDefinition $fireStatement): FireStatementDefinition => $fireStatement
                    );
                    // query: where:title where:content order:published_at limit:5
                    $action->query(
                        'FlightBookedMail',
                        static fn (QueryStatementDefinition $queryStatement): QueryStatementDefinition => $queryStatement
                    )->withMany([
                        'query' => 'where',
                        'where' => ['title', 'content'],
                        'order' => 'published_at',
                        'limit' => 5,
                    ]);
                    $action->render(
                        'flight.index',
                        static fn (RenderStatementDefinition $renderStatement): RenderStatementDefinition => $renderStatement
                    )
                           ->with('flights', $flight);
                    $action->session(
                        'FlashMessage',
                        static fn (SessionStatementDefinition $sessionStatement): SessionStatementDefinition => $sessionStatement
                    );
                    $action->validate(
                        'Flight',
                        static fn (ValidateStatementDefinition $validateStatement): ValidateStatementDefinition => $validateStatement
                    );

                    $action->statement([
                        'query' => 'all',
                        'render' => 'post.index with:posts'
                    ]);

                    return $action;
                }
            );
            return $controller;
        }
    );

    $draft->migration(
        '$flight',
        static function (Model $flight, Migration $migration) use ($user): Migration {
            $migration->id();
            $migration->string('name');
            $migration->string('airline');
            $migration->foreignIdFor($user);
            $migration->timestamps();

            return $migration;
        }
    );
    // ===
//    $draft->factory($user, $flight);
//    $draft->seeder($user, $flight);
    // ===
    dd([
        //    array_map(static function (Blueprint $migration): array {
        //        return array_map(static function (ColumnDefinition $column): mixed {
        //            return $column->getAttributes()['name'];
        //        }, $migration->getColumns());
        //    }, $draft->migrations()),
        $draft->controllers(),
        $draft->factories(),
        $draft->migrations(),
        $draft->models(),
        $draft->seeders(),
    ]);

    //    $draft->controller($controllers);
    //    $draft->seeders($seeders);
    //
    //    $post = $draft->model('Post', [
    //        'title' => 'string:400',
    //        'content' => 'longtext',
    //        'published_at' => 'nullable timestamp',
    //        'author_id' => 'id:user'
    //    ]);
    //
    //    $news = $draft->model('News', [
    //        'title' => 'string:400',
    //        'content' => 'longtext',
    //        'published_at' => 'nullable timestamp',
    //        'author_id' => $user
    //    ]);
    //
    //    $draft->controller('controllers', [
    //        'Post' => [
    //            'index' => [
    //                'query' => 'all',
    //                'render' => 'post.index with:posts'
    //            ],
    //            'store' => [
    //                'validate' => 'title, content, author_id',
    //                'save' => 'post',
    //                'send' => 'ReviewPost to:post.author.email with:post',
    //                'dispatch' => 'SyncMedia with:post',
    //                'fire' => 'NewPost with:post',
    //                'flash' => 'post.title',
    //                'redirect' => 'post.index'
    //            ]
    //        ]
    //    ]);
    //
    //        // Collections of Existing (maybe?)
    //        $models = $draft->models();
    //        $controllers = $draft->controllers();
    //        $seeders = $draft->seeders();
    //
    //        $models->merge([
    //
    //            $postModel = Model::generate('post', function (Model $model) {
    //                // Migration Specific
    //                $model->id();
    //                $model->string('title', 400);
    //                $model->longText('content');
    //                $model->timestamp('published_at')->nullable();
    //                $model->unsignedBigInteger('author_id')->casts('integer');
    //                $model->timestamps();
    //
    //                // Blueprint Specific
    //                // $model->unguard();
    //                $model->casts(['published_at' => \Carbon\Carbon::class]);
    //                $model->fillable(['title', 'content', 'published_at', 'author_id']);
    //            })
    //
    //        ]);
    //
    //        $controllers->merge([
    //            // $postResourceController = Controller::resource($postModel),
    //            // $postApiResourceController = Controller::apiResource($postModel),
    //            // $postApiResourceCollectionController = Controller::apiResourceCollection($postModel),
    //
    //            $postController = Controller::generate('post', function (Controller $controller) {
    //                // $invokeable = $controller->isInvokeable(); // bool - Single Action Controllers?
    //                // $model = $controller->model();
    //                // $controller->resource($model)
    //                // $controller->apiResource($model)
    //                // $controller->apiResourceCollection($model)
    //                // $controller->action($name, $callback(Action $action))
    //                $controller->action('index', function (Action $action) {
    //                    $controller = $action->controller();
    //                    $model = $controller->model();
    //
    //                    $action->statement(QueryStatement::class, 'all');
    //                    $action->statement(RenderStatement::class, 'post.index with:posts');
    //                    $action->statement(RenderStatement::class)->with('posts', $model);
    //                    $action->statement(RenderStatement::class)->with(['posts' => $model]);
    //                    $action->statement([
    //                        'query' => 'all',
    //                        'render' => 'post.index with:posts'
    //                    ]);
    //                });
    //
    //                Action::generate('index', function (Action $action) {
    //
    //                    Statement::generate([
    //                        'query' => 'all',
    //                        'render' => 'post.index with:posts'
    //                    ]),
    //
    //      }),
    //
    //      Action::generate('store', [
    //
    //          Statement::generate([
    //              'validate' => 'title, content, author_id',
    //              'save' => 'post',
    //              'send' => 'ReviewPost to:post.author.email with:post',
    //              'dispatch' => 'SyncMedia with:post',
    //              'fire' => 'NewPost with:post',
    //              'flash' => 'post.title',
    //              'redirect' => 'post.index',
    //          ]),
    //
    //      ])
    //
    //    }),
    //
    //        ]);
    //
    //        $draft->models($models);
    //};
    //models:
    //Post:
    //title: string:400
    //    content: longtext
    //    published_at: nullable timestamp
    //    author_id: id:user
    //
    //controllers:
    //  Post:
    //    index:
    //      query: all
    //      render: post.index with:posts
    //
    //    store:
    //      validate: title, content, author_id
    //      save: post
    //      send: ReviewPost to:post.author.email with:post
    //      dispatch: SyncMedia with:post
    //      fire: NewPost with:post
    //      flash: post.title
    //      redirect: post.index
};
