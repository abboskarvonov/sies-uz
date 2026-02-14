<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ImageHelper
{
    protected static ?ImageManager $manager = null;

    protected static function manager(): ImageManager
    {
        return self::$manager ??= new ImageManager(new Driver());
    }

    /**
     * Responsiv srcset generatsiya (webp'ga) - OPTIMIZED
     */
    public static function generateSrcset(
        string $src,
        array $widths = [640, 1280, 1920],
        int $quality = 70
    ): string {
        try {
            // 1) Normalize
            [$absolutePath, $publicRelative] = self::resolvePath($src);

            // 2) Fayl tekshirish
            if (!$absolutePath || !File::exists($absolutePath)) {
                return self::fallbackSrcset($widths);
            }

            // 3) Kengaytma tekshirish
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

            if (!$ext) {
                $mime = @File::mimeType($absolutePath) ?: '';
                if (str_contains($mime, 'jpeg')) $ext = 'jpg';
                elseif (str_contains($mime, 'png')) $ext = 'png';
                elseif (str_contains($mime, 'webp')) $ext = 'webp';
            }

            if (!$ext || !in_array($ext, $allowed, true)) {
                return self::fallbackSrcset($widths);
            }

            // 4) Cache direktoriya
            $cacheDir = public_path('cache/images');
            File::ensureDirectoryExists($cacheDir);

            $version = @filemtime($absolutePath) ?: time();
            $base = pathinfo($absolutePath, PATHINFO_FILENAME);

            $srcset = [];
            $needsProcessing = false;

            // *** YANGI: Avval barcha fayllar mavjudligini tekshiramiz ***
            foreach ($widths as $w) {
                $cachedName = sprintf('%s-w%s-q%s-v%s.webp', $base, $w, $quality, $version);
                $cachedPath = $cacheDir . DIRECTORY_SEPARATOR . $cachedName;

                if (!File::exists($cachedPath)) {
                    $needsProcessing = true;
                    break;
                }
            }

            // Agar cache'lanmagan bo'lsa va katta fayl bo'lsa - queue'ga
            if ($needsProcessing) {
                $fileSize = @filesize($absolutePath) ?: 0;

                // 500KB dan katta fayllarni background'da process qilamiz
                if ($fileSize > 500 * 1024) {
                    // Queue job dispatch
                    dispatch(function () use ($absolutePath, $widths, $quality, $cacheDir, $base, $version) {
                        self::processImage($absolutePath, $widths, $quality, $cacheDir, $base, $version);
                    })->afterResponse();

                    // Hozircha original rasm yoki fallback qaytaramiz
                    return self::temporarySrcset($src, $widths);
                } else {
                    // Kichik fayllarni darhol process qilamiz
                    self::processImage($absolutePath, $widths, $quality, $cacheDir, $base, $version);
                }
            }

            // Srcset yaratish
            foreach ($widths as $w) {
                $cachedName = sprintf('%s-w%s-q%s-v%s.webp', $base, $w, $quality, $version);
                $cachedPath = $cacheDir . DIRECTORY_SEPARATOR . $cachedName;

                // Agar hali cache'lanmagan bo'lsa - original rasm
                if (File::exists($cachedPath)) {
                    $srcset[] = asset('cache/images/' . $cachedName) . " {$w}w";
                } else {
                    $srcset[] = asset($publicRelative) . " {$w}w";
                }
            }

            return implode(', ', $srcset);
        } catch (\Throwable $e) {
            Log::error('ImageHelper error: ' . $e->getMessage());
            return self::fallbackSrcset($widths);
        }
    }

    /**
     * Rasmni qayta ishlash (asinxron ishlatish uchun ajratilgan)
     */
    protected static function processImage(
        string $absolutePath,
        array $widths,
        int $quality,
        string $cacheDir,
        string $base,
        int $version
    ): void {
        try {
            foreach ($widths as $w) {
                $cachedName = sprintf('%s-w%s-q%s-v%s.webp', $base, $w, $quality, $version);
                $cachedPath = $cacheDir . DIRECTORY_SEPARATOR . $cachedName;

                if (!File::exists($cachedPath)) {
                    $img = self::manager()->read($absolutePath);
                    $img->scaleDown(width: $w);
                    $img->toWebp(quality: $quality)->save($cachedPath);

                    // Memory tozalash
                    unset($img);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Image processing error: ' . $e->getMessage());
        }
    }

    /**
     * Vaqtinchalik srcset (cache qilinmaguncha original)
     */
    protected static function temporarySrcset(string $src, array $widths): string
    {
        $srcset = [];
        foreach ($widths as $w) {
            $srcset[] = asset($src) . " {$w}w";
        }
        return implode(', ', $srcset);
    }

    /**
     * Fallback srcset
     */
    public static function fallbackSrcset(array $widths = [640, 1280, 1920]): string
    {
        static $cached = null;

        if ($cached !== null) {
            return $cached;
        }

        try {
            $fallbackRelative = 'img/noimage.webp';
            $fallbackAbsolute = public_path($fallbackRelative);

            if (!File::exists($fallbackAbsolute)) {
                return $cached = asset($fallbackRelative);
            }

            $cacheDir = public_path('cache/images');
            File::ensureDirectoryExists($cacheDir);

            $version = @filemtime($fallbackAbsolute) ?: time();
            $base = pathinfo($fallbackAbsolute, PATHINFO_FILENAME);

            $srcset = [];
            foreach ($widths as $w) {
                $cachedName = sprintf('%s-w%s-v%s.webp', $base, $w, $version);
                $cachedPath = $cacheDir . DIRECTORY_SEPARATOR . $cachedName;

                if (!File::exists($cachedPath)) {
                    $img = self::manager()->read($fallbackAbsolute);
                    $img->scaleDown(width: $w);
                    $img->toWebp(quality: 75)->save($cachedPath);
                }

                $srcset[] = asset('cache/images/' . $cachedName) . " {$w}w";
            }

            return $cached = implode(', ', $srcset);
        } catch (\Throwable $e) {
            return $cached = asset('img/noimage.webp');
        }
    }

    protected static function resolvePath(string $src): array
    {
        $src = trim($src);

        if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
            $parsed = parse_url($src);
            $src = isset($parsed['path']) ? ltrim($parsed['path'], '/') : '';
        }

        $src = ltrim($src, '/');
        $absolute = public_path($src);

        if (File::exists($absolute)) {
            return [$absolute, $src];
        }

        if (str_starts_with($src, 'storage/')) {
            $maybe = storage_path('app/public/' . substr($src, strlen('storage/')));
            if (File::exists($maybe)) {
                return [$maybe, $src];
            }
        }

        return [File::exists($absolute) ? $absolute : null, $src ?: null];
    }
}
