<?php

namespace Requests\Core\Interfaces;

interface RequestLogger
{
    public function send(string $tag, string $uri, array $args, $result): void;
}