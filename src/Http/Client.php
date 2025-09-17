<?php

namespace CrunchzApp\Http;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Illuminate\Http\Client\PendingRequest;

class Client
{
    private PendingRequest $client;
    private string $token;
    private string $endpoint = 'https://api.crunchz.app/api';

    public function __construct(?string $token = null)
    {
        $this->token = $token ?? config('crunchzapp.token');

        if (empty($this->token)) {
            throw new RuntimeException('CrunchzApp token is required. Please set CRUNCHZAPP_TOKEN in your environment.');
        }

        $this->client = Http::baseUrl($this->endpoint)->withToken($this->token);
    }

    public function get(string $path, array $query = []): array
    {
        return $this->send('get', $path, $query);
    }

    public function post(string $path, array $data = []): array
    {
        return $this->send('post', $path, $data);
    }

    public function put(string $path, array $data = []): array
    {
        return $this->send('put', $path, $data);
    }

    public function delete(string $path, array $data = []): array
    {
        return $this->send('delete', $path, $data);
    }

    public function send(string $method, string $path, array $data = []): array
    {
        $response = $this->client->{$method}($path, $data);

        if (!$response->successful()) {
            throw new RuntimeException('API request failed with status ' . $response->status() . ': ' . $response->body());
        }

        return $response->json() ?? [];
    }

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
