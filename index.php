<?php
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CryptoX Pro Exchange | Advanced Crypto Trading Platform</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
 
    <nav>
        <a href="index.php" class="logo">
            <i class="fas fa-layer-group"></i> CryptoX <span>Pro</span>
        </a>
        <ul class="nav-links">
            <li><a href="index.php">Markets</a></li>
            <li><a href="#">Trade</a></li>
            <li><a href="#">Security</a></li>
            <li><a href="#">Fees</a></li>
        </ul>
        <div class="nav-actions">
            <?php if (is_logged_in()): ?>
                <a href="dashboard.php" class="btn btn-primary">Dashboard</a>
                <a href="logout.php" class="btn btn-outline">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline">Login</a>
                <a href="signup.php" class="btn btn-primary">Sign Up</a>
            <?php endif; ?>
        </div>
    </nav>
 
    <main>
        <section class="hero">
            <div class="moving-bg"></div>
            <div class="hero-content fade-in">
                <h1 class="neon-text">Trade Crypto with Confidence</h1>
                <p>Buy, sell, and manage over 100+ cryptocurrencies with the most advanced enterprise-level execution engine. Experience zero-latency and professional-grade security.</p>
                <div class="hero-btns">
                    <a href="signup.php" class="btn btn-primary" style="padding: 15px 40px; font-size: 1.1rem; margin-right: 15px;">Start Trading Now</a>
                    <a href="#" class="btn btn-outline" style="padding: 15px 40px; font-size: 1.1rem;">View Markets</a>
                </div>
 
                <div class="ticker-container glass">
                    <div class="ticker-wrap" id="liveTicker">
                        <!-- Simulated Ticker Data via JS -->
                        <div class="ticker-card" id="btc-card">
                            <i class="fab fa-bitcoin" style="color:#f7931a;"></i> BTC
                            <div class="ticker-price" id="btc-price">$64,250.00</div>
                            <small class="price-up" id="btc-change">+1.2%</small>
                        </div>
                        <div class="ticker-card" id="eth-card">
                            <i class="fab fa-ethereum" style="color:#627eea;"></i> ETH
                            <div class="ticker-price" id="eth-price">$3,450.00</div>
                            <small class="price-up" id="eth-change">+2.5%</small>
                        </div>
                        <div class="ticker-card" id="bnb-card">
                            <i class="fas fa-coins" style="color:#f3ba2f;"></i> BNB
                            <div class="ticker-price" id="bnb-price">$580.00</div>
                            <small class="price-down" id="bnb-change">-0.5%</small>
                        </div>
                        <div class="ticker-card" id="sol-card">
                            <i class="fas fa-bolt" style="color:#14f195;"></i> SOL
                            <div class="ticker-price" id="sol-price">$145.00</div>
                            <small class="price-up" id="sol-change">+4.1%</small>
                        </div>
                        <div class="ticker-card" id="xrp-card">
                            <i class="fas fa-times-circle" style="color:#23292f;"></i> XRP
                            <div class="ticker-price" id="xrp-price">$0.62</div>
                            <small class="price-down" id="xrp-change">-1.2%</small>
                        </div>
                    </div>
                </div>
            </div>
        </section>
 
        <section class="features" style="padding: 80px 5%;">
            <h2 style="text-align: center; margin-bottom: 50px; font-size: 2.5rem;">Why Choose CryptoX Pro?</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                <div class="glass card-hover" style="padding: 30px; text-align: center;">
                    <i class="fas fa-shield-halved" style="font-size: 3rem; color: var(--primary); margin-bottom: 20px;"></i>
                    <h3>Enterprise Security</h3>
                    <p style="color: var(--text-muted);">98% of digital assets are stored in cold storage. Multi-signature wallets for ultimate safety.</p>
                </div>
                <div class="glass card-hover" style="padding: 30px; text-align: center;">
                    <i class="fas fa-bolt" style="font-size: 3rem; color: var(--accent); margin-bottom: 20px;"></i>
                    <h3>Instant Execution</h3>
                    <p style="color: var(--text-muted);">Our matching engine can handle millions of orders per second with sub-millisecond latency.</p>
                </div>
                <div class="glass card-hover" style="padding: 30px; text-align: center;">
                    <i class="fas fa-chart-line" style="font-size: 3rem; color: var(--danger); margin-bottom: 20px;"></i>
                    <h3>Advanced Analytics</h3>
                    <p style="color: var(--text-muted);">Real-time charts powered by TradingView API and live order books for precision trading.</p>
                </div>
            </div>
        </section>
    </main>
 
    <footer style="padding: 50px 5%; border-top: 1px solid var(--glass-border); text-align: center;">
        <p style="color: var(--text-muted);">&copy; 2026 CryptoX Pro Exchange. All Digital Assets Are Subject to Market Risk.</p>
    </footer>
 
    <script src="script.js"></script>
</body>
</html>
 
