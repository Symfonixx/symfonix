<?php

/**
 * One-off helper: convert key public homepage images to WebP.
 * Run: php scripts/convert-home-images-webp.php
 */

$files = [
    __DIR__.'/../public/images/home/about_us.jpg',
    __DIR__.'/../public/images/home/why_choose_us.jpg',
    __DIR__.'/../public/images/home/banner-bg.jpg',
    __DIR__.'/../public/images/home/website.png',
    __DIR__.'/../public/images/home/app-development.png',
    __DIR__.'/../public/images/home/microchip.png',
    __DIR__.'/../public/images/home/cloud.png',
];

if (! function_exists('imagewebp')) {
    fwrite(STDERR, "GD WebP support is not available.\n");
    exit(1);
}

foreach ($files as $path) {
    if (! is_file($path)) {
        echo "Skip (missing): {$path}\n";
        continue;
    }

    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    $image = match ($ext) {
        'jpg', 'jpeg' => @imagecreatefromjpeg($path),
        'png' => @imagecreatefrompng($path),
        'gif' => @imagecreatefromgif($path),
        'webp' => @imagecreatefromwebp($path),
        default => false,
    };

    if (! $image) {
        echo "Skip (unreadable): {$path}\n";
        continue;
    }

    if ($ext === 'png') {
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
    }

    $dest = preg_replace('/\.(jpe?g|png|gif)$/i', '.webp', $path);
    $ok = imagewebp($image, $dest, 82);
    imagedestroy($image);

    echo ($ok ? 'OK  ' : 'ERR ').$dest.' ('.(is_file($dest) ? filesize($dest) : 0)." bytes)\n";
}
