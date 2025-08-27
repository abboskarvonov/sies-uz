<?php

namespace App\Helpers;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\File;

class ImageHelper
{
    // Bitta manager instance (each-call reinit bo‘lmasin)
    protected static ?ImageManager $manager = null;

    protected static function manager(): ImageManager
    {
        return self::$manager ??= new ImageManager(new Driver());
    }

    /**
     * Responsiv srcset generatsiya (webp’ga)
     *
     * @param string $src      Public ichidagi yo‘l yoki to‘liq URL
     * @param array  $widths   [640,1280,1920] kabi
     * @param int    $quality  1..100
     */
    public static function generateSrcset(string $src, array $widths = [640, 1280, 1920], int $quality = 75): string
    {
        try {
            // 1) Normalize kiruvchi yo‘l
            [$absolutePath, $publicRelative] = self::resolvePath($src);

            // 2) Fayl tekshirish
            if (!$absolutePath || !File::exists($absolutePath)) {
                return self::fallbackSrcset($widths);
            }

            // 3) Kengaytma / mime (GIF, SVG va boshqalarni o‘tkazib yuboramiz)
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
            if (!$ext) {
                // Ba’zan query bilan keladi, mime’dan taxmin qilamiz
                $mime = @File::mimeType($absolutePath) ?: '';
                if (str_contains($mime, 'jpeg')) $ext = 'jpg';
                elseif (str_contains($mime, 'png')) $ext = 'png';
                elseif (str_contains($mime, 'webp')) $ext = 'webp';
            }
            if (!$ext || !in_array($ext, $allowed, true)) {
                return self::fallbackSrcset($widths);
            }

            // 4) Keş katalogi (public/cache/images/…)
            $cacheDir = public_path('cache/images');
            File::ensureDirectoryExists($cacheDir);

            // Unikal versiya: manba mtime asosida
            $version = @filemtime($absolutePath) ?: time();

            $srcset = [];
            foreach ($widths as $w) {
                // cache fayl nomi: original_base-w{w}-q{q}-v{ver}.webp
                $base = pathinfo($absolutePath, PATHINFO_FILENAME);
                $cachedName = sprintf('%s-w%s-q%s-v%s.webp', $base, $w, $quality, $version);
                $cachedPath = $cacheDir . DIRECTORY_SEPARATOR . $cachedName;

                if (!File::exists($cachedPath)) {
                    // Re-encode faqat kerak bo‘lsa
                    $img = self::manager()->read($absolutePath);
                    $img->scaleDown(width: $w);
                    $img->toWebp(quality: $quality)->save($cachedPath);
                }

                $srcset[] = asset('cache/images/' . $cachedName) . " {$w}w";
            }

            return implode(', ', $srcset);
        } catch (\Throwable $e) {
            // Xatoda ham sahifa yiqilmasin
            return self::fallbackSrcset($widths);
        }
    }

    /**
     * Fallback — noimage’dan srcset
     */
    public static function fallbackSrcset(array $widths = [640, 1280, 1920]): string
    {
        try {
            $fallbackRelative = 'img/noimage.webp';
            $fallbackAbsolute = public_path($fallbackRelative);

            if (!File::exists($fallbackAbsolute)) {
                // minimal himoya
                return asset($fallbackRelative);
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

            return implode(', ', $srcset);
        } catch (\Throwable $e) {
            return asset('img/noimage.webp');
        }
    }

    /**
     * Kiruvchi $src ni absolute va public-relative yo‘lga keltirish
     * return [absolutePath|null, publicRelative|null]
     */
    protected static function resolvePath(string $src): array
    {
        $src = trim($src);

        // To‘liq URL bo‘lsa: faqat path qismini olamiz
        if (str_starts_with($src, 'http://') || str_starts_with($src, 'https://')) {
            $parsed = parse_url($src);
            $src = isset($parsed['path']) ? ltrim($parsed['path'], '/') : '';
        }

        // Oldidagi '/' ni olamiz
        $src = ltrim($src, '/');

        // public/storage/… bo‘lsa — shu bilan ishlayveramiz
        $absolute = public_path($src);
        if (File::exists($absolute)) {
            return [$absolute, $src];
        }

        // Agar storage/app/public ichidan kelsa, lekin symlink yo‘q bo‘lsa
        if (str_starts_with($src, 'storage/')) {
            $maybe = storage_path('app/public/' . substr($src, strlen('storage/')));
            if (File::exists($maybe)) {
                return [$maybe, $src];
            }
        }

        // Oxirgi urinish: public_path bilan qaytaramiz (bo‘lmasa exists tekshiruv ushlab qoladi)
        return [File::exists($absolute) ? $absolute : null, $src ?: null];
    }
}
