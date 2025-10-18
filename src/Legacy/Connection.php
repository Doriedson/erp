<?php
namespace App\Legacy;

use App\Database\Connection as NewConnection;
use PDO;
use PDOStatement;

class Connection
{
    /** @var PDO */
    protected PDO $pdo;

    /** === Propriedades legadas esperadas pelas classes filhas === */
    protected ?string $query = null;   // SQL a ser executado
    protected array $params = [];      // parâmetros do SQL (se houver)
    protected array $data   = [];      // resultado preenchido pelo Execute()
    protected ?PDOStatement $stmt = null; // último statement executado

    public function __construct()
    {
        // Reaproveita a conexão nova (singleton)
        $this->pdo = NewConnection::pdo();
    }

    /**
     * Compat: parent::Execute() sem argumentos usa $this->query/$this->params.
     * Se forem passados $sql/$params, atualiza as propriedades e executa.
     * Preenche $this->data com fetchAll() e retorna o PDOStatement.
     */
    protected function Execute(?string $sql = null, ?array $params = null): PDOStatement
    {
        if ($sql !== null)   { $this->query  = $sql; }
        if ($params !== null){ $this->params = $params; }

        $q = (string)($this->query ?? '');
        $p = $this->params ?? [];

        $stmt = $this->pdo->prepare($q);
        $stmt->execute($p);

        $this->stmt = $stmt;

        // Por padrão, carrega tudo no $this->data (padrão do legado)
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->data = is_array($rows) ? $rows : [];

        return $stmt;
    }

    /** Compat opcional: execução direta sem preparar parâmetros */
    protected function Query(string $sql): PDOStatement
    {
        $this->stmt = $this->pdo->query($sql);
        // Como Query pode ser usada para DDL/DML, não força fetchAll aqui.
        return $this->stmt;
    }

    /** Açúcares de transação (se o legado usava) */
    protected function Begin(): void    { $this->pdo->beginTransaction(); }
    protected function Commit(): void   { $this->pdo->commit(); }
    protected function Rollback(): void { if ($this->pdo->inTransaction()) $this->pdo->rollBack(); }

    /** Acessores úteis (caso o legado consuma) */
    protected function getData(): array       { return $this->data; }
    protected function getFirst(): ?array     { return $this->data[0] ?? null; }

    /** Normalizadores comuns no legado (não fazem mal existir na base) */
    public function getResult(): ?array       { return $this->data[0] ?? null; } // 1 linha
    public function getResults(): array       { return $this->data; }            // todas
}
