<?php

namespace Tests\Unit;

use Anthropic\Model;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function test_latest_model_constants(): void
    {
        $this->assertEquals('claude-opus-4-7', Model::CLAUDE_OPUS_4_7);
        $this->assertEquals('claude-sonnet-4-6', Model::CLAUDE_SONNET_4_6);
        $this->assertEquals('claude-haiku-4-5-20251001', Model::CLAUDE_HAIKU_4_5);
    }

    public function test_previous_generation_model_constants(): void
    {
        $this->assertEquals('claude-opus-4-6', Model::CLAUDE_OPUS_4_6);
        $this->assertEquals('claude-sonnet-4-5-20250929', Model::CLAUDE_SONNET_4_5);
        $this->assertEquals('claude-opus-4-5-20251101', Model::CLAUDE_OPUS_4_5);
        $this->assertEquals('claude-opus-4-1-20250805', Model::CLAUDE_OPUS_4_1);
    }

    public function test_deprecated_model_constants(): void
    {
        $this->assertEquals('claude-sonnet-4-20250514', Model::CLAUDE_SONNET_4_0);
        $this->assertEquals('claude-opus-4-20250514', Model::CLAUDE_OPUS_4_0);
    }

    public function test_legacy_model_constants(): void
    {
        $this->assertEquals('claude-3-opus-20240229', Model::CLAUDE_3_OPUS);
        $this->assertEquals('claude-3-haiku-20240307', Model::CLAUDE_3_HAIKU);
        $this->assertEquals('claude-3-5-sonnet-20241022', Model::CLAUDE_3_5_SONNET);
        $this->assertEquals('claude-3-5-haiku-20241022', Model::CLAUDE_3_5_HAIKU);
    }
}
