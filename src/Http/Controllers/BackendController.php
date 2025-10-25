<?php
namespace App\Http\Controllers;

use App\Http\Response;
use App\Auth\AuthService;
use App\View\View;

final class BackendController
{
    public function menu(): string
    {
        // exige login (JSON 401)
        (new AuthService())->requireAuthForApi();

        // Renderiza exatamente o HTML que antes vinha do backend.php
        // usando seus .tpl existentes
        $tplBackendIndex = new View('backend_index');
        $moduleHtml = $tplBackendIndex->getContent(['module' => 'backend'], 'BLOCK_PAGE');

        // Se o seu menu vinha de outro tpl, pegue aqui e retorne junto
        // $tplLeft = new View('backend_leftmenu');
        // $leftHtml = $tplLeft->getContent([...], 'BLOCK_MENU');

        return Response::json([
            'html' => $moduleHtml,
            // 'leftmenu' => $leftHtml,
        ]);
    }
}
