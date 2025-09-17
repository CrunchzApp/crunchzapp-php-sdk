<?php

namespace CrunchzApp\Services;

use CrunchzApp\Base\BaseService;
use InvalidArgumentException;
use RuntimeException;

final class OtpService extends BaseService
{
    private string $type;
    private ?string $code = null;
    private ?string $prompt = null;
    private ?string $successMessage = null;
    private ?string $failedMessage = null;
    private ?string $callbackSuccess = null;
    private ?string $callbackFailed = null;
    private ?string $expiredMessage = null;

    public function __construct($client, string $type)
    {
        parent::__construct($client);
        $this->validateOtpType($type);
        $this->type = $type;
    }

    public function send(): array
    {
        $this->validateContactId();
        $path = $this->type === 'code' ? '/otp/code/request' : '/otp/link/request';
        $body = $this->type === 'code' ? $this->bodyCode() : $this->bodyLink();
        return $this->client->post($path, $body);
    }

    public function validate(string $code): array
    {
        if ($this->type !== 'code') {
            throw new InvalidArgumentException('Validation is only supported for code-based OTP');
        }
        $this->code($code);
        $this->validateContactId();
        return $this->client->post('/otp/code/validate', $this->bodyValidate());
    }

    public function code(string $code): static
    {
        if (empty(trim($code))) {
            throw new InvalidArgumentException('OTP code cannot be empty');
        }
        $this->code = trim($code);
        return $this;
    }

    private function bodyCode(): array
    {
        return [
            'contact_id' => $this->contactId,
            'length' => config('crunchzapp.otp.code.length', 6),
            'useLetter' => config('crunchzapp.otp.code.useLetter', false),
        ];
    }

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
