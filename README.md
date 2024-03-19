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
$headers = [
    'anthropic-version' => '2023-06-01',
    'anthropic-beta' => 'messages-2023-12-15',
    'content-type' => 'application/json',
    'x-api-key' => env('ANTHROPIC_API_KEY')
];

$client = Anthropic::factory()
    ->withHeaders($headers)
    ->make();
```

## Chat with Claude

Send chat message.

```php
$model = 'claude-3-opus-20240229';
$max_tokens = 4096;
$systemMessage = 'Always reply with "Hello!"';
$messages = [
    [
        'role' => 'user',
        'content' => 'Hi there...'
    ]
];

$response = $client->chat()->create([
    'model' => $model,
    'temperature' => $temperature,
    'max_tokens' => $max_tokens,
    'system' => $systemMessage,
    'messages' => $messages,
]);

$content = $response->choices[0]->message->content;

echo $content;
```

## Chat Streaming

Process server sent event reply from chatbot.

```php
$model = 'claude-3-opus-20240229';
$max_tokens = 4096;
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

## LICENSE

MIT
