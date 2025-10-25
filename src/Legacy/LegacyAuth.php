<?php
namespace App\Legacy;

final class LegacyAuth
{
    /**
     * Retorna true se a sessão atual estiver autenticada.
     */
    public static function isAuthorized(): bool
    {
        if (session_status() !== \PHP_SESSION_ACTIVE) {
            @session_start();
        }
        // Evita acoplamento direto: usa o AuthService se existir
        if (class_exists(\App\Auth\AuthService::class)) {
            $auth = new \App\Auth\AuthService();
            return $auth->isAuthenticated();
        }
        // Fallback: aceita convenções antigas, se houver
        return !empty($_SESSION['auth']['id_entidade']);
    }

    /**
     * Exige autenticação para rotas/páginas "page-like" (HTML).
     * Responde 401 e encerra se não autorizado.
     */
    public static function requireAuthForPage(): void
    {
        if (self::isAuthorized()) {
            return;
        }
        http_response_code(401);
        header('Content-Type: text/plain; charset=utf-8');
        echo json_encode(['error' => 'Unauthorized', 'code' => 'AUTH_REQUIRED'], JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Exige autenticação para endpoints API (JSON).
     */
    public static function requireAuthForApi(): void
    {
        if (self::isAuthorized()) {
            return;
        }
        http_response_code(401);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => 'Unauthorized'], JSON_UNESCAPED_SLASHES);
        exit;
    }
}
