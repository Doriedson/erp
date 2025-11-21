<?php
namespace App\View;

use App\View\View as LegacyView;

final class ViewRenderer
{
    private string $templatesPath;

    public function __construct(?string $templatesPath = null)
    {
        // se não vier via container, usa /templates na raiz do projeto
        $this->templatesPath = $templatesPath
            ?? dirname(__DIR__, 2) . '/templates';
    }

    /** Renderiza um .tpl pelo nome e bloco raiz (ex.: BLOCK_PAGE) */
    public function render(string $name, array $data = [], string $rootBlock = 'BLOCK_PAGE'): string
    {
        // seu View carrega por nome de template: new View('index')
        $v = new LegacyView($name);
        return $v->getContent($data, $rootBlock);
    }

    /** Renderiza apenas um bloco extra de um template (EXTRA_...) */
    public function block(string $template, string $block, array $data = []): string
    {
        $v = new LegacyView($template);
        return $v->getContent($data, $block);
    }

    public function basePath(): string
    {
        return $this->templatesPath;
    }
}
