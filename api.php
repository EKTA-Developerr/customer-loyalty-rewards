<?php
// api.php — Backend endpoints for Customer Redemption, Tiered Loyalty Bonus, Fraud Prevention & Owner Dashboard

declare(strict_types=1);

require_once __DIR__ . '/config.php';

function getTierInfo(int $scans): array
{
    if ($scans >= 100) {
        return ['tier' => 'Platinum VIP', 'multiplier' => '2x Highest Cashback', 'nextTier' => null, 'scansToNext' => 0, 'progress' => 100];
    }
    if ($scans >= 50) {
        return ['tier' => 'Gold', 'multiplier' => '1.5x Cashback', 'nextTier' => 'Platinum VIP (2x)', 'scansToNext' => 100 - $scans, 'progress' => $scans];
    }
    if ($scans >= 10) {
        return ['tier' => 'Silver', 'multiplier' => '+₹10 Bonus / Box', 'nextTier' => 'Gold (1.5x)', 'scansToNext' => 50 - $scans, 'progress' => $scans];
    }
    return ['tier' => 'Bronze', 'multiplier' => '1x Standard', 'nextTier' => 'Silver (+₹10)', 'scansToNext' => 10 - $scans, 'progress' => $scans];
}

function calculateLoyaltyPayout(int $baseAmount, int $newScanCount): array
{
    if ($newScanCount >= 100) {
        $final = $baseAmount * 2;
        return ['final' => $final, 'bonus' => $final - $baseAmount, 'tier' => 'Platinum VIP'];
    }
    if ($newScanCount >= 50) {
        $final = (int)round($baseAmount * 1.5);
        return ['final' => $final, 'bonus' => $final - $baseAmount, 'tier' => 'Gold'];
    }
    if ($newScanCount >= 10) {
        $final = $baseAmount + 10;
        return ['final' => $final, 'bonus' => 10, 'tier' => 'Silver'];
    }
    return ['final' => $baseAmount, 'bonus' => 0, 'tier' => 'Bronze'];
}

$action = $_GET['action'] ?? '';

try {
    $pdo = getDb();
} catch (Throwable $e) {
    jsonResponse([
        'ok' => false,
        'error' => 'Database connection failed. Check .env and ensure MySQL is running.'
    ], 500);
}

if ($action === 'state') {
    $phone = trim($_GET['phone'] ?? '');

    $codesStmt = $pdo->query("
        SELECT pc.code, b.batch_name AS batch,
               CASE WHEN pc.status = 'REDEEMED' AND pc.final_payout > 0 THEN pc.final_payout ELSE pc.cashback_amount END AS amount,
               pc.status, pc.redeemed_phone, pc.redeemed_upi,
               DATE_FORMAT(pc.redeemed_at, '%d %b, %h:%i %p') AS redeemedAt
        FROM product_codes pc
        JOIN batches b ON b.id = pc.batch_id
        ORDER BY pc.id DESC
    ");
    $codes = $codesStmt->fetchAll();

    $fraudStmt = $pdo->query("
        SELECT code_attempted AS code, reason, phone,
               DATE_FORMAT(created_at, '%h:%i %p') AS time
        FROM fraud_logs
        ORDER BY id DESC
        LIMIT 30
    ");
    $fraudLogs = $fraudStmt->fetchAll();

    $customerClaims = [];
    $walletBalance = 0;
    $scans = 0;

    if ($phone !== '') {
        $custStmt = $pdo->prepare("SELECT total_scans, total_earned FROM customers WHERE phone = ?");
        $custStmt->execute([$phone]);
        $cust = $custStmt->fetch();
        if ($cust) {
            $scans = (int)$cust['total_scans'];
            $walletBalance = (int)$cust['total_earned'];
        }

        $claimStmt = $pdo->prepare("
            SELECT code,
                   CASE WHEN final_payout > 0 THEN final_payout ELSE cashback_amount END AS amount,
                   bonus_amount AS bonus,
                   redeemed_upi AS upi,
                   DATE_FORMAT(redeemed_at, '%d %b, %h:%i %p') AS time
            FROM product_codes
            WHERE status = 'REDEEMED' AND redeemed_phone = ?
            ORDER BY redeemed_at DESC
        ");
        $claimStmt->execute([$phone]);
        $customerClaims = $claimStmt->fetchAll();
    }

    $tierInfo = getTierInfo($scans);

    jsonResponse([
        'ok' => true,
        'codes' => $codes,
        'fraudLogs' => $fraudLogs,
        'customerClaims' => $customerClaims,
        'walletBalance' => $walletBalance,
        'loyalty' => array_merge(['scans' => $scans], $tierInfo)
    ]);
}

if ($action === 'claim' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input') ?: '{}', true);
    $phone = trim($input['phone'] ?? '');
    $upi   = trim($input['upi'] ?? '');
    $code  = strtoupper(trim($input['code'] ?? ''));
    $ip    = getClientIp();
    $rateKey = $ip . '|' . $phone;

    if ($phone === '' || $upi === '' || $code === '') {
        jsonResponse(['ok' => false, 'type' => 'error', 'title' => 'Missing Details', 'message' => 'Phone, UPI ID, and Box Code are required.'], 400);
    }

    // 1. Check Rate Limit
    $rlStmt = $pdo->prepare("SELECT failed_attempts, locked_until FROM rate_limits WHERE identifier = ?");
    $rlStmt->execute([$rateKey]);
    $rl = $rlStmt->fetch();
    if ($rl && $rl['locked_until'] !== null && strtotime($rl['locked_until']) > time()) {
        jsonResponse([
            'ok' => false,
            'type' => 'error',
            'title' => 'Too Many Invalid Attempts (Rate Limited)',
            'message' => 'Account temporarily locked for 15 minutes to prevent code guessing.'
        ], 429);
    }

    // 2. Atomic Transaction with Row Lock
    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare("SELECT id, cashback_amount, status, DATE_FORMAT(redeemed_at, '%d %b, %h:%i %p') AS redeemed_at FROM product_codes WHERE code = ? FOR UPDATE");
        $stmt->execute([$code]);
        $row = $stmt->fetch();

        if (!$row) {
            $pdo->rollBack();

            $logStmt = $pdo->prepare("INSERT INTO fraud_logs (code_attempted, phone, ip_address, reason) VALUES (?, ?, ?, ?)");
            $logStmt->execute([$code, $phone, $ip, 'Invalid / fake code attempt']);

            $attempts = $rl ? ((int)$rl['failed_attempts'] + 1) : 1;
            $lockedUntil = $attempts >= 3 ? date('Y-m-d H:i:s', time() + 900) : null;
            $upRl = $pdo->prepare("
                INSERT INTO rate_limits (identifier, failed_attempts, locked_until)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE failed_attempts = VALUES(failed_attempts), locked_until = VALUES(locked_until)
            ");
            $upRl->execute([$rateKey, $attempts, $lockedUntil]);

            if ($attempts >= 3) {
                jsonResponse([
                    'ok' => false,
                    'type' => 'error',
                    'title' => 'Rate Limit Triggered!',
                    'message' => '3 invalid codes in a row. Locked for 15 minutes to prevent guessing.'
                ], 429);
            }

            jsonResponse([
                'ok' => false,
                'type' => 'error',
                'title' => 'Invalid Box Code',
                'message' => "Code \"{$code}\" was not found. (" . (3 - $attempts) . " attempts left before lock)"
            ], 404);
        }

        if ($row['status'] === 'REDEEMED') {
            $pdo->rollBack();
            $logStmt = $pdo->prepare("INSERT INTO fraud_logs (code_attempted, phone, ip_address, reason) VALUES (?, ?, ?, ?)");
            $logStmt->execute([$code, $phone, $ip, 'Duplicate code reuse blocked']);

            jsonResponse([
                'ok' => false,
                'type' => 'warn',
                'title' => 'Code Already Redeemed!',
                'message' => "Code \"{$code}\" was already claimed ({$row['redeemed_at']}). Each box code works only once."
            ], 409);
        }

        // Fetch customer's current scan count to calculate Loyalty Tier Bonus
        $custStmt = $pdo->prepare("SELECT total_scans FROM customers WHERE phone = ? FOR UPDATE");
        $custStmt->execute([$phone]);
        $custRow = $custStmt->fetch();
        $priorScans = $custRow ? (int)$custRow['total_scans'] : 0;
        $newScanCount = $priorScans + 1;

        $baseAmount = (int)$row['cashback_amount'];
        $payout = calculateLoyaltyPayout($baseAmount, $newScanCount);

        $upd = $pdo->prepare("UPDATE product_codes SET status = 'REDEEMED', bonus_amount = ?, final_payout = ?, redeemed_phone = ?, redeemed_upi = ?, redeemed_at = NOW() WHERE id = ? AND status = 'UNUSED'");
        $upd->execute([$payout['bonus'], $payout['final'], $phone, $upi, $row['id']]);

        $upCust = $pdo->prepare("
            INSERT INTO customers (phone, total_scans, total_earned)
            VALUES (?, 1, ?)
            ON DUPLICATE KEY UPDATE total_scans = total_scans + 1, total_earned = total_earned + VALUES(total_earned)
        ");
        $upCust->execute([$phone, $payout['final']]);

        $resetRl = $pdo->prepare("DELETE FROM rate_limits WHERE identifier = ?");
        $resetRl->execute([$rateKey]);

        $pdo->commit();

        $leveledUp = null;
        $levelUpMessage = null;
        if ($priorScans < 100 && $newScanCount >= 100) {
            $leveledUp = 'Platinum VIP';
            $levelUpMessage = 'Congratulations! You reached Platinum VIP Level! You have unlocked 2x Highest Cashback on every box.';
        } elseif ($priorScans < 50 && $newScanCount >= 50) {
            $leveledUp = 'Gold';
            $levelUpMessage = 'Congratulations! You reached Gold Level! You now earn 1.5x cashback on every box.';
        } elseif ($priorScans < 10 && $newScanCount >= 10) {
            $leveledUp = 'Silver';
            $levelUpMessage = 'Congratulations! You reached Silver Level! You now earn +₹10 extra bonus on every box.';
        }

        $bonusNote = $payout['bonus'] > 0
            ? " (₹{$baseAmount} base + ₹{$payout['bonus']} {$payout['tier']} bonus!)"
            : "";

        jsonResponse([
            'ok' => true,
            'type' => 'success',
            'amount' => $payout['final'],
            'title' => "₹{$payout['final']} Cashback Credited!",
            'message' => "Scan #{$newScanCount} verified for {$upi}{$bonusNote}",
            'leveledUp' => $leveledUp,
            'levelUpMessage' => $levelUpMessage
        ]);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        jsonResponse(['ok' => false, 'type' => 'error', 'title' => 'Server Error', 'message' => 'Could not process claim.'], 500);
    }
}

if ($action === 'set_demo_level' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input') ?: '{}', true);
    $phone = trim($input['phone'] ?? '');
    $scans = max(0, (int)($input['scans'] ?? 0));
    $earned = $scans * 25;

    if ($phone === '') {
        jsonResponse(['ok' => false, 'error' => 'Phone required'], 400);
    }

    $stmt = $pdo->prepare("
        INSERT INTO customers (phone, total_scans, total_earned)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE total_scans = VALUES(total_scans), total_earned = VALUES(total_earned)
    ");
    $stmt->execute([$phone, $scans, $earned]);
    jsonResponse(['ok' => true, 'scans' => $scans]);
}

if ($action === 'generate_batch' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input') ?: '{}', true);
    $batchName = trim($input['batchName'] ?? 'Batch-New');
    $qty = max(1, min(500, (int)($input['qty'] ?? 10)));
    $amount = max(1, (int)($input['amount'] ?? 25));

    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $maxIdx = strlen($chars) - 1;

    $pdo->beginTransaction();
    try {
        $bStmt = $pdo->prepare("INSERT INTO batches (batch_name, cashback_amount, quantity) VALUES (?, ?, ?)");
        $bStmt->execute([$batchName, $amount, $qty]);
        $batchId = (int)$pdo->lastInsertId();

        $cStmt = $pdo->prepare("INSERT INTO product_codes (batch_id, code, cashback_amount, status) VALUES (?, ?, ?, 'UNUSED')");

        for ($i = 0; $i < $qty; $i++) {
            $p1 = '';
            $p2 = '';
            for ($j = 0; $j < 4; $j++) {
                $p1 .= $chars[random_int(0, $maxIdx)];
                $p2 .= $chars[random_int(0, $maxIdx)];
            }
            $newCode = "FB-{$p1}-{$p2}";
            $cStmt->execute([$batchId, $newCode, $amount]);
        }

        $pdo->commit();
        jsonResponse(['ok' => true, 'message' => "Generated {$qty} codes in {$batchName}"]);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        jsonResponse(['ok' => false, 'error' => 'Batch generation failed.'], 500);
    }
}

if ($action === 'reset_demo' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        $pdo->exec("TRUNCATE TABLE rate_limits");
        $pdo->exec("TRUNCATE TABLE fraud_logs");
        $pdo->exec("TRUNCATE TABLE product_codes");
        $pdo->exec("TRUNCATE TABLE batches");
        $pdo->exec("TRUNCATE TABLE customers");
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

        $pdo->exec("INSERT INTO batches (id, batch_name, cashback_amount, quantity) VALUES (1, 'Batch-Nov-01', 25, 3), (2, 'Batch-Oct-09', 25, 2)");
        $pdo->exec("INSERT INTO product_codes (batch_id, code, cashback_amount, bonus_amount, final_payout, status, redeemed_phone, redeemed_upi, redeemed_at) VALUES
            (1, 'FB-9X4K-2M8P', 25, 0, 0, 'UNUSED', NULL, NULL, NULL),
            (1, 'FB-7L2Q-9R1A', 50, 0, 0, 'UNUSED', NULL, NULL, NULL),
            (1, 'FB-4T8W-6C3N', 10, 0, 0, 'UNUSED', NULL, NULL, NULL),
            (2, 'FB-USED-0001', 25, 0, 25, 'REDEEMED', '9825011223', 'amit@ybl', NOW()),
            (2, 'FB-USED-0002', 50, 0, 50, 'REDEEMED', '9911223344', 'neha@oksbi', NOW())");
        $pdo->exec("INSERT INTO customers (phone, total_scans, total_earned) VALUES
            ('9825011223', 1, 25),
            ('9911223344', 1, 50),
            ('9888888888', 9, 225),
            ('9777777777', 49, 1225),
            ('9999999999', 99, 2475)");
        $pdo->exec("INSERT INTO fraud_logs (code_attempted, phone, ip_address, reason) VALUES
            ('FB-USED-0001', '9811002233', '127.0.0.1', 'Duplicate reuse attempt blocked'),
            ('FAKE-1122', '9700112233', '127.0.0.1', 'Invalid code guessed (Rate-limit warning)')");

        jsonResponse(['ok' => true, 'message' => 'All demo data reset to fresh initial state!']);
    } catch (Throwable $e) {
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        jsonResponse(['ok' => false, 'error' => 'Could not reset demo data.'], 500);
    }
}

if ($action === 'export_csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="freshbite_qr_codes.csv"');

    $out = fopen('php://output', 'w');
    fputcsv($out, ['Code', 'Batch', 'Base_INR', 'Bonus_INR', 'Final_Payout_INR', 'Status', 'Redeemed_Phone', 'Redeemed_UPI', 'Redeemed_At']);

    $rows = $pdo->query("
        SELECT pc.code, b.batch_name, pc.cashback_amount, pc.bonus_amount, pc.final_payout, pc.status, pc.redeemed_phone, pc.redeemed_upi, pc.redeemed_at
        FROM product_codes pc
        JOIN batches b ON b.id = pc.batch_id
        ORDER BY pc.id DESC
    ");
    while ($r = $rows->fetch()) {
        fputcsv($out, $r);
    }
    fclose($out);
    exit;
}

jsonResponse(['ok' => false, 'error' => 'Invalid API action'], 400);
