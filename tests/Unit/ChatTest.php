<?php

namespace Tests\Unit;

use Anthropic\Factory;
use Anthropic\Resources\Chat;
use Anthropic\Response\CreateResponse;
use Anthropic\Response\StreamResponse;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Mockery;
use PHPUnit\Framework\TestCase;

class ChatTest extends TestCase
{
    private Factory $factory;
    private Chat $chat;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new Factory();
        $this->factory->withHeaders([
            'x-api-key' => 'test-key',
            'anthropic-version' => '2023-06-01',
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_chat_instance_can_be_created(): void
    {
        $chat = new Chat($this->factory);

        $this->assertInstanceOf(Chat::class, $chat);
    }

    public function test_create_returns_create_response(): void
    {
        $mockResponse = [
            'id' => 'msg_123',
            'type' => 'message',
            'role' => 'assistant',
            'model' => 'claude-opus-4-7',
            'stop_reason' => 'end_turn',
            'stop_sequence' => null,
            'content' => [
                [
                    'type' => 'text',
                    'text' => 'Hello!'
                ]
            ],
            'usage' => [
                'input_tokens' => 10,
                'output_tokens' => 5
            ]
        ];

        $guzzleResponse = new GuzzleResponse(200, [], json_encode($mockResponse));

        $mockGuzzle = Mockery::mock(GuzzleClient::class);
        $mockGuzzle->shouldReceive('post')
            ->once()
            ->with('messages', Mockery::type('array'))
            ->andReturn($guzzleResponse);

        $chat = new Chat($this->factory);

        // Use reflection to inject mock Guzzle client
        $reflection = new \ReflectionClass($chat);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($chat, $mockGuzzle);

        $response = $chat->create([
            'model' => 'claude-opus-4-7',
            'max_tokens' => 1024,
            'messages' => [
                ['role' => 'user', 'content' => 'Hello']
            ]
        ]);

        $this->assertInstanceOf(CreateResponse::class, $response);
        $this->assertEquals('msg_123', $response->id);
        $this->assertEquals('assistant', $response->role);
    }

    public function test_create_throws_exception_on_error(): void
    {
        $mockResponse = [
            'error' => 'Invalid API key'
        ];

        $guzzleResponse = new GuzzleResponse(401, [], json_encode($mockResponse));

        $mockGuzzle = Mockery::mock(GuzzleClient::class);
        $mockGuzzle->shouldReceive('post')
            ->once()
            ->andReturn($guzzleResponse);

        $chat = new Chat($this->factory);

        $reflection = new \ReflectionClass($chat);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($chat, $mockGuzzle);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid API key');

        $chat->create([
            'model' => 'claude-opus-4-7',
            'max_tokens' => 1024,
            'messages' => [['role' => 'user', 'content' => 'Hello']]
        ]);
    }

    public function test_create_streamed_returns_stream_response(): void
    {
        $guzzleResponse = Mockery::mock(GuzzleResponse::class);

        $mockGuzzle = Mockery::mock(GuzzleClient::class);
        $mockGuzzle->shouldReceive('post')
            ->once()
            ->with('messages', Mockery::on(function ($arg) {
                return $arg['stream'] === true && $arg['json']['stream'] === true;
            }))
            ->andReturn($guzzleResponse);

        $chat = new Chat($this->factory);

        $reflection = new \ReflectionClass($chat);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($chat, $mockGuzzle);

        $response = $chat->createStreamed([
            'model' => 'claude-opus-4-7',
            'max_tokens' => 1024,
            'messages' => [['role' => 'user', 'content' => 'Hello']]
        ]);

        $this->assertInstanceOf(StreamResponse::class, $response);
    }
}