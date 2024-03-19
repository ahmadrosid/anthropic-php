<?php

namespace Anthropic\Response;

final class CreateResponseMessage
{

    private function __construct(
        public readonly string $type,
        public readonly string $content,
    ) {
    }

    public static function from(array $attributes)
    {
        return new self(
            $attributes['type'],
            $attributes['text'],
        );
    }

    public function toArray() 
    {
        return [
            'type' => $this->type,
            'content' => $this->content,
        ];
    }
}