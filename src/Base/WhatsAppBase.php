<?php

namespace CrunchzApp\Base;

use CrunchzApp\Traits\ChatTrait;
use CrunchzApp\Traits\ContactTrait;
use CrunchzApp\Traits\GroupTrait;
use CrunchzApp\Traits\MessageTrait;
use Illuminate\Http\Client\PendingRequest;
use InvalidArgumentException;
use RuntimeException;

abstract class WhatsAppBase {
    protected ?PendingRequest $client = null;
    protected ?string $contactId = null;
    protected array $payload = [];
    protected ?string $token = null;
    protected bool $seen = false;
    protected string $endpoint = 'https://api.crunchz.app/api';

    // Error message constants
    private const ERROR_TOKEN_REQUIRED = 'Channel token is required';
    private const ERROR_CONTACT_ID_REQUIRED = 'Contact ID is required';
    private const ERROR_PHONE_NOT_EXISTS = 'The phone number does not exist on WhatsApp';

    use MessageTrait, ContactTrait, ChatTrait, GroupTrait;

    /**
     * Set the contact ID for operations
     * 
     * @param string $contactId The WhatsApp contact ID
     * @return static
     * @throws InvalidArgumentException When contact ID is invalid
     */
    public function contact(string $contactId): static
    {
        if (empty($contactId)) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }
        
        $this->contactId = $contactId;
        return $this;
    }

    /**
     * Check if a phone number exists on WhatsApp
     * 
     * @param string $phoneNumber The phone number to check
     * @param bool $toVariable Whether to set contact ID if phone exists
     * @return static|array Returns $this if $toVariable is true, otherwise API response
     * @throws RuntimeException When token is missing or API request fails
     * @throws InvalidArgumentException When phone number is invalid
     */
    public function checkPhoneNumber(string $phoneNumber, bool $toVariable = false): static|array
    {
        $this->validateToken();
        $this->validatePhoneNumber($phoneNumber);
        
        try {
            $response = $this->client->withToken($this->token)->get('channel/check-phone-number', [
                'phone' => $phoneNumber
            ]);
            
            if (!$response->successful()) {
                throw new RuntimeException('Failed to check phone number: ' . $response->body());
            }
            
            if ($toVariable) {
                $data = $response->json();
                if ($data['data']['is_exists'] ?? false) {
                    $this->contactId = $data['data']['contact_id'];
                    return $this;
                } else {
                    throw new RuntimeException(self::ERROR_PHONE_NOT_EXISTS);
                }
            }
            
            return $response->json();
        } catch (\Exception $e) {
            throw new RuntimeException('Failed to check phone number: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Check channel health status
     * 
     * @return array The health check response
     * @throws RuntimeException When token is missing or API request fails
     */
    public function health(): array
    {
        $this->validateToken();
        
        try {
            $response = $this->client->withToken($this->token)->get('channel/health');
            
            if (!$response->successful()) {
                throw new RuntimeException('Health check failed: ' . $response->body());
            }
            
            return $response->json();
        } catch (\Exception $e) {
            throw new RuntimeException('Failed to check health: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * Validate that token is set
     * 
     * @throws RuntimeException When token is not set
     */
    protected function validateToken(): void
    {
        if (empty($this->token)) {
            throw new RuntimeException(self::ERROR_TOKEN_REQUIRED);
        }
    }
    
    /**
     * Validate that contact ID is set
     * 
     * @throws RuntimeException When contact ID is not set
     */
    protected function validateContactId(): void
    {
        if (empty($this->contactId)) {
            throw new RuntimeException(self::ERROR_CONTACT_ID_REQUIRED);
        }
    }
    
    /**
     * Validate phone number format
     * 
     * @param string $phoneNumber The phone number to validate
     * @throws InvalidArgumentException When phone number is invalid
     */
    private function validatePhoneNumber(string $phoneNumber): void
    {
        if (empty($phoneNumber)) {
            throw new InvalidArgumentException('Phone number cannot be empty');
        }
        
        // Basic phone number validation (can be enhanced based on requirements)
        if (!preg_match('/^[\d\+\-\(\)\s]+$/', $phoneNumber)) {
            throw new InvalidArgumentException('Invalid phone number format');
        }
    }
}
