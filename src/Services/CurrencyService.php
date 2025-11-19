<?php

declare(strict_types=1);

namespace DragonStone\Services;

final class CurrencyService
{
    private const SESSION_KEY = 'active_currency';
    private const BASE_CURRENCY = 'USD';

    /**
     * Base currency exchange rates (1 USD => rate).
     *
     * @var array<string,array{label:string,symbol:string,rate:float}>
     */
    private const SUPPORTED = [
        'USD' => ['label' => 'US Dollar', 'symbol' => '$', 'rate' => 1.00],
        'EUR' => ['label' => 'Euro', 'symbol' => '€', 'rate' => 0.92],
        'GBP' => ['label' => 'British Pound', 'symbol' => '£', 'rate' => 0.79],
        'ZAR' => ['label' => 'South African Rand', 'symbol' => 'R', 'rate' => 18.40],
    ];

    public static function available(): array
    {
        return self::SUPPORTED;
    }

    public static function getActiveCurrency(): string
    {
        $code = $_SESSION[self::SESSION_KEY] ?? self::BASE_CURRENCY;
        if (!isset(self::SUPPORTED[$code])) {
            $code = self::BASE_CURRENCY;
        }

        $_SESSION[self::SESSION_KEY] = $code;

        return $code;
    }

    public static function setActiveCurrency(?string $code): string
    {
        $code = strtoupper((string)$code);
        if (!isset(self::SUPPORTED[$code])) {
            $code = self::BASE_CURRENCY;
        }

        $_SESSION[self::SESSION_KEY] = $code;

        return $code;
    }

    public static function baseCurrency(): string
    {
        return self::BASE_CURRENCY;
    }

    public static function rate(string $currency): float
    {
        $currency = strtoupper($currency);
        return self::SUPPORTED[$currency]['rate'] ?? 1.0;
    }

    public static function convertFromBase(float $amount, ?string $currency = null): float
    {
        $currency = $currency ? strtoupper($currency) : self::getActiveCurrency();
        $rate = self::rate($currency);

        return round($amount * $rate, 2);
    }

    public static function convertToBase(float $amount, ?string $currency = null): float
    {
        $currency = $currency ? strtoupper($currency) : self::getActiveCurrency();
        $rate = self::rate($currency);
        if ($rate === 0.0) {
            return $amount;
        }

        return round($amount / $rate, 2);
    }

    public static function format(float $amount, ?string $currency = null): string
    {
        $currency = $currency ? strtoupper($currency) : self::getActiveCurrency();

        if (!class_exists('\NumberFormatter')) {
            return sprintf('%s %.2f', $currency, $amount);
        }

        $formatter = numfmt_create('en_US', \NumberFormatter::CURRENCY);
        if ($formatter === false) {
            return sprintf('%s %.2f', $currency, $amount);
        }

        $formatted = numfmt_format_currency($formatter, $amount, $currency);

        return $formatted !== false ? $formatted : sprintf('%s %.2f', $currency, $amount);
    }
}
