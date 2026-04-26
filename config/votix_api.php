<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Base URL (e.g. http://localhost:8000 for {baseUrl}/api/v1/...)
    |--------------------------------------------------------------------------
    */
    'base_url' => env('VOTIX_API_URL', 'http://127.0.0.1:8000'),

    /*
    |--------------------------------------------------------------------------
    | API Path (segment before version, e.g. "api" -> /api/v1/...)
    |--------------------------------------------------------------------------
    */
    'path_prefix' => env('VOTIX_API_PATH_PREFIX', 'api'),

    /*
    |--------------------------------------------------------------------------
    | API Version (prefix for all requests, e.g. /v1/)
    |--------------------------------------------------------------------------
    */
    'version' => env('VOTIX_API_VERSION', 'v1'),

    /*
    |--------------------------------------------------------------------------
    | OAuth2 Client Credentials (for public / unauthenticated API calls)
    |--------------------------------------------------------------------------
    */
    'client_id' => env('VOTIX_API_CLIENT_ID', ''),
    'client_secret' => env('VOTIX_API_CLIENT_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | OAuth2 Token Endpoints
    |--------------------------------------------------------------------------
    */
    'token_endpoint' => env('VOTIX_API_TOKEN_ENDPOINT', 'oauth/token'),
    'refresh_endpoint' => env('VOTIX_API_REFRESH_ENDPOINT', 'oauth/token'),

    /*
    |--------------------------------------------------------------------------
    | Cache key for client access token
    |--------------------------------------------------------------------------
    */
    'client_token_cache_key' => 'votix_api.client_token',
    'client_token_cache_ttl' => 3600, // seconds (1 hour, or less if API token expires sooner)

    /*
    |--------------------------------------------------------------------------
    | Session keys for user tokens (after login)
    |--------------------------------------------------------------------------
    */
    'session_access_token_key' => 'votix_api.access_token',
    'session_refresh_token_key' => 'votix_api.refresh_token',
    'session_token_expires_at_key' => 'votix_api.token_expires_at',
    'session_user_key' => 'votix_api.user',

    /*
    |--------------------------------------------------------------------------
    | HTTP timeout (seconds)
    |--------------------------------------------------------------------------
    */
    'timeout' => env('VOTIX_API_TIMEOUT', 30),

];
