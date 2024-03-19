<?php

namespace Anthropic;

class Factory
{
    public array $headers;

    public string $apiKey;

    public int $timeout = 5000;

    public string $baseUri = 'https://api.anthropic.com/v1/';

    public function withHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public function withHeaders(array $headers): self
    {
        foreach ($headers as $key => $value) {
            $this->withHeader($key, $value);
        }

        return $this;
    }

    public function withApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    public function withBaseUri(string $baseUri): self
    {
        $this->baseUri = $baseUri;

        return $this;
    }

    public function make() {
        return new Client($this);
    }
}