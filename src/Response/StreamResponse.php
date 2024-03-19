<?php

namespace Anthropic\Response;

use IteratorAggregate;
use Generator;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;

class StreamResponse implements IteratorAggregate
{
    public function __construct(
        private readonly ResponseInterface $response
    ) {
    }

    private function getMessageStartUsage($response)
    {
        return isset($response['message']['usage']) ? $response['message']['usage'] : [];
    }

    private function readLine(StreamInterface $stream): string
    {
        $buffer = '';

        while (!$stream->eof()) {
            if ('' === ($byte = $stream->read(1))) {
                return $buffer;
            }
            $buffer .= $byte;
            if ($byte === "\n") {
                break;
            }
        }

        return $buffer;
    }

    public function getIterator(): Generator
    {
        $body = $this->response->getBody();

        $usage = [];

        while (!$body->eof()) {
            $line = $this->readLine($body);

            if (!str_starts_with($line, 'data:')) {
                continue;
            }

            $data = trim(substr($line, strlen('data:')));

            $response = json_decode($data, true, flags: JSON_THROW_ON_ERROR);

            if (isset($response['error'])) {
                throw new \Exception($response['error']);
            }

            if (!isset($response['type'])) continue;

            if ($response['type'] == 'message_start') {
                $usage = $this->getMessageStartUsage($response);
            }

            if ($response['type'] == 'message_delta') {
                $usage['output_tokens'] = $response['usage']['output_tokens'];
                $response['usage'] = $usage;
                $response['delta'] = [
                    'type' => 'text_delta',
                    'text' => '',
                ];
                yield CreateStreamedResponse::from($response);
                continue;
            }

            if (!isset($response['delta'])) continue;

            if ($response['type'] !== 'content_block_delta') {
                continue;
            }

            $response['usage'] = $usage;
            yield CreateStreamedResponse::from($response);
        }
    }
}
