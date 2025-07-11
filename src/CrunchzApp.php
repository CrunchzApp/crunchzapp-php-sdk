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
 * 
 * @package CrunchzApp
 * @version 1.0.0
 */
final class CrunchzApp
{
    /**
     * Valid OTP types
     */
    private const VALID_OTP_TYPES = ['code', 'link'];
    
    /**
     * OTP type for code-based verification
     */
    public const OTP_TYPE_CODE = 'code';
    
    /**
     * OTP type for link-based verification
     */
    public const OTP_TYPE_LINK = 'link';
    /**
     * Create a new channel service instance for WhatsApp operations
     * 
     * @return ChannelService A new channel service instance configured with the app token
     * @throws \RuntimeException When CrunchzApp token is not configured
     */
    public static function channel(): ChannelService
    {
        return new ChannelService();
    }

    /**
     * Create a new OTP service instance
     * 
     * @param string $type The OTP type (use CrunchzApp::OTP_TYPE_CODE or CrunchzApp::OTP_TYPE_LINK)
     * @return OtpService A new OTP service instance configured for the specified type
     * @throws InvalidArgumentException When OTP type is invalid or empty
     * @throws \RuntimeException When CrunchzApp token is not configured
     * 
     * @example
     * // Create code-based OTP service
     * $otpService = CrunchzApp::otp(CrunchzApp::OTP_TYPE_CODE);
     * 
     * // Create link-based OTP service
     * $otpService = CrunchzApp::otp(CrunchzApp::OTP_TYPE_LINK);
     */
    public static function otp(string $type): OtpService
    {
        self::validateOtpType($type);
        return new OtpService($type);
    }
    
    /**
     * Validate OTP type parameter
     * 
     * @param string $type The OTP type to validate
     * @throws InvalidArgumentException When OTP type is invalid or empty
     */
    private static function validateOtpType(string $type): void
    {
        if (empty(trim($type))) {
            throw new InvalidArgumentException('OTP type cannot be empty');
        }
        
        if (!in_array($type, self::VALID_OTP_TYPES, true)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid OTP type "%s". Valid types are: %s. Use CrunchzApp::OTP_TYPE_CODE or CrunchzApp::OTP_TYPE_LINK constants.',
                    $type,
                    implode(', ', self::VALID_OTP_TYPES)
                )
            );
        }
    }
    
    /**
     * Get all valid OTP types
     * 
     * @return array List of valid OTP types
     */
    public static function getValidOtpTypes(): array
    {
        return self::VALID_OTP_TYPES;
    }
}
