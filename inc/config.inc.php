<?php

// if (true == true) {

//     ini_set('display_errors', 1);
//     ini_set('display_startup_errors', 1);
//     error_reporting(E_ALL);
// }

// 1. Carrega o autoloader do Composer (e o seu loader de .env)
require_once __DIR__ . '/../vendor/autoload.php';
loadEnv(__DIR__ . '/../.env');

// 2. Namespace da sua classe de Auth
use database\Notifier;
use App\Auth\Authorization;

// 3. Defina aqui as páginas públicas (sem autenticação)
$publicPages = [
    'index.php',
    'login.php',
    'backend.php',
    'message.php'
    // adicione outras rotas públicas, se houver
];

// 4. Verifica se a rota atual está na whitelist
$currentPage = basename($_SERVER['SCRIPT_NAME']);

if (! in_array($currentPage, $publicPages, true)) {
    Authorization::requireAuth();
}

// Detecta o ambiente
$env = getenv('APP_ENV') ?: 'production';

// Configurações comuns
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../storage/logs/php-error.log');

if ($env === 'production') {
    // Produção: não exibe nada na tela
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    // Reporta todos exceto notices e deprecated (opcional)
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
} else {
    // Desenvolvimento: exibe tudo
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

/**
 * Carrega um .env file sem dependências externas.
 */
function loadEnv(string $path): void {

    if (! file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // ignora comentários e linhas sem =
        if (strpos(trim($line), '#') === 0 || ! strpos($line, '=')) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);

        $name  = trim($name);
        $value = trim($value);

        // remove aspas se existirem
        $value = trim($value, "\"'");
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

require __DIR__ . "/version.php";

//Overload on operator new
// function __autoload($class) {
// }
spl_autoload_register(function ($className) {
// function AutoloadFunction ($className) {

    $className = ltrim($className, '\\');
    $fileName  = __DIR__ . '/../'; //'./';
    $namespace = '';

    if ($lastNsPos = strripos($className, '\\')) {

        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  .= str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    // $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    $fileName .= $className . '.php';

	if (file_exists($fileName)) {

		require_once $fileName;

	} else {

        // die ("file not found $fileName.");
        // die ($_SERVER['DOCUMENT_ROOT']);
        Notifier::Add("Class not found $fileName", Notifier::NOTIFIER_ERROR);
        Send(null);
	}
});

// spl_autoload_register("AutoloadFunction");

function Send($data) {

    global $version;

    // switch($message_type) {

    //     case "info":

    //         $message_type = 0;
    //         break;

    //     case "error":

    //         $message_type = 1;
    //         break;

    //     case "done":

    //         $message_type = 2;
    //         break;
    // }

    echo json_encode(
        array(
            "data"=> $data,
            "messages" => Notifier::getMessages(),
            "version" => $version
        )
    );

    exit();
}

function FormatTel($tel) {

    switch (strlen($tel)) {

        case 8:
            $tel = preg_replace('~(\d{4})(\d{4})~', '$1-$2', $tel);
            break;

        case 9:
            $tel = preg_replace('~(\d{1})(\d{4})(\d{4})~', '$1 $2-$3', $tel);
            break;

        case 10:
            $tel = preg_replace('~(\d{2})(\d{4})(\d{4})~', '($1) $2-$3', $tel);
            break;

        case 11:
            $tel = preg_replace('~(\d{2})(\d{1})(\d{4})(\d{4})~', '($1) $2 $3-$4', $tel);
            break;

        case 12:
            $tel = preg_replace('~(\d{2})(\d{2})(\d{4})(\d{4})~', '+$1 ($2) $3-$4', $tel);
            break;

        case 13:
            $tel = preg_replace('~(\d{2})(\d{2})(\d{1})(\d{4})(\d{4})~', '+$1 ($2) $3 $4-$5', $tel);
            break;
    }

    return $tel;
}

function FormatCEP($cep) {

    return preg_replace('~(\d{5})(\d{3})~', '$1-$2', $cep);
}

function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array, SORT_STRING);
            break;
            case SORT_DESC:
                arsort($sortable_array, SORT_STRING);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}