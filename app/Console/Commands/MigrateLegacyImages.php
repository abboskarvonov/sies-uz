<?php

namespace App\Console\Commands;

use App\Models\Menu;
use App\Models\Multimenu;
use App\Models\Page;
use App\Models\SiteSettings;
use App\Models\StaffMember;
use App\Models\Submenu;
use App\Models\Symbol;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateLegacyImages extends Command
{
    protected $signature = 'media:migrate-legacy {--dry-run : Show what would be migrated without doing it} {--model= : Only migrate a specific model class (e.g. Page)}';

    protected $description = 'Migrate legacy image file paths to spatie/laravel-medialibrary';

    private int $migrated = 0;
    private int $skipped = 0;
    private int $failed = 0;

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $onlyModel = $this->option('model');

        if ($dryRun) {
            $this->warn('DRY RUN mode — no changes will be made.');
        }

        $models = [
            'Page'         => [Page::class, 'image', 'image'],
            'Menu'         => [Menu::class, 'image', 'image'],
            'Submenu'      => [Submenu::class, 'image', 'image'],
            'Multimenu'    => [Multimenu::class, 'image', 'image'],
            'Tag'          => [Tag::class, 'image', 'image'],
            'Symbol'       => [Symbol::class, 'image', 'image'],
            'SiteSettings' => [SiteSettings::class, 'logo', 'logo'],
            'StaffMember'  => [StaffMember::class, 'image', 'image'],
        ];

        foreach ($models as $name => [$class, $column, $collection]) {
            if ($onlyModel && $onlyModel !== $name) {
                continue;
            }
            $this->info("Processing {$name}...");
            $this->migrateModel($class, $column, $collection, $dryRun);
        }

        // Page gallery (JSON images column)
        if (!$onlyModel || $onlyModel === 'Page') {
            $this->info('Processing Page gallery images...');
            $this->migratePageGallery($dryRun);
        }

        $this->newLine();
        $this->table(['Migrated', 'Skipped', 'Failed'], [[$this->migrated, $this->skipped, $this->failed]]);

        return self::SUCCESS;
    }

    private function migrateModel(string $class, string $column, string $collection, bool $dryRun): void
    {
        $class::whereNotNull($column)
            ->where($column, '!=', '')
            ->chunk(100, function ($records) use ($column, $collection, $dryRun) {
                foreach ($records as $record) {
                    if ($record->getMedia($collection)->isNotEmpty()) {
                        $this->line("  SKIP [{$record->id}] already has media");
                        $this->skipped++;
                        continue;
                    }

                    $path = $record->getRawOriginal($column) ?? $record->$column;
                    if (!$path) {
                        $this->skipped++;
                        continue;
                    }

                    $diskPath = ltrim($path, '/');
                    if (!Storage::disk('public')->exists($diskPath)) {
                        $this->warn("  MISSING [{$record->id}] {$diskPath}");
                        $this->failed++;
                        continue;
                    }

                    $fullPath = Storage::disk('public')->path($diskPath);
                    $fileName = basename($diskPath);

                    $this->line("  MIGRATE [{$record->id}] {$diskPath}");

                    if (!$dryRun) {
                        try {
                            $record->addMedia($fullPath)
                                ->preservingOriginal()
                                ->usingFileName($fileName)
                                ->toMediaCollection($collection, 'public');
                            $this->migrated++;
                        } catch (\Throwable $e) {
                            $this->error("  ERROR [{$record->id}]: {$e->getMessage()}");
                            $this->failed++;
                        }
                    } else {
                        $this->migrated++;
                    }
                }
            });
    }

    private function migratePageGallery(bool $dryRun): void
    {
        Page::whereNotNull('images')
            ->chunk(100, function ($pages) use ($dryRun) {
                foreach ($pages as $page) {
                    if ($page->getMedia('gallery')->isNotEmpty()) {
                        $this->skipped++;
                        continue;
                    }

                    $raw = $page->getRawOriginal('images') ?? $page->images;
                    $paths = is_array($raw) ? $raw : json_decode($raw, true);

                    if (empty($paths)) {
                        $this->skipped++;
                        continue;
                    }

                    foreach (array_filter($paths) as $imgPath) {
                        $diskPath = ltrim($imgPath, '/');
                        if (!Storage::disk('public')->exists($diskPath)) {
                            $this->warn("  GALLERY MISSING [{$page->id}] {$diskPath}");
                            $this->failed++;
                            continue;
                        }

                        $fullPath = Storage::disk('public')->path($diskPath);
                        $fileName = basename($diskPath);
                        $this->line("  GALLERY [{$page->id}] {$diskPath}");

                        if (!$dryRun) {
                            try {
                                $page->addMedia($fullPath)
                                    ->preservingOriginal()
                                    ->usingFileName($fileName)
                                    ->toMediaCollection('gallery', 'public');
                                $this->migrated++;
                            } catch (\Throwable $e) {
                                $this->error("  ERROR [{$page->id}] gallery: {$e->getMessage()}");
                                $this->failed++;
                            }
                        } else {
                            $this->migrated++;
                        }
                    }
                }
            });
    }
}
