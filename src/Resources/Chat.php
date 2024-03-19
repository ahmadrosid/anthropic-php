<?php

namespace Anthropic\Resources;

use Anthropic\Factory;
use Anthropic\Response\CreateResponse;
use Anthropic\Response\StreamResponse;
use GuzzleHttp\Client as GuzzleClient;

class Chat {

    private Factory $factory;
    private GuzzleClient $client;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
        $this->client = new GuzzleClient([
            'base_uri'        => $factory->baseUri,
            'timeout'         => $factory->timeout,
            'allow_redirects' => false,
        ]);
    }

    /**
     * Example payload
     * [
     *      'model' => 'claude-3-opus-20240229',
     *      'temperature' => $temperature,
     *      'max_tokens' => 1024,
     *      'system' => $systemMessage,
     *      'messages' => $messages,
     * ]
     */
    public function create(array $payload)
    {
        $response = $this->client->post('messages', [
            'headers' => $this->factory->headers,
            'json' => $payload,
        ]);

        $responseBody = $response->getBody()->getContents();
        
        $response = json_decode($responseBody, true, flags: JSON_THROW_ON_ERROR);

        if (isset($response['error'])) {
            throw new \Exception($response['error']);
        }

        return CreateResponse::from($response);
    }

    /**
     * Example payload
     * [
     *      'model' => 'claude-3-opus-20240229',
     *      'messages' => $messages,
     *      'max_tokens' => 1024,
     *      'stream' => true
     * ]
     */
    public function createStreamed(array $payload): StreamResponse
    {
        $payload['stream'] = true;
        $response = $this->client->post('messages', [
            'headers' => $this->factory->headers,
            'json' => $payload,
            'stream' => true
        ]);
        
        return new StreamResponse($response);
    }

}