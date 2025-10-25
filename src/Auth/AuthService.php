<?php
namespace App\Auth;

use App\Database\Connection;
use PDO;

final class AuthService
{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo;
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function authenticate(int $id_entidade, string $senha): bool {
        $pdo = Connection::pdo();
        $sql = "SELECT c.hash, e.ativo
                  FROM tab_colaborador c
                  JOIN tab_entidade e ON e.id_entidade = c.id_entidade
                 WHERE c.id_entidade = :id";
        $st = $pdo->prepare($sql);
        $st->execute([':id' => $id_entidade]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;
        if ((int)$row['ativo'] !== 1) return false;                 // opcional: só loga ativo
        if (!password_verify($senha, $row['hash'])) return false;   // hash é bcrypt

        if (session_status() !== \PHP_SESSION_ACTIVE) @session_start();
        session_regenerate_id(true);
        $_SESSION['auth'] = ['id_entidade' => $id_entidade];

        return true;
    }

    public function logout(): void {

        if (session_status() !== \PHP_SESSION_ACTIVE) @session_start();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']
            );
        }
        session_destroy();
    }

    public function check(): bool
    {
        return !empty($_SESSION['auth']) && !empty($_SESSION['uid']);
    }

    public function requireAuthForApi(): void
    {
        if ($this->isAuthenticated()) return;

        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Unauthorized', 'code' => 'AUTH_REQUIRED']);
        exit;
    }

    public function requireAuthForPage(string $loginPath = '/login'): void
    {
        if ($this->check()) return;
        header('Location: ' . $loginPath);
        exit;
    }

    public function isAuthenticated(): bool
    {
        if (session_status() !== \PHP_SESSION_ACTIVE) {
            @session_start();
        }
        return isset($_SESSION['auth']['id_entidade']);
    }

    public function userId(): ?int
    {
        return $_SESSION['auth']['id_entidade'] ?? null;
    }
}
