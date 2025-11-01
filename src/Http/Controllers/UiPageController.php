<?php

namespace App\Http\Controllers;

use App\Http\Response;
use App\Auth\AuthService;
use App\View\View;
use App\Database\Connection;
use App\Repositories\ProductExpiryRepository;
use Throwable;

final class UiPageController
{
    public function show(string $slug)
    {
        // 1) Autenticação
        $auth = new AuthService();
        if (!$auth->isAuthenticated()) {
            // Front já trata 401 mostrando login
            return Response::html('', 401);
        }

        // normaliza slug
        $slug = strtolower(trim($slug));

        // Mapa simples: slug -> [tpl, bloco]
        $map = [
            'home' => ['tpl' => 'home', 'block' => 'BLOCK_PAGE'],
            // adicione aqui outras páginas novas
            // 'product' => ['tpl' => 'product', 'block' => 'BLOCK_PAGE'],
        ];

        if (!isset($map[$slug])) {
            return Response::html('Página não encontrada.', 404);
        }

        $tplName = $map[$slug]['tpl'];
        $block   = $map[$slug]['block'];

        // 3) Tenta renderizar com blocos conhecidos
        try {
            $view = new View($tplName);

            $html = $view->getContent([], $block);

            if ($html === null || $html === '') {
                // Template existe mas não tem bloco esperado
                error_log("[UiPageController] Bloco não encontrado no template '{$tplName}.tpl'.");

                return Response::html(
                    "<div class='p-3'>Bloco não encontrado no template <code>{$tplName}.tpl</code>.</div>",
                    404
                );
            }

            return Response::html($html);

        } catch (Throwable $e) {
            // Provavelmente arquivo não existe OU erro dentro do View
            error_log("[UiPageController] Falha ao carregar '{$tplName}.tpl': " . $e->getMessage());
            return Response::html(
                "<div class='p-3'>Página <code>{$slug}</code> não encontrada.</div>",
                404
            );
        }
    }

    public function home(): string
    {
        $auth = new AuthService();
        if (!$auth->isAuthenticated()) {
            return Response::html('', 401);
        }

        // $uid = $auth->userId();

        $repo = new ProductExpiryRepository();

        // Lê preferência de dias do usuário (fallback: 30)
        $days = $repo->getExpiryDaysThreshold();

        // Busca contadores
        [$expirated, $toexpirate] = $repo->counters($days);

        // Monta o fragmento {extra_block_expiratedays}
        $tpl = new View('home'); // templates/home.tpl
        $chipHtml = $tpl->getContent(['product_expirate_days' => $days], 'EXTRA_BLOCK_EXPIRATEDAYS');

        // Render da página
        $html = $tpl->getContent([
            'extra_block_expiratedays' => $chipHtml,
            'expirated'                => $expirated,
            'toexpirate'               => $toexpirate,
            // Demais placeholders que seu BLOCK_PAGE usa…
        ], 'BLOCK_PAGE');

        return Response::html($html);
    }

    /** GET /ui/home/expirations?days=15 → devolve apenas as linhas EXTRA_BLOCK_CP_EXPDATE_TR */
    public function expirationsList(): string
    {
        $tpl  = new View('home');
        $repo = new ProductExpiryRepository();

        $days = $repo->getExpiryDaysThreshold();

        $rows = $repo->listExpirations($days);

        if (!$rows) {
            // devolve marcador para o front exibir “não encontrado”
            return Response::html('<div class="cp_expdate_notfound" style="padding: 40px 10px;">Nenhum produto com vencimento próximo <i class="icon fa-regular fa-face-smile-wink"></i></div>');
        }

        $html = '';
        foreach ($rows as $r) {
            $isExpired = (int)$r['dias'] < 0;

            $html .= $tpl->getContent([
                'id_produtovalidade' => (string)$r['id_produtovalidade'],
                'id_produto'         => (string)$r['id_produto'],
                'produto'            => $r['produto'],
                'produtotipo'        => $r['produtotipo'],
                'data_formatted'     => date('d/m/Y', strtotime($r['data'])),

                // blocos condicionais
                'extra_block_productexpdate_days'      => $isExpired ? '' : $tpl->getContent(
                    ['dias' => (string)$r['dias']],
                    'EXTRA_BLOCK_PRODUCTEXPDATE_DAYS'
                ),
                'extra_block_productexpdate_expirated' => $isExpired ? $tpl->getContent([], 'EXTRA_BLOCK_PRODUCTEXPDATE_EXPIRATED') : '',

                // se tiver botões de status, pode preencher aqui:
                'extra_block_product_button_status'    => '',
            ], 'EXTRA_BLOCK_CP_EXPDATE_TR');
        }

        return Response::html($html);
    }

    public function editDays(): string
    {
        $auth = new AuthService();
        if (!$auth->isAuthenticated()) {
            return Response::error('Unauthorized', 401);
        }

        // $uid  = $auth->userId();

        $repo = new ProductExpiryRepository(Connection::pdo());

        $days = $repo->getExpiryDaysThreshold();

        $tpl = new View('home');

        $chipHtml = $tpl->getContent(['product_expirate_days' => $days], 'EXTRA_BLOCK_EXPIRATEDAYS_FORM');

        return Response::html($chipHtml);
    }
    // Salva “Vencem em” (retorna JSON com novos contadores + chip)
    public function expirationDays(): string
    {

        $action = $_POST['action'];

        $auth = new AuthService();
        if (!$auth->isAuthenticated()) {
            return Response::error('Unauthorized', 401);
        }

        $uid  = $auth->userId();
        $days = max(0, min(365, (int)($_POST['days'] ?? 0)));

        // $this->setUserDaysPref($uid, $days);

        $repo = new ProductExpiryRepository(Connection::pdo());
        [$expirated, $toexpirate] = $repo->counters($days);

        $tpl = new View('home');
        $chipHtml = $tpl->getContent(['product_expirate_days' => $days], 'EXTRA_BLOCK_EXPIRATEDAYS');

        return Response::json([
            'days'                    => $days,
            'expirated'               => $expirated,
            'toexpirate'              => $toexpirate,
            'extra_block_expiratedays'=> $chipHtml,
        ]);
    }

    // Lista de itens (HTML de <div class="cp_expdate_tr">…)
    public function listExpirations(): string
    {
        $auth = new AuthService();
        if (!$auth->isAuthenticated()) {
            return Response::html('', 401);
        }

        $repo = new ProductExpiryRepository();

        $days = (int)($_GET['days'] ?? $repo->getExpiryDaysThreshold());
        $days = max(0, min(365, $days));

        $rows = $repo->listByDays($days); // retorna um array de linhas normalizadas

        $tpl = new View('home');
        $rowsHtml = '';
        if (!empty($rows)) {
            foreach ($rows as $r) {
                $rowsHtml .= $tpl->getContent($r, 'EXTRA_BLOCK_CP_EXPDATE_TR');
            }
        }

        // Se preferir devolver o bloco completo (com cabeçalho, etc), monte com EXTRA_BLOCK_POPUP_CP_EXPDATE
        return Response::html($rowsHtml);
    }

    private function renderDaysBlock(View $tpl, int $days): string
    {
        // se você quer o botão simples “{product_expirate_days} dias”:
        $btn = $tpl->getContent(['product_expirate_days' => (string)$days], 'EXTRA_BLOCK_EXPIRATEDAYS');

        // e/ou o formulário de edição (abre quando clicar):
        // $form = $tpl->getContent(['product_expirate_days' => (string)$days], 'EXTRA_BLOCK_EXPIRATEDAYS_FORM');

        return $btn;
    }
}
