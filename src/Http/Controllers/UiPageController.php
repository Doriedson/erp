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
    private const MIN_EXPIRY_DAYS = 0;
    private const MAX_EXPIRY_DAYS = 365;

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

        $repo = new ProductExpiryRepository();
        $tpl  = new View('home'); // templates/home.tpl

        $action = $this->resolveHomeAction();
        if ($action !== null) {
            return $this->handleExpiryDaysAction($repo, $tpl, $action);
        }

        // Lê preferência de dias do usuário (fallback: 30)
        $days = $repo->getExpiryDaysThreshold();

        // Busca contadores
        [$expirated, $toexpirate] = $repo->counters($days);

        // Monta o fragmento {extra_block_expiratedays}
        $chipHtml = $this->renderExpiryDaysButton($tpl, $days);

        // Render da página
        $html = $tpl->getContent([
            'extra_block_expiratedays' => $chipHtml,
            'expirated'                => $expirated,
            'toexpirate'               => $toexpirate,
            // Demais placeholders que seu BLOCK_PAGE usa…
        ], 'BLOCK_PAGE');

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
        $auth = new AuthService();
        if (!$auth->isAuthenticated()) {
            return Response::error('Unauthorized', 401);
        }

        $repo   = new ProductExpiryRepository(Connection::pdo());
        $tpl    = new View('home');
        $action = $this->resolveHomeAction() ?? 'save';

        return $this->handleExpiryDaysAction($repo, $tpl, $action);
    }

    // Lista de itens (HTML completo do bloco EXTRA_BLOCK_POPUP_CP_EXPDATE)
    public function listExpirations(): string
    {
        $auth = new AuthService();
        if (!$auth->isAuthenticated()) {
            return Response::html('', 401);
        }

        $repo = new ProductExpiryRepository();
        $tpl  = new View('home');

        $currentDays = $repo->getExpiryDaysThreshold();
        $days        = $this->sanitizeDays($_GET['days'] ?? $currentDays, $currentDays);

        $rows     = $repo->listByDays($days); // linhas normalizadas usadas pelo tpl legado
        $rowsHtml = $this->renderExpirationsRows($tpl, $rows);

        $hasRows = $rowsHtml !== '';

        $html = $tpl->getContent([
            'extra_block_expiratedays' => $this->renderExpiryDaysButton($tpl, $days),
            'extra_block_cp_expdate_tr'=> $rowsHtml,
            'cp_expdate_notfound'      => $hasRows ? 'hidden' : '',
            'productexpdate_bt_print'  => $hasRows ? '' : 'hidden',
            'product_expirate_days'    => (string)$days,
        ], 'EXTRA_BLOCK_POPUP_CP_EXPDATE');

        return Response::html($html);
    }

    private function resolveHomeAction(): ?string
    {
        $action = $_POST['action'] ?? $_GET['action'] ?? null;
        if (is_string($action) && $action !== '') {
            return $action;
        }
        return null;
    }

    private function handleExpiryDaysAction(ProductExpiryRepository $repo, View $tpl, string $action): string
    {
        $currentDays = $repo->getExpiryDaysThreshold();
        $normalized  = strtolower($action);
        $context     = $this->resolveExpiryContext();

        switch ($normalized) {
            case 'edit':
            case 'cp_expiratedays_edit':
                return Response::json($this->renderExpiryDaysForm($tpl, $currentDays));

            case 'cancel':
            case 'cp_expiratedays_cancel':
                return Response::json($this->renderExpiryDaysButton($tpl, $currentDays));

            case 'save':
            case 'cp_expiratedays_save':
                $days = $this->sanitizeDays($this->getDaysFromRequest(), $currentDays);
                $repo->setDaysThreshold($days);
                [$expirated, $toexpirate] = $repo->counters($days);

                $payload = [
                    'days'                     => $days,
                    'expirated'                => $expirated,
                    'toexpirate'               => $toexpirate,
                    'extra_block_expiratedays' => $this->renderExpiryDaysButton($tpl, $days),
                ];

                if ($context === 'list') {
                    $payload = array_merge(
                        $payload,
                        $this->buildListPayload($repo, $tpl, $days)
                    );
                }

                return Response::json($payload);

            case 'update':
            case 'cp_expiratedays_update':
                [$expirated, $toexpirate] = $repo->counters($currentDays);

                $payload = [
                    'days'                     => $currentDays,
                    'expirated'                => $expirated,
                    'toexpirate'               => $toexpirate,
                    'extra_block_expiratedays' => $this->renderExpiryDaysButton($tpl, $currentDays),
                ];

                if ($context === 'list') {
                    $payload = array_merge(
                        $payload,
                        $this->buildListPayload($repo, $tpl, $currentDays)
                    );
                }

                return Response::json($payload);

            default:
                return Response::error('Ação inválida.', 400);
        }
    }

    private function sanitizeDays($value, int $fallback): int
    {
        if (is_numeric($value)) {
            $value = (int)$value;
        } else {
            $value = $fallback;
        }

        if ($value < self::MIN_EXPIRY_DAYS) {
            return self::MIN_EXPIRY_DAYS;
        }

        if ($value > self::MAX_EXPIRY_DAYS) {
            return self::MAX_EXPIRY_DAYS;
        }

        return $value;
    }

    private function getDaysFromRequest()
    {
        if (isset($_POST['days'])) {
            return $_POST['days'];
        }
        if (isset($_POST['value'])) {
            return $_POST['value'];
        }
        if (isset($_GET['days'])) {
            return $_GET['days'];
        }
        if (isset($_GET['value'])) {
            return $_GET['value'];
        }

        return null;
    }

    private function resolveExpiryContext(): string
    {
        $ctx = $_POST['context'] ?? $_GET['context'] ?? '';

        if (is_string($ctx)) {
            $ctx = strtolower(trim($ctx));
        } else {
            $ctx = '';
        }

        return $ctx === 'list' ? 'list' : 'home';
    }

    private function renderExpiryDaysButton(View $tpl, int $days): string
    {
        return $tpl->getContent(
            ['product_expirate_days' => (string)$days],
            'EXTRA_BLOCK_EXPIRATEDAYS'
        );
    }

    private function buildListPayload(ProductExpiryRepository $repo, View $tpl, int $days): array
    {
        $rows     = $repo->listByDays($days);
        $rowsHtml = $this->renderExpirationsRows($tpl, $rows);

        return [
            'extra_block_cp_expdate_tr' => $rowsHtml,
            'cp_expdate_notfound'       => $rowsHtml === '' ? '' : 'hidden',
            'productexpdate_bt_print'   => $rowsHtml === '' ? 'hidden' : '',
            'product_expirate_days'     => (string)$days,
        ];
    }

    private function renderExpiryDaysForm(View $tpl, int $days): string
    {
        return $tpl->getContent(
            ['product_expirate_days' => (string)$days],
            'EXTRA_BLOCK_EXPIRATEDAYS_FORM'
        );
    }

    private function renderExpirationsRows(View $tpl, array $rows): string
    {
        if (empty($rows)) {
            return '';
        }

        $html = '';
        foreach ($rows as $row) {
            $html .= $tpl->getContent([
                'id_produtovalidade'               => (string)($row['id_produtovalidade'] ?? ''),
                'id_produto'                       => (string)($row['id_produto'] ?? ''),
                'produto'                          => $row['produto'] ?? '',
                'produtotipo'                      => $row['produtotipo'] ?? '',
                'data_formatted'                   => $row['data_formatted'] ?? '',
                'dias'                             => (string)($row['dias'] ?? '0'),
                'extra_block_productexpdate_days'  => $row['extra_block_productexpdate_days'] ?? '',
                'extra_block_productexpdate_expirated' => $row['extra_block_productexpdate_expirated'] ?? '',
                'extra_block_product_button_status'    => $row['extra_block_product_button_status'] ?? '',
            ], 'EXTRA_BLOCK_CP_EXPDATE_TR');
        }

        return $html;
    }
}
