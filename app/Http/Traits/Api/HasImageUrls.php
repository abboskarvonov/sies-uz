<?php

namespace App\Http\Traits\Api;

trait HasImageUrls
{
    protected function imageUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $path = ltrim($path, '/');

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/' . $path);
    }

    protected function imageUrls(mixed $images): array
    {
        if (is_string($images)) {
            $decoded = json_decode($images, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $decoded = json_decode(stripslashes($images), true);
            }
            $images = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($images)) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn($img) => $this->imageUrl($img),
            $images
        )));
    }
}
