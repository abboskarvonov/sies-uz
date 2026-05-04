<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Throwable;

class ImageCompressionService
{
    private const MAX_SIZE_KB = 300;
    private const QUALITY = 80;

    /**
     * Compress a stored image if it exceeds MAX_SIZE_KB.
     * Skips PNG and GIF (lossless formats — compression gain is negligible).
     * Returns true if compression was applied.
     */
    public static function compressIfNeeded(string $disk, string $path): bool
    {
        if (!$path) return false;

        $storage = Storage::disk($disk);

        if (!$storage->exists($path)) return false;

        if ($storage->size($path) <= self::MAX_SIZE_KB * 1024) return false;

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (!in_array($ext, ['jpg', 'jpeg', 'webp'])) return false;

        $absolutePath = $storage->path($path);

        try {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($absolutePath);

            if ($ext === 'webp') {
                $image->toWebp(self::QUALITY)->save($absolutePath);
            } else {
                $image->toJpeg(self::QUALITY)->save($absolutePath);
            }

            return true;
        } catch (Throwable $e) {
            Log::warning("ImageCompressionService: failed to compress {$disk}:{$path} — {$e->getMessage()}");
            return false;
        }
    }
}
