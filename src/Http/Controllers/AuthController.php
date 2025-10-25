<?php
namespace App\Http\Controllers;

use App\Http\Response;
use App\Auth\AuthService;
use \App\Legacy\ControlAccess;

final class AuthController
{
    public function login(): string {

        // Aceita senha de 'senha' ou 'pass' (legado)
        $id_entidade = (int)($_POST['id_entidade'] ?? 0);
        $senha       = (string)($_POST['senha'] ?? '');

        if ($id_entidade <= 0 || $senha === '') {
            return Response::json(
                ['error' => 'id_entidade e senha são obrigatórios'],
                422
            );
        }

        // exige acesso de servidor (ajuste se o módulo for outro)
        ControlAccess::login(
            $id_entidade,
            $senha,
            ControlAccess::CA_SERVIDOR,
            true
        );

        // Se chegou aqui, sessão PHP está ativa e token foi gerado
        return Response::json(['ok' => true]);
    }

    public function logout(): string {

        (new AuthService())->logout();
        return Response::json(['ok' => true]);
    }

    public function status(): string {

        if (session_status() !== \PHP_SESSION_ACTIVE) @session_start();

        $auth = isset($_SESSION['auth']['id_entidade']);
        $user = $auth ? ['id_entidade' => $_SESSION['auth']['id_entidade']] : null;
        return Response::json(['authenticated' => $auth, 'user' => $user]);
    }
}
