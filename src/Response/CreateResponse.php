<?php

namespace Anthropic\Response;

final class CreateResponse
{

    /**
     * @param  array<int, CreateResponseContent>  $choices
     */
    private function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $role,
        public readonly string $model,
        public readonly string $stop_reason,
        public readonly ?string $stop_sequence,
        public readonly array $choices,
        public readonly CreateResponseUsage $usage,
    ) {
    }

    /**
     * Acts as static factory, and returns a new Response instance.
     *
     * @param  array{id: string, object: string, created: int, model: string, system_fingerprint?: string, choices: array<int, array{index: int, message: array{role: string, content: ?string, function_call: ?array{name: string, arguments: string}, tool_calls: ?array<int, array{id: string, type: string, function: array{name: string, arguments: string}}>}, finish_reason: string|null}>, usage: array{prompt_tokens: int, completion_tokens: int|null, total_tokens: int}}  $attributes
     */
    public static function from(array $attributes): self
    {
        $index = 0;
        $content = array_map(function (array $result) use ($index): CreateResponseContent {
            $result['index'] = $index;
            $item = CreateResponseContent::from(
                $result
            );
            $index += 1;
            return $item;
        }, $attributes['content']);

        return new self(
            $attributes['id'],
            $attributes['type'],
            $attributes['role'],
            $attributes['model'],
            $attributes['stop_reason'],
            $attributes['stop_sequence'] ?? null,
            $content,
            CreateResponseUsage::from($attributes['usage']),
        );
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'role' => $this->role,
            'model' => $this->model,
            'stop_reason' => $this->stop_reason,
            'stop_sequence' => $this->stop_sequence,
            'choices' => array_map(
                static fn (CreateResponseContent $result): array => $result->toArray(),
                $this->choices,
            ),
            'usage' => $this->usage->toArray(),
        ];
    }
}
