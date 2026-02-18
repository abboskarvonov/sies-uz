<?php

namespace App\Http\Traits\Api;

use Illuminate\Database\Eloquent\Model;

trait HasLocalizedFields
{
    protected function localizedField(Model $model, string $field): ?string
    {
        $locale = app()->getLocale();
        return $model->{"{$field}_{$locale}"} ?? $model->{"{$field}_uz"};
    }

    protected function allLocalizedFields(Model $model, string $field): array
    {
        return [
            "{$field}_uz" => $model->{"{$field}_uz"},
            "{$field}_ru" => $model->{"{$field}_ru"},
            "{$field}_en" => $model->{"{$field}_en"},
        ];
    }
}
