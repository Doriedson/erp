<?php
namespace App\Http;

final class Router {
    private array $routes = [];

    public function get(string $path, $handler): void {
        $this->routes['GET'] ??= [];
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, $handler): void {
        $this->routes['POST'] ??= [];
        $this->routes['POST'][$path] = $handler;
    }

    public function patch(string $path, $handler): void {
        $this->routes['PATCH'] ??= [];
        $this->routes['PATCH'][$path] = $handler;
    }

    public function put(string $path, $handler): void {
        $this->routes['PUT'] ??= [];
        $this->routes['PUT'][$path] = $handler;
    }

    public function delete(string $path, $handler): void {
        $this->routes['DELETE'] ??= [];
        $this->routes['DELETE'][$path] = $handler;
    }

    public function mount(string $prefix, callable $group): void {
        $r = new self();
        $group($r);
        foreach ($r->routes as $method => $items) {
            $this->routes[$method] ??= [];
            foreach ($items as $path => $h) {
                $this->routes[$method][$prefix . $path] = $h;
            }
        }
    }

    public function run(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        $bag = $this->routes[$method] ?? [];

        foreach ($bag as $path => $handler) {
            $regex = '~^' . $path . '$~u';
            if (preg_match($regex, $uri, $m)) {
                array_shift($m);
                try {
                    $out = is_callable($handler)
                        ? $handler(...$m)
                        : $this->callController($handler, $m);
                    $this->respond($out);
                } catch (\Throwable $e) {
                    http_response_code(500);
                    $this->respond(['error' => 'Internal Server Error']);
                }
                return;
            }
        }

        http_response_code(404);
        $this->respond(['error' => 'Not Found']);
    }

    private function callController($handler, array $params) {
        if (is_array($handler) && count($handler) === 2) {
            [$classOrObj, $method] = $handler;
            $instance = is_string($classOrObj) ? new $classOrObj() : $classOrObj;
            return $instance->$method(...$params);
        }
        if (is_string($handler) && str_contains($handler, '@')) {
            [$class, $method] = explode('@', $handler, 2);
            $instance = new $class();
            return $instance->$method(...$params);
        }
        if (is_callable($handler)) {
            return $handler(...$params);
        }
        throw new \InvalidArgumentException('Handler de rota inválido');
    }

    private function respond($out): void {
        if (is_array($out) || is_object($out)) {
            if (!headers_sent()) {
                header('Content-Type: application/json; charset=utf-8');
            }
            echo json_encode($out, JSON_UNESCAPED_UNICODE);
            return;
        }
        if (is_string($out)) {
            echo $out;
            return;
        }
    }
}
