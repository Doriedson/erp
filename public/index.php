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
use App\Http\Controllers\LegacyCompatController;
use App\Auth\AuthService;
use App\Http\Controllers\MessageController;

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

// -----------------------------
// Router
// -----------------------------
$router = new Router();

/**
 * Dica: se quiser um healthcheck aqui (além do seu health.php estático):
 * $router->get('/health', fn() => ['ok' => true]);
 */

// -----------------------------
// Auth (API)
// -----------------------------
// POST /auth/login  – recebe {id_entidade, senha} (ou pass) e cria sessão
// POST /auth/logout – encerra sessão
$router->post('/auth/login',  [AuthController::class, 'login']);
$router->post('/auth/logout', [AuthController::class, 'logout']);

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
$router->post('/backend.php', [LegacyCompatController::class, 'backend']);
$router->post('/waiter.php',  [LegacyCompatController::class, 'waiter']);
$router->post('/pdv.php',     [LegacyCompatController::class, 'pdv']); // se usar

// -----------------------------
// Dispatch
// -----------------------------
$router->run();
