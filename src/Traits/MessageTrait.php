<?php

namespace CrunchzApp\Traits;

trait MessageTrait {

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

    public function text($message): static
    {
        if (!is_null($this->contactId)) {
            $this->payload[] = [
                'name' => __FUNCTION__,
                'method' => 'POST',
                'path' => '/send-message/text',
                'body' => [
                    'contact_id' => $this->contactId,
                    'message' => $message
                ]
            ];
            return $this;
        }
        throw new \Exception('Contact ID is required for sending text message, Make sure to declare the contact id after channel function');
    }

    public function image($caption, $mimeType, $filename, $url): static
    {
        if (!is_null($this->contactId)) {
            $this->payload[] = [
                'name' => __FUNCTION__,
                'method' => 'POST',
                'path' => '/send-image/image',
                'body' => [
                    'contact_id' => $this->contactId,
                    'caption' => $caption,
                    'file' => [
                        'mimeType' => $mimeType,
                        'filename' => $filename,
                        'url' => $url
                    ]
                ]
            ];
            return $this;
        }
        throw new \Exception('Contact ID is required for sending text message, Make sure to declare the contact id after channel function');
    }

    public function location($latitude, $longitude, $title): static
    {
        if (!is_null($this->contactId)) {
            $this->payload[] = [
                'name' => __FUNCTION__,
                'method' => 'POST',
                'path' => '/send-image/location',
                'body' => [
                    'contact_id' => $this->contactId,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'title' => $title
                ]
            ];
            return $this;
        }
        throw new \Exception('Contact ID is required for sending text message, Make sure to declare the contact id after channel function');
    }

    public function voice($audioUrl): static
    {
        if (!is_null($this->contactId)) {
            $this->payload[] = [
                'name' => __FUNCTION__,
                'method' => 'POST',
                'path' => '/send-image/voice',
                'body' => [
                    'contact_id' => $this->contactId,
                    'audioUrl' => $audioUrl
                ]
            ];
            return $this;
        }
        throw new \Exception('Contact ID is required for sending text message, Make sure to declare the contact id after channel function');
    }

    public function video($videoUrl, $caption): static
    {
        if (!is_null($this->contactId)) {
            $this->payload[] = [
                'name' => __FUNCTION__,
                'method' => 'POST',
                'path' => '/send-image/video',
                'body' => [
                    'contact_id' => $this->contactId,
                    'videoUrl' => $videoUrl,
                    'caption' => $caption
                ]
            ];
            return $this;
        }
        throw new \Exception('Contact ID is required for sending text message, Make sure to declare the contact id after channel function');
    }

    public function react($messageId, $reaction): static
    {
        if (!is_null($this->contactId)) {
            $this->payload[] = [
                'name' => __FUNCTION__,
                'method' => 'PUT',
                'path' => '/send-image/reaction',
                'body' => [
                    'message_id' => $messageId,
                    'reaction' => $reaction,
                ],
            ];
            return $this;
        }
        throw new \Exception('Contact ID is required for sending text message, Make sure to declare the contact id after channel function');
    }

    public function polling($title, $options = [], $isMultipleAnswer = false): static
    {
        if (!is_null($this->contactId)) {
            $this->payload[] = [
                'name' => __FUNCTION__,
                'method' => 'POST',
                'path' => '/send-image/poll',
                'body' => [
                    'contact_id' => $this->contactId,
                    'title' => $title,
                    'options' => $options,
                    'is_multiple_answer' => $isMultipleAnswer
                ]
            ];
            return $this;
        }
        throw new \Exception('Contact ID is required for sending text message, Make sure to declare the contact id after channel function');
    }

    public function star($messageId, $starred = true): static
    {
        if (!is_null($this->contactId)) {
            $this->payload[] = [
                'name' => __FUNCTION__,
                'method' => 'PUT',
                'path' => '/send-image/star',
                'body' => [
                    'message_id' => $messageId,
                    'starred' => $starred
                ]
            ];
            return $this;
        }
        throw new \Exception('Contact ID is required for sending text message, Make sure to declare the contact id after channel function');
    }

    public function delete($messageId): static
    {
        if (!is_null($this->contactId)) {
            $this->payload[] = [
                'name' => __FUNCTION__,
                'method' => 'DELETE',
                'path' => '/send-image/delete',
                'body' => [
                    'message_id' => $messageId,
                ]
            ];
            return $this;
        }
        throw new \Exception('Contact ID is required for sending text message, Make sure to declare the contact id after channel function');
    }

    public function seen($messageId): static
    {
        if (!is_null($this->contactId)) {
            $this->payload[] = [
                'name' => __FUNCTION__,
                'method' => 'POST',
                'path' => '/send-image/seen',
                'body' => [
                    'message_id' => $messageId,
                ]
            ];
            return $this;
        }
        throw new \Exception('Contact ID is required for sending text message, Make sure to declare the contact id after channel function');

    }
}
