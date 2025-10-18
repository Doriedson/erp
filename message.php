<?php

use App\View\View;
use database\Notifier;

require "inc/config.inc.php";

switch ($_POST['action']) {

	case "load":

        $tplIndex = new View("templates/index");

        $data = [
            "message_info" => $tplIndex->getContent([], "EXTRA_BLOCK_MESSAGE_INFO"),
            "message_error" => $tplIndex->getContent([], "EXTRA_BLOCK_MESSAGE_ERROR"),
            "message_done" => $tplIndex->getContent([], "EXTRA_BLOCK_MESSAGE_DONE"),
            "message_alert" => $tplIndex->getContent([], "EXTRA_BLOCK_MESSAGE_ALERT"),
            "popup" => $tplIndex->getContent([], "EXTRA_BLOCK_POPUP"),
            // "authenticator" => $tplIndex->getContent([], "EXTRA_BLOCK_AUTHENTICATOR"),
            "messagebox" => $tplIndex->getContent([], "EXTRA_BLOCK_MESSAGEBOX")
        ];

        Send($data);

        break;

    default:

        Notifier::Add("Requisição inválida!", Notifier::NOTIFIER_ERROR);
        Send(null);

        break;
}