<?php

namespace CrunchzApp\Base;

use CrunchzApp\Http\Client;
use InvalidArgumentException;
use RuntimeException;

/**
 * Base class for all services.
 *
 * This class provides common functionality for all services, such as handling the HTTP client
 * and managing the contact ID.
 */
abstract class BaseService
{
    /**
     * The HTTP client instance.
     *
     * @var Client
     */
    protected Client $client;

    /**
     * The payload for the request.
     *
     * @var array
     */
    protected array $payload = [];

    /**
     * The contact ID for the request.
     *
     * @var string|null
     */
    protected ?string $contactId = null;

    /**
     * Create a new BaseService instance.
     *
     * @param Client $client The HTTP client instance.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Set the contact ID for the request.
     *
     * @param string $contactId The contact ID to use for the request.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the contact ID is empty.
     */
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
