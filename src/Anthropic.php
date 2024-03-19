<?php

namespace Anthropic;

use Anthropic\Factory;

final class Anthropic
{
    public static function factory() {
        return new Factory();
    }
}