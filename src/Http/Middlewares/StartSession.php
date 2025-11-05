<?php
namespace App\Http\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\MiddlewareInterface as Middleware;

class StartSession implements Middleware
{
	public function process(Request $request, Handler $handler): Response
	{
		if (session_status() === PHP_SESSION_NONE) {
			session_start();
		}

		return $handler->handle($request);
	}
}