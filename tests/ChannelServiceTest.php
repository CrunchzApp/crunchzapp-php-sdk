<?php

namespace CrunchzApp\Tests;

use CrunchzApp\Http\Client;
use CrunchzApp\Services\ChannelService;
use Mockery;

class ChannelServiceTest extends TestCase
{
    private $client;
    private $channelService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = Mockery::mock(Client::class);
        $this->channelService = new ChannelService($this->client);
    }

    /** @test */
    public function it_can_send_a_text_message()
    {
        $this->client->shouldReceive('send')->with('POST', '/send-message/text', [
            'contact_id' => '12345',
            'message' => 'Hello'
        ])->andReturn(['success' => true]);

        $response = $this->channelService->contact('12345')->text('Hello')->send();

        $this->assertEquals(['success' => true], $response);
    }

    /** @test */
    public function it_can_check_a_phone_number()
    {
        $this->client->shouldReceive('get')->with('channel/check-phone-number', ['phone' => '1234567890'])
            ->andReturn(['data' => ['is_exists' => true, 'contact_id' => '12345']]);

        $response = $this->channelService->checkPhoneNumber('1234567890');

        $this->assertEquals(['data' => ['is_exists' => true, 'contact_id' => '12345']], $response);
    }
}
