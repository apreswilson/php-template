<?php

class Props {
    public function __construct(public array $props = []) {}

    public function get(string $key, mixed $default = null): mixed {
        return $this->props[$key] ?? $default;
    }
}