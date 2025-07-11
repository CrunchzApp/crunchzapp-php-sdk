<?php

namespace CrunchzApp\Base;

use InvalidArgumentException;
use RuntimeException;
use Illuminate\Http\Client\PendingRequest;

abstract class OtpBase
{

    protected ?string $token = null;
    protected ?string $contactId = null;
    protected array $payload = [];
    protected ?PendingRequest $client = null;
    protected ?string $code = null;
    protected string $type;
    protected string $endpoint = 'https://api.crunchz.app/api';

    protected ?string $prompt = null;
    protected ?string $successMessage = null;
    protected ?string $failedMessage = null;
    protected ?string $callbackSuccess = null;
    protected ?string $callbackFailed = null;
    protected ?string $expiredMessage = null;

    private const OTP_TYPE_CODE = 'code';
    private const OTP_TYPE_LINK = 'link';
    private const ERROR_CONTACT_ID_REQUIRED = 'Contact ID must be set before performing OTP operations';
    private const ERROR_INVALID_URL = 'The URL must be valid';
    private const ERROR_PROMPT_NOT_ALLOWED = 'You cannot declare prompt when the OTP type is code';
    private const ERROR_RESPONSE_NOT_ALLOWED = 'You cannot declare response messages when the OTP type is code';
    private const ERROR_CALLBACK_NOT_ALLOWED = 'You cannot declare callbacks when the OTP type is code';

    /**
     * Set the contact ID for OTP operations
     *
     * @param string $contactId The contact ID (phone number with country code)
     * @return static
     * @throws InvalidArgumentException When contact ID is invalid
     */
    public function contact(string $contactId): static
    {
        if (empty(trim($contactId))) {
            throw new InvalidArgumentException('Contact ID cannot be empty');
        }

        $this->contactId = trim($contactId);
        return $this;
    }

    /**
     * Generate request body for code-based OTP
     *
     * @return array The request body for code OTP
     * @throws RuntimeException When contact ID is not set
     */
    protected function bodyCode(): array
    {
        $this->validateContactId();

        return [
            'contact_id' => $this->contactId,
            'length' => config('crunchzapp.otp.code.length', 6),
            'useLetter' => config('crunchzapp.otp.code.useLetter', false),
            'useNumber' => config('crunchzapp.otp.code.useNumber', true),
            'allCapital' => config('crunchzapp.otp.code.allCapital', false),
            'name' => config('crunchzapp.otp.code.name', 'CrunchzApp'),
            'expires' => config('crunchzapp.otp.code.expires', 300)
        ];
    }

    /**
     * Generate request body for OTP validation
     *
     * @return array The request body for validation
     * @throws RuntimeException When contact ID or code is not set
     */
    protected function bodyValidate(): array
    {
        $this->validateContactId();
        $this->validateCode();

        return [
            'contact_id' => $this->contactId,
            'code' => $this->code,
        ];
    }

    /**
     * Set the OTP code for validation
     *
     * @param string $code The OTP code to validate
     * @return static
     * @throws InvalidArgumentException When code is invalid
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
     * Set the prompt message for link-based OTP
     *
     * @param string $message The prompt message
     * @return static
     * @throws InvalidArgumentException When message is invalid or OTP type is code
     */
    public function prompt(string $message): static
    {
        if ($this->type !== self::OTP_TYPE_LINK) {
            throw new InvalidArgumentException(self::ERROR_PROMPT_NOT_ALLOWED);
        }

        if (empty(trim($message))) {
            throw new InvalidArgumentException('Prompt message cannot be empty');
        }

        $this->prompt = trim($message);
        return $this;
    }

    /**
     * Set response messages for link-based OTP
     *
     * @param string|null $successResponse Success response message
     * @param string|null $failedResponse Failed response message
     * @param string|null $expiredResponse Expired response message
     * @return static
     * @throws InvalidArgumentException When OTP type is code
     */
    public function responseMessage(?string $successResponse = null, ?string $failedResponse = null, ?string $expiredResponse = null): static
    {
        if ($this->type !== self::OTP_TYPE_LINK) {
            throw new InvalidArgumentException(self::ERROR_RESPONSE_NOT_ALLOWED);
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
     * Set callback URLs for link-based OTP
     *
     * @param string|null $successCallback Success callback URL
     * @param string|null $failedCallback Failed callback URL
     * @return static
     * @throws InvalidArgumentException When OTP type is code or URLs are invalid
     */
    public function callback(?string $successCallback = null, ?string $failedCallback = null): static
    {
        if ($this->type !== self::OTP_TYPE_LINK) {
            throw new InvalidArgumentException(self::ERROR_CALLBACK_NOT_ALLOWED);
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

    /**
     * Generate request body for link-based OTP
     *
     * @return array The request body for link OTP
     * @throws RuntimeException When contact ID is not set
     */
    protected function bodyLink(): array
    {
        $this->validateContactId();

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
     * Validate that OTP code is set
     *
     * @throws RuntimeException When code is not set
     */
    protected function validateCode(): void
    {
        if (empty($this->code)) {
            throw new RuntimeException('OTP code must be set before validation');
        }
    }

    /**
     * Validate URL format
     *
     * @param string $url The URL to validate
     * @param string $context The context for error messages
     * @throws InvalidArgumentException When URL is invalid
     */
    protected function validateUrl(string $url, string $context = 'URL'): void
    {
        if (empty(trim($url))) {
            throw new InvalidArgumentException(sprintf('%s cannot be empty', ucfirst($context)));
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(sprintf('%s must be a valid URL', ucfirst($context)));
        }
    }

    /**
     * Check if current OTP type is code
     *
     * @return bool True if type is code, false otherwise
     */
    protected function isCodeType(): bool
    {
        return $this->type === self::OTP_TYPE_CODE;
    }

    /**
     * Check if current OTP type is link
     *
     * @return bool True if type is link, false otherwise
     */
    protected function isLinkType(): bool
    {
        return $this->type === self::OTP_TYPE_LINK;
    }
}
