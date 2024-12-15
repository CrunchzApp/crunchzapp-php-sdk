<?php

namespace CrunchzApp\Traits;

trait ContactTrait {

    /**
     * @throws \Exception
     */
    public function allContact()
    {
        if (is_null($this->token)) {
            throw new \Exception('Channel token is required');
        }
        $client = $this->client->withToken($this->token)->get('contact');
        return $client->json();
    }

    /**
     * @throws \Exception
     */
    public function detail()
    {
        if (is_null($this->token)) {
            throw new \Exception('Channel token is required');
        }
        if (is_null($this->contactId)) {
            throw new \Exception('The contact id cannot be null or empty');
        }
        $client = $this->client->withToken($this->token)->get('contact/info', [
            'contact_id' => $this->contactId
        ]);
        return $client->json();
    }

    /**
     * @throws \Exception
     */
    public function picture()
    {
        if (is_null($this->token)) {
            throw new \Exception('Channel token is required');
        }
        if (is_null($this->contactId)) {
            throw new \Exception('The contact id cannot be null or empty');
        }
        $client = $this->client->withToken($this->token)->get('contact/picture', [
            'contact_id' => $this->contactId
        ]);
        return $client->json();
    }

}
