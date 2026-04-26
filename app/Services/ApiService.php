<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ApiService
{
    protected string $baseUrl;

    protected string $version;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('votix_api.base_url'), '/');
        $this->version = trim(config('votix_api.version'), '/');
    }

    /**
     * Build full API URL: {base_url}/{path_prefix}/{version}/{uri}
     */
    public function buildUrl(string $uri, array $queryParams = []): string
    {
        $prefix = trim(config('votix_api.path_prefix', 'api'), '/');
        $path = '/' . $this->version . '/' . ltrim($uri, '/');
        $url = rtrim($this->baseUrl, '/') . '/' . ($prefix ? $prefix : '') . $path;
        if (! empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }

    /**
     * Get Authorization header value (Bearer token). Uses client token or user token.
     */
    protected function getAuthorizationHeader(bool $useClientToken): ?string
    {
        if ($useClientToken) {
            $token = $this->getClientToken();
        } else {
            $token = $this->getUserToken();
            if (! $token) {
                $token = $this->refreshUserToken();
            }
        }

        return $token ? 'Bearer ' . $token : null;
    }

    /**
     * Obtain and cache OAuth2 client_credentials access token.
     */
    public function getClientToken(): ?string
    {
        $key = config('votix_api.client_token_cache_key');
        $ttl = config('votix_api.client_token_cache_ttl', 3600);

        return Cache::remember($key, $ttl, function () {
            $clientId = trim((string) config('votix_api.client_id'));
            $clientSecret = trim((string) config('votix_api.client_secret'));
            if ($clientId === '' || $clientSecret === '') {
                return null;
            }

            $url = $this->buildUrl(config('votix_api.token_endpoint'), []);
            // Token endpoint is usually without version prefix; buildUrl adds version. Use base_url + token_endpoint if needed.
            $tokenEndpoint = config('votix_api.token_endpoint');
            $url = $this->baseUrl . '/' . ltrim($tokenEndpoint, '/');

            $response = Http::asForm()
                ->timeout(config('votix_api.timeout', 30))
                ->post($url, [
                    'grant_type' => 'client_credentials',
                    'client_id' => config('votix_api.client_id'),
                    'client_secret' => config('votix_api.client_secret'),
                ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            return $data['access_token'] ?? null;
        });
    }

    /**
     * Get user access token from session.
     */
    public function getUserToken(): ?string
    {
        return Session::get(config('votix_api.session_access_token_key'));
    }

    /**
     * Refresh user token using refresh_token and store new tokens in session.
     */
    public function refreshUserToken(): ?string
    {
        $refreshToken = Session::get(config('votix_api.session_refresh_token_key'));
        if (! $refreshToken) {
            return null;
        }

        $refreshEndpoint = config('votix_api.refresh_endpoint');
        $url = $this->baseUrl . '/' . ltrim($refreshEndpoint, '/');

        $response = Http::asForm()
            ->timeout(config('votix_api.timeout', 30))
            ->post($url, [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => config('votix_api.client_id'),
                'client_secret' => config('votix_api.client_secret'),
            ]);

        if (! $response->successful()) {
            Session::forget([
                config('votix_api.session_access_token_key'),
                config('votix_api.session_refresh_token_key'),
                config('votix_api.session_token_expires_at_key'),
            ]);
            return null;
        }

        $data = $response->json();
        $accessToken = $data['access_token'] ?? null;
        $newRefresh = $data['refresh_token'] ?? $refreshToken;
        $expiresIn = $data['expires_in'] ?? 3600;

        if ($accessToken) {
            Session::put(config('votix_api.session_access_token_key'), $accessToken);
            Session::put(config('votix_api.session_refresh_token_key'), $newRefresh);
            Session::put(config('votix_api.session_token_expires_at_key'), now()->addSeconds($expiresIn)->timestamp);
        }

        return $accessToken;
    }

    /**
     * Store user tokens in session (after login).
     */
    public function setUserTokens(string $accessToken, string $refreshToken, ?int $expiresIn = 3600): void
    {
        Session::put(config('votix_api.session_access_token_key'), $accessToken);
        Session::put(config('votix_api.session_refresh_token_key'), $refreshToken);
        Session::put(config('votix_api.session_token_expires_at_key'), now()->addSeconds($expiresIn)->timestamp);
    }

    /**
     * Clear user tokens (logout).
     */
    public function clearUserTokens(): void
    {
        Session::forget([
            config('votix_api.session_access_token_key'),
            config('votix_api.session_refresh_token_key'),
            config('votix_api.session_token_expires_at_key'),
        ]);
    }

    /**
     * GET request. Returns data (or nested key) or [] on failure.
     *
     * @param  array<string, mixed>  $queryParams
     * @param  array<string, string>  $headers
     * @return array<int|string, mixed>
     */
    public function getData(
        string $uri,
        array $queryParams = [],
        bool $returnNestedKeyValue = false,
        string $nestedKey = 'items',
        bool $useClientToken = true,
        array $headers = []
    ): array {
        $response = $this->makeApiRequest('GET', $uri, ['query' => $queryParams] + ($headers ? ['headers' => $headers] : []), $useClientToken);

        if (! $response->successful()) {
            return [];
        }

        $json = $response->json();
        if (! ($json['success'] ?? true)) {
            return [];
        }

        $data = $json['data'] ?? [];
        if ($returnNestedKeyValue && is_array($data) && isset($data[$nestedKey])) {
            return $data[$nestedKey];
        }

        return is_array($data) ? $data : [];
    }

    /**
     * GET request for paginated list. Returns LengthAwarePaginator.
     *
     * @param  array<string, mixed>  $queryParams
     * @return LengthAwarePaginator<mixed>
     */
    public function getPaginateData(
        string $uri,
        array $queryParams = [],
        string $itemsKey = 'items',
        bool $useClientToken = true,
        array $headers = []
    ): LengthAwarePaginator {
        $response = $this->makeApiRequest('GET', $uri, ['query' => $queryParams] + ($headers ? ['headers' => $headers] : []), $useClientToken);

        $items = [];
        $total = 0;
        $perPage = 15;
        $currentPage = 1;

        if ($response->successful()) {
            $json = $response->json();
            $data = $json['data'] ?? [];
            if (is_array($data)) {
                $items = $data[$itemsKey] ?? [];
                $total = (int) ($data['total'] ?? 0);
                $perPage = (int) ($data['per_page'] ?? 15);
                $currentPage = (int) ($data['current_page'] ?? 1);
            }
        }

        return new LengthAwarePaginator(
            $items,
            $total,
            $perPage ?: 15,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }

    /**
     * POST with JSON body. Returns data or [] on failure.
     *
     * @param  array<string, mixed>  $data
     * @return array<int|string, mixed>
     */
    public function sendData(string $uri, array $data = [], bool $useClientToken = true): array
    {
        $response = $this->makeApiRequest('POST', $uri, ['json' => $data], $useClientToken);

        if (! $response->successful()) {
            return [];
        }

        $json = $response->json();
        if (! ($json['success'] ?? true)) {
            return [];
        }

        $payload = $json['data'] ?? [];
        return is_array($payload) ? $payload : [];
    }

    /**
     * Generic HTTP request. Returns Laravel HTTP Response.
     *
     * @param  array<string, mixed>  $options  e.g. ['json' => $payload], ['query' => $params], ['headers' => [...]]
     */
    public function makeApiRequest(string $method, string $uri, array $options = [], bool $useClientToken = true): Response
    {
        $query = $options['query'] ?? [];
        $url = $this->buildUrl($uri, $query);

        $request = Http::timeout(config('votix_api.timeout', 30))
            ->withHeaders($options['headers'] ?? []);

        $bearer = $this->getAuthorizationHeader($useClientToken);
        if ($bearer) {
            $request = $request->withToken(str_replace('Bearer ', '', $bearer));
        }

        $method = strtoupper($method);
        $bodyOptions = array_diff_key($options, array_flip(['query', 'headers']));

        $body = [];
        if (isset($bodyOptions['json'])) {
            $request = $request->asJson();
            $body = $bodyOptions['json'];
        } elseif (! empty($bodyOptions)) {
            $body = $bodyOptions;
        }

        $httpMethod = strtolower($method);
        if ($method === 'GET' || $method === 'HEAD') {
            return $request->{$httpMethod}($url);
        }

        return $request->{$httpMethod}($url, $body);
    }

    /**
     * Multipart/form-data request (e.g. file upload). Use buildMultipart() for parts.
     *
     * @param  array<int, array{name: string, contents: string|resource|\Psr\Http\Message\StreamInterface}>  $partsArray
     */
    public function makeMultipartRequest(string $method, string $uri, array $partsArray, bool $useClientToken = true): Response
    {
        $url = $this->buildUrl($uri);

        $request = Http::timeout(config('votix_api.timeout', 30));

        $bearer = $this->getAuthorizationHeader($useClientToken);
        if ($bearer) {
            $request = $request->withToken(str_replace('Bearer ', '', $bearer));
        }

        $request = $request->asMultipart();
        $method = strtolower($method);

        return $request->{$method}($url, $partsArray);
    }

    /**
     * Build multipart parts from fields and files for makeMultipartRequest().
     *
     * @param  array<string, mixed>  $fields
     * @param  array<string, \Illuminate\Http\UploadedFile|null>  $files
     * @return array<int, array{name: string, contents: string|resource}>
     */
    public static function buildMultipart(array $fields, array $files = []): array
    {
        $parts = [];

        foreach ($fields as $name => $value) {
            if (is_bool($value)) {
                $value = self::boolToString($value);
            }
            $parts[] = [
                'name' => $name,
                'contents' => (string) $value,
            ];
        }

        foreach ($files as $name => $file) {
            if ($file && $file->isValid()) {
                $parts[] = [
                    'name' => $name,
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                ];
            }
        }

        return $parts;
    }

    /**
     * Convert boolean to string 'true' / 'false' for API.
     */
    public static function boolToString(mixed $value): string
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false';
    }

    /**
     * Invalidate cached client token (e.g. when credentials change).
     */
    public function forgetClientToken(): void
    {
        Cache::forget(config('votix_api.client_token_cache_key'));
    }
}
