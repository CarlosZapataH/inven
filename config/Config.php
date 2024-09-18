<?php

class Config
{
    protected $config = [
        'company' => [
            'document_type_code' => 6,
            'document' => '20357259976'
        ]
    ];

    public function __construct()
    {
    }

    public function get($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
}
