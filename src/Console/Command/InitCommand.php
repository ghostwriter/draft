<?php

declare(strict_types=1);

namespace Ghostwriter\Draft\Console\Command;

use Ghostwriter\Draft\Parser\Node\DraftFileNode;
use Ghostwriter\Draft\Parser\PrinterInterface;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

use function base_path;

final class InitCommand extends Command
{
    protected $description = 'Creates a draft.php file in the project base path.';

    protected $signature = 'draft:init {--force=false}';

    public function __construct(
        private readonly DraftFileNode $draftFileNode,
        private readonly Filesystem $filesystem,
        private readonly PrinterInterface $printer,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Checking for draft.php file...');

        $draftFile = base_path('draft.php');

        return match (true) {
            $this->filesystem->missing($draftFile) => $this->createDraftFile($draftFile),
            $this->option('force') => $this->updateDraftFile($draftFile),
            default => $this->cancel(),
        };
    }

    private function cancel(): int
    {
        $this->warn('[✕] draft.php file already exists, use --force to overwrite it!');

        return self::FAILURE;
    }

    private function createDraftFile(string $draftFile): int
    {
        $this->saveDraftFile($draftFile);

        $this->info('[✓] Created draft.php file');

        return self::SUCCESS;
    }

    private function saveDraftFile(string $draftFile): void
    {
        $this->filesystem->put($draftFile, $this->printer->print($this->draftFileNode));
    }

    private function updateDraftFile(string $draftFile): int
    {
        $this->saveDraftFile($draftFile);

        $this->info('[✓] Created draft.php file (force)');

        return self::SUCCESS;
    }
}
