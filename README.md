# Anthropic PHP

Anthropic PHP is library to interact with Anthropic API, this library is designed to be close to OpenAI PHP. The goal of this library is to have the same API to OpenAI PHP client, so you can switch from GPT model to Claude easily.

## Installation

Make sure you are using php: ^8.1.0.

```bash
composer require ahmadrosid/anthropic-php
```

## How to use?

Create anthropic client.

```php
use Anthropic\Anthropic;

$client = Anthropic::factory()
    ->withHeaders([
        'anthropic-version' => '2023-06-01',  // Required!
        'content-type' => 'application/json',
        'x-api-key' => env('ANTHROPIC_API_KEY', '')
    ])
    ->make();
```

**Important:** The `anthropic-version` header is required. Without it, you'll get a 404 error.

## Available Models

### Latest Models

| Model | API ID | Description |
|-------|--------|-------------|
| Claude Opus 4.7 | `claude-opus-4-7` | Most capable model for complex reasoning and agentic coding |
| Claude Sonnet 4.6 | `claude-sonnet-4-6` | Best combination of speed and intelligence |
| Claude Haiku 4.5 | `claude-haiku-4-5-20251001` | Fastest model with near-frontier intelligence |

### Previous Generation (still available)

| Model | API ID |
|-------|--------|
| Claude Opus 4.6 | `claude-opus-4-6` |
| Claude Sonnet 4.5 | `claude-sonnet-4-5-20250929` |
| Claude Opus 4.5 | `claude-opus-4-5-20251101` |
| Claude Opus 4.1 | `claude-opus-4-1-20250805` |

### Using Model Constants

You can use the `Model` class for convenient access to model identifiers:

```php
use Anthropic\Model;

$response = $client->chat()->create([
    'model' => Model::CLAUDE_OPUS_4_7,
    'max_tokens' => 4096,
    'messages' => [
        ['role' => 'user', 'content' => 'Hello!']
    ]
]);
```

## Chat with Claude

Send chat message.

```php
$response = $client->chat()->create([
    'model' => 'claude-opus-4-7',
    'max_tokens' => 4096,
    'system' => 'You are a helpful assistant.',
    'messages' => [
        [
            'role' => 'user',
            'content' => 'Hello, how are you?'
        ]
    ]
]);

$content = $response->choices[0]->message->content;
echo $content;
```

## Chat Streaming

Process server sent event reply from chatbot.

```php
$model = 'claude-sonnet-4-6';
$max_tokens = 4096;
$temperature = 0;
$systemMessage = 'Always reply with "Hello!"';
$messages = [
    [
        'role' => 'user',
        'content' => 'Hi there...'
    ]
];
$stream = $client->chat()->createStreamed([
    'model' => $model,
    'temperature' => $temperature,
    'max_tokens' => $max_tokens,
    'system' => $systemMessage,
    'messages' => $messages,
]);

foreach ($stream as $response) {
    $text = $response->choices[0]->delta->content;

    echo $text;
}
```

## Testing

This package includes both unit tests and integration tests.

### Running Unit Tests

Unit tests use mocked HTTP clients and don't require an API key:

```bash
composer test
```

Or run only unit tests:

```bash
vendor/bin/phpunit --testsuite Unit
```

### Running Integration Tests

Integration tests make real API calls to Anthropic. To run them:

1. Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```

2. Add your Anthropic API key to `.env`:
   ```
   ANTHROPIC_API_KEY=your-api-key-here
   ```

3. Run integration tests:
   ```bash
   vendor/bin/phpunit --testsuite Integration
   ```

**Note:** Integration tests will consume API credits.

### Running All Tests

```bash
vendor/bin/phpunit
```

## LICENSE

MIT
