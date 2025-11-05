<?php
declare(strict_types=1);

namespace App\Legacy;

use App\Infra\Database\Connection;
use App\Legacy\ControlAccess;
use PDO;
use PDOException;

final class Collaborator
{
    private PDO $pdo;

    /** Último “row” lido por Read(), para compatibilidade com getResult() */
    private ?array $lastRow = null;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Connection::pdo();
    }

    /**
     * Cria colaborador com hash bcrypt e vetor de acesso zerado (tamanho CA_MAX+1).
     */
    public function Create(int $id_entidade, string $pass): void
    {
        $access = array_fill(0, ControlAccess::CA_MAX + 1, 0);

        $sql = "INSERT INTO tab_colaborador (id_entidade, hash, acesso, sessao)
                VALUES (:id_entidade, :hash, :acesso, :sessao)";

        $st = $this->pdo->prepare($sql);
        $st->execute([
            ':id_entidade' => $id_entidade,
            ':hash'        => password_hash($pass, PASSWORD_BCRYPT),
            ':acesso'      => json_encode($access, JSON_UNESCAPED_UNICODE),
            ':sessao'      => '', // mantém compat (havia coluna sessao no legado)
        ]);
    }

    /**
     * Lê colaborador + entidade. Retorna true se encontrou (como no legado)
     * e armazena o row em $this->lastRow para getResult().
     */
    public function Read(int $id_entidade): bool
    {
        $sql = "SELECT *
                  FROM tab_colaborador
                  INNER JOIN tab_entidade
                          ON tab_entidade.id_entidade = tab_colaborador.id_entidade
                 WHERE tab_colaborador.id_entidade = :id_entidade
                 LIMIT 1";

        $st = $this->pdo->prepare($sql);
        $st->execute([':id_entidade' => $id_entidade]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        $this->lastRow = $row ?: null;
        return $this->lastRow !== null;
    }

    /**
     * Compatibilidade com o legado: retorna o row da última operação Read()
     */
    public function getResult(): ?array
    {
        return $this->lastRow;
    }

    /**
     * Lista colaboradores que possuem um determinado “access” habilitado
     * (usa o mesmo algoritmo do seu código original, filtrando em PHP).
     */
    public function getListHavingAccess(int $access): array
    {
        // Mantém SELECT amplo como no legado e filtra em PHP
        $sql = "SELECT *
                  FROM tab_colaborador
                  INNER JOIN tab_entidade
                          ON tab_entidade.id_entidade = tab_colaborador.id_entidade
                 ORDER BY nome";

        $st = $this->pdo->query($sql);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $result = [];
        foreach ($rows as $row) {
            $entity_access = json_decode($row['acesso'] ?? '[]', true) ?: [];
            // Evita notice se faltar índice
            $ok = isset($entity_access[$access]) && (int)$entity_access[$access] === 1;
            if ($ok) {
                $result[] = $row;
            }
        }
        return $result;
    }

    /**
     * Lista geral de colaboradores + entidade (mantém assinatura).
     * Retorna array (mais prático que depender de getResult() em loop).
     */
    public function getList(): array
    {
        $sql = "SELECT *
                  FROM tab_colaborador
                  INNER JOIN tab_entidade
                          ON tab_entidade.id_entidade = tab_colaborador.id_entidade
                 ORDER BY nome";

        $st = $this->pdo->query($sql);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Atualiza sessão do colaborador.
     * No legado, $data chegava como array de parâmetros para o UPDATE.
     * Aqui aceitamos tanto ['sessao' => '...', 'id_entidade' => n]
     * quanto um par simples (sessao, id).
     */
    public function RegistrySession(array $data): void
    {
        // Aceita formas diferentes para manter compatibilidade
        if (isset($data['sessao'], $data['id_entidade'])) {
            $sessao = (string)$data['sessao'];
            $id     = (int)$data['id_entidade'];
        } else {
            // fallback (não recomendado, mas evita quebra)
            $sessao = (string)($data[0] ?? '');
            $id     = (int)($data[1] ?? 0);
        }

        $sql = "UPDATE tab_colaborador
                   SET sessao = :sessao
                 WHERE id_entidade = :id_entidade";

        $st = $this->pdo->prepare($sql);
        $st->execute([
            ':sessao'      => $sessao,
            ':id_entidade' => $id,
        ]);
    }

    /**
     * Exclui colaborador.
     * Retorna número de linhas afetadas (como no legado).
     */
    public function Delete(int $id_entidade): int
    {
        $sql = "DELETE FROM tab_colaborador WHERE id_entidade = :id_entidade";
        $st  = $this->pdo->prepare($sql);
        $st->execute([':id_entidade' => $id_entidade]);
        return $st->rowCount();
    }

    /**
     * Define vetor de acessos (JSON) do colaborador.
     */
    public function setAccess(int $id_entidade, array $access): void
    {
        $sql = "UPDATE tab_colaborador
                   SET acesso = :acesso
                 WHERE id_entidade = :id_entidade";

        $st = $this->pdo->prepare($sql);
        $st->execute([
            ':acesso'      => json_encode($access, JSON_UNESCAPED_UNICODE),
            ':id_entidade' => $id_entidade,
        ]);
    }

    /**
     * Define nova senha (bcrypt).
     */
    public function setPass(int $id_entidade, string $pass): void
    {
        $sql = "UPDATE tab_colaborador
                   SET hash = :hash
                 WHERE id_entidade = :id_entidade";

        $st = $this->pdo->prepare($sql);
        $st->execute([
            ':hash'        => password_hash($pass, PASSWORD_BCRYPT),
            ':id_entidade' => $id_entidade,
        ]);
    }

    /**
     * Formata flags “checked” para os campos de acesso
     * mantendo total compatibilidade com o tpl legado.
     */
    public static function FormatFields(array $row): array
    {
        $access = json_decode($row['acesso'] ?? '[]', true) ?: [];

        $flag = static function(int $idx) use ($access): string {
            return (isset($access[$idx]) && (int)$access[$idx] === 1) ? 'checked' : '';
        };

        $row['CA_SERVIDOR']                           = $flag(ControlAccess::CA_SERVIDOR);
        $row['CA_SERVIDOR_PRODUTO']                   = $flag(ControlAccess::CA_SERVIDOR_PRODUTO);
        $row['CA_SERVIDOR_PRODUTO_SETOR']             = $flag(ControlAccess::CA_SERVIDOR_PRODUTO_SETOR);
        $row['CA_SERVIDOR_CLIENTE']                   = $flag(ControlAccess::CA_SERVIDOR_CLIENTE);
        $row['CA_SERVIDOR_COLABORADOR']               = $flag(ControlAccess::CA_SERVIDOR_COLABORADOR);
        $row['CA_SERVIDOR_EMISSAO_RECIBO']            = $flag(ControlAccess::CA_SERVIDOR_EMISSAO_RECIBO);
        $row['CA_SERVIDOR_FORNECEDOR']                = $flag(ControlAccess::CA_SERVIDOR_FORNECEDOR);
        $row['CA_SERVIDOR_ORDEM_COMPRA']              = $flag(ControlAccess::CA_SERVIDOR_ORDEM_COMPRA);
        $row['CA_PDV']                                = $flag(ControlAccess::CA_PDV);
        $row['CA_PDV_SANGRIA']                        = $flag(ControlAccess::CA_PDV_SANGRIA);
        $row['CA_PDV_REFORCO']                        = $flag(ControlAccess::CA_PDV_REFORCO);
        $row['CA_PDV_CANCELA_ITEM']                   = $flag(ControlAccess::CA_PDV_CANCELA_ITEM);
        $row['CA_PDV_CANCELA_VENDA']                  = $flag(ControlAccess::CA_PDV_CANCELA_VENDA);
        $row['CA_PDV_DESCONTO']                       = $flag(ControlAccess::CA_PDV_DESCONTO);
        $row['CA_SERVIDOR_ORDEM_VENDA_FRETE']         = $flag(ControlAccess::CA_SERVIDOR_ORDEM_VENDA_FRETE);
        $row['CA_SERVIDOR_PRODUTO_PRECO']             = $flag(ControlAccess::CA_SERVIDOR_PRODUTO_PRECO);
        $row['CA_SERVIDOR_ORDEM_COMPRA_LISTA']        = $flag(ControlAccess::CA_SERVIDOR_ORDEM_COMPRA_LISTA);
        $row['CA_SERVIDOR_CONTAS_A_PAGAR']            = $flag(ControlAccess::CA_SERVIDOR_CONTAS_A_PAGAR);
        $row['CA_SERVIDOR_CONTAS_A_RECEBER']          = $flag(ControlAccess::CA_SERVIDOR_CONTAS_A_RECEBER);
        $row['CA_SERVIDOR_ORDEM_VENDA']               = $flag(ControlAccess::CA_SERVIDOR_ORDEM_VENDA);
        $row['CA_VENDA_PRAZO_SEM_LIMITE']             = $flag(ControlAccess::CA_VENDA_PRAZO_SEM_LIMITE);
        $row['CA_ORDEM_VENDA_EDITAR']                 = $flag(ControlAccess::CA_ORDEM_VENDA_EDITAR);
        $row['CA_SERVIDOR_ORDEM_VENDA_ITEM_PRECO']    = $flag(ControlAccess::CA_SERVIDOR_ORDEM_VENDA_ITEM_PRECO);
        $row['CA_SERVIDOR_ORDEM_VENDA_ITEM_DESCONTO'] = $flag(ControlAccess::CA_SERVIDOR_ORDEM_VENDA_ITEM_DESCONTO);
        $row['CA_SERVIDOR_RELATORIO']                 = $flag(ControlAccess::CA_SERVIDOR_RELATORIO);
        $row['CA_CLIENTE_CREDITO']                    = $flag(ControlAccess::CA_CLIENTE_CREDITO);
        $row['CA_CLIENTE_LIMITE']                     = $flag(ControlAccess::CA_CLIENTE_LIMITE);
        $row['CA_SERVIDOR_CONFIG']                    = $flag(ControlAccess::CA_SERVIDOR_CONFIG);
        $row['CA_WAITER']                             = $flag(ControlAccess::CA_WAITER);
        $row['CA_PRODUTO_ESTOQUE_ADD']                = $flag(ControlAccess::CA_PRODUTO_ESTOQUE_ADD);
        $row['CA_PRODUTO_ESTOQUE_DEL']                = $flag(ControlAccess::CA_PRODUTO_ESTOQUE_DEL);
        $row['CA_TRANSFERENCIA_MESA']                 = $flag(ControlAccess::CA_TRANSFERENCIA_MESA);
        $row['CA_MESA_ITEM_ESTORNO']                  = $flag(ControlAccess::CA_MESA_ITEM_ESTORNO);
        $row['CA_VENDA_PRAZO_EDITAR']                 = $flag(ControlAccess::CA_VENDA_PRAZO_EDITAR);
        $row['CA_ESTOQUE_SECUNDARIO_ADD']             = $flag(ControlAccess::CA_ESTOQUE_SECUNDARIO_ADD);
        $row['CA_ESTOQUE_SECUNDARIO_DEL']             = $flag(ControlAccess::CA_ESTOQUE_SECUNDARIO_DEL);

        return $row;
    }
}