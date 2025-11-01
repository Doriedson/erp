<?php
namespace App\Http\Controllers;

use App\Auth\AuthService;
use App\Security\AclService;
use App\View\View;
use App\Http\Response;
use App\Database\Connection;
use PDO;
use Throwable;

final class UiBackendController
{
    public function menu()
    {
        $auth = new AuthService();

        if (!$auth->isAuthenticated()) {
            http_response_code(401);
            return Response::html('');
        }

        $pdo = Connection::pdo();

        $empresa = 'Empresa';

        try {
            $empresa = (string)($pdo->query("SELECT empresa FROM tab_empresa LIMIT 1")->fetchColumn() ?: 'Empresa');
        } catch (Throwable $e) {
            // log opcional
        }

        $uid = $auth->userId();
        $acl = new AclService($uid);

        // Defina sua lista de itens
        $items = [
            // Operação
            ['module'=>'backend',        'icon'=>'fa-solid fa-gauge',     'label'=>'Painel',      'path'=>'/home.php'],
            ['module'=>'orders',        'icon'=>'fa-solid fa-receipt',     'label'=>'Pedidos',      'path'=>'/sale_order.php'],
            // ['module'=>'suppliers',     'icon'=>'fa-solid fa-cart-arrow-down','label'=>'Compras',  'path'=>'/purchase_order.php'],

            // Cadastros
            ['module'=>'products',      'icon'=>'fa-solid fa-box',         'label'=>'Produtos',     'path'=>'/product.php'],
            ['module'=>'customers',     'icon'=>'fa-solid fa-user',        'label'=>'Clientes',     'path'=>'/customer.php'],
            ['module'=>'suppliers',     'icon'=>'fa-solid fa-truck',       'label'=>'Fornecedores',      'path'=>'/supplier.php'],
            ['module'=>'collaborators', 'icon'=>'fa-solid fa-user-group',  'label'=>'Colaborador',  'path'=>'/collaborator.php'],

            // Gestão
            ['module'=>'reports',       'icon'=>'fa-solid fa-chart-pie',   'label'=>'Relatórios',   'path'=>'/report.php'],
            ['module'=>'config',        'icon'=>'fa-solid fa-gear',        'label'=>'Configurações',      'path'=>'/config.php'],
        ];

        // Filtra por VIEW
        $renderItems = [];
        foreach ($items as $it) {
            if ($acl->canView($it['module'])) {
                $renderItems[] = [
                    'icon'  => $it['icon'],
                    'label' => $it['label'],
                    'path'  => ltrim($it['path'], '/'), // será resolvido por LoadPage
                ];
            }
        }

        $tpl = new View('menu'); // templates/menu.tpl

        $itemsHtml = '';

        foreach ($renderItems as $d) {
            $itemsHtml .= $tpl->getContent($d, 'EXTRA_BLOCK_MENU_ITEM');
        }

        $html = $tpl->getContent([
            'id_entidade' => $_SESSION['auth']['id_entidade'],
            'nome' => $_SESSION['auth']['nome'],
            'empresa' => $empresa, 'items' => $itemsHtml
        ], 'BLOCK_MENU');

        return Response::html($html);
    }
}
