<?php

namespace CrunchzApp\Base;

use CrunchzApp\Traits\ChatTrait;
use CrunchzApp\Traits\ContactTrait;
use CrunchzApp\Traits\GroupTrait;
use CrunchzApp\Traits\MessageTrait;

class WhatsAppBase {
    protected $client;
    protected $contactId;
    protected $payload = [];
    protected $token = null;
    protected $seen = false;
    protected $endpoint = 'https://api.crunchz.app/api';

    use MessageTrait, ContactTrait, ChatTrait, GroupTrait;

    public function contact($contactId): static
    {
        $this->contactId = $contactId;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function checkPhoneNumber($phoneNumber, $toVariable = false)
    {
        if (is_null($this->token)) {
            throw new \Exception('Channel token is required');
        }
        $client = $this->client->withToken($this->token)->get('channel/check-phone-number', [
            'phone' => $phoneNumber
        ]);
        if ($toVariable) {
            $data = $client->json();
            if ($data['data']['is_exists']) {
                $this->contactId = $data['data']['contact_id'];
                return $this;
            } else {
                throw new \Exception('The phone number is not exists on WhatsApp');
            }
        } else {
            return $client->json();
        }
    }

    /**
     * @throws \Exception
     */
    public function health()
    {
        if (is_null($this->token)) {
            throw new \Exception('Channel token is required');
        }
        $client = $this->client->withToken($this->token)->get('channel/health');
        return $client->json();
    }
}
