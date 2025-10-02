<?php

namespace Tests\Unit;

use Anthropic\Client;
use Anthropic\Factory;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    private Factory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new Factory();
    }

    public function test_with_header_sets_single_header(): void
    {
        $this->factory->withHeader('x-api-key', 'test-key');

        $this->assertEquals('test-key', $this->factory->headers['x-api-key']);
    }

    public function test_with_header_returns_self(): void
    {
        $result = $this->factory->withHeader('x-api-key', 'test-key');

        $this->assertSame($this->factory, $result);
    }

    public function test_with_headers_sets_multiple_headers(): void
    {
        $headers = [
            'x-api-key' => 'test-key',
            'anthropic-version' => '2023-06-01',
            'content-type' => 'application/json',
        ];

        $this->factory->withHeaders($headers);

        $this->assertEquals('test-key', $this->factory->headers['x-api-key']);
        $this->assertEquals('2023-06-01', $this->factory->headers['anthropic-version']);
        $this->assertEquals('application/json', $this->factory->headers['content-type']);
    }

    public function test_with_headers_returns_self(): void
    {
        $result = $this->factory->withHeaders(['x-api-key' => 'test']);

        $this->assertSame($this->factory, $result);
    }

    public function test_with_api_key_sets_api_key(): void
    {
        $this->factory->withApiKey('test-api-key');

        $this->assertEquals('test-api-key', $this->factory->apiKey);
    }

    public function test_with_api_key_returns_self(): void
    {
        $result = $this->factory->withApiKey('test-key');

        $this->assertSame($this->factory, $result);
    }

    public function test_with_base_uri_sets_base_uri(): void
    {
        $this->factory->withBaseUri('https://custom.api.com/');

        $this->assertEquals('https://custom.api.com/', $this->factory->baseUri);
    }

    public function test_with_base_uri_returns_self(): void
    {
        $result = $this->factory->withBaseUri('https://custom.api.com/');

        $this->assertSame($this->factory, $result);
    }

    public function test_make_returns_client_instance(): void
    {
        $this->factory->withHeaders([
            'anthropic-version' => '2023-06-01',
            'x-api-key' => 'test-key'
        ]);

        $client = $this->factory->make();

        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_make_throws_exception_when_anthropic_version_missing(): void
    {
        $this->factory->withHeader('x-api-key', 'test-key');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required header: anthropic-version');

        $this->factory->make();
    }

    public function test_make_throws_exception_when_api_key_missing(): void
    {
        $this->factory->withHeader('anthropic-version', '2023-06-01');

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing required header: x-api-key');

        $this->factory->make();
    }

    public function test_default_base_uri(): void
    {
        $this->assertEquals('https://api.anthropic.com/v1/', $this->factory->baseUri);
    }

    public function test_default_timeout(): void
    {
        $this->assertEquals(5000, $this->factory->timeout);
    }
}