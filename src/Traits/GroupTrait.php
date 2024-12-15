<?php

namespace CrunchzApp\Traits;

trait GroupTrait {

    /**
     * @throws \Exception
     */
    public function allGroup()
    {
        if (is_null($this->token)) {
            throw new \Exception('Channel token is required');
        }
        $client = $this->client->withToken($this->token)->get('groups');
        return $client->json();
    }

    /**
     * @throws \Exception
     */
    public function createGroup($name, $participants = [])
    {
        if (is_null($this->token)) {
            throw new \Exception('Channel token is required');
        }
        $client = $this->client->withToken($this->token)->post('groups/create', [
            'name' => $name,
            'participants' => $participants
        ]);
        return $client->json();
    }

    /**
     * @throws \Exception
     */
    public function participants($groupId)
    {
        if (is_null($this->token)) {
            throw new \Exception('Channel token is required');
        }
        $client = $this->client->withToken($this->token)->post('groups/participant', [
            'group_id' => $groupId,
        ]);
        return $client->json();
    }
}
