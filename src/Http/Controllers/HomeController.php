<?php
namespace App\Http\Controllers;

use App\View\View;
use App\Modules\Registry;
use App\Support\Version;
use App\Database\Connection;
use Throwable;

final class HomeController
{

    public function indexBackend(): string { return $this->render('backend'); }
    public function indexWaiter(): string  { return $this->render('waiter'); } // “waiter” = garçom

    private function render(string $module): string
    {
        header('Content-Type: text/html; charset=utf-8');

        $pdo = Connection::pdo();

        try {

            $empresa = (string)($pdo->query("SELECT empresa FROM tab_empresa LIMIT 1")->fetchColumn() ?: 'Empresa');

        } catch (Throwable $e) {
            // log opcional
        }

        // Carrega o TPL do módulo: backend_index / garcom_index (BLOCK_PAGE)
        $tplModule = new View($module === 'backend' ? 'backend_index' : 'garcom_index');

        $moduleHtml = $tplModule->getContent(['empresa' => $empresa], 'BLOCK_PAGE');

        // Index shell

        $tplIndex = new View('index');

        $tplIndex->Show([
            'version'  => Version::get(),
            'date'     => date('Y-m-d'),
            'title'    => 'Retaguarda',
            'module'   => $moduleHtml,
            'manifest' => 'backend_manifest.json',
        ], 'BLOCK_PAGE');

        return '';
    }
}
