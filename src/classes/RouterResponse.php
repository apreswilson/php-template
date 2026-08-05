<?php

// This is just used as an object to define a unified structure for returning network request data
class RouterResponse {
    public function __construct(
        public string $page,
        public string $method,
        public string $route,
        public array $data
    ) {}
}