<?php

namespace Modules\Finance\Support;

use ArPHP\I18N\Arabic;

/**
 * DomPDF lacks Arabic bidirectional/shaping support. Shape Arabic runs so they
 * render correctly when the PDF engine lays text out left-to-right.
 */
class DomPdfText
{
    public static function isRtl(): bool
    {
        return app()->getLocale() === 'ar';
    }

    public static function shape(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        if (! static::isRtl() || ! preg_match('/\p{Arabic}/u', $text)) {
            return $text;
        }

        static $arabic = null;
        $arabic ??= new Arabic;

        // Large max line length avoids ar-php inserting wraps mid-cell.
        // Keep Western digits so invoice dates and amounts stay familiar.
        return $arabic->utf8Glyphs($text, 200, false);
    }
}
