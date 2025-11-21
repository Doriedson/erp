<?php

declare(strict_types=1);

namespace App\View;

use RuntimeException;

/**
 * Motor de template por blocos:
 *  - Blocos: <!-- BEGIN NOME --> ... <!-- END NOME -->
 *  - Placeholders: {chave}
 *  - Convenção: EXTRA_* para trechos “parciais” (linhas, popups, mensagens, etc.)
 *  - ALL: arquivo inteiro (sem comentários)
 */
final class View
{
    private string $templateRaw = '';
    private array  $blocks      = [];   // [nome => htmlDoBlocoSemComentarios]
    private const  REG_NAME     = '([[:alnum:]_]+)';

    /**
     * @param string $template Nome do arquivo em /templates (sem .tpl), ex.: 'index'
     */
    public function __construct(string $template)
    {
        $base = \dirname(__DIR__, 2); // src/View -> src -> raiz
        $file = $base . '/templates/' . $template . '.tpl';

        if (!is_file($file)) {
            throw new RuntimeException("Template file not found: {$file}");
        }

        $raw = (string) file_get_contents($file);

        if ($raw === '') {
            throw new RuntimeException("Template file is empty: {$file}");
        }

        $this->templateRaw = $raw;

        // 1) Guarda ALL (arquivo sem comentários)
        $this->blocks['ALL'] = $this->stripComments($this->templateRaw);

        // 2) Extrai todos os blocos (inclusive EXTRA_*)
        // Usa backreference para garantir que BEGIN/END tem o mesmo nome
        $re = '/<!--\s*BEGIN\s+' . self::REG_NAME . '\s*-->\s*(.*?)\s*<!--\s*END\s+\1\s*-->/s';

        if (preg_match_all($re, $this->templateRaw, $matches, PREG_SET_ORDER)) {

            foreach ($matches as $m) {
                $name    = $m[1];
                $content = $m[2];

                // remove o HTML dos sub-blocos internos (para evitar duplicação)
                // Nota: os sub-blocos continuam disponíveis em $this->blocks
                $contentSansInner = preg_replace($re, '', $content) ?? $content;

                // remove COMENTÁRIOS do HTML do bloco
                $this->blocks[$name] = $this->stripComments($contentSansInner);
            }
        }
    }

    /**
     * Substitui {chave} -> valor no HTML do bloco informado.
     * Se o bloco não existir, retorna string vazia.
     */
    public function getContent(array $data, string $block): string
    {
        $html = $this->blocks[$block] ?? '';
        if ($html === '') {
            return '';
        }

		// 🔒 Blindagem: se estamos renderizando um bloco "normal",
		// remova qualquer bloco EXTRA_* que tenha ficado no HTML
		if (strpos($block, 'EXTRA_') !== 0) {
			$html = (string) preg_replace(
				'/<!--\s*BEGIN\s+EXTRA_[A-Z0-9_]+\s*-->.*?<!--\s*END\s+EXTRA_[A-Z0-9_]+\s*-->/si',
				'',
				$html
			);
		}

        if (!$data) {
            return $html;
        }

        // Monta mapa de substituição
        $map = [];
        foreach ($data as $key => $value) {
            $map['{' . $key . '}'] = (string) $value;
        }

        return strtr($html, $map);
    }

    /** Atalho para imprimir um bloco */
    public function Show(array $data, string $block): void
    {
        echo $this->getContent($data, $block);
    }

    /** Retorna true se o bloco existir */
    public function has(string $block): bool
    {
        return array_key_exists($block, $this->blocks);
    }

    /**
     * Helper para repetidores: renderiza $block N vezes e concatena.
     * Ex.: View('login')->list('EXTRA_BLOCK_COLLABORATOR', $linhas)
     */
    public function list(string $block, array $rows): string
    {
        if (!$rows) {
            return '';
        }
        $out = '';
        foreach ($rows as $row) {
            $out .= $this->getContent($row, $block);
        }
        return $out;
    }

    /**
     * Retorna o HTML completo (ALL) com substituição.
     * Útil se quiser usar o arquivo inteiro como base.
     */
    public function all(array $data = []): string
    {
        return $this->getContent($data, 'ALL');
    }

    // ----------------- internals -----------------

    /** Remove comentários HTML <!-- ... --> */
    private function stripComments(string $html): string
    {
        return (string) preg_replace('/<!--.*?-->/s', '', $html);
    }
}
