<?php

declare(strict_types=1);

use Ghostwriter\Draft\Application\Definition\Action\ControllerActionDefinition;
use Ghostwriter\Draft\Application\Definition\Action\InertiaActionDefinition;
use Ghostwriter\Draft\Application\Definition\Action\LivewireActionDefinition;
use Ghostwriter\Draft\Application\Definition\ControllerDefinition;
use Ghostwriter\Draft\Application\Definition\InertiaDefinition;
use Ghostwriter\Draft\Application\Definition\LivewireDefinition;
use Ghostwriter\Draft\Application\Definition\MigrationDefinition;
use Ghostwriter\Draft\Application\Definition\ModelDefinition;
use Ghostwriter\Draft\Application\Definition\Test\TestCaseDefinition;
use Ghostwriter\Draft\Application\Definition\TestDefinition;
use Ghostwriter\Draft\Draft;

$paths = [
    //    'Cast' => 'E.g. Json',
    //    'Channel' => 'E.g. OrderChannel',
    //    'Console command' => 'E.g. SendEmails',
    //    'Component' => 'E.g. Alert',
    //    'Controller' => 'E.g. UserController',
    //    'Event' => 'E.g. PodcastProcessed',
    //    'Exception' => 'E.g. InvalidOrderException',
    //    'Factory' => 'E.g. PostFactory',
    //    'Job' => 'E.g. ProcessPodcast',
    //    'Listener' => 'E.g. SendPodcastNotification',
    //    'Mailable' => 'E.g. OrderShipped',
    //    'Middleware' => 'E.g. EnsureTokenIsValid',
    //    'Model' => 'E.g. Flight',
    //    'Notification' => 'E.g. InvoicePaid',
    //    'Observer' => 'E.g. UserObserver',
    //    'Policy' => 'E.g. PostPolicy',
    //    'Provider' => 'E.g. ElasticServiceProvider',
    //    'Request' => 'E.g. StorePodcastRequest',
    //    'Resource' => 'E.g. UserResource',
    //    'Rule' => 'E.g. Uppercase',
    //    'Scope' => 'E.g. TrendingScope',
    //    'Seeder' => 'E.g. UserSeeder',
    //    'Test' => 'E.g. UserTest',
    'Component',
    'Controller',
    'Database',
    'Event',
    'Factory',
    'FormRequest',
    'Inertia',
    'Job',
    'Livewire',
    'Mail',
    'Migration',
    'Model',
    'Notification',
    'Policy',
    'Route',
    'Rule',
    'Seeder',
    'Test',
    'View',
];
$resourceActions = ['index', 'create', 'edit', 'show', 'store', 'update', 'destroy'];

return static function (Draft $draft): void {
    $draft->migration('Car', static function (MigrationDefinition $migrationDefinition): void {
        $migrationDefinition->id();
        $migrationDefinition->string('name');
        $migrationDefinition->timestamps();
    });

    $draft->test('CarTest', static function (TestDefinition $testDefinition): void {
        $testDefinition->test('index', static function (TestCaseDefinition $testCaseDefinition): void {
            $testCaseDefinition->get('car.index');
            $testCaseDefinition->assertStatus(200);
        });
        $testDefinition->test('store', static function (TestCaseDefinition $testCaseDefinition): void {
            $testCaseDefinition->post('car.store', [
                'name' => 'Car Name',
            ]);
            $testCaseDefinition->assertStatus(302);
            $testCaseDefinition->assertRedirect('car.index');
            $testCaseDefinition->assertSessionHas('success', 'Car Name');
            $testCaseDefinition->assertDatabaseHas('cars', [
                'name' => 'Car Name',
            ]);
        });
        $testDefinition->test('update', static function (TestCaseDefinition $testCaseDefinition): void {
            $testCaseDefinition->put('car.update', [
                'name' => 'Car Name Updated',
            ]);
            $testCaseDefinition->assertStatus(302);
            $testCaseDefinition->assertRedirect('car.index');
            $testCaseDefinition->assertSessionHas('success', 'Car Name Updated');
            $testCaseDefinition->assertDatabaseHas('cars', [
                'name' => 'Car Name Updated',
            ]);
        });
    });

    $draft->model('Car', static function (ModelDefinition $modelDefinition): void {

        //        $modelDefinition->seeder();
        //        $modelDefinition->factory([
        //            'name' => 'name', // $this->faker->name,
        //            'created_at' => 'now',
        //        ]);
        $modelDefinition->resourceController();
    });

    //    return;
    $draft->model('User', static function (ModelDefinition $modelDefinition): void {
        $modelDefinition->migration(static function (MigrationDefinition $migrationDefinition): void {
            $migrationDefinition->id();
            $migrationDefinition->uuid();
            $migrationDefinition->smallIncrements('small_id');
            $migrationDefinition->ulid();
            $migrationDefinition->string('name', 200);
            $migrationDefinition->string('email', 200)->unique();
            $migrationDefinition->string('password', 200);
            $migrationDefinition->rememberToken();
            $migrationDefinition->timestamp('email_verified_at')->nullable();
            $migrationDefinition->binary('binary');
            $migrationDefinition->string('title');
            $migrationDefinition->text('content')->charset('binary'); // BLOB
            $migrationDefinition->tinyText('notes');
            $migrationDefinition->foreignIdFor('User');
            $migrationDefinition->enum('status', ['active', 'inactive']);

            $migrationDefinition->unsignedInteger('unsigned_integer');
            $migrationDefinition->unsignedBigInteger('logos');
            $migrationDefinition->bigInteger('votes');

            // boolean
            $migrationDefinition->boolean('boolean_column');
            //
            // String & Text Types
            //
            // char
            $migrationDefinition->char('char_column');
            // longText
            $migrationDefinition->longText('long_text_column');
            // mediumText
            $migrationDefinition->mediumText('medium_text_column');
            // string
            $migrationDefinition->string('string_column');
            // text
            $migrationDefinition->text('text_column');
            // tinyText
            $migrationDefinition->tinyText('tiny_text_column');
            //
            // Numeric Types
            //
            // bigIncrements
            $migrationDefinition->bigIncrements('big_increments_column');
            // bigInteger
            $migrationDefinition->bigInteger('big_integer_column');
            // decimal
            $migrationDefinition->decimal('decimal_column');
            // double
            $migrationDefinition->double('double_column');
            // float
            $migrationDefinition->float('float_column');
            // id
            $migrationDefinition->id('id_column');
            // increments
            $migrationDefinition->increments('increments_column');
            // integer
            $migrationDefinition->integer('integer_column');
            // mediumIncrements
            $migrationDefinition->mediumIncrements('medium_increments_column');
            // mediumInteger
            $migrationDefinition->mediumInteger('medium_integer_column');
            // smallIncrements
            $migrationDefinition->smallIncrements('small_increments_column');
            // smallInteger
            $migrationDefinition->smallInteger('small_integer_column');
            // tinyIncrements
            $migrationDefinition->tinyIncrements('tiny_increments_column');
            // tinyInteger
            $migrationDefinition->tinyInteger('tiny_integer_column');
            // unsignedBigInteger
            $migrationDefinition->unsignedBigInteger('unsigned_big_integer_column');
            // unsignedInteger
            $migrationDefinition->unsignedInteger('unsigned_integer_column');
            // unsignedMediumInteger
            $migrationDefinition->unsignedMediumInteger('unsigned_medium_integer_column');
            // unsignedSmallInteger
            $migrationDefinition->unsignedSmallInteger('unsigned_small_integer_column');
            // unsignedTinyInteger
            $migrationDefinition->unsignedTinyInteger('unsigned_tiny_integer_column');
            //
            // Date & Time Types
            //
            // dateTime
            $migrationDefinition->dateTime('date_time_column');
            // dateTimeTz
            $migrationDefinition->dateTimeTz('date_time_tz_column');
            // date
            $migrationDefinition->date('date_column');
            // time
            $migrationDefinition->time('time_column');
            // timeTz
            $migrationDefinition->timeTz('time_tz_column');
            // timestamp
            $migrationDefinition->timestamp('timestamp_column');
            // timestamps
            $migrationDefinition->timestamps();
            // timestampsTz
            $migrationDefinition->timestampsTz();
            // softDeletes
            $migrationDefinition->softDeletes();
            // softDeletesTz
            $migrationDefinition->softDeletesTz();
            // year
            $migrationDefinition->year('year_column');
            //
            // Binary Types
            //
            // binary
            $migrationDefinition->binary('binary_column');
            //
            // Object & Json Types
            //
            // json
            $migrationDefinition->json('json_column');
            // jsonb
            $migrationDefinition->jsonb('jsonb_column');
            //
            // UUID & ULID Types
            //
            // ulid
            $migrationDefinition->ulid('ulid_column');
            // ulidMorphs
            $migrationDefinition->ulidMorphs('ulid_morphs_column');
            // uuid
            $migrationDefinition->uuid('uuid_column');
            // uuidMorphs
            $migrationDefinition->uuidMorphs('uuid_morphs_column');
            // nullableUlidMorphs
            $migrationDefinition->nullableUlidMorphs('nullable_ulid_morphs_column');
            // nullableUuidMorphs
            $migrationDefinition->nullableUuidMorphs('nullable_uuid_morphs_column');
            //
            // Spatial Types
            //
            // geography
            $migrationDefinition->geography('geography_column');
            // geometry
            $migrationDefinition->geometry('geometry_column');
            //
            // Relationship Types
            //
            // foreignId
            $migrationDefinition->foreignId('foreign_id_column');
            // foreignIdFor
            $migrationDefinition->foreignIdFor('foreign_id_for_column');
            // foreignUlid
            $migrationDefinition->foreignUlid('foreign_ulid_column');
            // foreignUuid
            $migrationDefinition->foreignUuid('foreign_uuid_column');
            // morphs
            $migrationDefinition->morphs('morphs_column');
            // nullableMorphs
            $migrationDefinition->nullableMorphs('nullable_morphs_column');
            //
            // Specialty Types
            //
            // enum
            $migrationDefinition->enum('enum_column', ['active', 'inactive']);
            // set
            $migrationDefinition->set('set_column', ['active', 'inactive']);
            // macAddress
            $migrationDefinition->macAddress('mac_address_column');
            // ipAddress
            $migrationDefinition->ipAddress('ip_address_column');
            // rememberToken
            $migrationDefinition->rememberToken();
            // vector
            $migrationDefinition->vector('vector_column');
        });
    });

    //    return;
    $draft->model('Post', static function (ModelDefinition $modelDefinition): void {
        $modelDefinition->migration(static function (MigrationDefinition $migrationDefinition): void {
            $migrationDefinition->id();
            $migrationDefinition->string('title');
            $migrationDefinition->text('content');
            $migrationDefinition->tinyText('tinyContent');
            $migrationDefinition->foreignIdFor('User');
            $migrationDefinition->timestamps();
            $migrationDefinition->bigIncrements('uid');
            $migrationDefinition->uuid();
            $migrationDefinition->bigInteger('votes');
        });

        //        return;

        $modelDefinition->relationships('author')->hasOne('User');
        $modelDefinition->relationships('comments')->hasMany('Comment');
        $modelDefinition->relationships('tags')->belongsToMany('Tag');

        $modelDefinition->controller(static function (ControllerDefinition $controllerDefinition): void {
            $controllerDefinition->action('index', static function (ControllerActionDefinition $action): void {
                $action->query([
                    'all' => true,
                ]);
                $action->render('post.index', ['posts']);
            });

            $controllerDefinition->action('store', static function (ControllerActionDefinition $action): void {
                $action->formRequest('StorePostRequest', [
                    'title' => 'required',
                    'content' => 'required',
                ]);

                $action->validate([
                    'title' => 'required',
                    'content' => 'required',
                ]);

                $action->save('post');
                $action->send('ReviewNotification', 'post.author', ['post']);
                $action->dispatch('SyncMedia', ['post']);
                $action->fire('NewPost', ['post']);
                $action->flash('success', 'post.title');
                $action->redirect('post.index');
            });
        });
    });

    $draft->model('Comment', static function (ModelDefinition $modelDefinition, Draft $draft): void {
        $modelDefinition->migration(
            static function (MigrationDefinition $migrationDefinition): void {
                $migrationDefinition->id();
                $migrationDefinition->string('title');
                $migrationDefinition->text('content');
                $migrationDefinition->foreignIdFor('User', 'author_id');
                $migrationDefinition->string('title', 400);
                $migrationDefinition->longText('content');
                $migrationDefinition->timestamp('published_at')->nullable();
                $migrationDefinition->timestamps();
                $migrationDefinition->softDeletes();
            },
            $modelDefinition->table(),
        );

        $modelDefinition->resourceController();

        //        $modelDefinition->hasOne('User')->method('author');
        $modelDefinition->relationships('post')->belongsTo('Post');
    });


    $draft->model('Tag', static function (ModelDefinition $modelDefinition, Draft $draft): void {
        $draft->resourceController($modelDefinition->name());

        $modelDefinition->relationships('posts')->belongsToMany('Post');
        $modelDefinition->relationships('events')->belongsToMany('Event');
    });

    $draft->livewire(
        'UpdateProfile',
        static function (LivewireDefinition $livewireDefinition): void {
            $livewireDefinition->mount('user');
            $livewireDefinition->action(
                'update',
                static function (
                    LivewireActionDefinition $livewireActionDefinition,
                ): void {
                    $livewireActionDefinition->fire('ProfileUpdated', ['user'])
                                             ->redirect('settings.profile')
                                             ->flash('success', 'Profile updated successfully.');
                },
            );
        },
    );
    $draft->inertia('UpdateProfile', static function (InertiaDefinition $livewireDefinition): void {
        $livewireDefinition->mount('user');
        $livewireDefinition->action(
            'update',
            static function (InertiaActionDefinition $livewireActionDefinition): void {
                $livewireActionDefinition->fire('ProfileUpdated', ['user']);
            },
        );
    });

    $draft->livewire('UpdateProfile', static function (
        LivewireDefinition $livewireDefinition,
    ): void {
        $livewireDefinitionName = $livewireDefinition->name(); // UserComponent

        foreach (Draft::RESOURCE_ACTIONS as $action) {
            $livewireDefinition->action($livewireDefinitionName . \ucfirst($action), static function (
                LivewireActionDefinition $livewireActionDefinition,
                LivewireDefinition $livewireDefinition,
            ): void {
                //                    $livewireActionDefinition->name(); // user.index
                //
                //                    $livewireDefinition->name(); // UserComponent
                //
                //                    $livewireActionDefinition
                //                        ->query('all')
                //                        ->render('post.index', ['posts']);
            });
        }
    });

    $draft->model('User', static function (ModelDefinition $modelDefinition, Draft $draft): void {
        $modelDefinitionName = $modelDefinition->name(); // User

        $modelDefinition->relationships('address')->hasOne('Address');
        $modelDefinition->relationships('roles')->belongsToMany(
            'Role',
            //            'role_user',
            //            'user_id',
            //            'role_id'
        );

        $draft->controller($modelDefinitionName, static function (
            ControllerDefinition $controllerDefinition,
        ) use ($modelDefinitionName): void {
            foreach (['index', 'create', 'edit', 'show', 'store', 'update', 'destroy'] as $action) {
                $controllerDefinition->action(\mb_strtolower($modelDefinitionName) . '.' . $action, static function (
                    ControllerActionDefinition $controllerActionDefinition,
                ): void {
                    // $controllerActionDefinition->name(); // user.index

                    $controllerActionDefinition
                        ->query([
                            'all' => true,
                        ])
                        ->render('post.index', ['posts']);
                });
            }
        });

//                $draft->controller($modelDefinitionName, static function (
//                        \ControllerDefinition $controllerDefinition,
//                        \Ghostwriter\Draft\Draft $draft
//                    ) : void {
//                        $controllerDefinitionName = $controllerDefinition->name(); // UserController
//                        foreach (['index', 'create', 'edit', 'show', 'store', 'update', 'destroy'] as $action) {
//                            $controllerDefinition->action($action, static function (
//                                \ControllerActionDefinition $controllerActionDefinition,
//                                \ControllerDefinition $controllerDefinition,
//                                \Ghostwriter\Draft\Draft $draft
//                            ) use ($action): void {
//                                $controllerActionDefinition->name(); // user.index
//
//                                $controllerDefinition->name(); // UserController
//
//                                $controllerActionDefinition
//                                    ->query(['all' => true])
//                                    ->render('post.index', ['posts']);
//                            });
//                        }
//                    });

        $draft->livewire($modelDefinitionName, static function (
            LivewireDefinition $livewireDefinition,
        ): void {
            $livewireDefinitionName = $livewireDefinition->name(); // UserComponent

            foreach (Draft::RESOURCE_ACTIONS as $action) {
                $livewireDefinition->action($livewireDefinitionName . \ucfirst($action), static function (
                    LivewireActionDefinition $livewireActionDefinition,
                    LivewireDefinition $livewireDefinition,
                ): void {
                    //                    $livewireActionDefinition->name(); // user.index
                    //
                    //                    $livewireDefinition->name(); // UserComponent
                    //
                    //                    $livewireActionDefinition
                    //                        ->query('all')
                    //                        ->render('post.index', ['posts']);
                });
            }

            $livewireDefinition->action('index', static function (
                LivewireActionDefinition $livewireActionDefinition,
                LivewireDefinition $livewireDefinition,
                Draft $draft,
            ): void {
                $livewireActionDefinition->name(); // user.index

                $livewireDefinition->name(); // UserComponent

                $livewireActionDefinition
                    ->query('all')
                    ->render('post.index', ['posts']);
            });

            // ['index', 'create', 'edit', 'show']
            // ['index', 'create', 'edit', 'show', 'store', 'update', 'destroy']
            //            $livewireDefinition->action('index');
            //            $draft->modelDefinition('User'); // ?ModelDefinition<User>
        });

        //        $draft->controllerDefinition('User'); // ?ControllerDefinition<UserController>
        //        $draft->controllerDefinition('User')->name(); // UserController

        $modelDefinition->id();
        $modelDefinition->string('name', 200);
        $modelDefinition->string('email', 200)->unique();
        $modelDefinition->string('password', 200);
        $modelDefinition->timestamps();
    });

    $draft->controller('PostController', static function (ControllerDefinition $controllerDefinition): void {
        $controllerDefinition->action(
            'index',
            static function (ControllerActionDefinition $controllerActionDefinition): void {
                $controllerActionDefinition->query([
                    'where' => ['title', 'content'],
                    'order' => 'published_at',
                    'limit' => 5,
                ]);

                $controllerActionDefinition->render('post.index', ['posts']);
            },
        );

        $controllerDefinition->action(
            'edit',
            static function (ControllerActionDefinition $controllerActionDefinition): void {
                $controllerActionDefinition->find('post');
                $controllerActionDefinition->render('post.edit', ['post']);
            },
        );

        $controllerDefinition->action(
            'update',
            static function (ControllerActionDefinition $controllerActionDefinition): void {
                $controllerActionDefinition->find('post.id');

                $controllerActionDefinition->query([
                    'where' => 'post.title',
                    'take' => 3,
                    'pluck' => 'post.id',
                ]);

                $controllerActionDefinition->save('post');

                $controllerActionDefinition->redirect('posts.edit');
            },
        );

        $controllerDefinition->action(
            'store',
            static function (ControllerActionDefinition $controllerActionDefinition): void {
                $controllerActionDefinition->formRequest('StorePostRequest', [
                    'title' => 'required',
                    'content' => 'required',
                ]);

                $controllerActionDefinition->validate([
                    'title' => 'required',
                    'content' => 'required',
                ]);

                $controllerActionDefinition->save('post');
                $controllerActionDefinition->send('ReviewNotification', 'post.author', ['post']);
                $controllerActionDefinition->dispatch('SyncMedia', ['post']);
                $controllerActionDefinition->fire('NewPost', ['post']);
                $controllerActionDefinition->flash('success', 'post.title');
                $controllerActionDefinition->redirect('post.index');
            },
        );
    });

    $draft->controller('PostController2', static function (ControllerDefinition $controllerDefinition): void {
        $controllerDefinition->action(
            'index',
            static function (ControllerActionDefinition $controllerActionDefinition): void {
                $controllerActionDefinition->query([
                    'all' => true,
                ]);
                $controllerActionDefinition->render('post.index', ['users']);
            },
        );

        $controllerDefinition->action(
            'store',
            static function (ControllerActionDefinition $controllerActionDefinition): void {
                $controllerActionDefinition->formRequest('StoreUserRequest', [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                ]);

                $controllerActionDefinition->validate([
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required',
                ]);

                $controllerActionDefinition->save('user');
                $controllerActionDefinition->send('WelcomeMail', 'user.email', ['user']);
                $controllerActionDefinition->dispatch('SyncMedia', ['user']);
                $controllerActionDefinition->fire('NewUser', ['user']);
                $controllerActionDefinition->flash('success', 'user.name');
                $controllerActionDefinition->redirect('user.index');
            },
        );
    });

    $draft->model('Journey', static function (ModelDefinition $modelDefinition): void {
        $modelDefinition->uuid('id');
        $modelDefinition->string('name');

        $modelDefinition->relationships()->belongsToMany('Diary');
    });
    $draft->model('Diary', static function (ModelDefinition $modelDefinition): void {
        $modelDefinition->uuid('id');
        $modelDefinition->foreignId('user_id');
        $modelDefinition->foreignId('journey_id');

        $modelDefinition->relationships()->hasMany('Journey');
    });

    $draft->model('Company', static function (ModelDefinition $modelDefinition): void {
        $modelDefinition->uuid('id');
        $modelDefinition->string('name');

        $modelDefinition->relationships()->belongsToMany('User', 'person');
    });
    $draft->model('Person', static function (ModelDefinition $modelDefinition): void {
        $modelDefinition->string('name');

        //        $modelDefinition->hasOne('User', 'id');
        $modelDefinition->relationships('company')->belongsToMany('Company');
    });
    $draft->model('Salesman', static function (ModelDefinition $modelDefinition): void {
        $modelDefinition->string('name');
        $modelDefinition->relationships('lead')->hasOne('User');
        $modelDefinition->relationships('many')->hasMany('ManyModel', 'ManyAlias');
        $modelDefinition->relationships('belongs')->belongsTo('BelongsModel', 'user_id');
    });

    $draft->model('Post', static function (ModelDefinition $modelDefinition, Draft $draft): void {
        // Post
        $draft->controller($modelDefinition->name(), static function (
            ControllerDefinition $controllerDefinition,
            Draft $draft,
        ): void {
            $controllerDefinitionName = $controllerDefinition->name(); // PostController
            //            $draft->controllerDefinition('Post'); // ?ControllerDefinition<PostController>

            //            $controllerDefinition->modelDefinition(); // ?ModelDefinition<Post>
            //            $controllerDefinition->modelDefinition()->name(); // Post

            //            $draft->modelDefinition('Post'); // ?ModelDefinition<Post>
            //            $draft->modelDefinition('Post')->name(); // Post
            //
            //            $draft->controllerDefinition($controllerDefinitionName); // ?ModelDefinition<Post>
            //            $draft->modelDefinition('Post')->name(); // Post

            //            $controllerDefinition->actionDefinition('index')->name(); // index

            //            $controllerDefinition->actionDefinition('index'); // ?ActionDefinition<index>
            //            $controllerDefinition->actionDefinition('index')->name(); // post.index

            $controllerDefinition->action(
                'index',
                static function (ControllerActionDefinition $actionDefinition): void {
                    $actionDefinition->query([
                        'all' => true,
                    ]);

                    $actionDefinition->render('post.index', ['posts']);
                },
            );

            $controllerDefinition->action('store', static function (
                ControllerActionDefinition $actionDefinition,
                Draft $draft,
            ): void {
                $actionDefinition->formRequest('StorePostRequest', [
                    'title' => 'required',
                    'content' => 'required',
                ]);

                $actionDefinition
                    ->save('post')
                    ->send('ReviewNotification', 'post.author', ['post'])
                    ->dispatch('SyncMedia', ['post'])
                    ->fire('NewPost', ['post'])
                    ->flash('success', 'post.title')
                    ->redirect('post.index');
            });
        });

    });
};
