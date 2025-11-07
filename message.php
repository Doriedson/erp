<?php

use App\View\View;

require "inc/config.inc.php";

$tplIndex = new View("index");

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