<?php

namespace Tests\Integration;

use Anthropic\Anthropic;
use Anthropic\Response\CreateResponse;
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for Claude Sonnet 4.5 model.
 *
 * Claude Sonnet 4.5 is Anthropic's most intelligent model, best for coding and complex agents.
 * Model name: claude-sonnet-4-5-20250929
 */
class ClaudeSonnet45Test extends TestCase
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

    public function test_claude_sonnet_45_basic_completion(): void
    {
        $response = $this->client->chat()->create([
            'model' => 'claude-sonnet-4-5-20250929',
            'max_tokens' => 1024,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Write a simple PHP function to check if a number is even. Just return the code, no explanation.'
                ]
            ]
        ]);

        $this->assertInstanceOf(CreateResponse::class, $response);
        $this->assertNotEmpty($response->id);
        $this->assertEquals('message', $response->type);
        $this->assertEquals('assistant', $response->role);
        $this->assertEquals('claude-sonnet-4-5-20250929', $response->model);
        $this->assertNotEmpty($response->choices);

        $content = $response->choices[0]->message->content;
        $this->assertStringContainsString('function', $content);
        $this->assertStringContainsString('even', strtolower($content));
    }

    public function test_claude_sonnet_45_coding_task(): void
    {
        $response = $this->client->chat()->create([
            'model' => 'claude-sonnet-4-5-20250929',
            'max_tokens' => 2048,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'What is 15 * 7? Reply with just the number.'
                ]
            ]
        ]);

        $this->assertInstanceOf(CreateResponse::class, $response);
        $content = $response->choices[0]->message->content;

        // Should contain the correct answer
        $this->assertStringContainsString('105', $content);
    }

    public function test_claude_sonnet_45_with_system_message(): void
    {
        $response = $this->client->chat()->create([
            'model' => 'claude-sonnet-4-5-20250929',
            'max_tokens' => 500,
            'system' => 'You are a helpful assistant that responds in exactly 5 words or less.',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'What is the capital of France?'
                ]
            ]
        ]);

        $this->assertInstanceOf(CreateResponse::class, $response);
        $this->assertNotEmpty($response->choices);

        $content = $response->choices[0]->message->content;

        // System message should make the response very concise
        $this->assertStringContainsString('Paris', $content);

        // Should be short (less than 20 words due to system prompt)
        $wordCount = str_word_count($content);
        $this->assertLessThan(20, $wordCount);
    }

    public function test_claude_sonnet_45_usage_tracking(): void
    {
        $response = $this->client->chat()->create([
            'model' => 'claude-sonnet-4-5-20250929',
            'max_tokens' => 100,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Say hello in 3 words.'
                ]
            ]
        ]);

        // Verify usage tracking works
        $this->assertNotNull($response->usage);
        $this->assertGreaterThan(0, $response->usage->inputTokens);
        $this->assertGreaterThan(0, $response->usage->outputTokens);
        $this->assertEquals(
            $response->usage->inputTokens + $response->usage->outputTokens,
            $response->usage->totalTokens
        );
    }

    public function test_claude_sonnet_45_streaming(): void
    {
        $streamResponse = $this->client->chat()->createStreamed([
            'model' => 'claude-sonnet-4-5-20250929',
            'max_tokens' => 200,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'List 3 programming languages.'
                ]
            ]
        ]);

        $chunks = [];
        foreach ($streamResponse as $chunk) {
            $chunks[] = $chunk;

            // Verify chunk structure
            $this->assertIsObject($chunk);

            // Break after collecting a few chunks
            if (count($chunks) >= 10) {
                break;
            }
        }

        $this->assertGreaterThan(0, count($chunks), 'Should receive at least one chunk from streaming response');
    }
}