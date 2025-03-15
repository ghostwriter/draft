<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Console\Command;

use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Draft\Parser\FindControllers;
use Ghostwriter\Draft\Parser\FindModels;
use Ghostwriter\Draft\Parser\Visitor\DraftVisitor;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

use function dd;
use function dump;
use function iterator_to_array;

final class BuildCommand extends Command
{
    protected $description = 'Trace the full project for models';

    protected $signature = 'draft:build';

    public function __construct(
        private readonly ContainerInterface $container,
        //        private readonly DraftVisitor $draft,
        //        private readonly Filesystem $filesystem,
        //        private readonly FindControllers $findControllers,
        //        private readonly FindModels $findModels,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // return self::SUCCESS;
        $models = iterator_to_array($this->container->invoke(FindModels::class));
        $controllers = iterator_to_array($this->container->invoke(FindControllers::class));
        //        $controllers = iterator_to_array((new FindControllers($this->draft, $this->filesystem))());

        dump([$models, $controllers]);
        //            $this->callSilently('queue:monitor', []);

        //        dd(app()->getNamespace());

        // dd($this->draft);
        //        $controllers = collect($this->filesystem->files(app()->basePath('app/http/controllers')))
        //            ->map->getPathname();
        //
        //        $formRequests = collect($this->filesystem->files(app()->basePath('app/http/requests')))
        //            ->map->getPathname();

        //        $models = $filesystem->files(app()->databasePath('app/models'));
        //        dump([
        //            'it works!',
        //            //            app()->basePath('app/models'),
        //            $models,
        //            $controllers,
        //            $formRequests,
        //            //            app()->configPath(),
        //            //            app()->databasePath(),
        //            //            app()->resourcePath(),
        //        ]);

        return self::SUCCESS;
    }
}
