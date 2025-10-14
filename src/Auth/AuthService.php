<?php
namespace App\Auth;

use App\Database\Connection;
use PDO;

final class AuthService {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Connection::pdo();
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    }

    /**
     * Autenticação estrita:
     * - Entrada: id_entidade (int) + senha (string)
     * - Hash: sempre bcrypt (password_verify)
     * - Tabela: tab_colaborador.hash
     */
    public function login(int $id_entidade, string $senha): bool {
        // Busca a credencial para o id_entidade informado
        $sql = "
            SELECT c.id_entidade, c.hash, e.nome, e.email
            FROM tab_colaborador c
            JOIN tab_entidade   e ON e.id_entidade = c.id_entidade
            WHERE c.id_entidade = ?
            LIMIT 1";
        $st = $this->pdo->prepare($sql);
        $st->execute([$id_entidade]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;

        $hash = (string)$row['hash'];
        // Somente bcrypt/argon (password_verify). Sem legado.
        if (!password_verify($senha, $hash)) return false;

        $_SESSION['auth']  = true;
        $_SESSION['uid']   = (int)$row['id_entidade'];
        $_SESSION['uname'] = (string)($row['nome'] ?? $row['email'] ?? 'Usuário');

        return true;
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    public function check(): bool { return !empty($_SESSION['auth']); }

    public function requireAuthForApi(): void {
        if (!$this->check()) {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'unauthorized']);
            exit;
        }
    }

    public function requireAuthForPage(): void {
        if (!$this->check()) { header('Location: /login'); exit; }
    }
}
