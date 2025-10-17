<?php
namespace App\Http\Controllers;

use App\Auth\AuthService;
use PDO;
use PDOException;

final class AuthController
{
    private function pdo(): PDO
    {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $db   = getenv('DB_DATABASE') ?: '';
        $user = getenv('DB_USERNAME') ?: '';
        $pass = getenv('DB_PASSWORD') ?: '';

        $dsn = "mysql:host={$host};port={$port};dbname={$db};charset=utf8mb4";

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        return $pdo;
    }

    public function login(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

        try {
            // Aceita JSON ou form-urlencoded
            $ct = $_SERVER['CONTENT_TYPE'] ?? '';
            $in = (stripos($ct, 'application/json') !== false)
                ? (json_decode(file_get_contents('php://input'), true) ?: [])
                : $_POST;

            $id_entidade = (int)($in['id_entidade'] ?? 0);
            $senha       = (string)($in['senha'] ?? $in['pass'] ?? '');

            $auth = new AuthService($this->pdo());
            $user = $auth->authenticate($id_entidade, $senha);

            $_SESSION['auth']  = true;
            $_SESSION['uid']   = $user['id_entidade'];
            $_SESSION['uname'] = $user['nome'];

            echo json_encode(['ok' => true] + $user);
        } catch (\Throwable $e) {
            // Em produção, evite vazar detalhe de exceção
            http_response_code(500);
            $payload = ['error' => 'Internal Server Error'];
            if (getenv('APP_DEBUG') === '1' || getenv('APP_DEBUG') === 'true') {
                $payload['detail'] = $e->getMessage();
            }
            echo json_encode($payload);
        }
    }

    public function logout(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $auth = new AuthService(); // guards não precisam de PDO
            $auth->logout($_SESSION['uid'] ?? null);
            echo json_encode(['ok' => true]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Internal Server Error']);
        }
    }
}
