<?php

declare(strict_types=1);

namespace DragonStone\Services;

use DragonStone\Repositories\ProductRepository;
use PDO;

class CartService
{
    private const SESSION_KEY = 'cart_items';

    /**
     * Add an item to the cart stored in session.
     */
    public static function addItem(int $productId, int $quantity = 1): void
    {
        if ($quantity < 1) {
            $quantity = 1;
        }

        $cart = $_SESSION[self::SESSION_KEY] ?? [];

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }

        $_SESSION[self::SESSION_KEY] = $cart;
    }

    /**
     * Update quantity for a given product.
     */
    public static function updateItem(int $productId, int $quantity): void
    {
        $cart = $_SESSION[self::SESSION_KEY] ?? [];
        if (!isset($cart[$productId])) {
            return;
        }

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId]['quantity'] = $quantity;
        }

        $_SESSION[self::SESSION_KEY] = $cart;
    }

    /**
     * Remove an item from the cart.
     */
    public static function removeItem(int $productId): void
    {
        $cart = $_SESSION[self::SESSION_KEY] ?? [];
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $_SESSION[self::SESSION_KEY] = $cart;
        }
    }

    /**
     * Clear the cart completely.
     */
    public static function clear(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
    }

    /**
     * Return cart items with product details.
     *
     * @return array<int,array<string,mixed>>
     */
    public static function detailedItems(PDO $pdo): array
    {
        $cart = $_SESSION[self::SESSION_KEY] ?? [];
        if (empty($cart)) {
            return [];
        }

        $productIds = array_keys($cart);
        $products = ProductRepository::findByIds($pdo, $productIds);

        $items = [];
        foreach ($cart as $productId => $cartItem) {
            if (!isset($products[$productId])) {
                continue;
            }

            $product = $products[$productId];
            $quantity = (int)$cartItem['quantity'];
            $price = (float)$product['price'];
            $subtotal = $quantity * $price;
            $estimatedPoints = $quantity * (int)$product['sustainability_score'];

            $items[] = [
                'product_id' => $productId,
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $price,
                'subtotal' => $subtotal,
                'estimated_points' => $estimatedPoints,
            ];
        }

        return $items;
    }

    /**
     * Calculate the total of all items in the cart.
     */
    public static function total(PDO $pdo): float
    {
        $items = self::detailedItems($pdo);
        return array_reduce($items, static function (float $carry, array $item): float {
            return $carry + (float)$item['subtotal'];
        }, 0.0);
    }

    /**
     * Count total units present in cart.
     */
    public static function itemCount(): int
    {
        $cart = $_SESSION[self::SESSION_KEY] ?? [];
        return array_reduce($cart, static function (int $carry, array $item): int {
            return $carry + (int)$item['quantity'];
        }, 0);
    }

    public static function pointsTotal(PDO $pdo): int
    {
        $items = self::detailedItems($pdo);
        return array_reduce($items, static function (int $carry, array $item): int {
            return $carry + (int)$item['estimated_points'];
        }, 0);
    }
}
