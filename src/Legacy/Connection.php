<?php
namespace App\Legacy;

use App\Infra\Database\Connection as NewConnection;
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
    protected int $rowIndex = 0;       // ponteiro para getResult() legado
    protected ?PDOStatement $stmt = null; // último statement executado

    public function __construct()
    {
        // Reaproveita a conexão nova (singleton)
        $this->pdo = NewConnection::pdo();
    }

    // === dentro de App\Legacy\Connection ==============================

    /**
    * Executa a query atual ($this->query) com $this->params de forma tolerante:
    * - suporta placeholders nomeados e posicionais
    * - expande arrays (IN (:ids) -> (:ids_0,:ids_1,...))
    * - intercala LIMIT/OFFSET como inteiros (sem bind)
    * - faz bind apenas dos placeholders efetivamente presentes no SQL
    */
    protected function Execute(): PDOStatement
    {
        if (!$this->query) {
            throw new \RuntimeException('Query vazia em Connection::Execute()');
        }

        $sql    = $this->query;
        $params = $this->params ?? [];

        // 1) Interpolar LIMIT/OFFSET como inteiros (evita HY093 e problemas de driver)
        $numericPlaceholders = ['limit','offset'];
        foreach ($numericPlaceholders as $ph) {
            if (preg_match('/\b' . $ph . '\s*:\s*' . $ph . '\b/i', $sql)) {
                // Caso raro "LIMIT :limit" (com palavra anterior), mantemos abaixo
            }
            // Troca segura: "LIMIT :limit" -> "LIMIT 10"
            $rx = '/\b' . strtoupper($ph) . '\b\s*:\s*' . $ph . '\b/i';
            if (preg_match('/:' . $ph . '\b/', $sql) && array_key_exists($ph, $params)) {
                $val = (int)$params[$ph];
                $sql = preg_replace('/:' . $ph . '\b/', (string)$val, $sql);
                unset($params[$ph]);
            }
        }

        // 2) Expandir arrays em placeholders nomeados (IN (:ids))
        [$sql, $params] = $this->expandArrayParams($sql, $params);

        // 3) Detectar placeholders
        $hasPositional = strpos($sql, '?') !== false;
        $named = $this->extractNamedPlaceholders($sql);

        // 4) Preparar statement
        $stmt = $this->pdo->prepare($sql);

        // 5) Bind de parâmetros
        if ($hasPositional) {
            // Posicionais: garantir ordem e contagem
            $needed = substr_count($sql, '?');
            $values = array_values($params);       // ignora chaves, usa ordem do array
            if (count($values) < $needed) {
                throw new \InvalidArgumentException("Parâmetros insuficientes: esperados $needed, fornecidos " . count($values));
            }
            // Bind apenas o necessário
            for ($i = 0; $i < $needed; $i++) {
                $this->bindValue($stmt, $i+1, $values[$i]); // 1-based
            }
            $stmt->execute();
        } else {
            // Nomeados: filtrar para somente os que estão no SQL
            $bindData = [];
            foreach ($named as $ph) {
                if (array_key_exists($ph, $params)) {
                    $bindData[$ph] = $params[$ph];
                } else {
                    // se o placeholder existir no SQL mas não no array, bind como null
                    $bindData[$ph] = null;
                }
            }
            // Ignorar chaves extras em $params que não aparecem no SQL
            foreach ($bindData as $key => $val) {
                $this->bindValue($stmt, ':' . $key, $val);
            }
            $stmt->execute();
        }

        // 6) Popular dados e resetar state legado
        $this->stmt     = $stmt;
        $this->data     = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $this->rowIndex = 0;

        return $stmt;
    }

    /** Extrai placeholders nomeados (sem ':') do SQL */
    private function extractNamedPlaceholders(string $sql): array
    {
        preg_match_all('/:([a-zA-Z_][a-zA-Z0-9_]*)/', $sql, $m);
        // Remover duplicados preservando ordem
        return array_values(array_unique($m[1] ?? []));
    }

    /**
    * Expande arrays:  ... IN (:ids)  com ['ids'=>[1,2]]  -> IN (:ids_0,:ids_1)
    * Retorna [sqlNovo, paramsNovos]
    */
    private function expandArrayParams(string $sql, array $params): array
    {
        if (!$params) return [$sql, $params];

        foreach ($params as $key => $val) {
            if (is_array($val) && preg_match('/:' . preg_quote($key, '/') . '\b/', $sql)) {
                if (count($val) === 0) {
                    // evita SQL inválido; gera IN (NULL)
                    $sql = preg_replace('/:' . preg_quote($key, '/') . '\b/', 'NULL', $sql);
                    unset($params[$key]);
                    continue;
                }
                $repls = [];
                foreach (array_values($val) as $i => $v) {
                    $newKey = $key . '_' . $i;
                    $repls[] = ':' . $newKey;
                    $params[$newKey] = $v;
                }
                $sql = preg_replace('/:' . preg_quote($key, '/') . '\b/', implode(',', $repls), $sql);
                unset($params[$key]);
            }
        }
        return [$sql, $params];
    }

    /** Bind com tipos coerentes (int/bool/string/null) */
    private function bindValue(PDOStatement $stmt, $placeholder, $value): void
    {
        if (is_int($value)) {
            $stmt->bindValue($placeholder, $value, PDO::PARAM_INT);
        } elseif (is_bool($value)) {
            $stmt->bindValue($placeholder, $value, PDO::PARAM_BOOL);
        } elseif (is_null($value)) {
            $stmt->bindValue($placeholder, null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue($placeholder, (string)$value, PDO::PARAM_STR);
        }
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
    public function getResult(): ?array
    {
        if (!isset($this->data[$this->rowIndex])) {
            return null;
        }
        return $this->data[$this->rowIndex++];
    }

    public function getResults(): array
    {
        return $this->data;
    }

    public function rowCount(): int
    {
        return $this->stmt ? $this->stmt->rowCount() : 0;
    }
}
