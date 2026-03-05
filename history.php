<?php
require_once 'db.php';
 
if (!is_logged_in()) {
    redirect('login.php');
}
 
$user_id = $_SESSION['user_id'];
 
// Get Transaction History
$stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$transactions = $stmt->fetchAll();
 
// Get Filters if any
$filter_type = isset($_GET['type']) ? sanitize($_GET['type']) : '';
$filter_coin = isset($_GET['coin']) ? sanitize($_GET['coin']) : '';
 
if ($filter_type || $filter_coin) {
    $sql = "SELECT * FROM transactions WHERE user_id = ?";
    $params = [$user_id];
 
    if ($filter_type) {
        $sql .= " AND type = ?";
        $params[] = $filter_type;
    }
    if ($filter_coin) {
        $sql .= " AND coin = ?";
        $params[] = $filter_coin;
    }
    $sql .= " ORDER BY created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History | CryptoX Pro Exchange</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
 
    <nav>
        <a href="index.php" class="logo">
            <i class="fas fa-layer-group"></i> CryptoX <span>Pro</span>
        </a>
        <div class="nav-actions">
            <span style="color:var(--text-muted); margin-right: 15px;">
                <i class="fas fa-user-circle"></i> <?= $_SESSION['user_name'] ?>
            </span>
            <a href="logout.php" class="btn btn-outline" style="padding: 5px 15px; font-size: 0.8rem;">Logout</a>
        </div>
    </nav>
 
    <div class="dashboard-wrapper">
        <aside class="sidebar">
            <a href="dashboard.php" class="sidebar-link">
                <i class="fas fa-chart-line"></i> <span>Trade</span>
            </a>
            <a href="wallet.php" class="sidebar-link">
                <i class="fas fa-wallet"></i> <span>Wallet</span>
            </a>
            <a href="portfolio.php" class="sidebar-link">
                <i class="fas fa-briefcase"></i> <span>Portfolio</span>
            </a>
            <a href="history.php" class="sidebar-link active">
                <i class="fas fa-history"></i> <span>History</span>
            </a>
        </aside>
 
        <main class="main-content">
            <h2 style="margin-bottom: 30px;">Transaction History</h2>
 
            <div class="glass" style="padding: 20px; margin-bottom: 30px;">
                <form method="GET" style="display: flex; gap: 20px; align-items: flex-end;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Order Type</label>
                        <select name="type" class="form-control" style="min-width: 150px;">
                            <option value="">All Types</option>
                            <option value="buy" <?= $filter_type == 'buy' ? 'selected' : '' ?>>Buy</option>
                            <option value="sell" <?= $filter_type == 'sell' ? 'selected' : '' ?>>Sell</option>
                            <option value="deposit" <?= $filter_type == 'deposit' ? 'selected' : '' ?>>Deposit</option>
                            <option value="withdraw" <?= $filter_type == 'withdraw' ? 'selected' : '' ?>>Withdraw</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Asset</label>
                        <select name="coin" class="form-control" style="min-width: 150px;">
                            <option value="">All Assets</option>
                            <option value="BTC" <?= $filter_coin == 'BTC' ? 'selected' : '' ?>>BTC</option>
                            <option value="ETH" <?= $filter_coin == 'ETH' ? 'selected' : '' ?>>ETH</option>
                            <option value="BNB" <?= $filter_coin == 'BNB' ? 'selected' : '' ?>>BNB</option>
                            <option value="SOL" <?= $filter_coin == 'SOL' ? 'selected' : '' ?>>SOL</option>
                            <option value="XRP" <?= $filter_coin == 'XRP' ? 'selected' : '' ?>>XRP</option>
                            <option value="USDT" <?= $filter_coin == 'USDT' ? 'selected' : '' ?>>USDT</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter History</button>
                    <a href="history.php" class="btn btn-outline">Reset</a>
                </form>
            </div>
 
            <div class="glass" style="padding: 20px;">
                <table class="order-history-table">
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Asset</th>
                            <th>Amount</th>
                            <th>Price</th>
                            <th>Total (USDT)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 40px;">No transactions found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($transactions as $tx): ?>
                                <tr class="shimmer-row">
                                    <td>#TX-<?= str_pad($tx['id'], 6, "0", STR_PAD_LEFT) ?></td>
                                    <td><?= date("Y-m-d H:i", strtotime($tx['created_at'])) ?></td>
                                    <td>
                                        <span class="btn btn-outline" style="padding: 2px 8px; font-size: 0.7rem; color: <?= in_array($tx['type'], ['buy', 'deposit']) ? 'var(--accent)' : 'var(--danger)' ?>; border-color: <?= in_array($tx['type'], ['buy', 'deposit']) ? 'var(--accent)' : 'var(--danger)' ?>;">
                                            <?= strtoupper($tx['type']) ?>
                                        </span>
                                    </td>
                                    <td><?= $tx['coin'] ?></td>
                                    <td style="font-weight: 700;"><?= number_format($tx['amount'], 4) ?></td>
                                    <td>$<?= number_format($tx['price'], 2) ?></td>
                                    <td>$<?= number_format($tx['total'], 2) ?></td>
                                    <td style="color: var(--accent);"><i class="fas fa-check-circle"></i> Success</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
 
    <script src="script.js"></script>
</body>
</html>
 
