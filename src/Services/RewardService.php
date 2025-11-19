<?php

declare(strict_types=1);

namespace DragonStone\Services;

final class RewardService
{
    private const POINTS_PER_BASE_CURRENCY = 10; // 10 EcoPoints = 1 USD equivalent

    public static function currencyValue(int $points): float
    {
        if ($points <= 0) {
            return 0.0;
        }

        return round($points / self::POINTS_PER_BASE_CURRENCY, 2);
    }

    public static function pointsForValue(float $amount): int
    {
        if ($amount <= 0) {
            return 0;
        }

        return (int)floor($amount * self::POINTS_PER_BASE_CURRENCY);
    }

    public static function clampRedeemablePoints(int $requestedPoints, int $availablePoints, float $orderSubtotal): int
    {
        $requested = max(0, $requestedPoints);
        $available = max(0, $availablePoints);
        $maxBySubtotal = self::pointsForValue($orderSubtotal);

        return min($requested, $available, $maxBySubtotal);
    }
}
