<?php
namespace App\UI;

use App\View\View;

/**
 * Agregador das UIs de mensagens (modais/toasts/alerts) baseadas nos seus .tpl.
 * Não altera JS/CSS do frontend: só entrega o HTML pronto para injetar no index.tpl.
 */
final class MessageInjector
{
    /**
     * Renderiza todos os blocos necessários dos templates de mensagens e
     * devolve um único string de HTML para inserir no index.tpl.
     */
    public function renderAll(): string
    {
        $html = [];

        // Exemplos de templates; ajuste os nomes/blocks para seus arquivos reais:
        // Se você já tem 1 único message.tpl com múltiplos blocks, basta carregar um só e chamar getContent() para cada block.
        if (is_file(__DIR__ . '/../../templates/message.tpl')) {
            $tpl = new View('message');
            // Ajuste os nomes dos blocks p/ os seus:
            $html[] = $tpl->getContent([], 'BLOCK_MODAL');      // overlay/modal base
            $html[] = $tpl->getContent([], 'BLOCK_TOAST');      // toast container
            $html[] = $tpl->getContent([], 'BLOCK_CONFIRM');    // caixa de confirmação
            $html[] = $tpl->getContent([], 'BLOCK_ALERT');      // alerta genérico
        }

        // Caso você tenha outros TPLs fragmentados:
        // if (is_file(__DIR__ . '/../../templates/message_extra.tpl')) {
        //     $tplX = new View('message_extra');
        //     $html[] = $tplX->getContent([], 'BLOCK_EXTRA');
        // }

        // Concatena em uma única string pronta para ser injetada
        return implode("\n", array_filter($html));
    }
}
