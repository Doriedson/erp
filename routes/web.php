<?php
use Slim\App;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\MessageController;
use App\Http\Middlewares\AuthMiddleware;


return function (App $app) {

	// APIs usadas pelo front logo no boot
    $app->map(['GET'],  '/auth/status', [AuthApiController::class, 'status']);
    $app->map(['POST'], '/message.php', [MessageController::class, 'message']);

	// Login (público)
	$app->get('/login', [AuthController::class, 'showLogin']);
	$app->post('/login', [AuthController::class, 'login']);
	$app->post('/logout', [AuthController::class, 'logout']);


	// Home (protegido)
	$app->get('/', [HomeController::class, 'index'])
	->add(AuthMiddleware::class);
};