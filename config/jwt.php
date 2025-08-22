<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Authentication Settings
    |--------------------------------------------------------------------------
    |
    | Configure as configurações da autenticação JWT
    |
    */

    'secret' => env('JWT_SECRET'),

    'ttl' => env('JWT_TTL', 3600),

    'refresh_ttl' => env('JWT_REFRESH_TTL', 86400),

    'algo' => env('JWT_ALGO', 'HS256'),
];
