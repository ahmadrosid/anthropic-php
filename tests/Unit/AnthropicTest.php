<?php

namespace Tests\Unit;

use Anthropic\Anthropic;
use Anthropic\Factory;
use PHPUnit\Framework\TestCase;

class AnthropicTest extends TestCase
{
    public function test_factory_returns_factory_instance(): void
    {
        $factory = Anthropic::factory();

        $this->assertInstanceOf(Factory::class, $factory);
    }
}