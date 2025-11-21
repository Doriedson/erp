<?php
namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Auth\AuthService;

final class AuthApiController
{
    public function __construct(private AuthService $auth) {}

    public function status(Request $req, Response $res): Response
    {
        $payload = [
            'ok'            => true,
            'authenticated' => $this->auth->isAuthenticated(),
            'user'          => $this->auth->user(),
        ];
        $res = $res->withHeader('Content-Type', 'application/json; charset=UTF-8');
        $res->getBody()->write(json_encode($payload));
        return $res;
    }
}
