<?php

namespace Anthropic\Response;

final class CreateResponseContent
{

    private function __construct(
        public readonly int $index,
        public readonly CreateResponseMessage $message,
     ) {
    }

    public static function from(array $attributes) {
        return new self(
            $attributes['index'],
            CreateResponseMessage::from($attributes),
        );
    }

    public function toArray() {
        return [
            'index' => $this->index,
            'message' => $this->message->toArray(),
        ];
    }
}
