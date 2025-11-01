<?php
namespace App\Http\Controllers;

use App\Auth\AuthService;
use App\Security\AclService;
use App\Database\Connection;
use App\View\View;
use App\Http\Response;
use PDO;

final class CollaboratorController
{
    public function add()
    {
        (new AuthService())->requireAuthForApi();

        $idEntidade = (int)($_POST['value'] ?? 0);
        if (!$idEntidade) return Response::json(['error' => 'id_entidade inválido'], 422);

        $pdo = Connection::pdo();
        // cria colaborador se não existir
        $sql = "INSERT IGNORE INTO tab_colaborador (id_entidade, hash, acesso)
                VALUES (:id, '', JSON_OBJECT())";
        $st = $pdo->prepare($sql);
        $st->execute([':id' => $idEntidade]);

        // renderiza um "cartão" do colaborador para append na UI
        $view = new View('collaborator'); // templates/collaborator.tpl
        $row  = $this->fetchRow($pdo, $idEntidade); // junte nome, etc (JOIN com tab_entidade)
        $html = $view->getContent($this->formatRow($row), 'BLOCK_COLLABORATOR');

        return Response::html($html);
    }

    public function del()
    {
        (new AuthService())->requireAuthForApi();

        $idEntidade = (int)($_POST['value'] ?? 0);
        if (!$idEntidade) return Response::json(['error' => 'id_entidade inválido'], 422);

        $pdo = Connection::pdo();
        $pdo->prepare("DELETE FROM tab_colaborador WHERE id_entidade = :id")
            ->execute([':id' => $idEntidade]);

        return Response::json(['ok' => true]);
    }

    public function toggleAcl()
    {
        (new AuthService())->requireAuthForApi();

        $idEntidade = (int)($_POST['id_entidade'] ?? 0);
        $module     = (string)($_POST['module'] ?? '');
        $perm       = (string)($_POST['perm'] ?? ''); // 'view'|'edit'
        $value      = filter_var($_POST['value'] ?? '0', FILTER_VALIDATE_BOOLEAN);

        if (!$idEntidade || !$module || !in_array($perm, [AclService::VIEW, AclService::EDIT], true)) {
            return Response::json(['error' => 'payload inválido'], 422);
        }

        $acl = new AclService($idEntidade);
        $acl->set($module, $perm, $value);

        // Re-render do bloco da lista (para manter compat com seu JS atual)
        $view = new View('collaborator'); // templates/collaborator.tpl
        $html = $view->getContent(
            ['acl_html' => $this->renderAclCheckboxes($acl->all(), $idEntidade)],
            'BLOCK_COLLABORATOR_ACCESSLIST'
        );

        return Response::html($html);
    }

    private function fetchRow(PDO $pdo, int $id): array
    {
        $st = $pdo->prepare("SELECT e.id_entidade, e.nome, c.acesso
                               FROM tab_entidade e
                          LEFT JOIN tab_colaborador c ON c.id_entidade = e.id_entidade
                              WHERE e.id_entidade = :id");
        $st->execute([':id'=>$id]);
        return $st->fetch(PDO::FETCH_ASSOC) ?: ['id_entidade'=>$id,'nome'=>'','acesso'=>'{}'];
    }

    private function formatRow(array $row): array
    {
        $acl = json_decode($row['acesso'] ?? '{}', true) ?: [];
        return [
            'id_entidade' => (int)$row['id_entidade'],
            'nome'        => $row['nome'] ?? '',
            'acl_html'    => $this->renderAclCheckboxes($acl, (int)$row['id_entidade']),
        ];
    }

    private function renderAclCheckboxes(array $acl, int $idEntidade): string
    {
        // gera os checkboxes (view/edit) por módulo — use seu tpl
        $modules = ['backend','products','orders','customers','suppliers','reports','config','waiter','pdv'];
        $tpl = new View('collaborator');

        $html = '';
        foreach ($modules as $m) {
            $row = [
                'module' => $m,
                'checked_view' => (!empty($acl[$m]['view'])) ? 'checked' : '',
                'checked_edit' => (!empty($acl[$m]['edit'])) ? 'checked' : '',
                'id_entidade'  => $idEntidade,
            ];
            $html .= $tpl->getContent($row, 'EXTRA_BLOCK_ACL_ITEM');
        }
        return $html;
    }
}
