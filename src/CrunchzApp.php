<?php

namespace CrunchzApp;

use CrunchzApp\Services\ChannelService;
use CrunchzApp\Services\OtpService;

final class CrunchzApp {

    public static function channel(): ChannelService
    {
        return new ChannelService();
    }

    public static function otp($type): OtpService
    {
        return new OtpService($type);
    }
}
