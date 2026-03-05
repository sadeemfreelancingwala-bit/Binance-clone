<?php
require_once 'db.php';
 
if (!is_logged_in()) {
    redirect('login.php');
}
 
$user_id = $_SESSION['user_id'];
 
// Get USDT Balance
$stmt = $pdo->prepare("SELECT balance_usdt FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$balance_usdt = $stmt->fetchColumn();
 
// Get Crypto Assets
$stmt = $pdo->prepare("SELECT coin_name, amount FROM wallets WHERE user_id = ?");
$stmt->execute([$user_id]);
$wallets = $stmt->fetchAll();
 
// Mock current prices for portfolio calculation
$mock_prices = [
    'BTC' => 64250.00,
    'ETH' => 3450.00,
    'BNB' => 580.00,
    'SOL' => 145.00,
    'XRP' => 0.62
];
 
$total_portfolio_value = $balance_usdt;
foreach ($wallets as $wallet) {
    if (isset($mock_prices[$wallet['coin_name']])) {
        $total_portfolio_value += $wallet['amount'] * $mock_prices[$wallet['coin_name']];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio | CryptoX Pro Exchange</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="portfolio.php" class="sidebar-link active">
                <i class="fas fa-briefcase"></i> <span>Portfolio</span>
            </a>
            <a href="history.php" class="sidebar-link">
                <i class="fas fa-history"></i> <span>History</span>
            </a>
        </aside>
 
        <main class="main-content">
            <h2 style="margin-bottom: 30px;">Portfolio Tracking</h2>
 
            <div style="display: grid; grid-template-columns: 2fr 1.2fr; gap: 20px; margin-bottom: 30px;">
                <div class="glass" style="padding: 30px;">
                    <p style="color:var(--text-muted)">Net Worth Estimation</p>
                    <h1 style="color: var(--primary); font-size: 3.5rem;">$<?= number_format($total_portfolio_value, 2) ?></h1>
                    <p style="color: var(--accent); margin-top: 10px;">
                        <i class="fas fa-arrow-up"></i> 14.2% Estimated Growth
                    </p>
                    <div style="margin-top: 30px; height: 100px; background: rgba(0, 255, 136, 0.05); border-radius: 12px; border: 1px dashed var(--accent);">
                         <div style="padding: 20px;">
                             <p style="font-size: 0.8rem; color: var(--text-muted);">Weekly Performance</p>
                             <div style="width: 100%; height: 2px; background: #333; margin: 10px 0; border-radius: 2px;">
                                 <div style="width: 75%; height: 100%; background: var(--accent); border-radius: 2px; box-shadow: 0 0 10px var(--accent);"></div>
                             </div>
                         </div>
                    </div>
                </div>
 
                <div class="glass" style="padding: 30px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                    <h4 style="margin-bottom: 20px;">Asset Allocation</h4>
                    <div style="width: 200px; height: 200px;">
                        <canvas id="assetAllocationChart"></canvas>
                    </div>
                </div>
            </div>
 
            <div class="glass" style="padding: 20px;">
                <h3 style="margin-bottom: 20px;">Your Assets Analytics</h3>
                <table class="order-history-table">
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th>Amount</th>
                            <th>Avg Price</th>
                            <th>Current Price</th>
                            <th>P/L</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($wallets as $wallet): ?>
                        <?php if ($wallet['amount'] > 0): ?>
                        <tr>
                            <td><?= $wallet['coin_name'] ?></td>
                            <td style="font-weight: 700;"><?= number_format($wallet['amount'], 4) ?></td>
                            <td>$<?= number_format($mock_prices[$wallet['coin_name']] * 0.95, 2) ?></td>
                            <td>$<?= number_format($mock_prices[$wallet['coin_name']], 2) ?></td>
                            <td style="color: var(--accent);">+5.21%</td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                        <?php if ($balance_usdt > 0): ?>
                        <tr>
                            <td>USDT</td>
                            <td style="font-weight: 700;"><?= number_format($balance_usdt, 2) ?></td>
                            <td>$1.00</td>
                            <td>$1.00</td>
                            <td style="color: var(--text-muted);">--</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
 
    <script src="script.js"></script>
</body>
</html>
 
