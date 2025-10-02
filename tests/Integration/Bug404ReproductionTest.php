<?php

namespace Tests\Integration;

use Anthropic\Anthropic;
use GuzzleHttp\Exception\ClientException;
use PHPUnit\Framework\TestCase;

/**
 * Test to reproduce the 404 error reported in GitHub issues.
 *
 * The 404 error occurs when the required 'anthropic-version' header is missing.
 */
class Bug404ReproductionTest extends TestCase
{
    private string $apiKey;

    protected function setUp(): void
    {
        parent::setUp();

        // Load environment variables
        if (file_exists(__DIR__ . '/../../.env')) {
            $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();
        }

        $this->apiKey = $_ENV['ANTHROPIC_API_KEY'] ?? getenv('ANTHROPIC_API_KEY');

        if (empty($this->apiKey)) {
            $this->markTestSkipped('ANTHROPIC_API_KEY not set in .env file');
        }
    }

    /**
     * This test verifies that missing anthropic-version header is now caught early
     * with a helpful error message instead of resulting in a 404 from the API.
     */
    public function test_missing_anthropic_version_header_throws_validation_error(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required header: anthropic-version');

        // This is the WRONG way - missing anthropic-version header
        // Now it throws an InvalidArgumentException immediately
        Anthropic::factory()
            ->withHeaders([
                'content-type' => 'application/json',
                'x-api-key' => $this->apiKey
                // Missing 'anthropic-version' header!
            ])
            ->make();
    }

    /**
     * This test shows the CORRECT way with all required headers.
     */
    public function test_with_required_anthropic_version_header_works(): void
    {
        // This is the CORRECT way - includes all required headers
        $client = Anthropic::factory()
            ->withHeaders([
                'anthropic-version' => '2023-06-01', // Required!
                'content-type' => 'application/json',
                'x-api-key' => $this->apiKey
            ])
            ->make();

        // This should work fine
        $response = $client->chat()->create([
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => 10,
            'messages' => [
                ['role' => 'user', 'content' => 'Hi']
            ]
        ]);

        $this->assertNotNull($response);
        $this->assertEquals('message', $response->type);
    }

    /**
     * Test with wrong anthropic-version header value.
     */
    public function test_invalid_anthropic_version_header(): void
    {
        $client = Anthropic::factory()
            ->withHeaders([
                'anthropic-version' => 'invalid-version',
                'content-type' => 'application/json',
                'x-api-key' => $this->apiKey
            ])
            ->make();

        $this->expectException(ClientException::class);

        $client->chat()->create([
            'model' => 'claude-3-haiku-20240307',
            'max_tokens' => 10,
            'messages' => [
                ['role' => 'user', 'content' => 'Hi']
            ]
        ]);
    }
}