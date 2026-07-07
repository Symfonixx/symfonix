<?php

namespace Modules\Project\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ProjectAttachmentFile implements ValidationRule
{
    private const DISALLOWED_EXTENSIONS = [
        'pdf',
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'ico', 'heic', 'heif', 'tif', 'tiff', 'avif',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            return;
        }

        $extension = strtolower($value->getClientOriginalExtension());
        $mime = strtolower($value->getMimeType() ?? '');

        if ($extension === 'pdf' || $mime === 'application/pdf') {
            $fail(__('project::project.validation.attachment_pdf_not_allowed'));

            return;
        }

        if (str_starts_with($mime, 'image/') || in_array($extension, self::DISALLOWED_EXTENSIONS, true)) {
            $fail(__('project::project.validation.attachment_image_not_allowed'));
        }
    }
}
