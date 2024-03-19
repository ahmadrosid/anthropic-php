<?php

namespace Anthropic\Response;

final class CreateStreamedResponse
{
    /**
     * @param  array<int, CreateStreamedResponseChoice>  $choices
     */
    private function __construct(
        public readonly array $choices,
        public readonly ?CreateStreamedResponseUsage $usage,
    ) {
    }

    public static function from(array $attributes)
    {
        return new self(
            [CreateStreamedResponseChoice::from($attributes['delta'])],
            isset($attributes['usage']) ? CreateStreamedResponseUsage::from($attributes['usage']) : null
        );
    }

    public function toArray()
    {
        return [
            'choiches' => $this->choices,
        ];
    }
}
