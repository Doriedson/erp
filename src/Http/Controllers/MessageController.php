<?php
namespace App\Http\Controllers;

use App\Http\Response;
use App\View\View;

final class MessageController
{
    /**
     * Retorna os templates de mensagens/popup usados no frontend.
     * Mantém as mesmas chaves que o legado entregava para o JS.
     */
    public function load(): string
    {

		$tplIndex = new View("index");

        $message_info = $tplIndex->getContent([], "EXTRA_BLOCK_MESSAGE_INFO");

        $message_error = $tplIndex->getContent([], "EXTRA_BLOCK_MESSAGE_ERROR");

        $message_done = $tplIndex->getContent([], "EXTRA_BLOCK_MESSAGE_DONE");

        $message_alert = $tplIndex->getContent([], "EXTRA_BLOCK_MESSAGE_ALERT");

        $popup = $tplIndex->getContent([], "EXTRA_BLOCK_POPUP");

        $messagebox = $tplIndex->getContent([], "EXTRA_BLOCK_MESSAGEBOX");

        // contrato esperado pelo front: chaves na raiz
        $payload = [
            'message_info'  => $message_info,
            'message_error' => $message_error,
            'message_done'  => $message_done,
            'message_alert' => $message_alert,
            'popup'         => $popup,
            'messagebox'    => $messagebox,
        ];

        return Response::json($payload);
    }
}