<?php

// src/Http/Controllers/UiPageController.php
namespace App\Http\Controllers;

use App\Http\Response;
use App\Legacy\ControlAccess;
use App\View\View;

final class UiPageController
{
    public function show(string $slug)
    {
        // se "home" exige login:
        ControlAccess::requireAccess(ControlAccess::CA_SERVIDOR);

        // mapeie slug -> template/bloco
        // ajuste conforme a sua estrutura real de templates
        $map = [
            'home' => ['tpl' => 'home', 'block' => 'BLOCK_PAGE'],
            // adicione aqui conforme for usando:
            // 'sale_order' => ['tpl' => 'sale_order', 'block' => 'BLOCK_PAGE'],
        ];

        if (!isset($map[$slug])) {
            return Response::raw('Página não encontrada', 'text/plain', 404);
        }

        $def = $map[$slug];
        $view = new View($def['tpl']);
        $html = $view->getContent([], $def['block']);

        return Response::raw($html, 'text/html; charset=UTF-8');
    }
}
