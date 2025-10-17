<?php
namespace App\Auth;

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

    public function authenticate(int $id_entidade, string $senha): array
    {
        if ($this->pdo === null) {
            throw new \RuntimeException('Conexão com DB indisponível');
        }
        if ($id_entidade <= 0 || $senha === '') {
            throw new \RuntimeException('Parâmetros inválidos');
        }

        $sql = "SELECT c.id_entidade, c.hash, c.acesso, e.nome
                  FROM tab_colaborador c
                  JOIN tab_entidade   e USING (id_entidade)
                 WHERE c.id_entidade = :id
                 LIMIT 1";
        $st = $this->pdo->prepare($sql);
        $st->execute([':id' => $id_entidade]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($senha, (string)($row['hash'] ?? ''))) {
            throw new \RuntimeException('Credenciais inválidas');
        }

        return [
            'id_entidade' => (int)$row['id_entidade'],
            'nome'        => (string)($row['nome'] ?? ''),
            'acesso'      => (string)($row['acesso'] ?? ''),
        ];
    }

    public function logout(?int $id_entidade = null): void
    {
        // Se quiser, zere token persistido aqui.
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public function check(): bool
    {
        return !empty($_SESSION['auth']) && !empty($_SESSION['uid']);
    }

    public function requireAuthForApi(): void
    {
        if ($this->check()) return;
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'unauthorized']);
        exit;
    }

    public function requireAuthForPage(string $loginPath = '/login'): void
    {
        if ($this->check()) return;
        header('Location: ' . $loginPath);
        exit;
    }
}
