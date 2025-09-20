<?php

namespace CrunchzApp\Tests;

use CrunchzApp\Http\Client;
use CrunchzApp\Services\OtpService;
use Mockery;

class OtpServiceTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Mockery::mock(Client::class);
    }

    /** @test */
    public function it_can_send_a_code_otp()
    {
        config([
            'crunchzapp.otp.code.length' => 6,
            'crunchzapp.otp.code.useLetter' => false,
        ]);

        $this->client->shouldReceive('post')->with('/otp/code/request', [
            'contact_id' => '12345',
            'length' => 6,
            'useLetter' => false,
        ])->andReturn(['success' => true]);

        $otpService = new OtpService($this->client, 'code');
        $response = $otpService->contact('12345')->send();

        $this->assertEquals(['success' => true], $response);
    }

    /** @test */
    public function it_can_validate_a_code_otp()
    {
        $this->client->shouldReceive('post')->with('/otp/code/validate', [
            'contact_id' => '12345',
            'code' => '123456'
        ])->andReturn(['success' => true]);

        $otpService = new OtpService($this->client, 'code');
        $response = $otpService->contact('12345')->validate('123456');

        $this->assertEquals(['success' => true], $response);
    }
}
