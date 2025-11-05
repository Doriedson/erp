<?php
use Slim\App;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


return function (App $app) {
	// Rotas modernas
	$app->get('/', [\App\Http\Controllers\HomeController::class, 'index']);


	// Exemplo de rota API moderna
	$app->get('/api/ping', function (Request $req, Response $res) {
		$res->getBody()->write('pong');
		return $res;
	});


	// PDV (ainda renderizando legado por enquanto)
	$app->get('/pdv', [\App\Http\Controllers\PdvController::class, 'index']);


	// Fallback de legado: última rota — tenta abrir home.php, pdv.php etc.
	$app->map(['GET','POST','PUT','PATCH','DELETE'], '/{path:.*}', [
		\App\Http\Controllers\LegacyController::class, 'dispatch'
	]);
};