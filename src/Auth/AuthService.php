<?php
namespace App\Auth;

use App\Infra\Repositories\UserRepository;
use App\Infra\Repositories\CollaboratorRepository;

class AuthService
{

    public function __construct(
        private UserRepository $users,                     // já existia
        private ?CollaboratorRepository $collabs = null    // injete via container
    ) {}

    public function authenticateByCollaboratorPin(int $idEntidade, string $pin): bool
    {
        if (!$this->collabs) return false;
        if (!$this->collabs->validatePin($idEntidade, $pin)) {
            return false;
        }
        $c = $this->collabs->findBasic($idEntidade);
        if (!$c) return false;

        // Seta sessão
        $_SESSION['user_id'] = (int)$c['id_entidade'];
        $_SESSION['user'] = [
            'id'       => (int)$c['id_entidade'],
            'username' => $c['username'] ?? (string)$c['id_entidade'],
            'name'     => $c['nome'] ?? 'Colaborador',
            'type'     => 'collaborator',
        ];
        return true;
    }

    public function isAuthenticated(): bool
    {
        return !empty($_SESSION['user_id'] ?? null);
    }

    public function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public function authenticate(string $username, string $password): bool
    {
        $u = $this->users->findByUsername($username);

        if (!$u) { return false; }

        $ok = password_verify($password, $u['password_hash'] ?? '');

        if ($ok) {
            $_SESSION['user_id'] = (int)$u['id'];
            $_SESSION['user'] = [
            'id' => (int)$u['id'],
            'username' => $u['username'],
            'name' => $u['name'] ?? $u['username'],
            ];
        }
        return $ok;
    }

    public function logout(): void
    {
        unset($_SESSION['user_id'], $_SESSION['user']);
    }
}