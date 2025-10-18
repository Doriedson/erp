<?php
namespace App\Http\Controllers;

use App\View\View;
use App\Legacy\Company;        // sua Company migrada para App\Legacy
use App\Modules\Registry;

final class HomeController
{
    public function index(): string
    {
        // 1) versão p/ cache-buster
        $version = getenv('APP_VERSION') ?: date('YmdHis');

        // 2) empresa (Company legado)
        $empresa = 'Nome da Empresa';
        try {
            $company = new Company();
            if (method_exists($company, 'Read')) { $company->Read(); }
            if (method_exists($company, 'getResult') && ($row = $company->getResult())) {
                $empresa = $row['empresa'] ?? $empresa;
            }
        } catch (\Throwable $e) {
            // opcional: log
        }

        // 3) descobrir módulo
        session_start(); // garantir sessão
        $override = isset($_GET['module']) ? strtolower(trim($_GET['module'])) : null;

        // você já guarda colaborador logado em sessão; ajuste o índice conforme seu Auth atual
        $colab = $_SESSION['auth']['colaborador'] ?? null;
        $acesso = $colab['acesso'] ?? null;

        $module = $override ?: Registry::moduleFromAccess($acesso);
        $cfg = Registry::get($module) ?? Registry::get(Registry::default());

        // 4) carregar o bloco do módulo (exatamente como você fazia)
        $tplModule = new View($cfg['tpl']); // ex.: backend_index → templates/backend_index.tpl
        $moduleHtml = $tplModule->getContent(['module' => $module], $cfg['block']);

        // 5) renderizar index.tpl injetando o módulo
        $tplIndex = new View('index');
        $tplIndex->Show([
            'version'  => $version,
            'date'     => date('Y-m-d'),
            'title'    => 'Retaguarda',
            'module'   => $moduleHtml,
            'manifest' => 'backend_manifest.json',
            'empresa'  => $empresa,
        ], 'BLOCK_PAGE'); // mantenha o mesmo bloco que seu index.tpl espera

        return '';
    }
}
