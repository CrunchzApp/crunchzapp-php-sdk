<?php

namespace CrunchzApp\Tests;

use CrunchzApp\CrunchzApp;
use CrunchzApp\Services\ChannelService;
use CrunchzApp\Services\OtpService;

class CrunchzAppTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated()
    {
        $crunchzApp = new CrunchzApp('test-token');
        $this->assertInstanceOf(CrunchzApp::class, $crunchzApp);
    }

    /** @test */
    public function it_returns_a_channel_service_instance()
    {
        $crunchzApp = new CrunchzApp('test-token');
        $this->assertInstanceOf(ChannelService::class, $crunchzApp->channel());
    }

    /** @test */
    public function it_returns_an_otp_service_instance()
    {
        $crunchzApp = new CrunchzApp('test-token');
        $this->assertInstanceOf(OtpService::class, $crunchzApp->otp('code'));
    }
}
