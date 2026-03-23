<?php

namespace App\Console\Commands;

use Throwable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearImageCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:clear-cache {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear cached optimized images (public/cache/images)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cacheDir = public_path('cache/images');

        if (!File::exists($cacheDir)) {
            $this->warn('Image cache directory does not exist: ' . $cacheDir);
            return Command::SUCCESS;
        }

        $files = File::files($cacheDir);
        $count = count($files);

        if ($count === 0) {
            $this->info('Image cache is already empty.');
            return Command::SUCCESS;
        }

        $this->info("Found {$count} cached images in: {$cacheDir}");

        // Confirmation agar --force yo'q bo'lsa
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to delete all cached images? They will be regenerated on next page load.', true)) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        $totalSize = 0;
        foreach ($files as $file) {
            $totalSize += $file->getSize();
        }

        try {
            File::cleanDirectory($cacheDir);

            $this->newLine();
            $this->info('✓ Image cache cleared successfully!');
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Files deleted', $count],
                    ['Space freed', $this->formatBytes($totalSize)],
                ]
            );

            $this->newLine();
            $this->comment('Images will be regenerated with new quality settings on next page load.');

            return Command::SUCCESS;

        } catch (Throwable $e) {
            $this->error('Failed to clear image cache: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
