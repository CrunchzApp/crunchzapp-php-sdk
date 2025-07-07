<?php

namespace CrunchzApp\Traits;

use InvalidArgumentException;
use RuntimeException;

trait GroupTrait
{
    private const GROUP_ACTION_ALL = 'all';
    private const GROUP_ACTION_CREATE = 'create';
    private const GROUP_ACTION_PARTICIPANTS = 'participants';

    /**
     * Get all groups
     * 
     * @return static
     * @throws RuntimeException When token is missing
     */
    public function allGroup(): static
    {
        return $this->addGroupPayload(self::GROUP_ACTION_ALL, [], 'GET');
    }

    /**
     * Create a new group
     * 
     * @param string $name The group name
     * @param array $participants Array of participant contact IDs
     * @return static
     * @throws InvalidArgumentException When parameters are invalid
     * @throws RuntimeException When token is missing
     */
    public function createGroup(string $name, array $participants): static
    {
        if (empty(trim($name))) {
            throw new InvalidArgumentException('Group name cannot be empty');
        }
        if (empty($participants)) {
            throw new InvalidArgumentException('At least one participant is required to create a group');
        }
        
        // Validate participant format
        foreach ($participants as $participant) {
            if (!is_string($participant) || empty(trim($participant))) {
                throw new InvalidArgumentException('All participants must be valid contact IDs');
            }
        }
        
        return $this->addGroupPayload(self::GROUP_ACTION_CREATE, [
            'name' => trim($name),
            'participants' => $participants
        ]);
    }

    /**
     * Get group participants
     * 
     * @param string $groupId The group ID
     * @return static
     * @throws InvalidArgumentException When group ID is invalid
     * @throws RuntimeException When token is missing
     */
    public function participants(string $groupId): static
    {
        if (empty(trim($groupId))) {
            throw new InvalidArgumentException('Group ID cannot be empty');
        }
        
        return $this->addGroupPayload(self::GROUP_ACTION_PARTICIPANTS, [
            'group_id' => $groupId
        ], 'GET');
    }
    
    /**
     * Add a group payload to the request queue
     * 
     * @param string $action The group action
     * @param array $body The request body
     * @param string $method The HTTP method
     * @return static
     * @throws RuntimeException When token is missing
     */
    private function addGroupPayload(string $action, array $body, string $method = 'POST'): static
    {
        $this->validateToken();
        
        $this->payload[] = [
            'method' => $method,
            'path' => '/group/' . $action,
            'body' => $body
        ];
        
        return $this;
    }
}
