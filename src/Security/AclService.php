<?php
namespace App\Security;

use App\Infra\Database\Connection;
use PDO;

final class AclService
{
    public const VIEW = 'view';
    public const EDIT = 'edit';

    private PDO $pdo;
    private int $userId;
    private array $acl = [];

    public function __construct(int $userId)
    {
        $this->pdo = Connection::pdo();
        $this->userId = $userId;
        $this->load();
    }

    private function load(): void
    {
        $st = $this->pdo->prepare("SELECT acesso FROM tab_colaborador WHERE id_entidade = :id");
        $st->execute([':id' => $this->userId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        $json = $row['acesso'] ?? '{}';
        $data = json_decode($json, true);
        $this->acl = is_array($data) ? $data : [];
    }

    public function can(string $module, string $perm): bool
    {
        return !empty($this->acl[$module][$perm]);
    }

    public function canView(string $module): bool
    {
        return $this->can($module, self::VIEW);
    }

    public function canEdit(string $module): bool
    {
        return $this->can($module, self::EDIT);
    }

    /** alterar um flag e salvar */
    public function set(string $module, string $perm, bool $value): void
    {
        $this->acl[$module] ??= [self::VIEW => false, self::EDIT => false];
        $this->acl[$module][$perm] = $value;

        $st = $this->pdo->prepare("UPDATE tab_colaborador SET acesso = :json WHERE id_entidade = :id");
        $st->execute([
            ':json' => json_encode($this->acl, JSON_UNESCAPED_UNICODE),
            ':id'   => $this->userId
        ]);
    }

    /** retorna para montar UI */
    public function all(): array
    {
        return $this->acl;
    }
}
