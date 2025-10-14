<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Http\Router;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Auth\AuthService;

$router = new Router();

// Auth
$router->post('/auth/login',  [AuthController::class, 'login']);
$router->post('/auth/logout', [AuthController::class, 'logout']);

// API protegida
$router->mount('/api', function($r) {
    $r->get('/orders', function() {
        (new App\Auth\AuthService())->requireAuthForApi();
        return (new OrderController())->index();
    });
    $r->post('/orders', function() {
        (new App\Auth\AuthService())->requireAuthForApi();
        return (new OrderController())->store();
    });
    $r->patch('/orders/(\d+)/status', function($id) {
        (new App\Auth\AuthService())->requireAuthForApi();
        return (new OrderController())->updateStatus((int)$id);
    });
});

// Proteção de páginas administrativas (ex.: /admin/*)
$router->get('/admin/.*', function() {
    (new AuthService())->requireAuthForPage();
    // deixa o Nginx/Static servir o arquivo (orders.html, css, js, etc.)
    return ''; // sem output — o Nginx resolve via try_files
});

// rota raiz opcional → redireciona pro login
$router->get('/', function() {
    header('Location: /login');
    return '';
});

$router->run();
