<?php

namespace Tests\Integration;

use Anthropic\Anthropic;
use Anthropic\Response\CreateResponse;
use Anthropic\Response\StreamResponse;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests that make real API calls to Anthropic.
 *
 * To run these tests:
 * 1. Copy .env.example to .env
 * 2. Add your ANTHROPIC_API_KEY to .env
 * 3. Run: vendor/bin/phpunit --testsuite Integration
 */
class ChatIntegrationTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // Load environment variables
        if (file_exists(__DIR__ . '/../../.env')) {
            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();
        }

        $apiKey = $_ENV['ANTHROPIC_API_KEY'] ?? getenv('ANTHROPIC_API_KEY');

        if (empty($apiKey)) {
            $this->markTestSkipped('ANTHROPIC_API_KEY not set in .env file');
        }

        $this->client = Anthropic::factory()
            ->withHeaders([
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
                'x-api-key' => $apiKey
            ])
            ->make();
    }

    public function test_create_chat_completion(): void
    {
        $response = $this->client->chat()->create([
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => 1024,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Say "Hello, World!" and nothing else.'
                ]
            ]
        ]);

        $this->assertInstanceOf(CreateResponse::class, $response);
        $this->assertNotEmpty($response->id);
        $this->assertEquals('message', $response->type);
        $this->assertEquals('assistant', $response->role);
        $this->assertEquals('claude-3-haiku-20240307', $response->model);
        $this->assertNotEmpty($response->choices);
        $this->assertStringContainsString('Hello', $response->choices[0]->message->content);
    }

    public function test_create_chat_with_system_message(): void
    {
        $response = $this->client->chat()->create([
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => 1024,
            'system' => 'You are a helpful assistant that always responds in exactly 3 words.',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'How are you?'
                ]
            ]
        ]);

        $this->assertInstanceOf(CreateResponse::class, $response);
        $this->assertNotEmpty($response->choices);

        // Count words in response (roughly)
        $content = $response->choices[0]->message->content;
        $wordCount = str_word_count($content);

        // System message should influence the response to be short
        $this->assertLessThan(10, $wordCount);
    }

    public function test_create_streamed_chat_completion(): void
    {
        $streamResponse = $this->client->chat()->createStreamed([
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => 100,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Count from 1 to 5.'
                ]
            ]
        ]);

        $this->assertInstanceOf(StreamResponse::class, $streamResponse);

        $chunks = [];
        foreach ($streamResponse as $chunk) {
            $chunks[] = $chunk;

            // Verify chunk structure
            $this->assertIsObject($chunk);

            // Break after a few chunks to avoid long test
            if (count($chunks) >= 5) {
                break;
            }
        }

        $this->assertGreaterThan(0, count($chunks));
    }

    public function test_response_usage_tracking(): void
    {
        $response = $this->client->chat()->create([
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => 50,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Hi'
                ]
            ]
        ]);

        $this->assertNotNull($response->usage);
        $this->assertGreaterThan(0, $response->usage->inputTokens);
        $this->assertGreaterThan(0, $response->usage->outputTokens);
    }
}