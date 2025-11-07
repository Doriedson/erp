<?php
namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\View\View;
use App\Auth\Authorization;

/**
 * Adapter para plugar a sua classe legada AuthUiController no Slim.
 * - Se o método existir na classe antiga, delega.
 * - Caso contrário, usa um fallback padrão.
 */
final class AuthUiAdapter
{
    private string $baseDir;
    private $legacy; // instância da sua AuthUiController (se existir)

    public function __construct()
    {
        $this->baseDir = dirname(__DIR__, 3);

        // tenta localizar e instanciar a classe legada (sem quebrar)
        $legacyClass = '\\AuthUiController';
        if (!class_exists($legacyClass)) {
            // tente também com namespace comum do projeto
            $alt = '\\App\\Auth\\AuthUiController';
            if (class_exists($alt)) $legacyClass = $alt;
        }
        if (class_exists($legacyClass)) {
            $this->legacy = new $legacyClass();
        }
    }

    /** GET /auth/status  -> JSON */
    public function status(Request $request, Response $response): Response
    {
        // Se a classe legada tiver um método equivalente, delega.
        foreach (['status','getStatus','authStatus'] as $m) {
            if ($this->legacy && method_exists($this->legacy, $m)) {
                $payload = $this->legacy->{$m}();
                return $this->json($response, $payload);
            }
        }

        // Fallback padrão usando sua Authorization
        $auth = class_exists(Authorization::class) ? new Authorization() : null;

        $authenticated = false;
        $user = null;

        if ($auth) {
            if (method_exists($auth, 'isAuthenticated')) $authenticated = (bool)$auth->isAuthenticated();
            elseif (method_exists($auth, 'check'))       $authenticated = (bool)$auth->check();

            if (method_exists($auth, 'currentUser'))     $user = $auth->currentUser();
            elseif (isset($_SESSION['user']))            $user = $_SESSION['user'];
        } else {
            $authenticated = !empty($_SESSION['user_id'] ?? null);
        }

        return $this->json($response, [
            'ok' => true,
            'authenticated' => $authenticated,
            'user' => $user,
        ]);
    }

    /** POST /auth/login  -> JSON */
    public function login(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody() ?? [];
        // Delegação para legado se existir
        foreach (['login','postLogin','doLogin'] as $m) {
            if ($this->legacy && method_exists($this->legacy, $m)) {
                $payload = $this->legacy->{$m}($body);
                return $this->json($response, $payload);
            }
        }

        // Fallback
        $auth = class_exists(Authorization::class) ? new Authorization() : null;
        $ok = false;
        $msg = 'Credenciais inválidas';

        if ($auth) {
            // tente cobrir assinaturas comuns
            if (method_exists($auth, 'attempt')) {
                $ok = (bool)$auth->attempt($body['username'] ?? null, $body['password'] ?? null);
            } elseif (method_exists($auth, 'login')) {
                $ok = (bool)$auth->login($body);
            }
        }
        return $this->json($response, ['ok' => $ok, 'message' => $ok ? 'OK' : $msg]);
    }

    /** POST /auth/logout -> JSON */
    public function logout(Request $request, Response $response): Response
    {
        foreach (['logout','postLogout','doLogout'] as $m) {
            if ($this->legacy && method_exists($this->legacy, $m)) {
                $payload = $this->legacy->{$m}();
                return $this->json($response, $payload);
            }
        }

        if (class_exists(Authorization::class)) {
            $auth = new Authorization();
            if (method_exists($auth, 'logout')) $auth->logout();
            unset($_SESSION['user'], $_SESSION['user_id']);
        } else {
            session_destroy();
        }

        return $this->json($response, ['ok' => true]);
    }

    /**
     * GET /auth/prompt -> HTML do popup autenticador vindo do index.tpl
     * Caso a sua AuthUiController já gere esse HTML, a gente delega.
     */
    public function prompt(Request $request, Response $response): Response
    {
        foreach (['prompt','getPrompt','authenticator'] as $m) {
            if ($this->legacy && method_exists($this->legacy, $m)) {
                $html = $this->legacy->{$m}();
                return $this->html($response, (string)$html);
            }
        }

        // Fallback: usa o bloco EXTRA_BLOCK_AUTHENTICATOR do index.tpl
        $tpl = new View('index');
        $html = $tpl->getContent([], 'EXTRA_BLOCK_AUTHENTICATOR');

        return $this->html($response, $html);
    }

    // ----------------- helpers -----------------
    private function json(Response $res, $payload, int $status = 200): Response
    {
        $res = $res->withHeader('Content-Type', 'application/json; charset=UTF-8')
                   ->withStatus($status);
        $res->getBody()->write(json_encode($payload));
        return $res;
    }

    private function html(Response $res, string $html, int $status = 200): Response
    {
        $res = $res->withHeader('Content-Type', 'text/html; charset=UTF-8')
                   ->withStatus($status);
        $res->getBody()->write($html);
        return $res;
    }
}
