<?php

namespace CrunchzApp\Services;

use CrunchzApp\Base\WhatsAppBase;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

final class ChannelService extends WhatsAppBase {

    public function __construct()
    {
        $this->client = Http::baseUrl($this->endpoint);
        $this->token = config('crunchzapp.token');
    }

    /**
     * @throws \Exception
     */
    public function sendPool(): array
    {
        if (is_null($this->token)) {
            throw new \Exception('Channel token is required');
        }
        if (count($this->payload) < 1) {
            throw new \Exception('You cannot use less than 2 payload when using sendPool method, please use send function instead');
        }

        try {
            $array = [];
            $client = Http::pool(fn (Pool $pool) => $this->poolHttpRequest($pool));
            foreach ($client as $key => $item) {
                if (!$item->ok()) {
                    throw new \Exception('Some api have an error sending. Please review what you are doing');
                } else {
                    $payload = $this->payload[$key];
                    $array[$key] = [
                        'path' => $payload['path'],
                        'body' => $payload['body'],
                        'result' => $item->json()
                    ];
                }
            }
            return $array;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @throws \Exception
     */
    public function send()
    {
        if (is_null($this->token)) {
            throw new \Exception('Channel token is required');
        }

        if (count($this->payload) > 1) {
            throw new \Exception('You cannot use more than 1 payload when using send method, please use sendPool function instead.');
        }

        try {
            $payload = $this->payload[0];
            return $this->payload;
//            return $this->client->withToken($this->token)->{$payload['method']}($payload['path'], $payload['body'])->json();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param Pool $pool
     * @return void
     */
    function poolHttpRequest(Pool $pool): void
    {
        foreach ($this->payload as $value) {
            $method = strtolower($value['method']);
            $pool->withToken($this->token)->{$method}('https://api.crunchz.app/api'.$value['path'], $value['body']);
        }
    }

}
