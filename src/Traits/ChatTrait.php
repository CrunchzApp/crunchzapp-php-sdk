<?php

namespace CrunchzApp\Traits;

use InvalidArgumentException;
use RuntimeException;

trait ChatTrait
{
    private const CHAT_ACTION_ALL = 'all';
    private const CHAT_ACTION_DETAIL = 'detail';
    private const CHAT_ACTION_ARCHIVE = 'archive';
    private const CHAT_ACTION_UNARCHIVE = 'unarchive';

    /**
     * Get all chats
     *
     * @return static
     * @throws RuntimeException When token is missing
     */
    public function allChat(): static
    {
        return $this->addChatPayload('', [], 'GET');
    }

    /**
     * Get chat details for a specific contact
     *
     * @param string $contactId The contact ID
     * @return static
     * @throws InvalidArgumentException When contact ID is invalid
     * @throws RuntimeException When token is missing
     */
    public function chatDetail(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }

        return $this->addChatPayload(self::CHAT_ACTION_DETAIL, [
            'contact_id' => $contactId
        ], 'GET');
    }

    /**
     * Archive a chat
     *
     * @param string $contactId The contact ID
     * @return static
     * @throws InvalidArgumentException When contact ID is invalid
     * @throws RuntimeException When token is missing
     */
    public function archiveChat(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }

        return $this->addChatPayload(self::CHAT_ACTION_ARCHIVE, [
            'contact_id' => $contactId
        ]);
    }

    /**
     * Unarchive a chat
     *
     * @param string $contactId The contact ID
     * @return static
     * @throws InvalidArgumentException When contact ID is invalid
     * @throws RuntimeException When token is missing
     */
    public function unArchiveChat(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }

        return $this->addChatPayload(self::CHAT_ACTION_UNARCHIVE, [
            'contact_id' => $contactId
        ]);
    }

    /**
     * Add a chat payload to the request queue
     *
     * @param string $action The chat action
     * @param array $body The request body
     * @param string $method The HTTP method
     * @return static
     * @throws RuntimeException When token is missing
     */
    private function addChatPayload(string $action, array $body, string $method = 'POST'): static
    {
        $this->validateToken();

        $this->payload[] = [
            'method' => $method,
            'path' => '/chat/' . $action,
            'body' => $body
        ];

        return $this;
    }
}
