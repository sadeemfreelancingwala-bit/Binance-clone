<?php
require_once 'db.php';
 
if (!is_logged_in()) {
    redirect('login.php');
}
 
$user_id = $_SESSION['user_id'];
 
// Get User balance (USDT)
$stmt = $pdo->prepare("SELECT balance_usdt FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$balance_usdt = $stmt->fetchColumn();
 
// Get Wallet Balances (Crypto)
$stmt = $pdo->prepare("SELECT coin_name, amount FROM wallets WHERE user_id = ?");
$stmt->execute([$user_id]);
$wallets = $stmt->fetchAll();
 
// Default crypto for chart
$active_coin = 'BTC';
 
// Recent Orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$user_id]);
$recent_orders = $stmt->fetchAll();
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | CryptoX Pro Exchange</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .active-tab {
            color: var(--primary) !important;
            border-bottom: 2px solid var(--primary);
        }
        .trade-tabs {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 10px;
        }
        .trade-tab {
            cursor: pointer;
            color: var(--text-muted);
            padding: 5px 15px;
            font-weight: 500;
        }
        .trade-tab.active {
            color: var(--primary);
            border-bottom: 2px solid var(--primary);
        }
    </style>
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
            <a href="dashboard.php" class="sidebar-link active">
                <i class="fas fa-chart-line"></i> <span>Trade</span>
            </a>
            <a href="wallet.php" class="sidebar-link">
                <i class="fas fa-wallet"></i> <span>Wallet</span>
            </a>
            <a href="portfolio.php" class="sidebar-link">
                <i class="fas fa-briefcase"></i> <span>Portfolio</span>
            </a>
            <a href="history.php" class="sidebar-link">
                <i class="fas fa-history"></i> <span>History</span>
            </a>
            <div style="flex-grow: 1;"></div>
            <div class="glass" style="padding: 15px;">
                <p style="font-size: 0.8rem; color: var(--text-muted);">Estimated Balance</p>
                <h3 style="color: var(--primary);">$<?= number_format($balance_usdt, 2) ?></h3>
                <small style="color: var(--accent);">+2.45% (24h)</small>
            </div>
        </aside>
 
        <main class="main-content">
            <div class="glass" style="padding: 20px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div>
                        <h2 id="trading-coin-symbol">BTC / USDT</h2>
                        <span id="active-coin-price" style="font-size: 1.5rem; font-weight: 700; color: var(--accent);">$64,250.00</span>
                        <small class="price-up" id="btc-change">+1.24%</small>
                    </div>
                    <div style="display: flex; gap: 20px; text-align: right;">
                        <div>
                            <small style="color:var(--text-muted)">24h High</small><br>
                            <span style="font-size: 0.9rem;">65,120.00</span>
                        </div>
                        <div>
                            <small style="color:var(--text-muted)">24h Low</small><br>
                            <span style="font-size: 0.9rem;">63,450.00</span>
                        </div>
                        <div>
                            <small style="color:var(--text-muted)">24h Vol</small><br>
                            <span style="font-size: 0.9rem;">1.2B USDT</span>
                        </div>
                    </div>
                </div>
                <!-- Trading Chart -->
                <div style="height: 450px; position: relative;">
                    <canvas id="tradingChart"></canvas>
                </div>
            </div>
 
            <div class="glass" style="padding: 20px;">
                <h3 style="margin-bottom: 15px;">Open Orders</h3>
                <table class="order-history-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Pair</th>
                            <th>Type</th>
                            <th>Side</th>
                            <th>Price</th>
                            <th>Amount</th>
                            <th>Filled</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_orders)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; color: var(--text-muted);">No open orders.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_orders as $order): ?>
                                <tr>
                                    <td><?= $order['created_at'] ?></td>
                                    <td><?= $order['coin'] ?>/USDT</td>
                                    <td><?= ucfirst($order['order_type']) ?></td>
                                    <td style="color: <?= $order['type'] == 'buy' ? 'var(--accent)' : 'var(--danger)' ?>">
                                        <?= strtoupper($order['type']) ?>
                                    </td>
                                    <td><?= number_format($order['price'], 2) ?></td>
                                    <td><?= number_format($order['amount'], 4) ?></td>
                                    <td><?= $order['status'] == 'completed' ? '100%' : '0%' ?></td>
                                    <td><button class="btn btn-outline" style="padding: 2px 8px; font-size: 0.7rem;">Cancel</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
 
        <aside class="trade-panel">
            <div class="trade-tabs">
                <div class="trade-tab active" data-type="buy">Buy</div>
                <div class="trade-tab" data-type="sell">Sell</div>
            </div>
 
            <div id="buy-section">
                <div class="glass" style="padding: 15px; margin-bottom: 15px;">
                    <p style="color: var(--text-muted); font-size: 0.8rem;">Available Balance</p>
                    <h4 style="color: var(--text-main);"><?= number_format($balance_usdt, 2) ?> USDT</h4>
                </div>
 
                <form action="trade.php" method="POST">
                    <input type="hidden" name="coin" value="BTC">
                    <input type="hidden" name="type" id="order-side" value="buy">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
 
                    <div class="form-group">
                        <label>Price</label>
                        <div style="position: relative;">
                            <input type="number" name="price" class="form-control" value="64250.00" step="0.01">
                            <span style="position: absolute; right: 10px; top: 12px; color: var(--text-muted);">USDT</span>
                        </div>
                    </div>
 
                    <div class="form-group">
                        <label>Amount</label>
                        <div style="position: relative;">
                            <input type="number" name="amount" class="form-control" placeholder="0.00" step="0.0001">
                            <span style="position: absolute; right: 10px; top: 12px; color: var(--text-muted);"><?= $active_coin ?></span>
                        </div>
                    </div>
 
                    <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                        <span class="btn btn-outline" style="padding: 5px 10px; font-size: 0.7rem;">25%</span>
                        <span class="btn btn-outline" style="padding: 5px 10px; font-size: 0.7rem;">50%</span>
                        <span class="btn btn-outline" style="padding: 5px 10px; font-size: 0.7rem;">75%</span>
                        <span class="btn btn-outline" style="padding: 5px 10px; font-size: 0.7rem;">100%</span>
                    </div>
 
                    <div id="buy-btn">
                        <button type="submit" name="trade" class="btn btn-buy" style="width: 100%; padding: 15px; font-size: 1.1rem;">Buy BTC</button>
                    </div>
                    <div id="sell-btn" style="display: none;">
                        <button type="submit" name="trade" class="btn btn-sell" style="width: 100%; padding: 15px; font-size: 1.1rem;">Sell BTC</button>
                    </div>
                </form>
            </div>
 
            <div style="margin-top: 30px;">
                <h4 style="margin-bottom: 15px;">Order Book</h4>
                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 10px;">
                    <span>Price (USDT)</span>
                    <span>Amount (BTC)</span>
                </div>
                <!-- Dummy Order Book -->
                <div style="color: var(--danger); font-family: monospace; font-size: 0.85rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;"><span>64258.45</span> <span>0.4124</span></div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;"><span>64257.00</span> <span>1.2250</span></div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;"><span>64255.12</span> <span>0.0450</span></div>
                </div>
                <div style="text-align: center; font-size: 1.2rem; font-weight: 700; margin: 15px 0; color: var(--accent);">64250.00</div>
                <div style="color: var(--accent); font-family: monospace; font-size: 0.85rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;"><span>64249.20</span> <span>0.5501</span></div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;"><span>64248.00</span> <span>2.1105</span></div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;"><span>64245.88</span> <span>0.0980</span></div>
                </div>
            </div>
        </aside>
    </div>
 
    <script src="script.js"></script>
</body>
</html>
 
