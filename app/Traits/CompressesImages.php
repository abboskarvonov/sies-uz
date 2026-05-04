<?php

namespace App\Traits;

use App\Services\ImageCompressionService;

/**
 * Automatically compresses uploaded images > 300 KB after model save.
 *
 * Models using this trait must define:
 *   protected array $compressibleImageFields = ['image'];
 *   // Optionally override disk (default: 'public'):
 *   protected string $compressibleImageDisk = 'public';
 *
 * Array-cast image fields (e.g. images JSON column) are supported —
 * each path in the array is checked and compressed individually.
 */
trait CompressesImages
{
    public static function bootCompressesImages(): void
    {
        static::saved(function (self $model) {
            /** @var string[] $fields */
            $fields = $model->compressibleImageFields ?? [];
            $disk = $model->compressibleImageDisk ?? 'public';
            $isNew = $model->wasRecentlyCreated;

            foreach ($fields as $field) {
                if (!$isNew && !$model->wasChanged($field)) continue;

                $value = $model->getAttribute($field);
                if (!$value) continue;

                if (is_array($value)) {
                    foreach ($value as $path) {
                        if (is_string($path) && $path) {
                            ImageCompressionService::compressIfNeeded($disk, $path);
                        }
                    }
                } else {
                    ImageCompressionService::compressIfNeeded($disk, (string) $value);
                }
            }
        });
    }
}
