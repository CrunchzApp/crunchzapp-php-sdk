<?php

namespace CrunchzApp\Traits;

use InvalidArgumentException;
use RuntimeException;

trait ContactTrait
{
    private const CONTACT_ACTION_ALL = 'all';
    private const CONTACT_ACTION_DETAIL = 'detail';
    private const CONTACT_ACTION_PICTURE = 'picture';

    /**
     * Get all contacts
     *
     * @return static
     * @throws RuntimeException When token is missing
     */
    public function allContact(): static
    {
        return $this->addContactPayload('', [], 'GET');
    }

    /**
     * Get contact details
     *
     * @param string $contactId The contact ID
     * @return static
     * @throws InvalidArgumentException When contact ID is invalid
     * @throws RuntimeException When token is missing
     */
    public function detail(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }

        return $this->addContactPayload(self::CONTACT_ACTION_DETAIL, [
            'contact_id' => $contactId
        ], 'GET');
    }

    /**
     * Get contact picture
     *
     * @param string $contactId The contact ID
     * @return static
     * @throws InvalidArgumentException When contact ID is invalid
     * @throws RuntimeException When token is missing
     */
    public function picture(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }

        return $this->addContactPayload(self::CONTACT_ACTION_PICTURE, [
            'contact_id' => $contactId
        ], 'GET');
    }

    /**
     * Add a contact payload to the request queue
     *
     * @param string $action The contact action
     * @param array $body The request body
     * @param string $method The HTTP method
     * @return static
     * @throws RuntimeException When token is missing
     */
    private function addContactPayload(string $action, array $body, string $method = 'GET'): static
    {
        $this->validateToken();

        $this->payload[] = [
            'method' => $method,
            'path' => '/contact/' . $action,
            'body' => $body
        ];

        return $this;
    }
}
