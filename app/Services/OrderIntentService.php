<?php

namespace App\Services;

use Illuminate\Http\Client\Response;

class OrderIntentService
{
    public function __construct(
        protected ApiService $api
    ) {}

    /**
     * @return array{ok: bool, data: mixed, message: ?string, http: int, raw: array}
     */
    public function parseResponse(Response $response): array
    {
        $json = $response->json() ?? [];

        return [
            'ok' => $response->successful() && (bool) ($json['success'] ?? false),
            'data' => $json['data'] ?? null,
            'message' => is_string($json['message'] ?? null) ? $json['message'] : null,
            'http' => $response->status(),
            'raw' => is_array($json) ? $json : [],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function jsonHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function create(array $payload): array
    {
        $response = $this->api->makeApiRequest(
            'POST',
            'order-intents/create',
            ['json' => $payload, 'headers' => $this->jsonHeaders()],
            false
        );

        return $this->parseResponse($response);
    }

    public function show(string $key, ?int $customerId = null): array
    {
        $query = [];
        if ($customerId !== null) {
            $query['customer_id'] = $customerId;
        }

        $response = $this->api->makeApiRequest(
            'GET',
            "order-intents/{$key}",
            ['query' => $query, 'headers' => $this->jsonHeaders()],
            false
        );

        return $this->parseResponse($response);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function checkout(string $key, array $payload): array
    {
        $response = $this->api->makeApiRequest(
            'POST',
            "order-intents/{$key}/checkout",
            ['json' => $payload, 'headers' => $this->jsonHeaders()],
            false
        );

        return $this->parseResponse($response);
    }

    public function verify(string $key, ?int $customerId = null): array
    {
        $body = [];
        if ($customerId !== null) {
            $body['customer_id'] = $customerId;
        }

        $response = $this->api->makeApiRequest(
            'POST',
            "order-intents/{$key}/verify",
            ['json' => $body, 'headers' => $this->jsonHeaders()],
            false
        );

        return $this->parseResponse($response);
    }

    public function cancel(string $key, ?int $customerId = null): array
    {
        $body = [];
        if ($customerId !== null) {
            $body['customer_id'] = $customerId;
        }

        $response = $this->api->makeApiRequest(
            'POST',
            "order-intents/{$key}/cancel",
            ['json' => $body, 'headers' => $this->jsonHeaders()],
            false
        );

        return $this->parseResponse($response);
    }

    /**
     * @return array{ok: bool, data: array<int|string, mixed>, message: ?string}
     */
    public function paymentMethods(): array
    {
        $response = $this->api->makeApiRequest(
            'GET',
            'payment-methods',
            ['headers' => ['Accept' => 'application/json']],
            false
        );

        $parsed = $this->parseResponse($response);
        $data = $parsed['data'];
        $list = [];
        if (is_array($data) && isset($data['data']) && is_array($data['data'])) {
            $list = $data['data'];
        } elseif (is_array($data) && isset($data[0])) {
            $list = $data;
        }

        return [
            'ok' => $parsed['ok'],
            'data' => is_array($list) ? $list : [],
            'message' => $parsed['message'],
        ];
    }
}
