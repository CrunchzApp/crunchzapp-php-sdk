<?php

namespace CrunchzApp\Base;

class OtpBase {

    protected $token;
    protected $contactId;
    protected $payload;
    protected $client;
    protected $code;
    protected $type;
    protected $endpoint = 'https://api.crunchz.app/api';

    protected $prompt;
    protected $successMessage;
    protected $failedMessage;
    protected $callbackSuccess;
    protected $callbackFailed;
    protected $expiredMessage;

    public function contact($contactId): static
    {
        $this->contactId = $contactId;
        return $this;
    }

    public function bodyCode(): array
    {
        return [
            'contact_id' => $this->contactId,
            'length' => config('crunchzapp.otp.code.length'),
            'useLetter' => config('crunchzapp.otp.code.useLetter'),
            'useNumber' => config('crunchzapp.otp.code.useNumber'),
            'allCapital' => config('crunchzapp.otp.code.allCapital'),
            'name' => config('crunchzapp.otp.code.name'),
            'expires' => config('crunchzapp.otp.code.expires')
        ];
    }

    public function bodyValidate(): array
    {
        return [
            'contact_id' => $this->contactId,
            'code' => $this->code,
        ];
    }

    public function code($code): static
    {
        $this->code = $code;
        return $this;
    }

    public function prompt($message): static
    {
        if ($this->payload['type'] === 'link') {
            $this->prompt = $message;
            return $this;
        } else {
            throw new \Exception('You cannot declare prompt when the otp type was code');
        }
    }

    /**
     * @throws \Exception
     */
    public function responseMessage($successResponse = null, $failedResponse = null, $expiredResponse = null): static
    {
        if ($this->payload['type'] === 'link') {
            if (!is_null($successResponse)) {
                $this->successMessage = $successResponse;
            }
            if (!is_null($failedResponse)) {
                $this->failedMessage = $failedResponse;
            }
            if (!is_null($expiredResponse)) {
                $this->expiredMessage = $expiredResponse;
            }
            return $this;
        } else {
            throw new \Exception('You cannot declare success response when the otp type was code');
        }
    }

    public function callback($successCallback, $failedCallback)
    {
        if ($this->payload['type'] === 'link') {
            if (!is_null($successCallback)) {
                if (filter_var($successCallback, FILTER_VALIDATE_URL)) {
                    $this->callbackSuccess = $successCallback;
                } else {
                    throw new \Exception('The success link callback must be a valid url');
                }
            }

            if (!is_null($failedCallback)) {
                if (filter_var($failedCallback, FILTER_VALIDATE_URL)) {
                    $this->callbackFailed = $failedCallback;
                } else {
                    throw new \Exception('The failed link callback must be a valid url');
                }
            }
            return $this;
        } else {
            throw new \Exception('You cannot declare success callback when the otp type was code');
        }
    }

    public function bodyLink(): array
    {
        return [
            'contact_id' => $this->contactId,
            'expires' => config('crunchzapp.otp.link.expires'),
            'name' => config('crunchzapp.otp.link.name'),
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
}
