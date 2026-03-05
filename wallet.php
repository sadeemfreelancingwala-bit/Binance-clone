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
 
// Handle Deposit Simulation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deposit'])) {
    check_csrf($_POST['csrf_token']);
    $amount = (float)$_POST['amount'];
    if ($amount > 0) {
        $stmt = $pdo->prepare("UPDATE users SET balance_usdt = balance_usdt + ? WHERE id = ?");
        $stmt->execute([$amount, $user_id]);
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, coin, amount, price, total) VALUES (?, 'deposit', 'USDT', ?, 1.0, ?)");
        $stmt->execute([$user_id, $amount, $amount]);
        redirect('wallet.php');
    }
}
 
// Handle Withdraw Simulation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['withdraw'])) {
    check_csrf($_POST['csrf_token']);
    $amount = (float)$_POST['amount'];
    if ($amount > 0 && $amount <= $balance_usdt) {
        $stmt = $pdo->prepare("UPDATE users SET balance_usdt = balance_usdt - ? WHERE id = ?");
        $stmt->execute([$amount, $user_id]);
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, coin, amount, price, total) VALUES (?, 'withdraw', 'USDT', ?, 1.0, ?)");
        $stmt->execute([$user_id, $amount, $amount]);
        redirect('wallet.php');
    }
}
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wallet | CryptoX Pro Exchange</title>
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
            <a href="wallet.php" class="sidebar-link active">
                <i class="fas fa-wallet"></i> <span>Wallet</span>
            </a>
            <a href="portfolio.php" class="sidebar-link">
                <i class="fas fa-briefcase"></i> <span>Portfolio</span>
            </a>
            <a href="history.php" class="sidebar-link">
                <i class="fas fa-history"></i> <span>History</span>
            </a>
        </aside>
 
        <main class="main-content">
            <h2 style="margin-bottom: 30px;">Your Wallet Overview</h2>
 
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div class="glass" style="padding: 30px; text-align: center;">
                    <p style="color:var(--text-muted)">Total Balance (USDT)</p>
                    <h1 style="color: var(--primary); font-size: 3rem;">$<?= number_format($balance_usdt, 2) ?></h1>
                    <div style="display: flex; justify-content: center; gap: 15px; margin-top: 20px;">
                        <button class="btn btn-buy" onclick="document.getElementById('depositModal').style.display='flex'">Deposit</button>
                        <button class="btn btn-outline" onclick="document.getElementById('withdrawModal').style.display='flex'">Withdraw</button>
                    </div>
                </div>
            </div>
 
            <div class="glass" style="padding: 20px;">
                <h3 style="margin-bottom: 20px;">Asset Holdings</h3>
                <table class="order-history-table">
                    <thead>
                        <tr>
                            <th>Asset</th>
                            <th>Icon</th>
                            <th>Balance</th>
                            <th>On Order</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>USDT</td>
                            <td><i class="fas fa-dollar-sign" style="color: #26a17b;"></i></td>
                            <td style="font-weight: 700;"><?= number_format($balance_usdt, 2) ?></td>
                            <td>0.00</td>
                            <td><a href="dashboard.php" style="color: var(--primary);">Trade</a></td>
                        </tr>
                        <?php foreach($wallets as $wallet): ?>
                        <tr>
                            <td><?= $wallet['coin_name'] ?></td>
                            <td><i class="fab fa-bitcoin" style="color: var(--primary);"></i></td>
                            <td style="font-weight: 700;"><?= number_format($wallet['amount'], 4) ?></td>
                            <td>0.0000</td>
                            <td><a href="dashboard.php" style="color: var(--primary);">Trade</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
 
    <!-- Deposit Modal -->
    <div id="depositModal" class="glass" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:2000; align-items:center; justify-content:center;">
        <div class="glass" style="padding: 40px; width: 400px; text-align: center;">
            <h2 style="color: var(--primary); margin-bottom: 20px;">Deposit USDT</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="form-group">
                    <label>Amount to Deposit</label>
                    <input type="number" name="amount" class="form-control" placeholder="100.00" step="0.01" required>
                </div>
                <button type="submit" name="deposit" class="btn btn-primary" style="width: 100%; padding: 12px; margin-top: 10px;">Deposit Now</button>
                <button type="button" class="btn btn-outline" style="width: 100%; margin-top: 10px;" onclick="document.getElementById('depositModal').style.display='none'">Cancel</button>
            </form>
        </div>
    </div>
 
    <!-- Withdraw Modal -->
    <div id="withdrawModal" class="glass" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:2000; align-items:center; justify-content:center;">
        <div class="glass" style="padding: 40px; width: 400px; text-align: center;">
            <h2 style="color: var(--danger); margin-bottom: 20px;">Withdraw USDT</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <div class="form-group">
                    <label>Amount to Withdraw</label>
                    <input type="number" name="amount" class="form-control" placeholder="100.00" step="0.01" max="<?= $balance_usdt ?>" required>
                </div>
                <button type="submit" name="withdraw" class="btn btn-sell" style="width: 100%; padding: 12px; margin-top: 10px;">Withdraw Now</button>
                <button type="button" class="btn btn-outline" style="width: 100%; margin-top: 10px;" onclick="document.getElementById('withdrawModal').style.display='none'">Cancel</button>
            </form>
        </div>
    </div>
 
    <script src="script.js"></script>
</body>
</html>
 
