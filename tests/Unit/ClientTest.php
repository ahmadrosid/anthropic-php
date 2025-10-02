<?php

namespace Tests\Unit;

use Anthropic\Client;
use Anthropic\Factory;
use Anthropic\Resources\Chat;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private Client $client;
    private Factory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new Factory();
        $this->client = new Client($this->factory);
    }

    public function test_chat_returns_chat_instance(): void
    {
        $chat = $this->client->chat();

        $this->assertInstanceOf(Chat::class, $chat);
    }
}