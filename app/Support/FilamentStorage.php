<?php

namespace App\Support;

class FilamentStorage
{
    /**
     * Public URL for a path stored on the `public` disk (relative path in DB).
     * Uses a root-relative /storage/... URL so Filament previews work even when
     * APP_URL points at a different public domain than the admin host.
     */
    public static function url(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $path = trim($path);
        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) {
            return $path;
        }

        $path = ltrim($path, '/');
        if (str_starts_with($path, 'storage/')) {
            return '/' . $path;
        }

        return '/storage/' . $path;
    }

    /**
     * Closure for Filament FileUpload::getUploadedFileUrlUsing().
     */
    public static function uploadedFileUrl(): \Closure
    {
        return static function ($file) {
            return self::url(is_string($file) ? $file : null);
        };
    }
}
