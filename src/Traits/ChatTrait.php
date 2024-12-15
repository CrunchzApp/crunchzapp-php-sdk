<?php

namespace CrunchzApp\Traits;

trait ChatTrait {

    /**
     * @throws \Exception
     */
    public function allChat($limit = 10, $offset = 0)
    {
        if (is_null($this->token)) {
            throw new \Exception('Channel token is required');
        }
        $client = $this->client->withToken($this->token)->get('chat', [
            'limit' => $limit,
            'offset' => $offset
        ]);
        return $client->json();
    }

    /**
     * @throws \Exception
     */
    public function chatDetail()
    {
        if (is_null($this->token)) {
            throw new \Exception('Channel token is required');
        }
        if (is_null($this->contactId)) {
            throw new \Exception('Contact Id is required');
        }
        $client = $this->client->withToken($this->token)->get('chat/detail', [
            'contact_id' => $this->contactId
        ]);
        return $client->json();
    }

    /**
     * @throws \Exception
     */
    public function archiveChat()
    {
        if (is_null($this->token)) {
            throw new \Exception('Channel token is required');
        }
        if (is_null($this->contactId)) {
            throw new \Exception('Contact Id is required');
        }
        $client = $this->client->withToken($this->token)->post('chat/archive', [
            'contact_id' => $this->contactId
        ]);
        return $client->json();
    }

    /**
     * @throws \Exception
     */
    public function unArchiveChat()
    {
        if (is_null($this->token)) {
            throw new \Exception('Channel token is required');
        }
        if (is_null($this->contactId)) {
            throw new \Exception('Contact Id is required');
        }
        $client = $this->client->withToken($this->token)->post('chat/un-archive', [
            'contact_id' => $this->contactId
        ]);
        return $client->json();
    }
}
