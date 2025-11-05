<?php
namespace App\Support;

final class Version
{
    private static ?string $cached = '0.77';

    public static function get(): string
    {
		  return self::$cached;
    }
}
