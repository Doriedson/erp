<?php
declare(strict_types=1);

use DI\Container;
use Slim\Factory\AppFactory;


require __DIR__ . '/../vendor/autoload.php';


$BASE = dirname(__DIR__); // /var/www/html/erp


// Dotenv (opcional)
if (class_exists(\Dotenv\Dotenv::class) && is_file($BASE.'/.env')) {
\Dotenv\Dotenv::createImmutable($BASE)->safeLoad();
}


// Erros em dev
if (($_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? '1') === '1') {
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
}

// Container
$container = new Container();

// Definitions (exemplos)
$container->set(App\Support\LegacyRenderer::class, fn() =>
    new App\Support\LegacyRenderer($BASE)
);

// Exemplo: Connection e Repositórios, se tiver
$container->set(App\Infra\Database\Connection::class, function () {
    // leia env aqui
    return new App\Infra\Database\Connection(
        host: $_ENV['DB_HOST'] ?? 'db',
        db:   $_ENV['DB_DATABASE'] ?? 'erp',
        user: $_ENV['DB_USERNAME'] ?? 'erp',
        pass: $_ENV['DB_PASSWORD'] ?? 'secret',
        port: (int)($_ENV['DB_PORT'] ?? 3306),
    );
});

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addRoutingMiddleware();

// Middleware de sessão (para o legado)
$app->add(new App\Http\Middlewares\StartSession());

// Error middleware (dev)
$errorMiddleware = $app->addErrorMiddleware(true, true, true);


// Carrega as rotas
(require __DIR__ . '/../routes/web.php')($app);


$app->run();