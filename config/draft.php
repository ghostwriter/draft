<?php

declare(strict_types=1);

use App\Models\User;

$appNamespace = \app()->getNamespace();

return [
    'app' => [
        'namespace' => $appNamespace,
    ],
    'namespaces' => [
        'app' => $appNamespace,
        'rules' => $appNamespace . 'Rules',
        'commands' => $appNamespace . 'Console\\Commands',
        'console' => $appNamespace . 'Console',
        'listeners' => $appNamespace . 'Listeners',
        'models' => $appNamespace . 'Models',
        'notification' => $appNamespace . 'Notification',
        'policies' => $appNamespace . 'Policies',
        'providers' => $appNamespace . 'Providers',
        'http' => [
            'controllers' => $appNamespace . 'Http\\Controllers',
            'middleware' => $appNamespace . 'Http\\Middleware',
            'requests' => $appNamespace . 'Http\\Requests',
            'livewire' => $appNamespace . 'Http\\Livewire',
        ],
    ],
    'paths' => [
        'app' => \app_path(),
        'base' => \base_path(),
        'bootstrap' => \base_path('bootstrap'),
        'commands' => \app_path('Console/Commands'),
        'config' => \config_path(),
        'console' => \app_path('Console'),
        'controllers' => \app_path('Http/Controllers'),
        'css' => \resource_path('css'),
        'database' => \database_path(),
        'events' => \app_path('Events'),
        'factories' => \database_path('factories'),
        'jobs' => \app_path('Jobs'),
        'js' => \resource_path('js'),
        'lang' => \lang_path(),
        'listeners' => \app_path('Listeners'),
        'livewire' => \app_path('Http/Livewire'),
        'livewire_components' => \app_path('Http/Livewire/Components'),
        'middleware' => \app_path('Http/Middleware'),
        'migrations' => \database_path('migrations'),
        'models' => \app_path('Models'),
        'notifications' => \app_path('Notifications'),
        'observables' => \app_path('Observables'),
        'policies' => \app_path('Policies'),
        'providers' => \app_path('Providers'),
        'public' => \public_path(),
        'requests' => \app_path('Http/Requests'),
        'resource' => \resource_path(),
        'routes' => \base_path('routes'),
        'seeders' => \database_path('seeders'),
        'storage' => \storage_path(),
        'stubs' => \base_path('stubs'),
        'tests' => \base_path('tests'),
        'translations' => \base_path('lang'),
        'vendor' => \base_path('vendor'),
        'view' => \app_path('View'),
        'view_components' => \app_path('View/Components'),
        'views' => \resource_path('views'),
    ],

    'default' => [
        'user' => User::class,
    ],
];
