<?php
namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Support\LegacyRenderer;

class HomeController
{
    public function __construct(private readonly LegacyRenderer $legacy) {}

    public function index(Request $r, Response $res): Response
    {
        $res->getBody()->write($this->legacy->render('home.php'));
        return $res;
    }
}