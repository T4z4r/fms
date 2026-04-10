<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public static function getCompanySettings(): array
    {
        return [
            'company_name' => static::get('company_name', ''),
            'company_currency' => static::get('company_currency', 'USD'),
            'company_date_format' => static::get('company_date_format', 'Y-m-d'),
            'company_fiscal_year_start' => static::get('company_fiscal_year_start', 'January'),
        ];
    }

    public static function getCurrencySymbol(?string $currency = null): string
    {
        $currency = $currency ?? static::get('company_currency', 'USD');

        return match ($currency) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'JPY' => '¥',
            'CAD' => 'C$',
            'AUD' => 'A$',
            'CHF' => 'CHF',
            'CNY' => '¥',
            default => '$',
        };
    }
}
