<?php

use Slim\App;
use App\Http\Controllers\LegacyApiController;
use App\Http\Controllers\AuthUiAdapter;

return function (App $app) {

    // Home nova (HTML direto):
    $app->get('/', [\App\Http\Controllers\HomeController::class, 'index']);

    // ... outras rotas modernas ...


    // Endpoints AJAX legados
    $app->map(['GET','POST'], '/message.php', [LegacyApiController::class, 'message']);
    // $app->get('/auth/status', [LegacyApiController::class, 'authStatus']);

    $app->get('/auth/status', [AuthUiAdapter::class, 'status']);
    $app->post('/auth/login', [AuthUiAdapter::class, 'login']);
    $app->post('/auth/logout', [AuthUiAdapter::class, 'logout']);

    // retorna HTML do popup autenticador (usa index.tpl -> bloco EXTRA_BLOCK_AUTHENTICATOR)
    $app->get('/auth/prompt', [AuthUiAdapter::class, 'prompt']);

    // Fallback do legado — sempre por último
    $app->map(['GET','POST','PUT','PATCH','DELETE'], '/{path:.*}', [
        \App\Http\Controllers\LegacyController::class, 'dispatch'
    ]);
};
