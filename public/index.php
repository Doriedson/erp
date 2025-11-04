<?php
declare(strict_types=1);

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Dotenv
$envPath = dirname(__DIR__);
if (file_exists($envPath . '/.env')) {
    Dotenv\Dotenv::createImmutable($envPath)->load();
}

// Instância do app
$app = AppFactory::create();

// Middlewares básicos
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();

// Exemplo de rota antiga “encapsulada” como handler
$app->get('/', [\App\Http\Controllers\HomeController::class, 'index']);
$app->get('/pdv', [\App\Http\Controllers\PdvController::class, 'index']);

// API (exemplo)
$app->get('/api/products', [\App\Http\Controllers\Api\ProductController::class, 'list']);

// 404 handler simples
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->run();
