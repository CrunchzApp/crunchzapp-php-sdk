<?php

namespace CrunchzApp\Traits;

use InvalidArgumentException;

trait MessageTrait
{
    private const MESSAGE_TYPE_TEXT = 'text';
    private const MESSAGE_TYPE_IMAGE = 'image';
    private const MESSAGE_TYPE_LOCATION = 'location';
    private const MESSAGE_TYPE_VOICE = 'voice';
    private const MESSAGE_TYPE_VIDEO = 'video';
    private const MESSAGE_TYPE_REACT = 'react';
    private const MESSAGE_TYPE_POLLING = 'polling';
    private const MESSAGE_TYPE_STAR = 'star';
    private const MESSAGE_TYPE_DELETE = 'delete';
    private const MESSAGE_TYPE_TYPING = 'typing';

    public function startTyping(): static
    {
        if (!is_null($this->contactId)) {
            $this->payload[] = [
                'name' => __FUNCTION__,
                'method' => 'POST',
                'path' => '/send-message/typing',
                'body' => [
                    'contact_id' => $this->contactId
                ]
            ];
            return $this;
        }
        throw new \Exception('Contact ID is required for sending text message, Make sure to declare the contact id after channel function');
    }

    public function stopTyping(): static
    {
        if (!is_null($this->contactId)) {
            $this->payload[] = [
                'name' => __FUNCTION__,
                'method' => 'POST',
                'path' => '/send-message/stop-typing',
                'body' => [
                    'contact_id' => $this->contactId
                ]
            ];
            return $this;
        }
        throw new \Exception('Contact ID is required for sending text message, Make sure to declare the contact id after channel function');
    }

    /**
     * Send a text message
     *
     * @param string $message The text message to send
     * @return static
     * @throws InvalidArgumentException When message is empty
     */
    public function text(string $message): static
    {
        if (empty(trim($message))) {
            throw new InvalidArgumentException('Message cannot be empty');
        }

        return $this->addMessagePayload(self::MESSAGE_TYPE_TEXT, [
            'contact_id' => $this->getValidatedContactId(),
            'message' => $message
        ]);
    }

    /**
     * Send an image message
     *
     * @param string $url The image URL
     * @param string|null $caption Optional image caption
     * @param string|null $mimeType Optional MIME type
     * @param string|null $filename Optional filename
     * @return static
     * @throws InvalidArgumentException When URL is invalid
     */
    public function image(string $url, ?string $caption = null, ?string $mimeType = null, ?string $filename = null): static
    {
        $this->validateUrl($url);

        $body = [
            'contact_id' => $this->getValidatedContactId(),
            'caption' => $caption
        ];

        if ($mimeType && $filename) {
            $body['file'] = [
                'mimeType' => $mimeType,
                'filename' => $filename,
                'url' => $url
            ];
        } else {
            $body['url'] = $url;
        }

        return $this->addMessagePayload(self::MESSAGE_TYPE_IMAGE, $body);
    }

    /**
     * Send a location message
     *
     * @param float $latitude The latitude coordinate
     * @param float $longitude The longitude coordinate
     * @param string|null $title Optional location title
     * @return static
     * @throws InvalidArgumentException When coordinates are invalid
     */
    public function location(float $latitude, float $longitude, ?string $title = null): static
    {
        $this->validateCoordinates($latitude, $longitude);

        return $this->addMessagePayload(self::MESSAGE_TYPE_LOCATION, [
            'contact_id' => $this->getValidatedContactId(),
            'latitude' => (string) $latitude,
            'longitude' => (string) $longitude,
            'title' => $title
        ]);
    }

    /**
     * Send a voice message
     *
     * @param string $audioUrl The voice file URL
     * @return static
     * @throws InvalidArgumentException When URL is invalid
     */
    public function voice(string $audioUrl): static
    {
        $this->validateUrl($audioUrl);

        return $this->addMessagePayload(self::MESSAGE_TYPE_VOICE, [
            'contact_id' => $this->getValidatedContactId(),
            'audioUrl' => $audioUrl
        ]);
    }

    /**
     * Send a video message
     *
     * @param string $videoUrl The video file URL
     * @param string|null $caption Optional video caption
     * @return static
     * @throws InvalidArgumentException When URL is invalid
     */
    public function video(string $videoUrl, ?string $caption = null): static
    {
        $this->validateUrl($videoUrl);

        return $this->addMessagePayload(self::MESSAGE_TYPE_VIDEO, [
            'contact_id' => $this->getValidatedContactId(),
            'videoUrl' => $videoUrl,
            'caption' => $caption
        ]);
    }

    /**
     * React to a message with an emoji
     *
     * @param string $messageId The message ID to react to
     * @param string $reaction The emoji reaction
     * @return static
     * @throws InvalidArgumentException When parameters are invalid
     */
    public function react(string $messageId, string $reaction): static
    {
        if (empty(trim($messageId))) {
            throw new InvalidArgumentException('Message ID cannot be empty');
        }
        if (empty(trim($reaction))) {
            throw new InvalidArgumentException('Reaction cannot be empty');
        }

        return $this->addMessagePayload(self::MESSAGE_TYPE_REACT, [
            'message_id' => $messageId,
            'reaction' => $reaction,
        ], 'PUT');
    }

    /**
     * Send a polling message
     *
     * @param string $title The poll question
     * @param array $options Array of poll options
     * @param bool $isMultipleAnswer Whether multiple answers are allowed
     * @return static
     * @throws InvalidArgumentException When parameters are invalid
     */
    public function polling(string $title, array $options = [], bool $isMultipleAnswer = false): static
    {
        if (empty(trim($title))) {
            throw new InvalidArgumentException('Poll title cannot be empty');
        }
        if (empty($options) || count($options) < 2) {
            throw new InvalidArgumentException('Poll must have at least 2 options');
        }

        return $this->addMessagePayload(self::MESSAGE_TYPE_POLLING, [
            'contact_id' => $this->getValidatedContactId(),
            'title' => $title,
            'options' => $options,
            'is_multiple_answer' => $isMultipleAnswer
        ]);
    }

    /**
     * Star a message
     *
     * @param string $messageId The message ID to star
     * @param bool $starred Whether to star or unstar the message
     * @return static
     * @throws InvalidArgumentException When message ID is empty
     */
    public function star(string $messageId, bool $starred = true): static
    {
        if (empty(trim($messageId))) {
            throw new InvalidArgumentException('Message ID cannot be empty');
        }

        return $this->addMessagePayload(self::MESSAGE_TYPE_STAR, [
            'message_id' => $messageId,
            'starred' => $starred
        ], 'PUT');
    }

    /**
     * Delete a message
     *
     * @param string $messageId The message ID to delete
     * @return static
     * @throws InvalidArgumentException When message ID is empty
     */
    public function delete(string $messageId): static
    {
        if (empty(trim($messageId))) {
            throw new InvalidArgumentException('Message ID cannot be empty');
        }

        return $this->addMessagePayload(self::MESSAGE_TYPE_DELETE, [
            'message_id' => $messageId,
        ], 'DELETE');
    }

    /**
     * Mark a message as seen
     *
     * @param string $messageId The message ID to mark as seen
     * @return static
     * @throws InvalidArgumentException When message ID is empty
     */
    public function seen(string $messageId): static
    {
        if (empty(trim($messageId))) {
            throw new InvalidArgumentException('Message ID cannot be empty');
        }

        return $this->addMessagePayload('seen', [
            'message_id' => $messageId,
        ]);
    }

    /**
     * Add a message payload to the request queue
     *
     * @param string $type The message type
     * @param array $body The request body
     * @param string $method The HTTP method
     * @return static
     */
    private function addMessagePayload(string $type, array $body, string $method = 'POST'): static
    {
        $this->payload[] = [
            'name' => $type,
            'method' => $method,
            'path' => '/send-message/' . $type,
            'body' => $body
        ];
        return $this;
    }

    /**
     * Get validated contact ID
     *
     * @return string The validated contact ID
     * @throws \Exception When contact ID is not set
     */
    private function getValidatedContactId(): string
    {
        if (is_null($this->contactId)) {
            throw new \Exception('Contact ID is required for sending messages. Make sure to declare the contact id after channel function');
        }
        return $this->contactId;
    }

    /**
     * Validate URL format
     *
     * @param string $url The URL to validate
     * @throws InvalidArgumentException When URL is invalid
     */
    private function validateUrl(string $url): void
    {
        if (empty(trim($url))) {
            throw new InvalidArgumentException('URL cannot be empty');
        }
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL format');
        }
    }

    /**
     * Validate coordinates
     *
     * @param float $latitude The latitude coordinate
     * @param float $longitude The longitude coordinate
     * @throws InvalidArgumentException When coordinates are out of valid range
     */
    private function validateCoordinates(float $latitude, float $longitude): void
    {
        if ($latitude < -90 || $latitude > 90) {
            throw new InvalidArgumentException('Latitude must be between -90 and 90 degrees');
        }
        if ($longitude < -180 || $longitude > 180) {
            throw new InvalidArgumentException('Longitude must be between -180 and 180 degrees');
        }
    }
}
