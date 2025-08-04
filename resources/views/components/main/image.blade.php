@props([
    'src',
    'class' => '',
    'alt' => null,
    'lazy' => true,
    'width' => null,
    'height' => null,
    'srcset' => null, // agar manual bersang
    'sizes' => '(max-width: 640px) 100vw, 640px',
])

@php
    use App\Helpers\ImageHelper;

    $altText = $alt ?? pathinfo($src, PATHINFO_FILENAME);

    // Agar srcset prop berilmagan bo‘lsa → avtomatik yasaymiz:
    $autoSrcset = $srcset ?? ImageHelper::generateSrcset($src);
@endphp

<img src="{{ asset($src) }}" alt="{{ $altText }}" {{ $lazy ? 'loading=lazy' : '' }}
    {{ $width ? "width={$width}" : '' }} {{ $height ? "height={$height}" : '' }} srcset="{{ $autoSrcset }}"
    sizes="{{ $sizes }}" class="{{ $class }}" />
