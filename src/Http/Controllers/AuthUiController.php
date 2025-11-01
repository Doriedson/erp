<?php
namespace App\Http\Controllers;

use App\Database\Connection;
use App\Http\Response;
use App\View\View;
use App\Legacy\Collaborator;
use App\Legacy\ControlAccess;
use App\Auth\AuthService;
use PDO;

final class AuthUiController
{

    // Página de login (form diferente do autenticador/popup)
    public function loginPage(): string
    {

        return $this->loginPageHtml();
    }

    public function popup(): string
    {
        // Renderiza o bloco que contém o <form id="frm_authenticator"> já existente
        // Ajuste o caminho/nome do tpl e bloco para o que você usa hoje.
        $tpl = new View('index'); // ex.: authenticator.tpl
        $html = $tpl->getContent([], 'EXTRA_BLOCK_AUTHENTICATOR'); // ex.: bloco que tem o form

        return Response::json(['html' => $html]);
    }

    public function backendMenu()
    {
        // exige estar logado e ter acesso ao backend
        ControlAccess::requireAccess(ControlAccess::CA_SERVIDOR);

        // Renderize seu menu a partir de um .tpl:
        // ajuste o nome do template conforme seu repositório
        $tpl = new View('menu');
        $html = $tpl->getContent([], 'BLOCK_PAGE'); // ou bloco adequado

        return Response::Text($html, 'text/html; charset=UTF-8');
    }

    /**
     * GET /login
     * Retorna o HTML do formulário de login, populando o select de colaboradores
     * com acesso ao backend (CA_SERVIDOR).
     */
    public function loginPageHtml(): string
    {
        // opcional: manter último id pré-selecionado
        $idSelecionado = isset($_GET['id_entidade']) ? (int)$_GET['id_entidade'] : null;

        $pdo = Connection::pdo();

        // CA_SERVIDOR = índice 0 no JSON 'acesso' (pelo legado)
        // Se seu MySQL é 5.7+ dá pra filtrar já no SQL; senão, filtre em PHP (veja fallback abaixo).
        $sql = "
            SELECT c.id_entidade, e.nome
            FROM   tab_colaborador c
            JOIN   tab_entidade   e ON e.id_entidade = c.id_entidade
            WHERE  e.ativo = 1
            AND JSON_EXTRACT(c.acesso, '$.backend.view') = TRUE
            ORDER  BY e.nome ASC;
        ";

        $st = $pdo->query($sql);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);

        // Filtra em PHP quem tem CA_SERVIDOR == 1
        $lista = [];
        foreach ($rows as $r) {
            // $acesso = json_decode($r['acesso'] ?? '[]', true) ?: [];
            // if (isset($acesso[0]) && (int)$acesso[0] === 1) { // 0 = CA_SERVIDOR (retaguarda)
                $lista[] = [
                    'id_entidade' => (int)$r['id_entidade'],
                    'nome'        => (string)$r['nome'],
                    'selected'    => ($idSelecionado && (int)$r['id_entidade'] === $idSelecionado) ? 'selected' : '',
                ];
            // }
        }

        // Renderiza o .tpl
        // Ajuste o caminho conforme sua estrutura; pelo seu View legado, geralmente “templates/backend_login”
        $tpl = new View('login');

        $options = '';
        foreach ($lista as $item) {
            $options .= $tpl->getContent($item, 'EXTRA_BLOCK_COLLABORATOR');
        }

        $html = $tpl->getContent(['collaborators' => $options], 'BLOCK_PAGE');

        return Response::html($html);
    }

    /**
     * GET /ui/backend/menu
     * Retorna HTML do menu lateral, condicionado por permissões.
     */
    public function menu(): string
    {
        (new AuthService())->requireAuthForPage(); // garante sessão

        // Recupera usuário logado
        // $user = AuthService::user(); // supondo que você já tenha esse helper; se não, leia da sessão
        $user = $_SESSION['user'];

        // Carrega acessos do colaborador (legado, mantendo nomes/estrutura)
        $col = new Collaborator();
        $col->Read($user['id_entidade']);
        $row = $col->getResult(); // legado mantém esse método
        $access = $row ? json_decode($row['acesso'] ?? '[]', true) : [];

        // Monta itens de menu conforme regras atuais
        $items = [];

        $items[] = [
            'icon' => 'fa-solid fa-house',
            'label'=> 'Início',
            'path' => 'home.php'
        ];

        if (!empty($access[ControlAccess::CA_SERVIDOR])) {
            $items[] = ['icon'=>'fa-solid fa-box', 'label'=>'Produtos', 'path'=>'product.php'];
            $items[] = ['icon'=>'fa-solid fa-users', 'label'=>'Clientes', 'path'=>'entity.php'];
            $items[] = ['icon'=>'fa-solid fa-truck', 'label'=>'Fornecedores', 'path'=>'supplier.php'];
        }
        if (!empty($access[ControlAccess::CA_SERVIDOR_ORDEM_VENDA])) {
            $items[] = ['icon'=>'fa-solid fa-receipt', 'label'=>'Vendas/Pedidos', 'path'=>'sale_order.php'];
        }
        if (!empty($access[ControlAccess::CA_SERVIDOR_CONTAS_A_PAGAR])) {
            $items[] = ['icon'=>'fa-solid fa-file-invoice-dollar', 'label'=>'Contas a Pagar', 'path'=>'bills_to_pay.php'];
        }
        if (!empty($access[ControlAccess::CA_SERVIDOR_CONTAS_A_RECEBER])) {
            $items[] = ['icon'=>'fa-solid fa-file-invoice', 'label'=>'Contas a Receber', 'path'=>'bills_to_receive.php'];
        }
        if (!empty($access[ControlAccess::CA_SERVIDOR_RELATORIO])) {
            $items[] = ['icon'=>'fa-solid fa-chart-line', 'label'=>'Relatórios', 'path'=>'report.php'];
        }
        if (!empty($access[ControlAccess::CA_SERVIDOR_CONFIG])) {
            $items[] = ['icon'=>'fa-solid fa-gear', 'label'=>'Configurações', 'path'=>'company.php'];
        }

        // Renderiza via View (mantendo o motor de template existente)
        $tpl = new View('menu');
        $itemHtml = '';
        foreach ($items as $it) {
            $itemHtml .= $tpl->getContent([
                'icon'  => $it['icon'],
                'label' => $it['label'],
                'path'  => $it['path'],
            ], 'EXTRA_BLOCK_MENU_ITEM');
        }

        return $tpl->getContent([
            'items' => $itemHtml,
        ], 'BLOCK_MENU');
    }
}