<?php

namespace CrunchzApp\Services;

use CrunchzApp\Base\WhatsAppBase;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class ChannelService extends WhatsAppBase
{

    private const MIN_POOL_PAYLOADS = 2;
    private const MAX_SINGLE_PAYLOADS = 1;

    public function __construct()
    {
        $this->client = Http::baseUrl($this->endpoint);
        $this->token = config('crunchzapp.token');

        if (empty($this->token)) {
            throw new RuntimeException('CrunchzApp token is required. Please set CRUNCHZAPP_TOKEN in your environment.');
        }
    }

    /**
     * Send multiple requests in parallel using HTTP pool
     *
     * @return array Array of responses with path, body, and result
     * @throws RuntimeException When token is missing, insufficient payloads, or API request fails
     */
    public function sendPool(): array
    {
        $this->validateToken();
        $this->validatePoolPayloads();

        try {
            $responses = Http::pool(fn(Pool $pool) => $this->buildPoolRequests($pool));
            return $this->processPoolResponses($responses);
        } catch (\Exception $e) {
            throw new RuntimeException('Failed to send pool requests: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Send a single request
     *
     * @return array The API response
     * @throws RuntimeException When token is missing, multiple payloads, or API request fails
     */
    public function send(): array
    {
        $this->validateToken();
        $this->validateSinglePayload();

        try {
            $payload = $this->payload[0];
            $method = strtolower($payload['method']);

            $response = $this->client->withToken($this->token)->{$method}($payload['path'], $payload['body']);

            if (!$response->successful()) {
                throw new RuntimeException('API request failed with status ' . $response->status() . ': ' . $response->body());
            }

            $jsonResponse = $response->json();
            return $jsonResponse ?? [];
        } catch (\Exception $e) {
            throw new RuntimeException('Failed to send request: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Validate payloads for pool requests
     *
     * @throws RuntimeException When insufficient payloads for pool
     */
    private function validatePoolPayloads(): void
    {
        if (count($this->payload) < self::MIN_POOL_PAYLOADS) {
            throw new RuntimeException(
                sprintf('Pool requests require at least %d payloads. Use send() method for single requests.', self::MIN_POOL_PAYLOADS)
            );
        }
    }

    /**
     * Validate payloads for single requests
     *
     * @throws RuntimeException When multiple payloads for single request
     */
    private function validateSinglePayload(): void
    {
        if (count($this->payload) > self::MAX_SINGLE_PAYLOADS) {
            throw new RuntimeException(
                sprintf('Single requests support only %d payload. Use sendPool() method for multiple requests.', self::MAX_SINGLE_PAYLOADS)
            );
        }

        if (empty($this->payload)) {
            throw new RuntimeException('No payload found. Please add at least one operation before sending.');
        }
    }

    /**
     * Build HTTP pool requests
     *
     * @param Pool $pool The HTTP pool instance
     */
    private function buildPoolRequests(Pool $pool): void
    {
        foreach ($this->payload as $payload) {
            $method = strtolower($payload['method']);
            $pool->withToken($this->token)->{$method}($this->endpoint . $payload['path'], $payload['body']);
        }
    }

    /**
     * Process pool responses and format results
     *
     * @param array $responses Array of HTTP responses
     * @return array Formatted response array
     * @throws RuntimeException When any response fails
     */
    private function processPoolResponses(array $responses): array
    {
        $results = [];

        foreach ($responses as $key => $response) {
            /** @var Response $response */
            if (!$response->successful()) {
                throw new RuntimeException(
                    sprintf('Request %d failed with status %d: %s', $key, $response->status(), $response->body())
                );
            }

            $payload = $this->payload[$key];
            $jsonResponse = $response->json();
            $results[$key] = [
                'path' => $payload['path'],
                'body' => $payload['body'],
                'result' => $jsonResponse ?? []
            ];
        }

        return $results;
    }
}
