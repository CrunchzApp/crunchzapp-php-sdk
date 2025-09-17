<?php

namespace CrunchzApp\Services;

use CrunchzApp\Base\BaseService;
use InvalidArgumentException;
use RuntimeException;

final class ChannelService extends BaseService
{
    public function send(): array
    {
        $this->validateSinglePayload();
        $payload = $this->payload[0];
        return $this->client->send($payload['method'], $payload['path'], $payload['body']);
    }

    public function sendPool(): array
    {
        return $this->client->sendPool($this->payload);
    }

    // Methods from MessageTrait
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
    public function allContact(): static
    {
        return $this->addPayload('GET', '/contact/all', []);
    }

    public function detail(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }
        return $this->addPayload('GET', '/contact/detail', ['contact_id' => $contactId]);
    }

    // Methods from ChatTrait
    public function allChat(): static
    {
        return $this->addPayload('GET', '/chat/all', []);
    }

    public function archiveChat(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }
        return $this->addPayload('POST', '/chat/archive', ['contact_id' => $contactId]);
    }

    // Methods from GroupTrait
    public function allGroup(): static
    {
        return $this->addPayload('GET', '/groups/all', []);
    }

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

    public function voice(string $audioUrl): static
    {
        $this->validateUrl($audioUrl);
        $this->validateContactId();
        return $this->addPayload('POST', '/send-message/voice', [
            'contact_id' => $this->contactId,
            'audioUrl' => $audioUrl
        ]);
    }

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

    public function delete(string $messageId): static
    {
        if (empty(trim($messageId))) {
            throw new InvalidArgumentException('Message ID cannot be empty');
        }
        return $this->addPayload('DELETE', '/send-message/delete', [
            'message_id' => $messageId,
        ]);
    }

    public function seen(string $messageId): static
    {
        if (empty(trim($messageId))) {
            throw new InvalidArgumentException('Message ID cannot be empty');
        }
        return $this->addPayload('POST', '/send-message/seen', [
            'message_id' => $messageId,
        ]);
    }

    public function startTyping(): static
    {
        $this->validateContactId();
        return $this->addPayload('POST', '/send-message/typing', [
            'contact_id' => $this->contactId
        ]);
    }

    public function stopTyping(): static
    {
        $this->validateContactId();
        return $this->addPayload('POST', '/send-message/stop-typing', [
            'contact_id' => $this->contactId
        ]);
    }

    public function picture(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }
        return $this->addPayload('GET', '/contact/picture', ['contact_id' => $contactId]);
    }

    public function participants(string $groupId): static
    {
        if (empty(trim($groupId))) {
            throw new InvalidArgumentException('Group ID cannot be empty');
        }
        return $this->addPayload('GET', '/groups/participants', ['group_id' => $groupId]);
    }

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

    public function health(): array
    {
        return $this->client->get('channel/health');
    }

    public function unArchiveChat(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }
        return $this->addPayload('POST', '/chat/unarchive', ['contact_id' => $contactId]);
    }

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
