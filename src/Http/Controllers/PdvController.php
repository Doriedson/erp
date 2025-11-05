<?php
namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Support\LegacyRenderer;

class PdvController
{
    private LegacyRenderer $legacy;

    public function __construct(?LegacyRenderer $legacy = null)
    {
        $this->legacy = $legacy ?? new LegacyRenderer(dirname(__DIR__, 3));
    }

    public function index(Request $request, Response $response): Response
    {
        $html = $this->legacy->render('pdv.php');
        $response->getBody()->write($html);
        return $response;
    }
}
