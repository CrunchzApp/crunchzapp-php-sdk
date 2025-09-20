<?php

namespace CrunchzApp\Services;

use CrunchzApp\Base\BaseService;
use CrunchzApp\Http\Client;
use InvalidArgumentException;
use RuntimeException;

/**
 * Service for handling OTP (One-Time Password) operations.
 *
 * This service allows you to send and validate OTPs via WhatsApp.
 * It supports two types of OTPs: 'code' and 'link'.
 */
final class OtpService extends BaseService
{
    /**
     * The type of OTP to use ('code' or 'link').
     *
     * @var string
     */
    private string $type;

    /**
     * The OTP code.
     *
     * @var string|null
     */
    private ?string $code = null;

    /**
     * The prompt message for link-based OTPs.
     *
     * @var string|null
     */
    private ?string $prompt = null;

    /**
     * The success message for link-based OTPs.
     *
     * @var string|null
     */
    private ?string $successMessage = null;

    /**
     * The failed message for link-based OTPs.
     *
     * @var string|null
     */
    private ?string $failedMessage = null;

    /**
     * The success callback URL for link-based OTPs.
     *
     * @var string|null
     */
    private ?string $callbackSuccess = null;

    /**
     * The failed callback URL for link-based OTPs.
     *
     * @var string|null
     */
    private ?string $callbackFailed = null;

    /**
     * The expired message for link-based OTPs.
     *
     * @var string|null
     */
    private ?string $expiredMessage = null;

    /**
     * Create a new OtpService instance.
     *
     * @param Client $client The HTTP client instance.
     * @param string $type The type of OTP to use ('code' or 'link').
     * @throws InvalidArgumentException If the OTP type is invalid.
     */
    public function __construct(Client $client, string $type)
    {
        parent::__construct($client);
        $this->validateOtpType($type);
        $this->type = $type;
    }

    /**
     * Send the OTP request.
     *
     * @return array The API response.
     * @throws RuntimeException If the contact ID is not set.
     */
    public function send(): array
    {
        $this->validateContactId();
        $path = $this->type === 'code' ? '/otp/code/request' : '/otp/link/request';
        $body = $this->type === 'code' ? $this->bodyCode() : $this->bodyLink();
        return $this->client->post($path, $body);
    }

    /**
     * Validate an OTP code.
     *
     * @param string $code The OTP code to validate.
     * @return array The API response.
     * @throws InvalidArgumentException If the OTP type is not 'code'.
     * @throws RuntimeException If the contact ID is not set.
     */
    public function validate(string $code): array
    {
        if ($this->type !== 'code') {
            throw new InvalidArgumentException('Validation is only supported for code-based OTP');
        }
        $this->code($code);
        $this->validateContactId();
        return $this->client->post('/otp/code/validate', $this->bodyValidate());
    }

    /**
     * Set the OTP code for validation.
     *
     * @param string $code The OTP code.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the code is empty.
     */
    public function code(string $code): static
    {
        if (empty(trim($code))) {
            throw new InvalidArgumentException('OTP code cannot be empty');
        }
        $this->code = trim($code);
        return $this;
    }

    /**
     * Get the request body for a code-based OTP.
     *
     * @return array The request body.
     */
    private function bodyCode(): array
    {
        return [
            'contact_id' => $this->contactId,
            'length' => config('crunchzapp.otp.code.length', 6),
            'useLetter' => config('crunchzapp.otp.code.useLetter', false),
        ];
    }

    /**
     * Set the prompt message for a link-based OTP.
     *
     * @param string $message The prompt message.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the message is empty or the OTP type is not 'link'.
     */
    public function prompt(string $message): static
    {
        if ($this->type !== 'link') {
            throw new InvalidArgumentException('You cannot declare prompt when the OTP type is code');
        }
        if (empty(trim($message))) {
            throw new InvalidArgumentException('Prompt message cannot be empty');
        }
        $this->prompt = trim($message);
        return $this;
    }

    /**
     * Set the response messages for a link-based OTP.
     *
     * @param string|null $successResponse The message to show on successful validation.
     * @param string|null $failedResponse The message to show on failed validation.
     * @param string|null $expiredResponse The message to show when the OTP has expired.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the OTP type is not 'link'.
     */
    public function responseMessage(?string $successResponse = null, ?string $failedResponse = null, ?string $expiredResponse = null): static
    {
        if ($this->type !== 'link') {
            throw new InvalidArgumentException('You cannot declare response messages when the OTP type is code');
        }
        if ($successResponse !== null) {
            $this->successMessage = trim($successResponse);
        }
        if ($failedResponse !== null) {
            $this->failedMessage = trim($failedResponse);
        }
        if ($expiredResponse !== null) {
            $this->expiredMessage = trim($expiredResponse);
        }
        return $this;
    }

    /**
     * Set the callback URLs for a link-based OTP.
     *
     * @param string|null $successCallback The URL to call on successful validation.
     * @param string|null $failedCallback The URL to call on failed validation.
     * @return static The current service instance.
     * @throws InvalidArgumentException If the URLs are invalid or the OTP type is not 'link'.
     */
    public function callback(?string $successCallback = null, ?string $failedCallback = null): static
    {
        if ($this->type !== 'link') {
            throw new InvalidArgumentException('You cannot declare callbacks when the OTP type is code');
        }
        if ($successCallback !== null) {
            $this->validateUrl($successCallback, 'success callback');
            $this->callbackSuccess = $successCallback;
        }
        if ($failedCallback !== null) {
            $this->validateUrl($failedCallback, 'failed callback');
            $this->callbackFailed = $failedCallback;
        }
        return $this;
    }

    private function bodyLink(): array
    {
        return [
            'contact_id' => $this->contactId,
            'expires' => config('crunchzapp.otp.link.expires', 300),
            'name' => config('crunchzapp.otp.link.name', 'CrunchzApp'),
            'message' => [
                'prompt' => $this->prompt,
                'success' => $this->successMessage,
                'failed' => $this->failedMessage,
                'expired' => $this->expiredMessage
            ],
            'callback' => [
                'success' => $this->callbackSuccess,
                'failed' => $this->callbackFailed
            ]
        ];
    }

    private function bodyValidate(): array
    {
        if (empty($this->code)) {
            throw new RuntimeException('OTP code must be set before validation');
        }
        return [
            'contact_id' => $this->contactId,
            'code' => $this->code,
        ];
    }

    private function validateOtpType(string $type): void
    {
        if (!in_array($type, ['code', 'link'], true)) {
            throw new InvalidArgumentException('Invalid OTP type. Valid types are: code, link.');
        }
    }

    private function validateUrl(string $url, string $context = 'URL'): void
    {
        if (empty(trim($url))) {
            throw new InvalidArgumentException(sprintf('%s cannot be empty', ucfirst($context)));
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(sprintf('%s must be a valid URL', ucfirst($context)));
        }
    }
}
