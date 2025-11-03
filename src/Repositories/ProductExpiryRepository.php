<?php
namespace App\Repositories;

use App\Database\Connection;
use App\View\View;
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
            SELECT
                pv.id_produtovalidade,
                pv.id_produto,
                p.produto,
                pt.produtotipo,
                pv.data,
                p.ativo,
                DATEDIFF(pv.data, CURRENT_DATE()) AS dias
            FROM tab_produtovalidade pv
            JOIN tab_produto p ON p.id_produto = pv.id_produto
            LEFT JOIN tab_produtotipo pt ON pt.id_produtotipo = p.id_produtotipo
            WHERE pv.data <= DATE_ADD(CURRENT_DATE(), INTERVAL :d DAY)
            ORDER BY pv.data ASC, p.produto ASC
        ";
        $st = $this->pdo->prepare($sql);
        $st->execute([':d' => $days]);
        $out = [];
        $homeView    = new View('home');
        $productView = new View('product');
        while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
            $daysDiff = (int)$r['dias'];
            $isExpired = $daysDiff < 0;
            $diasValue = max(0, $daysDiff);

            $statusHtml = ((int)($r['ativo'] ?? 0) === 1)
                ? $productView->getContent(
                    ['id_produto' => (string)$r['id_produto']],
                    'EXTRA_BLOCK_PRODUCT_BUTTON_ATIVO'
                )
                : $productView->getContent(
                    ['id_produto' => (string)$r['id_produto']],
                    'EXTRA_BLOCK_PRODUCT_BUTTON_INATIVO'
                );

            $out[] = [
                'id_produtovalidade'               => $r['id_produtovalidade'],
                'id_produto'                       => $r['id_produto'],
                'produto'                          => $r['produto'],
                'produtotipo'                      => $r['produtotipo'] ?: '',
                'data_formatted'                   => $r['data'] ? date('d/m/Y', strtotime($r['data'])) : '',
                'dias'                             => (string)$diasValue,
                'extra_block_productexpdate_days'  => $isExpired
                    ? ''
                    : $homeView->getContent(
                        ['dias' => (string)$diasValue],
                        'EXTRA_BLOCK_PRODUCTEXPDATE_DAYS'
                    ),
                'extra_block_productexpdate_expirated' => $isExpired
                    ? $homeView->getContent([], 'EXTRA_BLOCK_PRODUCTEXPDATE_EXPIRATED')
                    : '',
                'extra_block_product_button_status'    => $statusHtml,
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
        $hasConfig = (int)$this->pdo
            ->query('SELECT COUNT(*) FROM tab_config')
            ->fetchColumn() > 0;

        if ($hasConfig) {
            $st = $this->pdo->prepare('UPDATE tab_config SET product_expirate_days = :v');
            $st->execute([':v' => $days]);
            return;
        }

        $st = $this->pdo->prepare('INSERT INTO tab_config (product_expirate_days) VALUES (:v)');
        $st->execute([':v' => $days]);
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
