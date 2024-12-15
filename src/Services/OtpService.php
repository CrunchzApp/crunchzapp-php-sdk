<?php

namespace Crunchzapp\Services;

use Crunchzapp\Base\OtpBase;
use Illuminate\Support\Facades\Http;

final class OtpService extends OtpBase
{

    public function __construct($type)
    {
        $this->type = $type;
        $this->payload = $this->getPayload();

        $this->tokenHandler();
        $this->otpLinkConstruct();

        $this->client = Http::baseUrl($this->endpoint)
            ->withToken($this->token);
    }

    /**
     * @throws \Exception
     */
    public function send()
    {
        try {
            $payload = $this->getPayload();
            $client = $this->client->post($payload['path_request'], $payload['body_request']);
            return $client->json();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function validate($code)
    {
        try {
            $this->code = $code;
            $payload = $this->getPayload();
            if ($payload['type'] === 'code') {
                $client = $this->client->post($payload['path_validate'], $payload['body_validate']);
                return $client->json();
            } else {
                throw new \Exception('Validate only accept otp code method');
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function tokenHandler(): void
    {
        $this->token = config('crunchzapp.token');
    }

    /**
     * @return void
     */
    public function otpLinkConstruct(): void
    {
        $this->prompt = config('crunchzapp.otp.link.prompt');
        $this->successMessage = config('crunchzapp.otp.link.respond.success');
        $this->failedMessage = config('crunchzapp.otp.link.respond.failed');
        $this->expiredMessage = config('crunchzapp.otp.link.respond.expired');
        $this->callbackSuccess = config('crunchzapp.otp.link.callback.success');
        $this->callbackFailed = config('crunchzapp.otp.link.callback.failed');
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return match ($this->type) {
            'code' => [
                'type' => 'code',
                'method_request' => 'POST',
                'method_validate' => 'POST',
                'path_request' => '/otp/code/request',
                'path_global_request' => '/otp/code/global',
                'path_validate' => '/otp/code/validate',
                'path_global_validate' => '/otp/code/validate',
                'body_request' => $this->bodyCode(),
                'body_validate' => $this->bodyValidate()
            ],
            'link' => [
                'type' => 'link',
                'method_request' => 'POST',
                'path_request' => '/otp/link/request',
                'path_global_request' => '/otp/link/global',
                'body_request' => $this->bodyLink()
            ]
        };
    }
}
