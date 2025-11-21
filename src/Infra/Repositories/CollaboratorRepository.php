<?php
namespace App\Infra\Repositories;

use App\Infra\Database\Connection;
use PDO;

final class CollaboratorRepository
{
    public function __construct(private Connection $conn) {}

    public function listForLogin(): array
    {
        $sql = "SELECT c.id_entidade, e.nome
				FROM tab_colaborador as c
				INNER JOIN tab_entidade as e on e.id_entidade = c.id_entidade
				WHERE e.ativo = 1
              	ORDER BY e.nome ASC";

        $st = $this->conn->pdo()->prepare($sql);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** Valida hash (4 dígitos) para o colaborador */
    public function validatehash(int $idEntidade, string $hash): bool
    {
        $sql = "SELECT c.hash
				FROM tab_colaborador as c
				INNER JOIN tab_entidade as e on e.id_entidade = c.id_entidade
				WHERE c.id_entidade = :id_entidade AND e.ativo = 1
				LIMIT 1";

        $st = $this->conn->pdo()->prepare($sql);
        $st->bindValue(':id_entidade', $idEntidade, PDO::PARAM_INT);
        $st->execute();
        $row = $st->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;

        // Se o hash estiver em texto claro (legado), compare direto.
        // Se estiver hash (recomendado), troque para password_verify($hash, $row['hash'])
        return trim($row['hash'] ?? '') === trim($hash);
    }

    /** Dados básicos do colaborador para sessão */
    public function findBasic(int $idEntidade): ?array
    {
        $sql = "SELECT id_entidade, e.nome
                FROM tab_colaborador as c
				INNER JOIN tab_entidade as e on e.id_entidade = c.id_entidade
				WHERE c.id_entidade = :id_entidade
				LIMIT 1";
        $st = $this->conn->pdo()->prepare($sql);
        $st->bindValue(':id_entidade', $idEntidade, PDO::PARAM_INT);
        $st->execute();
        return $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
