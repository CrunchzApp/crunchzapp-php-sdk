<?php

namespace CrunchzApp\Services;

use CrunchzApp\Base\BaseService;
use InvalidArgumentException;
use RuntimeException;

/**
 * Service for interacting with the Channel API.
 *
 * This service provides methods for sending messages, managing contacts, and other channel-related operations.
 */
final class ChannelService extends BaseService
{
    /**
     * Send a single request to the API.
     *
     * @return array The response from the API.
     * @throws RuntimeException If more than one payload is present or if no payload is found.
     */
    public function send(): array
    {
        $this->validateSinglePayload();
        $payload = $this->payload[0];
        return $this->client->send($payload['method'], $payload['path'], $payload['body']);
    }

    /**
     * Send multiple requests to the API in parallel.
     *
     * @return array The responses from the API.
     * @throws RuntimeException If the pool request fails.
     */
    public function sendPool(): array
    {
        return $this->client->sendPool($this->payload);
    }

    // Methods from MessageTrait
    /**
     * Add a text message to the payload.
     *
     * @param string $message The message to send.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the message is empty.
     */
    public function text(string $message): static
    {
        if (empty(trim($message))) {
            throw new InvalidArgumentException('Message cannot be empty');
        }
        $this->validateContactId();
        return $this->addPayload('POST', '/send-message/text', [
            'contact_id' => $this->contactId,
            'message' => $message
        ]);
    }

    /**
     * Add an image message to the payload.
     *
     * @param string $url The URL of the image.
     * @param string|null $caption The caption for the image.
     * @param string|null $mimeType The MIME type of the image.
     * @param string|null $filename The filename of the image.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the URL is invalid.
     */
    public function image(string $url, ?string $caption = null, ?string $mimeType = null, ?string $filename = null): static
    {
        $this->validateUrl($url);
        $this->validateContactId();
        $body = ['contact_id' => $this->contactId, 'caption' => $caption];
        if ($mimeType && $filename) {
            $body['file'] = ['mimeType' => $mimeType, 'filename' => $filename, 'url' => $url];
        } else {
            $body['url'] = $url;
        }
        return $this->addPayload('POST', '/send-message/image', $body);
    }

    /**
     * Add a location message to the payload.
     *
     * @param float $latitude The latitude of the location.
     * @param float $longitude The longitude of the location.
     * @param string|null $title The title of the location.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the coordinates are invalid.
     */
    public function location(float $latitude, float $longitude, ?string $title = null): static
    {
        $this->validateCoordinates($latitude, $longitude);
        $this->validateContactId();
        return $this->addPayload('POST', '/send-message/location', [
            'contact_id' => $this->contactId,
            'latitude' => (string) $latitude,
            'longitude' => (string) $longitude,
            'title' => $title
        ]);
    }

    // Methods from ContactTrait
    /**
     * Get all contacts.
     *
     * @return static The current service instance.
     */
    public function allContact(): static
    {
        return $this->addPayload('GET', '/contact/all', []);
    }

    /**
     * Get the details of a contact.
     *
     * @param string $contactId The ID of the contact.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the contact ID is empty.
     */
    public function detail(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }
        return $this->addPayload('GET', '/contact/detail', ['contact_id' => $contactId]);
    }

    // Methods from ChatTrait
    /**
     * Get all chats.
     *
     * @return static The current service instance.
     */
    public function allChat(): static
    {
        return $this->addPayload('GET', '/chat/all', []);
    }

    /**
     * Archive a chat.
     *
     * @param string $contactId The ID of the contact to archive the chat with.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the contact ID is empty.
     */
    public function archiveChat(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }
        return $this->addPayload('POST', '/chat/archive', ['contact_id' => $contactId]);
    }

    // Methods from GroupTrait
    /**
     * Get all groups.
     *
     * @return static The current service instance.
     */
    public function allGroup(): static
    {
        return $this->addPayload('GET', '/groups/all', []);
    }

    /**
     * Create a new group.
     *
     * @param string $name The name of the group.
     * @param array $participants An array of contact IDs to add to the group.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the group name or participants are empty.
     */
    public function createGroup(string $name, array $participants): static
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException('Group name cannot be empty');
        }
        if (empty($participants)) {
            throw new InvalidArgumentException('At least one participant is required to create a group');
        }
        return $this->addPayload('POST', '/groups/create', ['name' => trim($name), 'participants' => $participants]);
    }

    /**
     * Add a voice message to the payload.
     *
     * @param string $audioUrl The URL of the audio file.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the URL is invalid.
     */
    public function voice(string $audioUrl): static
    {
        $this->validateUrl($audioUrl);
        $this->validateContactId();
        return $this->addPayload('POST', '/send-message/voice', [
            'contact_id' => $this->contactId,
            'audioUrl' => $audioUrl
        ]);
    }

    /**
     * Add a video message to the payload.
     *
     * @param string $videoUrl The URL of the video file.
     * @param string|null $caption The caption for the video.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the URL is invalid.
     */
    public function video(string $videoUrl, ?string $caption = null): static
    {
        $this->validateUrl($videoUrl);
        $this->validateContactId();
        return $this->addPayload('POST', '/send-message/video', [
            'contact_id' => $this->contactId,
            'videoUrl' => $videoUrl,
            'caption' => $caption
        ]);
    }

    /**
     * Add a reaction to a message to the payload.
     *
     * @param string $messageId The ID of the message to react to.
     * @param string $reaction The reaction to add.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the message ID or reaction is empty.
     */
    public function react(string $messageId, string $reaction): static
    {
        if (empty(trim($messageId))) {
            throw new InvalidArgumentException('Message ID cannot be empty');
        }
        if (empty(trim($reaction))) {
            throw new InvalidArgumentException('Reaction cannot be empty');
        }
        return $this->addPayload('PUT', '/send-message/react', [
            'message_id' => $messageId,
            'reaction' => $reaction,
        ]);
    }

    /**
     * Add a poll to the payload.
     *
     * @param string $title The title of the poll.
     * @param array $options The options for the poll.
     * @param boolean $isMultipleAnswer Whether the poll allows multiple answers.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the title or options are invalid.
     */
    public function polling(string $title, array $options = [], bool $isMultipleAnswer = false): static
    {
        if (empty(trim($title))) {
            throw new InvalidArgumentException('Poll title cannot be empty');
        }
        if (empty($options) || count($options) < 2) {
            throw new InvalidArgumentException('Poll must have at least 2 options');
        }
        $this->validateContactId();
        return $this->addPayload('POST', '/send-message/polling', [
            'contact_id' => $this->contactId,
            'title' => $title,
            'options' => $options,
            'is_multiple_answer' => $isMultipleAnswer
        ]);
    }

    /**
     * Star a message.
     *
     * @param string $messageId The ID of the message to star.
     * @param boolean $starred Whether to star or unstar the message.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the message ID is empty.
     */
    public function star(string $messageId, bool $starred = true): static
    {
        if (empty(trim($messageId))) {
            throw new InvalidArgumentException('Message ID cannot be empty');
        }
        return $this->addPayload('PUT', '/send-message/star', [
            'message_id' => $messageId,
            'starred' => $starred
        ]);
    }

    /**
     * Delete a message.
     *
     * @param string $messageId The ID of the message to delete.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the message ID is empty.
     */
    public function delete(string $messageId): static
    {
        if (empty(trim($messageId))) {
            throw new InvalidArgumentException('Message ID cannot be empty');
        }
        return $this->addPayload('DELETE', '/send-message/delete', [
            'message_id' => $messageId,
        ]);
    }

    /**
     * Mark a message as seen.
     *
     * @param string $messageId The ID of the message to mark as seen.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the message ID is empty.
     */
    public function seen(string $messageId): static
    {
        if (empty(trim($messageId))) {
            throw new InvalidArgumentException('Message ID cannot be empty');
        }
        return $this->addPayload('POST', '/send-message/seen', [
            'message_id' => $messageId,
        ]);
    }

    /**
     * Start typing indicator.
     *
     * @return static The current service instance.
     */
    public function startTyping(): static
    {
        $this->validateContactId();
        return $this->addPayload('POST', '/send-message/typing', [
            'contact_id' => $this->contactId
        ]);
    }

    /**
     * Stop typing indicator.
     *
     * @return static The current service instance.
     */
    public function stopTyping(): static
    {
        $this->validateContactId();
        return $this->addPayload('POST', '/send-message/stop-typing', [
            'contact_id' => $this->contactId
        ]);
    }

    /**
     * Get the picture of a contact.
     *
     * @param string $contactId The ID of the contact.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the contact ID is empty.
     */
    public function picture(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }
        return $this->addPayload('GET', '/contact/picture', ['contact_id' => $contactId]);
    }

    /**
     * Get the participants of a group.
     *
     * @param string $groupId The ID of the group.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the group ID is empty.
     */
    public function participants(string $groupId): static
    {
        if (empty(trim($groupId))) {
            throw new InvalidArgumentException('Group ID cannot be empty');
        }
        return $this->addPayload('GET', '/groups/participants', ['group_id' => $groupId]);
    }

    /**
     * Check if a phone number exists on WhatsApp.
     *
     * @param string $phoneNumber The phone number to check.
     * @param boolean $toVariable Whether to store the contact ID in the service for later use.
     * @return static|array The current service instance if $toVariable is true and the number exists, otherwise an array with the API response.
     * @throws RuntimeException If the phone number does not exist on WhatsApp and $toVariable is true.
     */
    public function checkPhoneNumber(string $phoneNumber, bool $toVariable = false): static|array
    {
        $this->validatePhoneNumber($phoneNumber);
        $response = $this->client->get('channel/check-phone-number', ['phone' => $phoneNumber]);

        if ($toVariable) {
            if ($response['data']['is_exists'] ?? false) {
                $this->contactId = $response['data']['contact_id'];
                return $this;
            } else {
                throw new RuntimeException('The phone number does not exist on WhatsApp');
            }
        }

        return $response;
    }

    /**
     * Check the health of the channel.
     *
     * @return array The API response.
     */
    public function health(): array
    {
        return $this->client->get('channel/health');
    }

    /**
     * Unarchive a chat.
     *
     * @param string $contactId The ID of the contact to unarchive the chat with.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the contact ID is empty.
     */
    public function unArchiveChat(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }
        return $this->addPayload('POST', '/chat/unarchive', ['contact_id' => $contactId]);
    }

    /**
     * Get the details of a chat.
     *
     * @param string $contactId The ID of the contact to get the chat details for.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the contact ID is empty.
     */
    public function chatDetail(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }
        return $this->addPayload('GET', '/chat/detail', ['contact_id' => $contactId]);
    }

    private function validateSinglePayload(): void
    {
        if (count($this->payload) > 1) {
            throw new RuntimeException('Single requests support only 1 payload. Use sendPool() method for multiple requests.');
        }
        if (empty($this->payload)) {
            throw new RuntimeException('No payload found. Please add at least one operation before sending.');
        }
    }

    private function validateUrl(string $url): void
    {
        if (empty(trim($url)) || !filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Invalid URL format');
        }
    }

    private function validateCoordinates(float $latitude, float $longitude): void
    {
        if ($latitude < -90 || $latitude > 90) {
            throw new InvalidArgumentException('Latitude must be between -90 and 90 degrees');
        }
        if ($longitude < -180 || $longitude > 180) {
            throw new InvalidArgumentException('Longitude must be between -180 and 180 degrees');
        }
    }

    private function validatePhoneNumber(string $phoneNumber): void
    {
        if (empty($phoneNumber)) {
            throw new InvalidArgumentException('Phone number cannot be empty');
        }
        if (!preg_match('/^[\d\+\-\(\)\s]+$/', $phoneNumber)) {
            throw new InvalidArgumentException('Invalid phone number format');
        }
    }
}
