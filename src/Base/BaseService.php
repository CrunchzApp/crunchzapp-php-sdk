<?php

namespace CrunchzApp\Base;

use CrunchzApp\Http\Client;
use InvalidArgumentException;
use RuntimeException;

abstract class BaseService
{
    protected Client $client;
    protected array $payload = [];
    protected ?string $contactId = null;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function contact(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }

        $this->contactId = trim($contactId);
        return $this;
    }

    protected function validateContactId(): void
    {
        if (empty($this->contactId)) {
            throw new RuntimeException('Contact ID must be set before performing this operation.');
        }
    }

    protected function addPayload(string $method, string $path, array $body): static
    {
        $this->payload[] = [
            'method' => $method,
            'path' => $path,
            'body' => $body
        ];

        return $this;
    }
}
