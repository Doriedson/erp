<?php
namespace App\Http\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\View\ViewRenderer;

final class MessageController
{
    public function __construct(private ViewRenderer $view) {}

    public function message(Request $req, Response $res): Response
    {
        // Gera os mesmos blocos do index.tpl
        $tpl = 'index';
        $data = [
            'message_info'  => $this->view->block($tpl, 'EXTRA_BLOCK_MESSAGE_INFO', []),
            'message_error' => $this->view->block($tpl, 'EXTRA_BLOCK_MESSAGE_ERROR', []),
            'message_done'  => $this->view->block($tpl, 'EXTRA_BLOCK_MESSAGE_DONE', []),
            'message_alert' => $this->view->block($tpl, 'EXTRA_BLOCK_MESSAGE_ALERT', []),
            'popup'         => $this->view->block($tpl, 'EXTRA_BLOCK_POPUP', []),
            'messagebox'    => $this->view->block($tpl, 'EXTRA_BLOCK_MESSAGEBOX', []),
        ];

        $res = $res->withHeader('Content-Type', 'application/json; charset=UTF-8');
        $res->getBody()->write(json_encode($data));
        return $res;
    }
}
