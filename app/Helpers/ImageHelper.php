<?php

namespace App\Helpers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class ImageHelper
{
    public static function generateSrcset(string $src, array $widths = [640, 1280, 1920], int $quality = 75): string
    {
        $manager = new ImageManager(new Driver());

        // Clean src
        $src = ltrim($src, '/');

        // URL bo‘lsa parse qilish
        if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
            $parsed = parse_url($src);
            $src = ltrim($parsed['path'], '/');
        }

        $absolutePath = public_path($src);
        $pathInfo = pathinfo($absolutePath);

        // Supported formats
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array(strtolower($pathInfo['extension']), $allowedExtensions)) {
            // Unsupported format → fallback to noimage
            return self::fallbackSrcset($widths);
        }

        // File exists check
        if (!File::exists($absolutePath)) {
            // Fayl yo‘q → fallback to noimage
            return self::fallbackSrcset($widths);
        }

        $srcset = [];

        foreach ($widths as $width) {
            $newFilename = "{$pathInfo['filename']}-{$width}.webp";
            $newPath = "{$pathInfo['dirname']}/{$newFilename}";

            if (!File::exists($newPath)) {
                $image = $manager->read($absolutePath);
                $image = $image->scaleDown(width: $width);
                $image->toWebp(quality: $quality)->save($newPath);
            }

            $relativePath = str_replace(public_path(), '', $newPath);
            $srcset[] = asset($relativePath) . " {$width}w";
        }

        return implode(', ', $srcset);
    }

    // Fallback uchun metod
    public static function fallbackSrcset(array $widths = [640, 1280, 1920]): string
    {
        $fallbackSrc = 'img/noimage.webp';
        $fallbackPath = public_path($fallbackSrc);
        $pathInfo = pathinfo($fallbackPath);

        $manager = new ImageManager(new Driver());

        $srcset = [];

        foreach ($widths as $width) {
            $newFilename = "{$pathInfo['filename']}-{$width}.webp";
            $newPath = "{$pathInfo['dirname']}/{$newFilename}";

            if (!File::exists($newPath)) {
                $image = $manager->read($fallbackPath);
                $image = $image->scaleDown(width: $width);
                $image->toWebp(quality: 75)->save($newPath);
            }

            $relativePath = str_replace(public_path(), '', $newPath);
            $srcset[] = asset($relativePath) . " {$width}w";
        }

        return implode(', ', $srcset);
    }
}
