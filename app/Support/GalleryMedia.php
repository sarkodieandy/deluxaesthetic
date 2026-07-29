<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

class GalleryMedia
{
    public static function isRemoteUrl(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        return (bool) filter_var($path, FILTER_VALIDATE_URL);
    }

    public static function isLocalStoredPath(?string $path): bool
    {
        return $path !== null && $path !== '' && ! self::isRemoteUrl($path);
    }

    public static function normalizeUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        return $url !== '' ? $url : null;
    }

    public static function resolvePath(
        ?UploadedFile $upload,
        ?string $urlInput,
        ?string $existing,
        callable $storeUpload
    ): ?string {
        if ($upload) {
            return $storeUpload($upload);
        }

        $url = self::normalizeUrl($urlInput);
        if ($url !== null) {
            return $url;
        }

        return $existing;
    }

    public static function urlFieldValue(?string $stored): string
    {
        return self::isRemoteUrl($stored) ? $stored : '';
    }
}
