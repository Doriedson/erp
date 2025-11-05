<?php
namespace App\Support;

final class DotenvLoader {
    public static function boot(string $root): void {
        if (file_exists($root.'/.env')) {
            $dotenv = \Dotenv\Dotenv::createImmutable($root);
            $dotenv->safeLoad();
        }
        date_default_timezone_set('America/Sao_Paulo');
    }
}
