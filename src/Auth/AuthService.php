<?php
namespace App\Auth;

use App\Infra\Database\Connection;
use PDO;

final class AuthService
{
    private ?PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::pdo();
        if (session_status() !== \PHP_SESSION_ACTIVE) {
            @session_start();
        }
    }

    public function authenticate(int $id_entidade, string $senha): bool
    {
        $sql = "SELECT c.hash, e.ativo, e.nome
                  FROM tab_colaborador c
                  JOIN tab_entidade e ON e.id_entidade = c.id_entidade
                 WHERE c.id_entidade = :id";
        $st = $this->pdo->prepare($sql);
        $st->execute([':id' => $id_entidade]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        if (!$row) return false;
        if ((int)$row['ativo'] !== 1) return false;            // só loga se ativo
        if (!password_verify($senha, $row['hash'])) return false;

        if (session_status() !== \PHP_SESSION_ACTIVE) {
            @session_start();
        }
        session_regenerate_id(true);

        $_SESSION['auth'] = [
            'id_entidade' => $id_entidade,
            'nome' => $row['nome']
            // espaço para armazenar mais coisas se quiser (nome, acessos, etc.)
        ];

        return true;
    }

    public function logout(): void
    {
        if (session_status() !== \PHP_SESSION_ACTIVE) {
            @session_start();
        }

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        @session_destroy();
        // Opcionalmente, regenerar um novo id “limpo”:
        // @session_start(); session_regenerate_id(true);
    }

    /** Checa sessão válida */
    public function isAuthenticated(): bool
    {
        if (session_status() !== \PHP_SESSION_ACTIVE) {
            @session_start();
        }
        return isset($_SESSION['auth']['id_entidade']) && is_numeric($_SESSION['auth']['id_entidade']);
    }

    public function requireAuthForApi(): void
    {
        if ($this->isAuthenticated()) return;

        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Unauthorized', 'code' => 'AUTH_REQUIRED']);
        exit;
    }

    public function requireAuthForPage(string $loginPath = '/ui/login'): void
    {
        if ($this->isAuthenticated()) return;
        header('Location: ' . $loginPath);
        exit;
    }

    public function userId(): ?int
    {
        return $_SESSION['auth']['id_entidade'] ?? null;
    }
}
