<?php

namespace CrunchzApp\Http;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Illuminate\Http\Client\PendingRequest;

/**
 * HTTP client for making requests to the CrunchzApp API.
 *
 * This class is a wrapper around Laravel's Http client and handles authentication,
 * request sending, and error handling.
 */
class Client
{
    /**
     * The underlying HTTP client instance.
     *
     * @var PendingRequest
     */
    private PendingRequest $client;

    /**
     * The API token used for authentication.
     *
     * @var string
     */
    private string $token;

    /**
     * The base URL for the API endpoint.
     *
     * @var string
     */
    private string $endpoint = 'https://api.crunchz.app/api';

    /**
     * Create a new Client instance.
     *
     * @param string|null $token The API token. If not provided, it will be taken from the config.
     * @throws RuntimeException If the token is not provided and cannot be found in the config.
     */
    public function __construct(?string $token = null)
    {
        $this->token = $token ?? config('crunchzapp.token');

        if (empty($this->token)) {
            throw new RuntimeException('CrunchzApp token is required. Please set CRUNCHZAPP_TOKEN in your environment.');
        }

        $this->client = Http::baseUrl($this->endpoint)->withToken($this->token);
    }

    /**
     * Send a GET request to the API.
     *
     * @param string $path The API path to request.
     * @param array $query The query parameters to send with the request.
     * @return array The JSON response from the API.
     * @throws RuntimeException If the API request fails.
     */
    public function get(string $path, array $query = []): array
    {
        return $this->send('get', $path, $query);
    }

    /**
     * Send a POST request to the API.
     *
     * @param string $path The API path to request.
     * @param array $data The data to send with the request.
     * @return array The JSON response from the API.
     * @throws RuntimeException If the API request fails.
     */
    public function post(string $path, array $data = []): array
    {
        return $this->send('post', $path, $data);
    }

    /**
     * Send a PUT request to the API.
     *
     * @param string $path The API path to request.
     * @param array $data The data to send with the request.
     * @return array The JSON response from the API.
     * @throws RuntimeException If the API request fails.
     */
    public function put(string $path, array $data = []): array
    {
        return $this->send('put', $path, $data);
    }

    /**
     * Send a DELETE request to the API.
     *
     * @param string $path The API path to request.
     * @param array $data The data to send with the request.
     * @return array The JSON response from the API.
     * @throws RuntimeException If the API request fails.
     */
    public function delete(string $path, array $data = []): array
    {
        return $this->send('delete', $path, $data);
    }

    /**
     * Send a request to the API.
     *
     * @param string $method The HTTP method to use (get, post, put, delete).
     * @param string $path The API path to request.
     * @param array $data The data to send with the request.
     * @return array The JSON response from the API.
     * @throws RuntimeException If the API request fails.
     */
    public function send(string $method, string $path, array $data = []): array
    {
        $response = $this->client->{$method}($path, $data);

        if (!$response->successful()) {
            throw new RuntimeException('API request failed with status ' . $response->status() . ': ' . $response->body());
        }

        return $response->json() ?? [];
    }

    /**
     * Send multiple requests to the API in parallel.
     *
     * @param array $payloads An array of payloads to send. Each payload should be an array with 'method', 'path', and 'body' keys.
     * @return array An array of responses from the API.
     * @throws RuntimeException If the pool request fails or any of the individual requests fail.
     */
    public function sendPool(array $payloads): array
    {
        if (count($payloads) < 2) {
            throw new RuntimeException('Pool requests require at least 2 payloads.');
        }

        try {
            $responses = Http::pool(function (Pool $pool) use ($payloads) {
                foreach ($payloads as $payload) {
                    $method = strtolower($payload['method']);
                    $pool->withToken($this->token)->{$method}($this->endpoint . $payload['path'], $payload['body']);
                }
            });

            return $this->processPoolResponses($responses, $payloads);
        } catch (\Exception $e) {
            throw new RuntimeException('Failed to send pool requests: ' . $e->getMessage(), 0, $e);
        }
    }

    private function processPoolResponses(array $responses, array $payloads): array
    {
        $results = [];

        foreach ($responses as $key => $response) {
            if (!$response->successful()) {
                throw new RuntimeException(
                    sprintf('Request %d failed with status %d: %s', $key, $response->status(), $response->body())
                );
            }

            $payload = $payloads[$key];
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
