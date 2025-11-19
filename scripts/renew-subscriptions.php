#!/usr/bin/env php
<?php

declare(strict_types=1);

use DragonStone\Services\SubscriptionService;

$basePath = realpath(__DIR__ . '/..');
if ($basePath === false) {
    fwrite(STDERR, "Unable to determine project path.\n");
    exit(1);
}

require $basePath . '/vendor/autoload.php';
require $basePath . '/config/database.php';
require $basePath . '/src/helpers.php';

loadEnv($basePath);

$pdo = databaseConnection();

$results = SubscriptionService::processDueRenewals($pdo);

if (empty($results)) {
    fwrite(STDOUT, "No subscriptions required renewal today.\n");
    exit(0);
}

$successCount = 0;
foreach ($results as $result) {
    if (!empty($result['error'])) {
        fwrite(STDERR, sprintf(
            "Subscription %d failed: %s\n",
            $result['subscription_id'] ?? 0,
            $result['error']
        ));
        continue;
    }

    $successCount++;
    fwrite(STDOUT, sprintf(
        "Processed order %s for customer %s %s\n",
        $result['order_reference'] ?? 'N/A',
        $result['first_name'] ?? '',
        $result['last_name'] ?? ''
    ));
}

fwrite(STDOUT, sprintf("Completed %d renewals.\n", $successCount));
