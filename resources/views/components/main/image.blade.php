@props([
    'src' => null,
    'class' => '',
    'alt' => null,
    'lazy' => true,
    'width' => null,
    'height' => null,
    'srcset' => null,
    'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 640px',
    'fallback' => 'img/noimage.webp',
    'eager' => false, // LCP uchun
    'quality' => 70,
    'widths' => [640, 1024, 1920],
])

@php
    use App\Helpers\ImageHelper;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Cache;

    // eager true bo'lsa lazy false
$lazy = $eager ? false : $lazy;

// 1) SRC normalize va validation
$rawSrc = trim($src ?: $fallback);

if (empty($rawSrc)) {
    $rawSrc = $fallback;
}

$isAbsolute = Str::startsWith($rawSrc, ['http://', 'https://']);

// XSS himoya: javascript: va data: URL'larni bloklash
    if (Str::startsWith(strtolower($rawSrc), ['javascript:', 'data:'])) {
        $rawSrc = $fallback;
        $isAbsolute = false;
    }

    $finalSrc = $isAbsolute ? $rawSrc : asset(ltrim($rawSrc, '/'));

    // 2) Alt matn: SEO uchun muhim
    $altText = $alt;
    if (is_null($altText) || trim($altText) === '') {
        $parsedPath = parse_url($finalSrc, PHP_URL_PATH);
        $base = $parsedPath ? pathinfo($parsedPath, PATHINFO_FILENAME) : 'image';

        $altText = Str::of($base)
            ->replace(['-', '_', '.'], ' ')
            ->squish()
            ->limit(125) // Alt text recommended max length
            ->ucfirst()
            ->toString();

        // Agar hali ham bo'sh bo'lsa
        if (empty($altText)) {
            $altText = 'Image';
        }
    } else {
        // Alt text sanitize
        $altText = strip_tags($altText);
        $altText = Str::limit($altText, 125);
    }

    // 3) Kengaytma tekshirish
    $pathForExt = parse_url($finalSrc, PHP_URL_PATH) ?? '';
    $ext = strtolower(pathinfo($pathForExt, PATHINFO_EXTENSION));

    // 4) SVG/GIF/WEBP animatsiyalari uchun srcset yasalmasin
    $isVectorOrAnimated = in_array($ext, ['svg', 'gif'], true);

    // SVG uchun alohida classlar
    $isSvg = $ext === 'svg';

    // 5) Srcset generatsiya (cache bilan)
    $autoSrcset = $srcset;

    if (!$autoSrcset && !$isVectorOrAnimated && !$isAbsolute) {
        // Cache key yaratish
        $cacheKey = 'srcset:' . md5($rawSrc . implode(',', $widths) . $quality);

        // 1 soat cache
        $autoSrcset = Cache::remember($cacheKey, 3600, function () use ($rawSrc, $widths, $quality) {
            try {
                $helperInput = ltrim($rawSrc, '/');
                return ImageHelper::generateSrcset($helperInput, $widths, $quality);
            } catch (\Throwable $e) {
                \Log::warning('Image srcset generation failed', [
                    'src' => $rawSrc,
                    'error' => $e->getMessage(),
                ]);
                return null;
            }
        });
    }

    // 6) Loading strategiya
    $loadingAttr = $lazy ? 'lazy' : 'eager';
    $fetchPriority = $eager ? 'high' : ($lazy ? 'low' : 'auto');
    $decodingAttr = $eager ? 'sync' : 'async';

    // 7) Width/Height aspect ratio uchun
    $hasAspectRatio = $width && $height;

    // 8) Additional classes
    $additionalClasses = [];
    if ($isSvg) {
        $additionalClasses[] = 'svg-image';
    }
    if (!$hasAspectRatio) {
        $additionalClasses[] = 'aspect-auto';
    }

    $finalClass = trim($class . ' ' . implode(' ', $additionalClasses));

    // 9) Fallback URL escape
    $escapedFallback = e(asset($fallback));
@endphp

<img src="{{ $finalSrc }}" alt="{{ $altText }}" decoding="{{ $decodingAttr }}" loading="{{ $loadingAttr }}"
    fetchpriority="{{ $fetchPriority }}" @if ($width) width="{{ $width }}" @endif
    @if ($height) height="{{ $height }}" @endif
    @if (!$isVectorOrAnimated && $autoSrcset) srcset="{{ $autoSrcset }}" 
        sizes="{{ $sizes }}" @endif
    @if ($finalClass) class="{{ $finalClass }}" @endif
    @unless ($isAbsolute) referrerpolicy="no-referrer" @endunless
    onerror="if(this.src!=='{{ $escapedFallback }}'){this.onerror=null;this.src='{{ $escapedFallback }}';this.removeAttribute('srcset');this.removeAttribute('sizes');}"
    {{ $attributes->except(['src', 'class', 'alt', 'lazy', 'width', 'height', 'srcset', 'sizes', 'fallback', 'eager', 'quality', 'widths']) }} />
