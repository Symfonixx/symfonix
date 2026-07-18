<?php

namespace Modules\Core\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DummyImageGenerator
{
    /**
     * @var list<array{0: int, 1: int, 2: int}>
     */
    private const PALETTE = [
        [37, 99, 235],
        [16, 185, 129],
        [245, 158, 11],
        [239, 68, 68],
        [139, 92, 246],
        [14, 165, 233],
        [236, 72, 153],
        [34, 197, 94],
        [99, 102, 241],
        [249, 115, 22],
    ];

    private int $colorIndex = 0;

    /**
     * Create a placeholder JPEG and store it on the public disk.
     */
    public function store(string $directory, string $label, int $width = 800, int $height = 600): string
    {
        $directory = trim($directory, '/');
        $filename = Str::slug(Str::limit($label, 40, '')).'-'.Str::lower(Str::random(8)).'.jpg';
        $path = $directory.'/'.$filename;

        Storage::disk('public')->put($path, $this->renderJpeg($label, $width, $height));

        return $path;
    }

    /**
     * Square avatar-style placeholder.
     */
    public function storeAvatar(string $label, string $directory = 'avatars'): string
    {
        return $this->store($directory, $label, 400, 400);
    }

    private function renderJpeg(string $label, int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);

        [$r, $g, $b] = self::PALETTE[$this->colorIndex % count(self::PALETTE)];
        $this->colorIndex++;

        $background = imagecolorallocate($image, $r, $g, $b);
        $overlay = imagecolorallocatealpha($image, 255, 255, 255, 100);
        $textColor = imagecolorallocate($image, 255, 255, 255);

        imagefilledrectangle($image, 0, 0, $width, $height, $background);
        imagefilledellipse($image, (int) ($width * 0.75), (int) ($height * 0.25), (int) ($width * 0.5), (int) ($height * 0.5), $overlay);
        imagefilledrectangle($image, 0, (int) ($height * 0.72), $width, $height, $overlay);

        $text = Str::limit($label, 42, '…');
        $font = 5;
        $textWidth = imagefontwidth($font) * strlen($text);
        $x = max(16, (int) (($width - $textWidth) / 2));
        $y = (int) ($height * 0.82);

        imagestring($image, $font, $x, $y, $text, $textColor);

        ob_start();
        imagejpeg($image, null, 85);
        $binary = (string) ob_get_clean();
        imagedestroy($image);

        return $binary;
    }
}
