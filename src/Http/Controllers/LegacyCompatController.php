<?php

// src/Http/Controllers/LegacyCompatController.php
namespace App\Http\Controllers;

final class LegacyCompatController
{

    /** backend.php legado (menus, etc.) */
    public function backend(): string { return $this->includeLegacy(APP_ROOT . '/backend.php'); }

    /** waiter.php legado */
    public function waiter(): string  { return $this->includeLegacy(APP_ROOT . '/waiter.php'); }

    /** pdv.php legado (se existir) */
    public function pdv(): string     { return $this->includeLegacy(APP_ROOT . '/pdv.php'); }

    private function includeLegacy(string $absolutePath): string
    {
        if (!is_file($absolutePath)) {

            http_response_code(404);
            header('Content-Type: text/plain; charset=utf-8');
            return "legacy not found: {$absolutePath}";

        }

        try {
            // muitos scripts legados dependem de estar na raiz
            chdir(APP_ROOT);

            // Capta a saída (HTML) e devolve como text/html
            ob_start();
            include $absolutePath;
            $out = ob_get_clean();

            header('Content-Type: text/html; charset=utf-8');
            return is_string($out) ? $out : '';

        } catch (\Throwable $e) {

            http_response_code(500);
            header('Content-Type: application/json; charset=utf-8');
            return json_encode([
                'error'   => 'legacy-fatal',
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
        }
    }
}
