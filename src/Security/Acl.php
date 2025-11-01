<?php
namespace App\Security;

final class Acl
{
    // Módulos “canônicos” do ERP
    public const MOD_BACKEND      = 'backend';
    public const MOD_ORDERS       = 'orders';
    public const MOD_PRODUCTS     = 'products';
    public const MOD_CLIENTS      = 'clients';
    public const MOD_REPORTS      = 'reports';
    public const MOD_PDV          = 'pdv';
    public const MOD_WAITER       = 'waiter';
    public const MOD_FINANCE_AP   = 'finance_ap';
    public const MOD_FINANCE_AR   = 'finance_ar';
    public const MOD_SUPPLIERS    = 'suppliers';
    public const MOD_COLLABORATORS= 'collaborators';
    public const MOD_PRICING      = 'pricing';
    public const MOD_STOCK        = 'stock';

    public const ACTION_VIEW = 'view';
    public const ACTION_EDIT = 'edit';

    /** Módulos reconhecidos no sistema (útil para saneamento/validação) */
    public static function modules(): array
    {
        return [
            self::MOD_BACKEND, self::MOD_ORDERS, self::MOD_PRODUCTS, self::MOD_CLIENTS,
            self::MOD_REPORTS, self::MOD_PDV, self::MOD_WAITER, self::MOD_FINANCE_AP,
            self::MOD_FINANCE_AR, self::MOD_SUPPLIERS, self::MOD_COLLABORATORS,
            self::MOD_PRICING, self::MOD_STOCK
        ];
    }
}
