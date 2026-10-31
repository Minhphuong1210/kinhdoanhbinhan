<?php

return [
    // Tài khoản trung gian: bị trừ vi_tien khi khách nạp tiền qua PayOS
    'trung_gian_id' => (int) env('PAYOS_TRUNG_GIAN_ID', 22205),

    'payos' => [
        'api_url'        => 'https://api-merchant.payos.vn/v2/payment-requests',
        'client_id'      => env('PAYOS_CLIENT_ID'),
        'api_key'        => env('PAYOS_API_KEY'),
        'checksum_key'   => env('PAYOS_CHECKSUM_KEY'),
        'expire_minutes' => 10,
    ],
];
