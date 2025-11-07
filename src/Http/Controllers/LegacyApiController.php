<?php
namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Auth\Authorization;

final class LegacyApiController
{
    private string $baseDir;

    public function __construct()
    {
        // /src/Http/Controllers -> sobe 3 níveis até a raiz do projeto
        $this->baseDir = dirname(__DIR__, 3);
    }

    /** Proxy para o legado: /message.php (retorna JSON gerado pelo legado) */
    public function message(Request $request, Response $response): Response
	{
		$cwd = getcwd();
		$oldIncludePath = get_include_path();

		$base = $this->baseDir; // raiz do projeto
		chdir($base);

		// include_path para o legado encontrar inc/* e demais includes relativos
		set_include_path($base . PATH_SEPARATOR . $base . '/inc' . PATH_SEPARATOR . $oldIncludePath);

		// Seeds típicos que o legado espera
		if (!isset($_SERVER['SCRIPT_NAME']))  $_SERVER['SCRIPT_NAME']  = '/message.php';
		if (!isset($_SERVER['PHP_SELF']))     $_SERVER['PHP_SELF']     = '/message.php';
		if (!isset($_SERVER['REQUEST_URI']))  $_SERVER['REQUEST_URI']  = '/message.php';
		if (!isset($_SERVER['DOCUMENT_ROOT'])) $_SERVER['DOCUMENT_ROOT'] = $base;
		if (!defined('BASE_PATH')) define('BASE_PATH', $base);

		// Páginas públicas (inclui message.php)
		$publicPages = ['index.php','home.php','login.php','auth.php','message.php'];

		// Captura saída do legado
		ob_start();
		$payload = null;
		$status  = 200;

		try {
			$file = $base . '/message.php';
			if (is_file($file)) {
				require $file; // deve chamar Send($data) dentro do message.php
			} else {
				// Se o arquivo não existir, devolve JSON padrão
				$payload = ['ok' => false, 'error' => 'message.php not found'];
				echo json_encode($payload);
				$status = 404;
			}
		} catch (\Throwable $e) {
			// Fallback de erro legível em JSON (sem stack em prod)
			$payload = ['ok' => false, 'error' => $e->getMessage()];
			echo json_encode($payload);
			$status = 500;
		}

		$out = ob_get_clean();

		// Restaura ambiente
		set_include_path($oldIncludePath);
		chdir($cwd);

		// Garante JSON
		$res = $response->withHeader('Content-Type', 'application/json; charset=UTF-8')
						->withStatus($status);
		$res->getBody()->write($out);
		return $res;
	}


    /** Status de autenticação para /auth/status (JSON) */
    public function authStatus(Request $request, Response $response): Response
    {
        $auth = class_exists(Authorization::class) ? new Authorization() : null;

        $authenticated = false;
        $user = null;

        if ($auth) {
            // Ajuste estes métodos conforme sua classe real
            if (method_exists($auth, 'isAuthenticated')) {
                $authenticated = (bool) $auth->isAuthenticated();
            } elseif (method_exists($auth, 'check')) {
                $authenticated = (bool) $auth->check();
            }
            if (method_exists($auth, 'currentUser')) {
                $user = $auth->currentUser();
            } elseif (isset($_SESSION['user'])) {
                $user = $_SESSION['user'];
            }
        } else {
            // fallback simples por sessão
            $authenticated = !empty($_SESSION['user_id'] ?? null);
        }

        $payload = [
            'ok' => true,
            'authenticated' => $authenticated,
            'user' => $user,
        ];

        $response->getBody()->write(json_encode($payload));
        return $response->withHeader('Content-Type', 'application/json; charset=UTF-8');
    }
}
