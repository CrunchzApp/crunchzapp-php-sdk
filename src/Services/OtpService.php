<?php

namespace CrunchzApp\Services;

use CrunchzApp\Base\OtpBase;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use RuntimeException;

final class OtpService extends OtpBase
{

    /**
     * Initialize OTP service with specified type
     *
     * @param string $type The OTP type ('code' or 'link')
     * @throws InvalidArgumentException When type is invalid
     * @throws RuntimeException When token is missing
     */
    /**
     * Initialize OTP service with specified type
     *
     * @param string $type The OTP type ('code' or 'link')
     * @throws InvalidArgumentException When type is invalid
     * @throws RuntimeException When token is missing
     */
    public function __construct(string $type)
    {
        $this->validateOtpType($type);
        $this->type = $type;

        $this->tokenHandler();
        if (empty($this->token)) {
            throw new RuntimeException('CrunchzApp token is required. Please set it in your configuration.');
        }

        $this->otpLinkConstruct();
        $this->client = Http::baseUrl($this->endpoint)->withToken($this->token);
    }

    /**
     * Send OTP to the specified contact
     *
     * @return array The JSON response from the API
     * @throws RuntimeException When API request fails or contact ID is not set
     */
    public function send(): array
    {
        try {
            $payload = $this->getPayload();
            $response = $this->client->post($payload['path_request'], $payload['body_request']);

            if (!$response->successful()) {
                throw new RuntimeException(
                    sprintf(
                        'Failed to send OTP. API responded with status %d: %s',
                        $response->status(),
                        $response->body()
                    )
                );
            }

            return $response->json();
        } catch (\Exception $e) {
            if ($e instanceof RuntimeException) {
                throw $e;
            }
            throw new RuntimeException('Failed to send OTP: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Validate the provided OTP code
     *
     * @param string $code The OTP code to validate
     * @return array The JSON response from the API
     * @throws RuntimeException When API request fails or validation is not supported for this type
     * @throws InvalidArgumentException When code is empty or type is not 'code'
     */
    public function validate(string $code): array
    {
        if (!$this->isCodeType()) {
            throw new InvalidArgumentException('Validation is only supported for code-based OTP');
        }

        try {
            // Set the code first before getting payload
            $this->code($code);

            // Now get the payload which will include the validation body
            $payload = $this->getPayload();
            $response = $this->client->post($payload['path_validate'], $payload['body_validate']);

            if (!$response->successful()) {
                throw new RuntimeException(
                    sprintf(
                        'Failed to validate OTP. API responded with status %d: %s',
                        $response->status(),
                        $response->body()
                    )
                );
            }

            return $response->json();
        } catch (\Exception $e) {
            if ($e instanceof RuntimeException || $e instanceof InvalidArgumentException) {
                throw $e;
            }
            throw new RuntimeException('Failed to validate OTP: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Generate OTP (alias for send method)
     *
     * @return array The JSON response from the API
     * @throws RuntimeException When API request fails or contact ID is not set
     */
    public function generate(): array
    {
        return $this->send();
    }

    /**
     * Validate the previously set OTP code
     *
     * @return array The JSON response from the API
     * @throws RuntimeException When API request fails, validation is not supported for this type, or code is not set
     * @throws InvalidArgumentException When type is not 'code'
     */
    public function validateOtp(): array
    {
        if (!$this->isCodeType()) {
            throw new InvalidArgumentException('Validation is only supported for code-based OTP');
        }

        try {
            // Get the payload which will validate that code is set
            $payload = $this->getPayload();
            $response = $this->client->post($payload['path_validate'], $payload['body_validate']);

            if (!$response->successful()) {
                throw new RuntimeException(
                    sprintf(
                        'Failed to validate OTP. API responded with status %d: %s',
                        $response->status(),
                        $response->body()
                    )
                );
            }

            return $response->json();
        } catch (\Exception $e) {
            if ($e instanceof RuntimeException || $e instanceof InvalidArgumentException) {
                throw $e;
            }
            throw new RuntimeException('Failed to validate OTP: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Handle token configuration
     *
     * @return void
     */
    private function tokenHandler(): void
    {
        $this->token = config('crunchzapp.token');
    }

    /**
     * Validate OTP type during construction
     *
     * @param string $type The OTP type to validate
     * @throws InvalidArgumentException When type is invalid
     */
    private function validateOtpType(string $type): void
    {
        $validTypes = ['code', 'link'];

        if (empty(trim($type))) {
            throw new InvalidArgumentException('OTP type cannot be empty');
        }

        if (!in_array($type, $validTypes, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid OTP type "%s". Valid types are: %s',
                    $type,
                    implode(', ', $validTypes)
                )
            );
        }
    }

    /**
     * Initialize link-based OTP configuration from config
     *
     * @return void
     */
    private function otpLinkConstruct(): void
    {
        if ($this->isLinkType()) {
            $this->prompt = config('crunchzapp.otp.link.prompt');
            $this->successMessage = config('crunchzapp.otp.link.respond.success');
            $this->failedMessage = config('crunchzapp.otp.link.respond.failed');
            $this->expiredMessage = config('crunchzapp.otp.link.respond.expired');
            $this->callbackSuccess = config('crunchzapp.otp.link.callback.success');
            $this->callbackFailed = config('crunchzapp.otp.link.callback.failed');
        }
    }

    /**
     * Get the complete payload structure for OTP operations
     *
     * @return array The payload structure with paths and request bodies
     * @throws RuntimeException When contact ID is not set
     */
    public function getPayload(): array
    {
        $this->validateContactId();

        $payload = match ($this->type) {
            'code' => [
                'type' => 'code',
                'method_request' => 'POST',
                'method_validate' => 'POST',
                'path_request' => '/otp/code/request',
                'path_global_request' => '/otp/code/global',
                'path_validate' => '/otp/code/validate',
                'path_global_validate' => '/otp/code/validate',
                'body_request' => $this->bodyCode()
            ],
            'link' => [
                'type' => 'link',
                'method_request' => 'POST',
                'path_request' => '/otp/link/request',
                'path_global_request' => '/otp/link/global',
                'body_request' => $this->bodyLink()
            ],
            default => throw new InvalidArgumentException("Unsupported OTP type: {$this->type}")
        };

        // Add body_validate only if code is set (for validation operations)
        if ($this->type === 'code' && !empty($this->code)) {
            $payload['body_validate'] = $this->bodyValidate();
        }

        return $payload;
    }
}
