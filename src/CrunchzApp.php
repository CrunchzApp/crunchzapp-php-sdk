<?php

namespace CrunchzApp;

use CrunchzApp\Http\Client;
use CrunchzApp\Services\ChannelService;
use CrunchzApp\Services\OtpService;
use RuntimeException;

/**
 * Main class for interacting with the CrunchzApp API.
 *
 * This class provides access to all the services offered by the SDK.
 */
class CrunchzApp
{
    /**
     * The HTTP client used to make requests to the API.
     *
     * @var Client
     */
    private Client $client;

    /**
     * Create a new CrunchzApp instance.
     *
     * @param string|null $token The API token. If not provided, it will be taken from the config.
     * @throws RuntimeException If the token is not provided and cannot be found in the config.
     */
    public function __construct(?string $token = null)
    {
        $this->client = new Client($token);
    }

    /**
     * Get the channel service for sending messages and managing contacts.
     *
     * @return ChannelService The channel service instance.
     */
    public function channel(): ChannelService
    {
        return new ChannelService($this->client);
    }

    /**
     * Get the OTP service for sending and validating OTPs.
     *
     * @param string $type The type of OTP to use. Either 'code' or 'link'.
     * @return OtpService The OTP service instance.
     */
    public function otp(string $type): OtpService
    {
        return new OtpService($this->client, $type);
    }
}
