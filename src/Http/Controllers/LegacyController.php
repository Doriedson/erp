<?php
namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Support\LegacyRenderer;

class LegacyController
{
    private array $whitelist = [
        'home','pdv','garcom','about','settings','product','provider',
        'collaborator','company','sale_order','receipt'
    ];

    private LegacyRenderer $legacy;

    public function __construct(?LegacyRenderer $legacy = null)
    {
        $this->legacy = $legacy ?? new LegacyRenderer(dirname(__DIR__, 3));
    }

    public function dispatch(Request $request, Response $response, array $args): Response
    {
        $path = trim((string)($args['path'] ?? ''), '/');
        $p = $path !== '' ? $path : 'home';

        $qp = $request->getQueryParams()['p'] ?? null;
        if (is_string($qp) && $qp !== '') {
            $p = $qp;
        }

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $p)) {
            $response->getBody()->write('Bad request');
            return $response->withStatus(400);
        }

        if (!in_array($p, $this->whitelist, true)) {
            $p = 'home';
        }

        $html = $this->legacy->render($p . '.php');
        if (str_starts_with($html, 'Arquivo não encontrado:')) {
            $response->getBody()->write('Página não encontrada');
            return $response->withStatus(404);
        }

        $response->getBody()->write($html);
        return $response;
    }
}
