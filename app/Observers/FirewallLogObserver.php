<?php

namespace App\Observers;

use Akaunting\Firewall\Models\Log;

class FirewallLogObserver
{
    /**
     * Handle the Log "creating" event.
     * Ensure request data is properly UTF-8 encoded before saving.
     */
    public function creating(Log $log): void
    {
        if (isset($log->request) && !empty($log->request)) {
            // Ensure the request field is properly UTF-8 encoded
            if (!mb_check_encoding($log->request, 'UTF-8')) {
                $log->request = mb_convert_encoding($log->request, 'UTF-8', 'auto');
            }
            // Ensure it's valid UTF-8
            $log->request = mb_convert_encoding($log->request, 'UTF-8', 'UTF-8');
        }
        
        // Also fix other text fields
        if (isset($log->url) && !empty($log->url)) {
            if (!mb_check_encoding($log->url, 'UTF-8')) {
                $log->url = mb_convert_encoding($log->url, 'UTF-8', 'auto');
            }
        }
        
        if (isset($log->referrer) && !empty($log->referrer)) {
            if (!mb_check_encoding($log->referrer, 'UTF-8')) {
                $log->referrer = mb_convert_encoding($log->referrer, 'UTF-8', 'auto');
            }
        }
    }

    /**
     * Handle the Log "updating" event.
     */
    public function updating(Log $log): void
    {
        $this->creating($log);
    }
}
