<?php

use App\View\View;
use App\Legacy\Table;
use App\Legacy\Notifier;
use App\Support\Version;

require "./inc/config.inc.php";

if (!isset($_POST['action'])) {

    $tplIndex = new View("index");

    $content = [
        "version" => Version::get(),
        "title" => 'Hortifruti - Garçom',
        "module" => 'waiter',
        'manifest' => 'waiter_manifest.json'
    ];

    $tplIndex->Show($content, "BLOCK_PAGE");

    exit();
}

$GLOBALS['authorized_skip'] = true;
require "./inc/authorization.php";

function Unauthorized() {

    header('HTTP/1.0 401 Unauthorized');
}

switch($_POST['action']) {

	case "load":

		Send(Table::LoadWaiterTable("waiter_table"));
    break;

    default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);
    break;
}