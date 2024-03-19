<?php

namespace Anthropic\Response;

final class CreateResponseUsage
{
    private function __construct(
        public readonly int $inputTokens,
        public readonly int $outputTokens,
        public readonly int $totalTokens,
    ) {
    }

    /**
     * @param  array{input_tokens: int, output_tokens: int}  $attributes
     */
    public static function from(array $attributes): self
    {
        return new self(
            $attributes['input_tokens'],
            $attributes['output_tokens'],
            $attributes['input_tokens'] + $attributes['output_tokens'] 
        );
    }

    /**
     * @return array{prompt_tokens: int, completion_tokens: int|null, total_tokens: int}
     */
    public function toArray(): array
    {
        return [
            'prompt_tokens' => $this->inputTokens,
            'completion_tokens' => $this->outputTokens,
            'total_tokens' => $this->totalTokens,
        ];
    }
}