<?php
namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\View\ViewRenderer; use App\Auth\AuthService;

final class HomeController
{
    public function __construct(private ViewRenderer $view, private AuthService $auth) {}


    /** GET / (após login) -> index.tpl com menu + home.tpl */
    public function index(Request $request, Response $response): Response
    {
        $assetsVersion = $_ENV['ASSETS_VERSION'] ?? (string) time();
        $user = $this->auth->user();

        // Conteúdo principal (home)
        $homeHtml = $this->view->render('home', [
        // preencha dados/contadores usados pelo home.tpl aqui
        ], 'BLOCK_PAGE');

        // Menu
        $menuHtml = $this->view->render('menu', [
            'username' => $user['name'] ?? $user['username'] ?? 'Usuário',
        ], 'BLOCK_PAGE');

        // Index com {module} = home e {menu}
        $html = $this->view->render('index', [
            'title' => 'ERP — Dashboard',
            'version' => $assetsVersion,
            'manifest' => 'manifest.webmanifest',
            'menu' => $menuHtml,
            'module' => $homeHtml,
        ], 'BLOCK_PAGE');

        $res = $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
        $res->getBody()->write($html);
        return $res;
    }
}