<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class OptimizeStaticImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize-static {--quality=70}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-optimize static images (hero-bg, field, og-image) with better compression';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $manager = new ImageManager(new Driver());
        $quality = (int) $this->option('quality');

        $this->info("Optimizing static images with quality: {$quality}");

        // Asosiy static rasmlar
        $staticImages = [
            'hero-bg.jpg',
            'hero-bg.webp',
            'field.jpg',
            'field.webp',
            'og-image.webp',
        ];

        $publicImg = public_path('img');
        $optimized = 0;
        $failed = 0;

        foreach ($staticImages as $filename) {
            $path = $publicImg . DIRECTORY_SEPARATOR . $filename;

            if (!File::exists($path)) {
                $this->warn("Skipping {$filename} - file not found");
                continue;
            }

            try {
                $originalSize = filesize($path);
                $this->info("Processing: {$filename} (Original: " . $this->formatBytes($originalSize) . ")");

                // Backup yaratish
                $backupPath = $path . '.backup';
                if (!File::exists($backupPath)) {
                    File::copy($path, $backupPath);
                }

                // Rasmni optimallashtirish
                $img = $manager->read($path);

                // WebP yoki JPEG ga optimize
                if (str_ends_with($filename, '.webp')) {
                    $img->toWebp(quality: $quality)->save($path);
                } elseif (str_ends_with($filename, '.jpg') || str_ends_with($filename, '.jpeg')) {
                    $img->toJpeg(quality: $quality)->save($path);
                }

                $newSize = filesize($path);
                $saved = $originalSize - $newSize;
                $percent = $originalSize > 0 ? round(($saved / $originalSize) * 100, 2) : 0;

                $this->info("✓ Optimized: " . $this->formatBytes($newSize) .
                           " (Saved: " . $this->formatBytes($saved) . " / {$percent}%)");

                $optimized++;

                // Responsiv versiyalarni yaratish (640, 1280, 1920)
                if (in_array($filename, ['hero-bg.jpg', 'hero-bg.webp', 'field.jpg', 'field.webp'])) {
                    $this->createResponsiveVersions($manager, $path, $filename, $quality);
                }

                // Memory tozalash
                unset($img);

            } catch (\Throwable $e) {
                $this->error("✗ Failed to optimize {$filename}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Optimization complete!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Optimized', $optimized],
                ['Failed', $failed],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Responsiv versiyalarni yaratish
     */
    protected function createResponsiveVersions(ImageManager $manager, string $path, string $filename, int $quality): void
    {
        $widths = [640, 1280, 1920];
        $dir = dirname($path);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        foreach ($widths as $width) {
            $responsiveName = "{$name}-{$width}.webp";
            $responsivePath = $dir . DIRECTORY_SEPARATOR . $responsiveName;

            try {
                $img = $manager->read($path);
                $img->scaleDown(width: $width);
                $img->toWebp(quality: $quality)->save($responsivePath);

                $size = filesize($responsivePath);
                $this->line("  → Created {$responsiveName} (" . $this->formatBytes($size) . ")");

                unset($img);
            } catch (\Throwable $e) {
                $this->warn("  ✗ Failed to create {$responsiveName}");
            }
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
