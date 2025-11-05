<?php

namespace App\Auth;

// inc/authorization.php
// Requer autoload do Composer (ou faça require manual do JWT.php)
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Authorization
{
    /** Lê o header Authorization de qualquer SAPI */
    public static function getAuthorizationHeader(): ?string
    {
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['HTTP_AUTHORIZATION']);
        }
        if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            // Ajuste de casing no array de headers
            $headers = array_combine(
                array_map('ucwords', array_keys($headers)),
                array_values($headers)
            );
            if (isset($headers['Authorization'])) {
                return trim($headers['Authorization']);
            }
        }
        return null;
    }

    /** Extrai o token Bearer do header */
    public static function getBearerToken(): ?string
    {
        $header = self::getAuthorizationHeader();
        if (!$header) {
            return null;
        }
        if (preg_match('/Bearer\s+(\S+)/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /** Verifica e decodifica o JWT, ou retorna 401 */
    public static function requireAuth(): object
    {
        header('Content-Type: application/json; charset=utf-8');

        $token = self::getBearerToken();

        if (!$token) {
            self::sendUnauthorized('Token não fornecido');
        }

        try {
            $secret = getenv('JWT_SECRET');
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));

        } catch (\Exception $e) {

            self::sendUnauthorized('Token inválido ou expirado');
        }

        // Checa expiração manual se quiser (o JWT::decode já faz isso se houver 'exp')
        if (isset($decoded->exp) && $decoded->exp < time()) {

            self::sendUnauthorized('Token expirado');
        }

        // Opcional: você pode colocar o usuário em $_SERVER ou em uma classe Context
        $_SERVER['user_id'] = $decoded->sub;

        return $decoded;
    }

    protected static function sendUnauthorized(string $msg): void
    {
        // header('HTTP/1.1 410 GONE');
        // http_response_code(410);
        http_response_code(401);
        echo json_encode(['error' => $msg]);
        exit;
    }
}