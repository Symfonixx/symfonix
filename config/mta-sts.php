<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MTA-STS Policy
    |--------------------------------------------------------------------------
    |
    | Served at https://mta-sts.{domain}/.well-known/mta-sts.txt
    |
    | Also add this DNS TXT record at your provider (required for enforcement):
    |   Host: _mta-sts.{domain}
    |   Value: v=STSv1; id={MTA_STS_DNS_ID}
    |
    | Point mta-sts.{domain} (A or CNAME) to this application host.
    |
    */

    'mode' => env('MTA_STS_MODE', 'enforce'),

    'max_age' => (int) env('MTA_STS_MAX_AGE', 86400),

    'mx' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('MTA_STS_MX', 'mail.symfonix.io'))
    ))),

    'dns_id' => env('MTA_STS_DNS_ID', '2026072701'),

];
