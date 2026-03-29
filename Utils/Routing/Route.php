<?php

namespace R301\Utils\Routing;

class Route {
    private string $method;
    private string $path;
    private $handler;

    public function __construct(string $method, string $path, callable $handler)
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->handler = $handler;
    }

    public function match(string $method, string $uri): array|false
    {
        if ($this->method !== strtoupper($method)) {
            return false;
        }

        $pattern = preg_replace('#\{([a-zA-Z_]\w*)\}#', '(?P<$1>[^/]+)', $this->path);
        $pattern = '#^' . $pattern . '$#';

        if (!preg_match($pattern, $uri, $matches)) {
            return false;
        }

        $params = [];
        foreach ($matches as $key => $value) {
            if (!is_int($key)) {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    public function getHandler(): callable
    {
        return $this->handler;
    }
}
