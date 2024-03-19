<?php

namespace Anthropic\Response;

final class CreateStreamedResponseChoice
{

    private function __construct(
        public readonly int $index,
        public readonly CreateStreamedResponseDelta $delta,
    ) {
    }

    public static function from(array $attributes) : self {
        return new self(
            0,
            CreateStreamedResponseDelta::from($attributes)
        );
    }

    public function toArray() {
        return [
            'index' => $this->index,
            'delta' => $this->delta->toArray()
        ];
    }
}
