<?php

namespace RMS\Backend\Core;

class Container
{
    private array $bindings = [];
    private array $instances = [];

    public function bind(string $key, callable $resolver): void
    {
        $this->bindings[$key] = $resolver;
    }

    public function get(string $key)
    {
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        if (!isset($this->bindings[$key])) {
            throw new \Exception("No binding found for {$key}");
        }

        $this->instances[$key] = $this->bindings[$key]($this);

        return $this->instances[$key];
    }
}