<?php

namespace Modules\Core\Traits;

use Throwable;

/**
 * Helper trait for executing repository operations with exception  handling.
 */
trait ExceptionHandlerTrait
{
    public function execute(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (Throwable $throwable) {
            $msg = env('APP_DEBUG') ? $throwable->getMessage() : __('An Error Occurred!');
            session()->flushMessage(false, $msg, $throwable);

            return null;
        }
    }
}
