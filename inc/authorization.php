<?php
/**
 * Compat para o legado: mantém o require 'inc/authorization.php',
 * mas delega a autenticação para App\Auth\Authorization.
 */

use App\Auth\Authorization;

$script = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
if (!isset($publicPages) || !is_array($publicPages)) {
    $publicPages = ['index.php','home.php','login.php','auth.php'];
}
$isPublic = in_array($script, $publicPages, true);
if ($isPublic) {
    return;
}

$auth = class_exists(Authorization::class) ? new Authorization() : null;

// Ajuste estes métodos conforme a sua classe:
$authenticated = false;
if ($auth && method_exists($auth, 'isAuthenticated')) {
    $authenticated = (bool) $auth->isAuthenticated();
}

if (!$authenticated) {
    if ($auth && method_exists($auth, 'requireLogin')) {
        $auth->requireLogin(); // ex.: redirect para /login
    } else {
        http_response_code(401);
        header('Location: /login');
    }
    exit;
}
