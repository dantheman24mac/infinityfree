<?php

declare(strict_types=1);

namespace DragonStone\Services;

final class CarbonCalculator
{
    private const TREE_ABSORPTION_KG_PER_DAY = 0.059; // 21.5 kg per year
    private const CAR_KG_PER_KM = 0.192; // avg passenger vehicle

    public static function perUnit(array $product): float
    {
        return isset($product['carbon_footprint_kg']) ? (float)$product['carbon_footprint_kg'] : 0.0;
    }

    public static function forItem(array $item): float
    {
        $perUnit = self::perUnit($item['product'] ?? $item);
        $quantity = (int)($item['quantity'] ?? 1);

        return round($perUnit * $quantity, 3);
    }

    /**
     * @param array<int,array<string,mixed>> $items
     */
    public static function forItems(array $items): float
    {
        return round(array_reduce($items, static function (float $carry, array $item): float {
            return $carry + self::forItem($item);
        }, 0.0), 3);
    }

    public static function treeOffsetDays(float $kg): float
    {
        if ($kg <= 0) {
            return 0.0;
        }

        return round($kg / self::TREE_ABSORPTION_KG_PER_DAY, 1);
    }

    public static function commuteKilometers(float $kg): float
    {
        if ($kg <= 0) {
            return 0.0;
        }

        return round($kg / self::CAR_KG_PER_KM, 1);
    }
}
