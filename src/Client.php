<?php

namespace Anthropic;

use Anthropic\Factory;
use Anthropic\Resources\Chat;

class Client
{
    private Factory $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function chat() {
        return new Chat($this->factory);
    }
}