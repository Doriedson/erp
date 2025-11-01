<?php
/**
 * ERP – Front Controller
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
use App\Http\Controllers\MessageController;
use App\Http\Controllers\AuthUiController;
use App\Http\Controllers\UiPageController;
use App\Http\Controllers\UiBackendController;
use App\Http\Controllers\CollaboratorController;
use App\Http\Controllers\CollaboratorLegacyController;
use App\Auth\AuthService;

// Fuso horário do projeto
@date_default_timezone_set('America/Sao_Paulo');

// Sessão (usada no guard de rotas)
if (session_status() !== PHP_SESSION_ACTIVE) {

    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => false, // true em https
    ]);

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

// Logout via GET => só limpa sessão e redireciona (não escreve corpo)
$router->get('/logout', function () {
    (new AuthService())->logout();
    header('Location: /');
    return '';
});

$router->get('/auth/status', [AuthController::class, 'status']);

/** LOGIN UI (HTML puro) */
$router->get('/ui/login', [AuthUiController::class, 'loginPage']);   // usado pelo JS atual
$router->get('/login',     [AuthUiController::class, 'loginPageHtml']); // compat opcional (também HTML)
$router->get('/ui/auth/popup', [AuthUiController::class, 'popup']); // popup HTML (se usado)

/** MENSAGENS (legacy compat) */
$router->post('/message.php',       [MessageController::class, 'load']);         // legado
$router->post('/ui/messages/load',  [MessageController::class, 'load']);         // nova rota idem

// Backend UI (menu da retaguarda)
$router->get('/ui/backend/menu', [UiBackendController::class, 'menu']);

/** PÁGINAS (carregam index.tpl + módulo) */
$router->get('/',       [HomeController::class, 'indexBackend']); // Retaguarda
$router->get('/garcom', [HomeController::class, 'indexWaiter']);  // Garçom
$router->get('/pdv',    [HomeController::class, 'indexPdv']);     // PDV

// $router->get('/ui/pages/home', [UiPageController::class, 'home']);

// Popup: lista de produtos por validade (HTML para o bloco EXTRA_BLOCK_CP_EXPDATE_TR)
$router->get('/ui/home/expirations', [UiPageController::class, 'expirationsList']);

// Atualiza preferência “dias para expirar” (opcional; guarda em tab_config)
// $router->post('/ui/home/expirations/days', [UiPageController::class, 'saveExpirationDays']);

$router->post('/ui/home/expirations/days', [UiPageController::class, 'expirationDays']);
$router->get('/ui/home/expirations', [UiPageController::class, 'listExpirations']); // Linhas (HTML) da lista

// 1) Rotas ESPECÍFICAS (têm lógica própria)
$router->get('/ui/pages/home', [UiPageController::class, 'home']);                  // HTML da Home

/** PÁGINAS ESTÁTICAS/GENÉRICAS (HTML) */
$router->get('/ui/pages/(.+)', [UiPageController::class, 'show']);

// public/index.php (rotas novas)
$router->post('/admin/collaborators/add',  [CollaboratorController::class, 'add']);
$router->post('/admin/collaborators/del',  [CollaboratorController::class, 'del']);
$router->post('/admin/collaborators/acl',  [CollaboratorController::class, 'toggleAcl']);

// Ponte de compatibilidade com o JS legado (mesmo endpoint)
$router->post('/collaborator.php', [CollaboratorLegacyController::class, 'handleAction']);

/** HEALTH */
$router->get('/health', function () {
    header('Content-Type: application/json; charset=utf-8');
    $out = ['app' => 'ok'];
    try {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            getenv('DB_HOST') ?: '127.0.0.1',
            getenv('DB_PORT') ?: '3306',
            getenv('DB_DATABASE') ?: ''
        );
        $pdo = new \PDO($dsn, getenv('DB_USERNAME') ?: '', getenv('DB_PASSWORD') ?: '');
        $pdo->query('SELECT 1');
        $out['db'] = 'ok';
    } catch (\Throwable $e) {
        http_response_code(503);
        $out['db'] = 'down';
        $out['error'] = 'DB connection failed';
    }
    return $out; // Router deve responder JSON para arrays
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

/** ADMIN — protege qualquer /admin/* e deixa Nginx servir estático */
$router->get('/admin/.*', function () {
    (new AuthService())->requireAuthForPage();
    return ''; // Nginx resolve via try_files
});

// -----------------------------
// Dispatch
// -----------------------------
$router->run();
