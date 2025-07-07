<?php

namespace CrunchzApp;

use CrunchzApp\Services\ChannelService;
use CrunchzApp\Services\OtpService;
use InvalidArgumentException;

/**
 * CrunchzApp SDK main factory class
 * 
 * This class provides static factory methods to create service instances
 * for interacting with the CrunchzApp API.
 */
final class CrunchzApp
{
    /**
     * Create a new channel service instance
     * 
     * @return ChannelService A new channel service instance for WhatsApp operations
     */
    public static function channel(): ChannelService
    {
        return new ChannelService();
    }

    /**
     * Create a new OTP service instance
     * 
     * @param string $type The OTP type ('code' or 'link')
     * @return OtpService A new OTP service instance
     * @throws InvalidArgumentException When OTP type is invalid
     */
    public static function otp(string $type): OtpService
    {
        if (empty(trim($type))) {
            throw new InvalidArgumentException('OTP type cannot be empty');
        }
        
        $validTypes = ['code', 'link'];
        if (!in_array($type, $validTypes, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid OTP type "%s". Valid types are: %s', $type, implode(', ', $validTypes))
            );
        }
        
        return new OtpService($type);
    }
}
