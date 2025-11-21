<?php
namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\View\ViewRenderer;
use App\Auth\AuthService;
use App\Infra\Repositories\CollaboratorRepository;

final class AuthController
{
    public function __construct(
        private ViewRenderer $view,
        private AuthService $auth,
        private CollaboratorRepository $collabs
    ) {}

    /** GET /login */
    public function showLogin(Request $request, Response $response): Response
    {
        $assetsVersion = $_ENV['ASSETS_VERSION'] ?? (string) time();

        // Monta as <option> com o bloco EXTRA_BLOCK_COLLABORATOR
        $optionsHtml = '';
        $list = $this->collabs->listForLogin();
        foreach ($list as $i => $row) {
            $optionsHtml .= $this->view->block('login', 'EXTRA_BLOCK_COLLABORATOR', [
                'id_entidade' => (int)$row['id_entidade'],
                'nome'        => $row['nome'],
                'selected'    => ($i === 0 ? 'selected' : ''), // seleciona o 1º por padrão
            ]);
        }

        $loginHtml = $this->view->render('login', [
            'collaborators' => $optionsHtml, // placeholder {collaborators}
        ], 'BLOCK_PAGE');

        $html = $this->view->render('index', [
            'title'    => 'ERP — Login',
            'version'  => $assetsVersion,
            'manifest' => 'manifest.webmanifest',
            'menu'     => '',
            'module'   => $loginHtml,
        ], 'BLOCK_PAGE');

        $res = $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
        $res->getBody()->write($html);
        return $res;
    }

    /** POST /login */
    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody() ?? [];
        $idEntidade = (int)($data['id_entidade'] ?? 0);
        $pin        = (string)($data['senha'] ?? '');

        if ($idEntidade > 0 && $pin !== '' && $this->auth->authenticateByCollaboratorPin($idEntidade, $pin)) {
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        // Reapresenta com erro e mantém o selecionado
        $assetsVersion = $_ENV['ASSETS_VERSION'] ?? (string) time();

        $optionsHtml = '';
        $list = $this->collabs->listForLogin();
        foreach ($list as $row) {
            $optionsHtml .= $this->view->block('login', 'EXTRA_BLOCK_COLLABORATOR', [
                'id_entidade' => (int)$row['id_entidade'],
                'nome'        => $row['nome'],
                'selected'    => ((int)$row['id_entidade'] === $idEntidade ? 'selected' : ''),
            ]);
        }

        // Caso seu login.tpl tenha bloco de erro próprio, você pode criar aqui
        // $errorHtml = $this->view->block('login', 'EXTRA_ERROR', ['error' => 'Credenciais inválidas']);
        $loginHtml = $this->view->render('login', [
            'collaborators' => $optionsHtml,
            // 'error_block' => $errorHtml, // se existir no tpl
        ], 'BLOCK_PAGE');

        $html = $this->view->render('index', [
            'title'    => 'ERP — Login',
            'version'  => $assetsVersion,
            'manifest' => 'manifest.webmanifest',
            'menu'     => '',
            'module'   => $loginHtml,
        ], 'BLOCK_PAGE');

        $res = $response->withHeader('Content-Type', 'text/html; charset=UTF-8');
        $res->getBody()->write($html);
        return $res;
    }
}
