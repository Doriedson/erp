<?php
namespace App\Modules;

final class Registry
{
    /** Mapeia o “nome lógico” do módulo → arquivo .tpl e bloco */
    private const MAP = [
        'backend' => ['tpl' => 'backend_index', 'block' => 'BLOCK_PAGE'],
        'garcom'  => ['tpl' => 'garcom_index',  'block' => 'BLOCK_PAGE'],
        'pdv'     => ['tpl' => 'pdv_index',     'block' => 'BLOCK_PAGE'],
    ];

    /** Retorna config do módulo, ou null se não existir */
    public static function get(string $module): ?array
    {
        return self::MAP[$module] ?? null;
    }

    /** Nome padrão se nada for informado/permitido */
    public static function default(): string
    {
        return 'backend';
    }

    /** Converte “acesso” do colaborador (string/JSON) para um módulo padrão */
    public static function moduleFromAccess(?string $acesso): string
    {
        // Regra simples: se incluir “garcom” → garçom; se incluir “pdv” → pdv; senão backend.
        $a = strtolower((string)$acesso);
        if (strpos($a, 'garcom') !== false) return 'garcom';
        if (strpos($a, 'pdv') !== false)    return 'pdv';
        return self::default();
    }
}
