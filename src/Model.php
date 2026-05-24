<?php

namespace Anthropic;

final class Model
{
    // Latest models
    public const CLAUDE_OPUS_4_7 = 'claude-opus-4-7';
    public const CLAUDE_SONNET_4_6 = 'claude-sonnet-4-6';
    public const CLAUDE_HAIKU_4_5 = 'claude-haiku-4-5-20251001';

    // Previous generation (still available)
    public const CLAUDE_OPUS_4_6 = 'claude-opus-4-6';
    public const CLAUDE_SONNET_4_5 = 'claude-sonnet-4-5-20250929';
    public const CLAUDE_OPUS_4_5 = 'claude-opus-4-5-20251101';
    public const CLAUDE_OPUS_4_1 = 'claude-opus-4-1-20250805';

    // Deprecated (retiring June 15, 2026)
    public const CLAUDE_SONNET_4_0 = 'claude-sonnet-4-20250514';
    public const CLAUDE_OPUS_4_0 = 'claude-opus-4-20250514';

    // Legacy models
    public const CLAUDE_3_OPUS = 'claude-3-opus-20240229';
    public const CLAUDE_3_HAIKU = 'claude-3-haiku-20240307';
    public const CLAUDE_3_5_SONNET = 'claude-3-5-sonnet-20241022';
    public const CLAUDE_3_5_HAIKU = 'claude-3-5-haiku-20241022';
}
