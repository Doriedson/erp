<?php
namespace App\Http\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Slim\Psr7\Response as SlimResponse;
use App\Auth\AuthService;

class AuthMiddleware implements Middleware
{
	public function __construct(private AuthService $auth) {}

	public function process(Request $request, Handler $handler): Response
	{
		if (!$this->auth->isAuthenticated()) {
			$res = new SlimResponse(302);
			return $res->withHeader('Location', '/login');
		}

		return $handler->handle($request);
	}
}