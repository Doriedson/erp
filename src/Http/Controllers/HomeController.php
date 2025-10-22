<?php
namespace App\Http\Controllers;

use App\View\View;
use App\Legacy\Company;        // sua Company migrada para App\Legacy
use App\Modules\Registry;
use App\Support\Version;

final class HomeController
{

    public function indexBackend(): string { return $this->render('backend'); }
    public function indexWaiter(): string  { return $this->render('waiter'); } // “waiter” = garçom

    private function render(string $module): string
    {
        header('Content-Type: text/html; charset=utf-8');

        // Empresa (legado, fica igual)
        $empresa = 'Nome da Empresa';
        try {
            $company = new Company();
            if (method_exists($company, 'Read')) { $company->Read(); }
            if (method_exists($company, 'getResult') && ($row = $company->getResult())) {
                $empresa = $row['empresa'] ?? $empresa;
            }
        } catch (\Throwable $e) {}

        // Carrega o TPL do módulo: backend_index / garcom_index (BLOCK_PAGE)
        $tplModule = new View($module === 'backend' ? 'backend_index' : 'garcom_index');
        $moduleHtml = $tplModule->getContent(['module' => $module], 'BLOCK_PAGE');

        // Index shell

        $tplIndex = new View('index');

        $tplIndex->Show([
            'version'  => Version::get(),
            'date'     => date('Y-m-d'),
            'title'    => 'Retaguarda',
            'module'   => $moduleHtml,
            'manifest' => 'backend_manifest.json',
            'empresa'  => $empresa,
            // IMPORTANTE: o JS atual lê esse data-module
            'data_module_attr' => $module,
        ], 'BLOCK_PAGE');

        return '';
    }
}
