<?php

return [
    'endpoint' => [
        'active' => true,
        'url' => 'paynow-webhook',
    ],
    'credentials' => [
        'api_key' => env('PAYNOW_API_KEY', '97a55694-5478-43b5-b406-fb49ebfdd2b5'),
        'signature_key' => env('PAYNOW_SIGNATURE_KEY', 'b305b996-bca5-4404-a0b7-2ccea3d2b64b'),
    ],
    /*
     * Protect PayNow models from deletion.
     * Only failed refunds can be deleted.
     * For production environments, set to true.
     * When set to false, you can delete any PayNow model.
     */
    'protect' => env('PAYNOW_PROTECTED', true),
    /*
     * PayNow timeout for transaction
     * Min: 1m, Max: 10d
     * Default: 24h
     * Example: 60m, 1h, 1d, 1w, 10d
     */
    'timeout' => env('PAYNOW_TIMEOUT', '24h'),
];
