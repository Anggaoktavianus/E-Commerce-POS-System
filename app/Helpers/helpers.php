<?php

use Illuminate\Support\Facades\File;

if (!function_exists('formatFileSize')) {
    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    function formatFileSize($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

if (!function_exists('sanitizeFileName')) {
    /**
     * Sanitize file name
     *
     * @param string $filename
     * @return string
     */
    function sanitizeFileName($filename)
    {
        // Remove anything which isn't a word, whitespace, number
        // or any of the following characters -_~,;[]().
        $filename = preg_replace('([^\w\s\d\-\_\.\(\)\[\]~\(\),;])', '', $filename);
        // Remove any runs of periods
        $filename = preg_replace('([\.]{2,})', '', $filename);
        
        return $filename;
    }
}

if (!function_exists('encode_id')) {
    /**
     * Encode integer ID to URL-safe string
     */
    function encode_id(int $id): string
    {
        return rtrim(strtr(base64_encode((string) $id), '+/', '-_'), '=');
    }
}

if (!function_exists('decode_id')) {
    /**
     * Decode URL-safe string back to integer ID
     */
    function decode_id(string $hash): ?int
    {
        $hash = strtr($hash, '-_', '+/');
        $decoded = base64_decode($hash, true);
        if ($decoded === false || !ctype_digit($decoded)) {
            return null;
        }
        return (int) $decoded;
    }
}
