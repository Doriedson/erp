<?php
declare(strict_types=1);

use App\Database\Connection;

require __DIR__ . '/../vendor/autoload.php';

@ini_set('display_errors', '1');
error_reporting(E_ALL);

$dryRun = in_array('--dry', $argv, true);

// -----------------------------
// Helpers
// -----------------------------
function oldHas(array $old, int $idx): bool {
    // $old é o array numérico (0/1) vindo do JSON antigo
    return isset($old[$idx]) && (int)$old[$idx] === 1;
}

function buildNewAcl(array $old): array {
    // Constantes (índices)
    $CA = [
        'SERVIDOR'                         => 0,
        'SERVIDOR_PRODUTO'                 => 1,
        'SERVIDOR_PRODUTO_SETOR'           => 2,
        'SERVIDOR_CLIENTE'                 => 3,
        'SERVIDOR_COLABORADOR'             => 4, // não mapeado (pode virar config depois)
        'SERVIDOR_FORNECEDOR'              => 5,
        'SERVIDOR_ORDEM_COMPRA'            => 6,
        'PDV'                              => 7,
        'PDV_SANGRIA'                      => 8,
        'PDV_CANCELA_ITEM'                 => 9,
        'PDV_CANCELA_VENDA'                => 10,
        'SERVIDOR_PRODUTO_PRECO'           => 11,
        'SERVIDOR_ORDEM_COMPRA_LISTA'      => 12,
        'SERVIDOR_CONTAS_A_PAGAR'          => 13,
        'SERVIDOR_EMISSAO_RECIBO'          => 14,
        'SERVIDOR_ORDEM_VENDA'             => 15,
        'SERVIDOR_ORDEM_VENDA_ITEM_PRECO'  => 16,
        'SERVIDOR_ORDEM_VENDA_ITEM_DESCONTO'=>17,
        'CLIENTE_LIMITE'                   => 18,
        'SERVIDOR_RELATORIO'               => 19,
        'PDV_DESCONTO'                     => 20,
        'SERVIDOR_CONFIG'                  => 21,
        'SERVIDOR_ORDEM_VENDA_FRETE'       => 22,
        'SERVIDOR_CONTAS_A_RECEBER'        => 23,
        'CLIENTE_CREDITO'                  => 24,
        'PDV_REFORCO'                      => 25,
        'WAITER'                           => 26,
        'PRODUTO_ESTOQUE_ADD'              => 27,
        'PRODUTO_ESTOQUE_DEL'              => 28,
        'VENDA_PRAZO_SEM_LIMITE'           => 29,
        'TRANSFERENCIA_MESA'               => 30,
        'VENDA_PRAZO_EDITAR'               => 31,
        'ESTOQUE_SECUNDARIO_ADD'           => 32,
        'ESTOQUE_SECUNDARIO_DEL'           => 33,
        'ORDEM_VENDA_EDITAR'               => 34,
        'MESA_ITEM_ESTORNO'                => 35,
    ];

    $acl = [
        'backend'   => ['view'=>false,'edit'=>false],
        'products'  => ['view'=>false,'edit'=>false],
        'orders'    => ['view'=>false,'edit'=>false],
        'customers' => ['view'=>false,'edit'=>false],
        'suppliers' => ['view'=>false,'edit'=>false],
        'reports'   => ['view'=>false,'edit'=>false],
        'config'    => ['view'=>false,'edit'=>false],
        'waiter'    => ['view'=>false,'edit'=>false],
        'pdv'       => ['view'=>false,'edit'=>false],
    ];

    // backend
    if (oldHas($old, $CA['SERVIDOR'])) {
        $acl['backend']['view'] = true;
    }

    // products
    if (
        oldHas($old, $CA['SERVIDOR_PRODUTO']) ||
        oldHas($old, $CA['SERVIDOR_PRODUTO_SETOR']) ||
        oldHas($old, $CA['SERVIDOR_PRODUTO_PRECO']) ||
        oldHas($old, $CA['PRODUTO_ESTOQUE_ADD']) ||
        oldHas($old, $CA['PRODUTO_ESTOQUE_DEL']) ||
        oldHas($old, $CA['ESTOQUE_SECUNDARIO_ADD']) ||
        oldHas($old, $CA['ESTOQUE_SECUNDARIO_DEL'])
    ) {
        $acl['products']['view'] = true;
    }
    if (
        oldHas($old, $CA['SERVIDOR_PRODUTO']) ||
        oldHas($old, $CA['SERVIDOR_PRODUTO_PRECO']) ||
        oldHas($old, $CA['PRODUTO_ESTOQUE_ADD']) ||
        oldHas($old, $CA['PRODUTO_ESTOQUE_DEL']) ||
        oldHas($old, $CA['ESTOQUE_SECUNDARIO_ADD']) ||
        oldHas($old, $CA['ESTOQUE_SECUNDARIO_DEL'])
    ) {
        $acl['products']['edit'] = true;
    }

    // orders
    if (oldHas($old, $CA['SERVIDOR_ORDEM_VENDA']) || oldHas($old, $CA['SERVIDOR_ORDEM_VENDA_FRETE'])) {
        $acl['orders']['view'] = true;
    }
    if (
        oldHas($old, $CA['ORDEM_VENDA_EDITAR']) ||
        oldHas($old, $CA['SERVIDOR_ORDEM_VENDA_ITEM_PRECO']) ||
        oldHas($old, $CA['SERVIDOR_ORDEM_VENDA_ITEM_DESCONTO']) ||
        oldHas($old, $CA['VENDA_PRAZO_SEM_LIMITE']) ||
        oldHas($old, $CA['VENDA_PRAZO_EDITAR'])
    ) {
        $acl['orders']['edit'] = true;
    }

    // customers
    if (oldHas($old, $CA['SERVIDOR_CLIENTE'])) {
        $acl['customers']['view'] = true;
    }
    if (oldHas($old, $CA['CLIENTE_CREDITO']) || oldHas($old, $CA['CLIENTE_LIMITE'])) {
        $acl['customers']['edit'] = true;
    }

    // suppliers
    if (
        oldHas($old, $CA['SERVIDOR_FORNECEDOR']) ||
        oldHas($old, $CA['SERVIDOR_ORDEM_COMPRA_LISTA']) ||
        oldHas($old, $CA['SERVIDOR_ORDEM_COMPRA'])
    ) {
        $acl['suppliers']['view'] = true;
    }
    if (oldHas($old, $CA['SERVIDOR_ORDEM_COMPRA'])) {
        $acl['suppliers']['edit'] = true;
    }

    // reports (somente leitura)
    if (
        oldHas($old, $CA['SERVIDOR_RELATORIO']) ||
        oldHas($old, $CA['SERVIDOR_CONTAS_A_PAGAR']) ||
        oldHas($old, $CA['SERVIDOR_CONTAS_A_RECEBER']) ||
        oldHas($old, $CA['SERVIDOR_EMISSAO_RECIBO'])
    ) {
        $acl['reports']['view'] = true;
    }

    // config
    if (oldHas($old, $CA['SERVIDOR_CONFIG'])) {
        $acl['config']['view'] = true;
        $acl['config']['edit'] = true;
    }

    // waiter
    if (oldHas($old, $CA['WAITER'])) {
        $acl['waiter']['view'] = true;
    }
    if (oldHas($old, $CA['TRANSFERENCIA_MESA']) || oldHas($old, $CA['MESA_ITEM_ESTORNO'])) {
        $acl['waiter']['edit'] = true;
    }

    // pdv
    if (oldHas($old, $CA['PDV'])) {
        $acl['pdv']['view'] = true;
    }
    if (
        oldHas($old, $CA['PDV_SANGRIA']) ||
        oldHas($old, $CA['PDV_REFORCO']) ||
        oldHas($old, $CA['PDV_CANCELA_ITEM']) ||
        oldHas($old, $CA['PDV_CANCELA_VENDA']) ||
        oldHas($old, $CA['PDV_DESCONTO'])
    ) {
        $acl['pdv']['edit'] = true;
    }

    return $acl;
}

// -----------------------------
// Main
// -----------------------------
$pdo = Connection::pdo();

$rows = $pdo->query("SELECT id_entidade, acesso FROM tab_colaborador")->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
$updated = 0;

foreach ($rows as $r) {
    $total++;

    $id   = (int)$r['id_entidade'];
    $raw  = $r['acesso'] ?? '';

    // tenta decodificar como array numérico antigo
    $old = json_decode((string)$raw, true);

    // se já está no formato novo (objeto com módulos), pule
    $isNewFormat = is_array($old) && array_keys($old) !== range(0, count($old) - 1) && isset($old['backend']) && is_array($old['backend']);

    if ($isNewFormat) {
        echo "[SKIP] id_entidade={$id} já no novo formato\n";
        continue;
    }

    if (!is_array($old)) {
        // se não decodificou, considera tudo vazio
        $old = [];
    }

    $new = buildNewAcl($old);
    $json = json_encode($new, JSON_UNESCAPED_UNICODE);

    echo "[MIGRATE] id_entidade={$id} -> {$json}\n";

    if (!$dryRun) {
        $st = $pdo->prepare("UPDATE tab_colaborador SET acesso = :json WHERE id_entidade = :id");
        $st->execute([':json' => $json, ':id' => $id]);
        $updated++;
    }
}

echo "\nTotal: {$total}\n";
echo $dryRun ? "Atualizações simuladas (dry-run)\n" : "Atualizados: {$updated}\n";
