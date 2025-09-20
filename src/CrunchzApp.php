<?php

namespace CrunchzApp;

use CrunchzApp\Http\Client;
use CrunchzApp\Services\ChannelService;
use CrunchzApp\Services\OtpService;

class CrunchzApp
{
    private Client $client;

    public function __construct(?string $token = null)
    {
        $this->client = new Client($token);
    }

    public function channel(): ChannelService
    {
        return new ChannelService($this->client);
    }

    public function otp(string $type): OtpService
    {
        return new OtpService($this->client, $type);
    }
}
