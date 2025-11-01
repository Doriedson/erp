<?php
namespace App\Http\Controllers;

use App\Http\Response;

final class CollaboratorLegacyController
{
    public function handleAction()
    {
        $action = $_POST['action'] ?? '';

        switch ($action) {
            case 'collaborator_add':
                return (new CollaboratorController())->add();

            case 'collaborator_del':
                return (new CollaboratorController())->del();

            case 'collaborator_access':
                // No legado vinha key/value; agora mapeamos para module/perm
                // Ex.: key = "products.view" | "products.edit"
                $key   = (string)($_POST['key'] ?? '');
                $value = $_POST['value'] ?? '0';

                if (!str_contains($key, '.')) {
                    return Response::json(['error'=>'key inválida'], 422);
                }
                [$module, $perm] = explode('.', $key, 2);
                $_POST['module'] = $module;
                $_POST['perm']   = $perm;
                $_POST['value']  = $value;

                return (new CollaboratorController())->toggleAcl();

            default:
                return Response::json(['error'=>'action inválida'], 422);
        }
    }
}
