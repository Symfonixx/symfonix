<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocsController extends Controller
{
    /**
     * Serve a file from the static documentation directory.
     */
    public function __invoke(Request $request, ?string $path = null): BinaryFileResponse|RedirectResponse|Response
    {
        $docsRoot = realpath(config('docs.path'));

        if ($docsRoot === false || ! is_dir($docsRoot)) {
            abort(404);
        }

        $relative = $this->normalizePath($path);
        $candidate = $docsRoot.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
        $resolved = realpath($candidate);

        if (! $this->isInsideDocs($resolved, $docsRoot)) {
            abort(404);
        }

        // Directory indexes must be served under a trailing slash so relative
        // assets (style.css, images/…) resolve to /docs/… instead of /.
        // Use RedirectResponse directly — Laravel's redirect()/url() helpers
        // strip trailing slashes via UrlGenerator.
        if (is_dir($resolved)) {
            $requestPath = $request->getPathInfo();

            if (! str_ends_with($requestPath, '/')) {
                $target = $request->getUriForPath(rtrim($requestPath, '/').'/');

                if ($query = $request->getQueryString()) {
                    $target .= '?'.$query;
                }

                return new RedirectResponse($target);
            }

            $resolved = realpath($resolved.DIRECTORY_SEPARATOR.'index.html');
        }

        if ($resolved === false || ! is_file($resolved) || ! $this->isInsideDocs($resolved, $docsRoot)) {
            abort(404);
        }

        $response = response()->file($resolved);
        $response->headers->set('Content-Type', $this->mimeType($resolved));

        return $response;
    }

    private function normalizePath(?string $path): string
    {
        $path = trim((string) $path, '/');

        if ($path === '' || $path === '.') {
            return '.';
        }

        $path = str_replace('\\', '/', $path);
        $segments = array_values(array_filter(explode('/', $path), static fn (string $segment) => $segment !== '' && $segment !== '.'));

        foreach ($segments as $segment) {
            if ($segment === '..') {
                abort(404);
            }
        }

        return implode('/', $segments);
    }

    private function isInsideDocs(string|false $resolved, string $docsRoot): bool
    {
        return $resolved === $docsRoot
            || ($resolved !== false && str_starts_with($resolved, $docsRoot.DIRECTORY_SEPARATOR));
    }

    private function mimeType(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'css' => 'text/css; charset=utf-8',
            'js', 'mjs' => 'application/javascript; charset=utf-8',
            'html', 'htm' => 'text/html; charset=utf-8',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'json' => 'application/json',
            'pdf' => 'application/pdf',
            'xml' => 'application/xml',
            'txt' => 'text/plain; charset=utf-8',
            default => (function_exists('mime_content_type') ? mime_content_type($path) : false) ?: 'application/octet-stream',
        };
    }
}
