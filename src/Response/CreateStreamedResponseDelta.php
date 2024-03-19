<?php

namespace Anthropic\Response;

final class CreateStreamedResponseDelta
{

    private function __construct(
        public readonly string $role,
        public readonly string $type,
        public readonly string $content,
    ) {
    }

    public static function from(array $attributes) : self {
        return new self(
            'assistant',
            $attributes['type'],
            $attributes['text'],
        );
    }

    public function toArray() 
    {
        return [
            'role' => $this->role,
            'content' => $this->content
        ];
    }
}
