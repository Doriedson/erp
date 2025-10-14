<?php
namespace App\Http\Controllers;

use App\Auth\AuthService;

final class AuthController {
    private AuthService $auth;
    public function __construct() { $this->auth = new AuthService(); }

    // POST /auth/login
    // Body aceito:
    //   - form-urlencoded: id_entidade=...&senha=...
    //   - JSON: {"id_entidade": 123, "senha": "..." }
    public function login(): array {
        $ct = $_SERVER['CONTENT_TYPE'] ?? '';
        $data = str_contains($ct, 'application/json')
              ? (json_decode(file_get_contents('php://input'), true) ?: [])
              : $_POST;

        $id_entidade = isset($data['id_entidade']) ? (int)$data['id_entidade'] : 0;
        $senha       = (string)($data['senha'] ?? '');

        if ($id_entidade <= 0 || $senha === '') {
            http_response_code(400);
            return ['error' => 'parâmetros inválidos (id_entidade e senha são obrigatórios)'];
        }

        if (!$this->auth->login($id_entidade, $senha)) {
            http_response_code(401);
            return ['error' => 'credenciais inválidas'];
        }

        // Se veio de formulário HTML, redireciona para o painel atual
        if (!str_contains($ct, 'application/json')) {
            header('Location: /admin'); // mantenha seu fluxo/template atual
            exit;
        }

        return ['ok' => true, 'user' => ['id_entidade' => $id_entidade]];
    }

    // POST /auth/logout
    public function logout(): array {
        $this->auth->logout();
        if (($_SERVER['HTTP_ACCEPT'] ?? '') !== 'application/json') {
            header('Location: /login'); exit;
        }
        return ['ok' => true];
    }
}
