<?php
declare(strict_types=1);

namespace App\Legacy;

use App\Infra\Database\Connection;
use App\Http\Response;

final class ControlAccess
{
    // ==== Constantes de acesso (mantidas) ====
    public const CA_SERVIDOR                         = 0;
    public const CA_SERVIDOR_PRODUTO                 = 1;
    public const CA_SERVIDOR_PRODUTO_SETOR           = 2;
    public const CA_SERVIDOR_CLIENTE                 = 3;
    public const CA_SERVIDOR_COLABORADOR             = 4;
    public const CA_SERVIDOR_FORNECEDOR              = 5;
    public const CA_SERVIDOR_ORDEM_COMPRA            = 6;
    public const CA_PDV                              = 7;
    public const CA_PDV_SANGRIA                      = 8;
    public const CA_PDV_CANCELA_ITEM                 = 9;
    public const CA_PDV_CANCELA_VENDA                = 10;
    public const CA_SERVIDOR_PRODUTO_PRECO           = 11;
    public const CA_SERVIDOR_ORDEM_COMPRA_LISTA      = 12;
    public const CA_SERVIDOR_CONTAS_A_PAGAR          = 13;
    public const CA_SERVIDOR_EMISSAO_RECIBO          = 14;
    public const CA_SERVIDOR_ORDEM_VENDA             = 15;
    public const CA_SERVIDOR_ORDEM_VENDA_ITEM_PRECO  = 16;
    public const CA_SERVIDOR_ORDEM_VENDA_ITEM_DESCONTO= 17;
    public const CA_CLIENTE_LIMITE                   = 18;
    public const CA_SERVIDOR_RELATORIO               = 19;
    public const CA_PDV_DESCONTO                     = 20;
    public const CA_SERVIDOR_CONFIG                  = 21;
    public const CA_SERVIDOR_ORDEM_VENDA_FRETE       = 22;
    public const CA_SERVIDOR_CONTAS_A_RECEBER        = 23;
    public const CA_CLIENTE_CREDITO                  = 24;
    public const CA_PDV_REFORCO                      = 25;
    public const CA_WAITER                           = 26;
    public const CA_PRODUTO_ESTOQUE_ADD              = 27;
    public const CA_PRODUTO_ESTOQUE_DEL              = 28;
    public const CA_VENDA_PRAZO_SEM_LIMITE           = 29;
    public const CA_TRANSFERENCIA_MESA               = 30;
    public const CA_VENDA_PRAZO_EDITAR               = 31;
    public const CA_ESTOQUE_SECUNDARIO_ADD           = 32;
    public const CA_ESTOQUE_SECUNDARIO_DEL           = 33;
    public const CA_ORDEM_VENDA_EDITAR               = 34;
    public const CA_MESA_ITEM_ESTORNO                = 35;
    public const CA_MAX                              = 35;

    public static function unauthorized(): never
    {
        Response::error('Unauthorized', 401);
    }

    /**
     * Verifica se um id_entidade possui o bit de acesso informado.
     */
    public static function hasAccess(int $id_entidade, int $access_type): bool
    {
        $pdo = Connection::pdo();
        $st = $pdo->prepare("
            SELECT c.acesso
              FROM tab_colaborador c
             WHERE c.id_entidade = :id
             LIMIT 1
        ");
        $st->execute([':id' => $id_entidade]);
        $row = $st->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return false;

        $acc = json_decode($row['acesso'] ?? '[]', true) ?: [];
        return isset($acc[$access_type]) && (int)$acc[$access_type] === 1;
    }

    /**
     * Exige que o usuário logado (na sessão PHP) possua o acesso informado.
     *  - Se não houver sessão ou não tiver permissão: 401.
     */
    public static function requireAccess(int $access_type): void
    {
        if (session_status() !== \PHP_SESSION_ACTIVE) @session_start();
        $id = (int)($_SESSION['auth']['id_entidade'] ?? 0);
        if ($id <= 0) self::unauthorized();
        if (!self::hasAccess($id, $access_type)) self::unauthorized();
    }

    /**
     * Valida credenciais + acesso e (opcionalmente) registra sessão no banco.
     * Retorna um token (compat com legado) e ativa a sessão PHP.
     */
    public static function login(int $id_entidade, string $pass, int $access_type, bool $register_session = true): array
    {
        if (trim((string)$id_entidade) === '' || trim($pass) === '') self::unauthorized();

        $col = new Collaborator();
        if (!$col->Read($id_entidade)) self::unauthorized();

        $row = $col->getResult();
        if (!$row || !password_verify($pass, $row['hash'] ?? '')) self::unauthorized();

        $nome = $row['nome'];

        $acc = json_decode($row['acesso'] ?? '[]', true) ?: [];
        if (!isset($acc[$access_type]) || (int)$acc[$access_type] !== 1) self::unauthorized();

        // registra sessão “legada” no banco se solicitado
        $session = password_hash((string)random_int(1, PHP_INT_MAX), PASSWORD_BCRYPT);
        if ($register_session) {
            $col->RegistrySession(['sessao' => $session, 'id_entidade' => $id_entidade]);
        }

        // Sessão PHP do ERP
        if (session_status() !== \PHP_SESSION_ACTIVE) @session_start();
        session_regenerate_id(true);
        // $_SESSION['auth'] = ['id_entidade' => $id_entidade];

        // JWT compat (igual ao legado), usando segredo configurável
        $secret = $_ENV['JWT_SECRET'] ?? 'minha-senha';
        $header  = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT'], JSON_UNESCAPED_UNICODE));
        $payload = base64_encode(json_encode(['id' => $id_entidade, 'session' => $session], JSON_UNESCAPED_UNICODE));
        $signature = base64_encode(hash_hmac('sha256', "$header.$payload", $secret, true));
        $token = "$header.$payload.$signature";

        // Cabeçalho compat
        header("Authorization: x-auth-token $token");

        return [
            'token'       => $token,
            'id_entidade' => $id_entidade,
            'nome' => $nome,
        ];
    }
}
