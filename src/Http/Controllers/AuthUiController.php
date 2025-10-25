<?php
namespace App\Http\Controllers;

use App\Database\Connection;
use App\Http\Response;
use App\View\View;
use App\Legacy\Collaborator;
use App\Legacy\ControlAccess;

final class AuthUiController
{

    // Página de login (form diferente do autenticador/popup)
    public function loginPage(): string
    {

        if (session_status() !== \PHP_SESSION_ACTIVE) @session_start();

        // Pode vir do POST (ex.: retorno de erro) ou da sessão, senão 0
        $id_entidade = (int)($_POST['id_entidade'] ?? ($_SESSION['auth']['id_entidade'] ?? 0));

        $tplLogin = new View('login');

        $collaborator = new Collaborator();
        // Lista somente quem tem acesso ao backend
        $rows = $collaborator->getListHavingAccess(ControlAccess::CA_SERVIDOR);

        $options = '';

        if (!empty($rows)) {

            foreach ($rows as $row) {
                // Garante a chave 'selected' correta (havia 'selecte' no original)
                $row['selected'] = ((int)$row['id_entidade'] === $id_entidade) ? 'selected' : '';
                // Renderiza o bloco <option>
                $options .= $tplLogin->getContent($row, 'EXTRA_BLOCK_COLLABORATOR');
            }

        } else {

            // Opcional: placeholder caso não haja colaboradores elegíveis
            $options .= '<option value="">Nenhum colaborador habilitado</option>';
        }

        // Monta a página de login (BLOCK_PAGE) injetando as <option>
        $html = $tplLogin->getContent([
            'collaborators' => $options,
        ], 'BLOCK_PAGE');

        // Retorna JSON no formato esperado pelo front (data.html)
        return Response::json(['html' => $html]);
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

        return Response::raw($html, 'text/html; charset=UTF-8');
    }

}