<?php
namespace App\Infra\Repositories;

use App\Infra\Database\Connection;
use PDO;

class OrderRepository {
    private PDO $pdo;
    public bool $withCliente = true;
    public bool $withStatus  = true;

    public function __construct() {
        $this->pdo = Connection::pdo();
    }

    private function selectColumns(): string {
        $cols = [
            'v.id_venda',
            'v.id_vendastatus',
            'v.id_entidade',
            'v.id_colaborador',
            'v.id_caixa',
            'v.`data`',
            'v.frete',
            'v.valor_servico',
            'v.obs',
            'v.mesa',
            'v.versao',
        ];
        if ($this->withCliente) {
            $cols[] = 'e.nome AS cliente_nome';
            $cols[] = 'e.email AS cliente_email';
            $cols[] = 'e.telcelular AS cliente_telcelular';
            $cols[] = 'e.telresidencial AS cliente_telresidencial';
            $cols[] = 'e.telcomercial AS cliente_telcomercial';
        }
        if ($this->withStatus) {
            $cols[] = 's.vendastatus';
        }
        return implode(', ', $cols);
    }

    private function joinClause(): string {
        $parts = [];
        if ($this->withCliente) {
            $parts[] = 'LEFT JOIN tab_entidade e ON e.id_entidade = v.id_entidade';
        }
        if ($this->withStatus) {
            $parts[] = 'LEFT JOIN tab_vendastatus s ON s.id_vendastatus = v.id_vendastatus';
        }
        return $parts ? implode(' ', $parts) : '';
    }

    public function list(array $opts = []): array {
        $limit  = max(1, min(100, (int)($opts['limit']  ?? 20)));
        $offset = max(0, (int)($opts['offset'] ?? 0));
        $q      = trim((string)($opts['q'] ?? ''));

        // ---- SORT seguro (whitelist) ----
        $sortIn  = (string)($opts['sort'] ?? 'data'); // default
        $dirIn   = (string)($opts['dir']  ?? 'desc'); // default

        if ($sortIn === 'mais_recente') { $sortIn = 'data'; $dirIn = 'desc'; }
        if ($sortIn === 'mais_antigo')  { $sortIn = 'data'; $dirIn = 'asc';  }

        $sortable = [
            'id'      => 'v.id_venda',
            'data'    => 'v.`data`',
            'cliente' => 'e.nome',
            'status'  => 's.vendastatus',
            'frete'   => 'v.frete',
            'servico' => 'v.valor_servico',
        ];
        $col = $sortable[$sortIn] ?? 'v.`data`';
        $dir = (strtolower($dirIn) === 'asc') ? 'ASC' : 'DESC';

        if ($col === 'e.nome' && !$this->withCliente)       $this->withCliente = true;
        if ($col === 's.vendastatus' && !$this->withStatus) $this->withStatus  = true;
        // ---------------------------------

        $whereParts = [];
        $positionalBinds = [];

        if ($q !== '') {
            $needle = '%'.$q.'%';
            $like = ["v.obs LIKE ?", "v.mesa LIKE ?"];
            $positionalBinds[] = $needle;
            $positionalBinds[] = $needle;

            if ($this->withCliente) {
                $like[] = "e.nome LIKE ?";
                $like[] = "e.email LIKE ?";
                $like[] = "e.telcelular LIKE ?";
                $like[] = "e.telresidencial LIKE ?";
                $like[] = "e.telcomercial LIKE ?";
                array_push($positionalBinds, $needle, $needle, $needle, $needle, $needle);
            }
            if ($this->withStatus) {
                $like[] = "s.vendastatus LIKE ?";
                $positionalBinds[] = $needle;
            }
            $whereParts[] = '(' . implode(' OR ', $like) . ')';
        }

        $where = $whereParts ? ('WHERE ' . implode(' AND ', $whereParts)) : '';

        $sql = "SELECT
                {$this->selectColumns()}
                FROM tab_venda v
                {$this->joinClause()}
                {$where}
                ORDER BY {$col} {$dir}, v.id_venda DESC
                LIMIT {$limit} OFFSET {$offset}";
        $st = $this->pdo->prepare($sql);
        foreach ($positionalBinds as $i => $val) {
            $st->bindValue($i + 1, $val, PDO::PARAM_STR);
        }
        $st->execute();
        $items = $st->fetchAll(PDO::FETCH_ASSOC);

        $countSql = "SELECT COUNT(*) FROM tab_venda v {$this->joinClause()} {$where}";
        $stc = $this->pdo->prepare($countSql);
        foreach ($positionalBinds as $i => $val) {
            $stc->bindValue($i + 1, $val, PDO::PARAM_STR);
        }
        $stc->execute();
        $total = (int)$stc->fetchColumn();

        return ['items' => $items, 'total' => $total, 'limit' => $limit, 'offset' => $offset];
    }

    public function updateStatusById(int $idVenda, int $idStatus): bool {
        $sql = "UPDATE tab_venda SET id_vendastatus = ? WHERE id_venda = ?";
        $st = $this->pdo->prepare($sql);
        return $st->execute([$idStatus, $idVenda]);
    }

    public function updateStatusByName(int $idVenda, string $nomeStatus): bool {
        $sql = "UPDATE tab_venda v
                JOIN tab_vendastatus s ON s.id_vendastatus = v.id_vendastatus
                SET v.id_vendastatus = (
                    SELECT s2.id_vendastatus FROM tab_vendastatus s2 WHERE s2.vendastatus = ? LIMIT 1
                )
                WHERE v.id_venda = ?";
        $st = $this->pdo->prepare($sql);
        return $st->execute([$nomeStatus, $idVenda]);
    }
}
