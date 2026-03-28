<?php

namespace R301\Utils\Routing;

class Router {
    /** @var Route[] */
    private array $routes = [];

    public function add(string $method, string $path, callable $handler): void
    {
        $this->routes[] = new Route($method, $path, $handler);
    }

    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function put(string $path, callable $handler): void
    {
        $this->add('PUT', $path, $handler);
    }

    public function delete(string $path, callable $handler): void
    {
        $this->add('DELETE', $path, $handler);
    }

    public function dispatch(string $method, string $uri): bool
    {
        foreach ($this->routes as $route) {
            $params = $route->match($method, $uri);
            if ($params === false) {
                continue;
            }

            if ($params === []) {
                call_user_func($route->getHandler());
            } else {
                call_user_func($route->getHandler(), $params);
            }
            return true;
        }

        return false;
    }
}
