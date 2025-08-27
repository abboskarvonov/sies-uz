@props([
    'src' => null,
    'class' => '',
    'alt' => null,
    'lazy' => true,
    'width' => null,
    'height' => null,
    'srcset' => null, // manual berilsa – shu ishlatiladi
    'sizes' => '(max-width: 640px) 100vw, 640px',
    'fallback' => 'img/noimage.webp',
])

@php
    use App\Helpers\ImageHelper;
    use Illuminate\Support\Str;

    // 1) SRC normalize
    $rawSrc = $src ?: $fallback;

    $isAbsolute = Str::startsWith($rawSrc, ['http://', 'https://']);
    $finalSrc = $isAbsolute ? $rawSrc : asset(ltrim($rawSrc, '/'));

    // 2) Alt matn: parametr bo‘lmasa, fayl nomidan
    $altText = $alt;
    if (is_null($altText)) {
        $base = pathinfo(parse_url($finalSrc, PHP_URL_PATH) ?? '', PATHINFO_FILENAME);
        $altText = Str::of($base)
            ->replace(['-', '_'], ' ')
            ->squish()
            ->ucfirst();
    }

    // 3) Kengaytma
    $pathForExt = parse_url($finalSrc, PHP_URL_PATH) ?? '';
    $ext = strtolower(pathinfo($pathForExt, PATHINFO_EXTENSION));

    // 4) SVG/GIF uchun srcset yasalmasin
    $isVectorOrAnimated = in_array($ext, ['svg', 'gif'], true);

    // 5) Agar srcset berilmagan va rasm mos bo‘lsa → helperdan avtomatik
    $autoSrcset = $srcset;
    if (!$autoSrcset && !$isVectorOrAnimated) {
        // ImageHelper lokal yo‘l kutadi: absolute bo‘lsa path’ni uzatamiz,
        // bo‘lmasa xuddi sen uzatayotgan nisbiy yo‘lni beramiz.
        $helperInput = $isAbsolute ? ltrim(parse_url($finalSrc, PHP_URL_PATH) ?? '', '/') : ltrim($rawSrc, '/');

        $autoSrcset = ImageHelper::generateSrcset($helperInput);
    }

    // 6) Lazy / fetchpriority
    $loadingAttr = $lazy ? 'lazy' : 'eager';
    $fetchPriority = $lazy ? 'auto' : 'high';
@endphp

<img src="{{ $finalSrc }}" alt="{{ $altText }}" decoding="async" referrerpolicy="no-referrer"
    loading="{{ $loadingAttr }}" fetchpriority="{{ $fetchPriority }}"
    @if ($width) width="{{ $width }}" @endif
    @if ($height) height="{{ $height }}" @endif
    @if (!$isVectorOrAnimated && $autoSrcset) srcset="{{ $autoSrcset }}" sizes="{{ $sizes }}" @endif
    class="{{ $class }}"
    onerror="this.onerror=null;this.src='{{ asset($fallback) }}';this.removeAttribute('srcset');" />
