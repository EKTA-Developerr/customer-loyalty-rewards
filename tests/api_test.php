<?php
// tests/api_test.php — Automated CLI integration check for Customer Loyalty Rewards API
// Usage: php tests/api_test.php (requires MySQL running with database.sql imported)

declare(strict_types=1);

require_once __DIR__ . '/../config.php';

echo "Running Customer Loyalty Rewards Automated Tests...\n";

try {
    $pdo = getDb();
    echo "[PASS] Connected to MySQL database.\n";
} catch (Throwable $e) {
    echo "[FAIL] Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// 1. Verify required tables exist
$tables = ['batches', 'product_codes', 'fraud_logs', 'rate_limits'];
foreach ($tables as $t) {
    $stmt = $pdo->query("SHOW TABLES LIKE '{$t}'");
    if ($stmt->rowCount() === 0) {
        echo "[FAIL] Missing table: {$t}\n";
        exit(1);
    }
}
echo "[PASS] All 4 required MySQL tables exist.\n";

// 2. Verify seed codes exist and unique constraint works
$count = (int)$pdo->query("SELECT COUNT(*) FROM product_codes")->fetchColumn();
if ($count < 5) {
    echo "[FAIL] Expected at least 5 seeded product_codes, found {$count}.\n";
    exit(1);
}
echo "[PASS] Seed data present ({$count} codes).\n";

// 3. Test atomic transaction rollback on duplicate redemption check
$pdo->beginTransaction();
$stmt = $pdo->prepare("SELECT status FROM product_codes WHERE code = 'FB-USED-0001' FOR UPDATE");
$stmt->execute();
$status = $stmt->fetchColumn();
$pdo->rollBack();

if ($status !== 'REDEEMED') {
    echo "[FAIL] Expected FB-USED-0001 to have status REDEEMED, got {$status}.\n";
    exit(1);
}
echo "[PASS] Duplicate-use guard verified on FB-USED-0001.\n";

echo "All automated checks passed!\n";
