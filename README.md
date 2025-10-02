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

- `claude-sonnet-4-5-20250929` - Latest and most intelligent model (best for coding)
- `claude-3-opus-20240229` - Powerful model for complex tasks
- `claude-3-haiku-20240307` - Fast and cost-effective model

## Chat with Claude

Send chat message.

```php
$response = $client->chat()->create([
    'model' => 'claude-sonnet-4-5-20250929',
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
$model = 'claude-3-opus-20240229';
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
