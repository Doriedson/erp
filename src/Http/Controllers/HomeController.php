<?php
namespace App\Http\Controllers;

use App\Support\Version;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\View\View;
use App\Legacy\ProductExpDate;

final class HomeController
{
    public function index(Request $request, Response $response): Response
    {

        $tplHome = new View('home');

        [$product_list, $expirated, $toexpirate, $extra_block_expiratedays] = ProductExpDate::getListHUD();

        $homeData = [
            'timestamp'                 => (new \DateTimeImmutable())->getTimestamp(),
            'expirated'                 => $expirated,
            'toexpirate'                => $toexpirate,
            'extra_block_expiratedays'  => $extra_block_expiratedays,
            // Se o home.tpl tiver mais placeholders, alimente-os aqui
        ];

        $homeHtml = $tplHome->getContent($homeData, 'BLOCK_PAGE');

        // 2) Render do MENU
        $tplMenu = new View('menu');

        // Se o menu for estático ou não tiver repetidores, pode ser vazio:
        $menuData = $menuData ?? [];

        $menuRootBlock = 'BLOCK_MENU'; // tente primeiro esse nome
        $menuHtml = $tplMenu->getContent($menuData, $menuRootBlock);

        // Se o bloco raiz do menu for outro (ex.: BLOCK_PAGE), faça um fallback simples:
        if (str_starts_with($menuHtml, 'View não encontrada') || $menuHtml === '') {
            $menuHtml = $tplMenu->getContent($menuData, 'BLOCK_PAGE');
        }

        $tplIndex = new View('index');

        // Exemplo de placeholders simples:
        $indexData = [
            'menu' => $menuHtml,
            'module' => $homeHtml,
            'username' => $_SESSION['username'] ?? 'Usuário',
            'today'    => (new \DateTimeImmutable('now'))->format('d/m/Y'),
            'title' => 'Retaguarda',
            'version' => Version::get(),
            'manifest'     => 'manifest.webmanifest',
            // Adicione aqui todos os placeholders simples usados no index.tpl
            // 'kpi_orders'   => $kpis['orders'] ?? 0,
            // 'kpi_revenue'  => number_format((float)($kpis['revenue'] ?? 0), 2, ',', '.'),
        ];



        // Exemplo de repetidor (se houver no index.tpl):
        // Suponha um bloco <!-- BEGIN EXTRA_MENU_ITEM --> ... {label} {url} ... <!-- END EXTRA_MENU_ITEM -->
        // e que o bloco raiz receba o placeholder {extra_menu_item}.
        $menuItems = [
            // ['label' => 'PDV',    'url' => '/pdv'],
            // ['label' => 'Garçom', 'url' => '/garcom'],
        ];

        // $indexData['menu'] = $this->buildBlock($tplIndex, 'EXTRA_MENU_ITEM', $menuItems);

        // Caso o seu template use classes condicionais, siga o padrão:
        // $indexData['hide_empty'] = $menuItems ? 'hidden' : '';

        // =========================================================
        // 3) Render do bloco raiz
        //    → Se o seu index.tpl usa "BLOCK_PAGE" como bloco raiz, mantenha.
        //    → Se o bloco raiz tiver outro nome, troque abaixo.
        // =========================================================
        $rootBlock = 'BLOCK_PAGE'; // ajuste se o index.tpl usar outro bloco raiz
        $html = $tplIndex->getContent($indexData, $rootBlock);

        // =========================================================
        // 4) Garantir Content-Type HTML (evita “herdar” JSON)
        // =========================================================
        if (!headers_sent()) {
            header_remove('Content-Type');
            header('Content-Type: text/html; charset=UTF-8');
        }

        $response->getBody()->write($html);
        return $response;
    }

    /**
     * Helper para montar repetidores do seu View:
     * - $blockName: nome exato do bloco (ex.: 'EXTRA_MENU_ITEM')
     * - $rows: lista de arrays associativos com as chaves esperadas pelo bloco
     * - $placeholderCase: seu View normalmente espera o placeholder
     *   minúsculo: {extra_menu_item}. Então, no controller, guarde o retorno
     *   em 'extra_menu_item' para injetar no bloco raiz.
     */
    private function buildBlock(View $tpl, string $blockName, array $rows): string
    {
        if (!$rows) {
            return '';
        }
        $out = '';
        foreach ($rows as $row) {
            $out .= $tpl->getContent($row, $blockName);
        }
        return $out;
    }
}
