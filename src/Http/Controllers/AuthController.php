<?php
namespace App\Http\Controllers;

use App\Auth\AuthService;
use App\Http\Response;
use Throwable;

final class AuthController
{
    public function login()
    {
        try {
            // 1) Campos obrigatórios
            $id_entidade = isset($_POST['id_entidade']) ? (int)$_POST['id_entidade'] : 0;
            $senha       = isset($_POST['senha']) ? (string)$_POST['senha'] : '';

            if ($id_entidade <= 0 || $senha === '') {
                return Response::json(['error' => 'Parâmetros inválidos'], 422);
            }

            // 2) Autentica
            $auth = new AuthService();
            $ok = $auth->authenticate($id_entidade, $senha);

            if (!$ok) {
                // senha errada, usuário inativo, etc.
                return Response::json(['error' => 'Credenciais inválidas'], 401);
            }

            // 3) Sucesso
            return Response::json([
                'ok'   => true,
                'user' => ['id_entidade' => $auth->userId()],
            ], 200);

        } catch (Throwable $e) {
            // Erro inesperado -> 500 (logar para depuração)
            error_log("[/auth/login] ".$e->getMessage()."\n".$e->getTraceAsString());
            $debug = getenv('APP_DEBUG');
            $msg = ($debug === '1' || $debug === 'true')
                ? ('Erro interno: '.$e->getMessage())
                : 'Erro interno';
            return Response::json(['error' => $msg], 500);
        }
    }

    public function logout()
    {
        (new AuthService())->logout();
        return Response::json(['ok' => true], 200);
    }

    public function status()
    {
        $auth = new AuthService();
        return Response::json([
            'authenticated' => $auth->isAuthenticated(),
            'user' => $auth->isAuthenticated() ? ['id_entidade' => $auth->userId()] : null,
        ], 200);
    }
}
