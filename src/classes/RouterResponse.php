<?php

// This is just used as an object to define a unified structure for returning network request data
class RouterResponse {

    // Fields
    public string $page;
    public string $method;
    public string $route;
    public array $data;

    //Constructor
    public function __construct(array $response_data) {
        if (
            !isset($response_data['page']) || 
            !isset($response_data['method']) ||
            !isset($response_data['route']) || 
            !isset($response_data['data'])
            ) {
            throw new InvalidArgumentException('Missing required field');
        }

        $this->page   = $response_data['page'];
        $this->method = $response_data['method'];
        $this->route  = $response_data['route'];
        $this->data   = $response_data['data'];
    }
}