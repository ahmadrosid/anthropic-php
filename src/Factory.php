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
        $this->validateHeaders();
        return new Client($this);
    }

    /**
     * Validate that required headers are present.
     *
     * @throws \InvalidArgumentException
     */
    private function validateHeaders(): void
    {
        if (empty($this->headers['anthropic-version'])) {
            throw new \InvalidArgumentException(
                'Missing required header: anthropic-version. ' .
                'This header is required by the Anthropic API. ' .
                'Use ->withHeader("anthropic-version", "2023-06-01") or include it in withHeaders().'
            );
        }

        if (empty($this->headers['x-api-key'])) {
            throw new \InvalidArgumentException(
                'Missing required header: x-api-key. ' .
                'You must provide your Anthropic API key. ' .
                'Use ->withHeader("x-api-key", "your-api-key") or include it in withHeaders().'
            );
        }
    }
}