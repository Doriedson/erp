<?php
/**
 * ERP – Front Controller
 * Rotas principais da aplicação (Auth + API + Admin)
 */

declare(strict_types=1);

// -----------------------------
// Autoload / Boot / Ambiente
// -----------------------------
if (!defined('APP_ROOT')) {

    define('APP_ROOT', dirname(__DIR__)); // /var/www/html/erp
}

require __DIR__ . '/../vendor/autoload.php';

use App\Http\Router;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackendController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthUiController;
use App\Http\Controllers\UiPageController;
use App\Auth\AuthService;

// Fuso horário do projeto
@date_default_timezone_set('America/Sao_Paulo');

// Sessão (usada no guard de rotas)
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Error reporting (ajuste se tiver APP_DEBUG no ambiente)
$debug = getenv('APP_DEBUG');

if ($debug === '1' || $debug === 'true') {

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

} else {

    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
    ini_set('display_errors', '0');
}

$router = new Router();

// Auth
$router->post('/auth/login',  [AuthController::class, 'login']);
$router->post('/auth/logout', [AuthController::class, 'logout']);
$router->get('/logout', function () {
    (new AuthController())->logout(); // limpa sessão + cookie
    header('Location: /');
    return '';
});

$router->get('/ui/login', [AuthUiController::class, 'loginPage']);  // login page (não popup)

// Mensagens (já migrado p/ View .tpl)
$router->post('/ui/messages/load', [MessageController::class, 'load']);

// Backend UI (menu da retaguarda)
$router->post('/ui/backend/menu', [BackendController::class, 'menu']);
$router->get('/ui/backend/menu', [AuthUiController::class, 'backendMenu']);

// Módulos
$router->get('/',          fn() => (new HomeController())->indexWithModule('backend'));
$router->get('/garcom',    fn() => (new HomeController())->indexWithModule('waiter'));
$router->get('/pdv',       fn() => (new HomeController())->indexWithModule('pdv'));

$router->get('/auth/status', [AuthController::class, 'status']);

$router->get('/ui/auth/popup', [AuthUiController::class, 'popup']); // entrega o HTML do formulário

$router->get('/ui/pages/([a-zA-Z0-9_-]+)', [UiPageController::class, 'show']);





// Healthcheck simples (app + DB)
$router->get('/health', function () {
    header('Content-Type: application/json; charset=utf-8');

    $out = ['app' => 'ok'];

    // Ping do banco (usando variáveis de ambiente já configuradas)
    try {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            getenv('DB_HOST') ?: '127.0.0.1',
            getenv('DB_PORT') ?: '3306',
            getenv('DB_DATABASE') ?: ''
        );
        $pdo = new PDO($dsn, getenv('DB_USERNAME') ?: '', getenv('DB_PASSWORD') ?: '');
        $pdo->query('SELECT 1'); // ping
        $out['db'] = 'ok';
    } catch (Throwable $e) {
        http_response_code(503);
        $out['db'] = 'down';
        $out['error'] = 'DB connection failed';
    }

    return $out; // o Router converte array em JSON
});

// -----------------------------
// API protegida
// -----------------------------
// Usa o guard do AuthService antes de invocar os controllers
$router->mount('/api', function (Router $r) {

    $r->get('/orders', function () {
        (new AuthService())->requireAuthForApi();
        return (new OrderController())->index();
    });

    $r->post('/orders', function () {
        (new AuthService())->requireAuthForApi();
        return (new OrderController())->store();
    });

    $r->patch('/orders/(\d+)/status', function ($id) {
        (new AuthService())->requireAuthForApi();
        return (new OrderController())->updateStatus((int)$id);
    });
});

// -----------------------------
// Páginas administrativas
// -----------------------------
// Qualquer rota começando com /admin/ exige sessão.
// O retorno vazio deixa o Nginx servir os estáticos via try_files.
$router->get('/admin/.*', function () {

    (new AuthService())->requireAuthForPage();
    return ''; // Nginx/estático resolve (HTML, CSS, JS, etc).
});

/** PÁGINAS (render de index.tpl + módulo) */
$router->get('/',        [HomeController::class, 'indexBackend']); // Retaguarda
$router->get('/garcom',  [HomeController::class, 'indexWaiter']);  // Garçom

/** ENDPOINTS LEGADOS (compat p/ JS atual) */
$router->post('/message.php', [MessageController::class, 'load']);

// -----------------------------
// Dispatch
// -----------------------------
$router->run();
