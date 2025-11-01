<?php
namespace App\Repositories;

use App\Database\Connection;
use PDO;

final class ProductExpiryRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?: Connection::pdo();
    }

// Contadores para topo
    public function counters(int $days): array
    {
        // vencidos
        $exp = (int)$this->pdo->query("
            SELECT COUNT(*)
              FROM tab_produtovalidade
             WHERE data < CURRENT_DATE()
        ")->fetchColumn();

// SELECT *, DATEDIFF(data, now()) as dias
//                 FROM tab_produtovalidade
//                 INNER JOIN tab_produto on tab_produto.id_produto = tab_produtovalidade.id_produto
//                 INNER JOIN tab_produtotipo on tab_produtotipo.id_produtotipo = tab_produto.id_produtotipo
//                 WHERE data < CURRENT_DATE()
//                 ORDER BY data

        // a vencer nos próximos $days
        $to = 0;
        if ($days > 0) {
            $st = $this->pdo->prepare("
                SELECT COUNT(*)
                  FROM tab_produtovalidade
                 WHERE data >= CURRENT_DATE()
                   AND data <= DATE_ADD(CURRENT_DATE(), INTERVAL :d DAY)
            ");

            $st->execute([':d' => $days]);
            $to = (int)$st->fetchColumn();
        }
        return [$exp, $to];
    }

    // Linhas para a lista
    public function listByDays(int $days): array
    {
        $sql = "
            SELECT v.id_produtovalidade, v.id_produto, p.nome AS produto, p.tipo AS produtotipo,
                   DATE_FORMAT(v.data, '%d/%m/%Y') AS data_formatted,
                   DATEDIFF(v.data, CURRENT_DATE()) AS dias
              FROM tab_produto_validade v
              JOIN tab_produto p ON p.id_produto = v.id_produto
             WHERE v.data <= DATE_ADD(CURRENT_DATE(), INTERVAL :d DAY)
             ORDER BY v.data ASC
        ";
        $st = $this->pdo->prepare($sql);
        $st->execute([':d' => $days]);
        $out = [];
        while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
            $out[] = [
                'id_produtovalidade' => $r['id_produtovalidade'],
                'id_produto'         => $r['id_produto'],
                'produto'            => $r['produto'],
                'produtotipo'        => $r['produtotipo'] ?: '',
                'data_formatted'     => $r['data_formatted'],
                // decide mostrar EXPIRATED x DAYS
                'extra_block_productexpdate_days'      => ($r['dias'] >= 0) ? 'BLOCK' : '',
                'extra_block_productexpdate_expirated' => ($r['dias'] < 0) ? 'BLOCK' : '',
                'dias'               => max(0, (int)$r['dias']),
                // Se você usa botões auxiliares no tpl, preencha aqui…
            ];
        }
        return $out;
    }

    public function getExpiryDaysThreshold(): int
    {
        // tenta vir de tab_config; se não, usa 15 por padrão
        $sql = "SELECT product_expirate_days FROM tab_config LIMIT 1";
        $v = (int)($this->pdo->query($sql)->fetchColumn() ?: 0);
        return $v > 0 ? $v : 15;
    }

    public function setDaysThreshold(int $days): void
    {
        $st = $this->pdo->prepare("
            INSERT INTO tab_config (chave, valor)
            VALUES ('product_expirate_days', :v)
            ON DUPLICATE KEY UPDATE valor = VALUES(valor)
        ");
        $st->execute([':v' => (string)$days]);
    }

    public function countExpired(): int
    {
        $sql = "
            SELECT COUNT(*)
              FROM tab_produtovalidade pv
             WHERE pv.data < CURRENT_DATE()
        ";

        return (int)$this->pdo->query($sql)->fetchColumn();
    }

    public function countToExpireWithin(int $days): int
    {
        $sql = "
            SELECT COUNT(*)
              FROM tab_produtovalidade pv
             WHERE pv.data >= CURRENT_DATE()
               AND pv.data <= DATE_ADD(CURRENT_DATE(), INTERVAL :d DAY)
        ";
        $st = $this->pdo->prepare($sql);
        $st->execute([':d' => $days]);
        return (int)$st->fetchColumn();
    }

    /** Lista para o popup */
    public function listExpirations(int $days): array
    {
        $sql = "
            SELECT
                pv.id_produtovalidade,
                pv.id_produto,
                p.produto,
                p.produtotipo,
                pv.data,
                DATEDIFF(pv.data, CURRENT_DATE()) AS dias
            FROM tab_produtovalidade pv
            JOIN tab_produto p ON p.id_produto = pv.id_produto
            WHERE pv.data <= DATE_ADD(CURRENT_DATE(), INTERVAL :d DAY)
            ORDER BY pv.data ASC, p.produto ASC
        ";
        $st = $this->pdo->prepare($sql);
        $st->execute([':d' => $days]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
