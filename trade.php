<?php
require_once 'db.php';

if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

check_csrf($_POST['csrf_token']);

$user_id = $_SESSION['user_id'];
$coin = $_POST['coin'];
$type = $_POST['type']; // 'buy' or 'sell'
$amount = (float)$_POST['amount'];
$price = (float)$_POST['price'];
$total = $amount * $price;

if ($amount <= 0 || $price <= 0) {
    die("Invalid amount or price.");
}

$pdo->beginTransaction();

try {
    if ($type === 'buy') {
        // 1. Check USDT Balance
        $stmt = $pdo->prepare("SELECT balance_usdt FROM users WHERE id = ? FOR UPDATE");
        $stmt->execute([$user_id]);
        $balance = $stmt->fetchColumn();
        
        if ($balance < $total) {
            throw new Exception("Insufficient USDT balance.");
        }
        
        // 2. Deduct USDT
        $stmt = $pdo->prepare("UPDATE users SET balance_usdt = balance_usdt - ? WHERE id = ?");
        $stmt->execute([$total, $user_id]);
        
        // 3. Update/Add Crypto Wallet
        $stmt = $pdo->prepare("UPDATE wallets SET amount = amount + ? WHERE user_id = ? AND coin_name = ?");
        $stmt->execute([$amount, $user_id, $coin]);
        
    } else {
        // SELL Logic
        // 1. Check Crypto Balance
        $stmt = $pdo->prepare("SELECT amount FROM wallets WHERE user_id = ? AND coin_name = ? FOR UPDATE");
        $stmt->execute([$user_id, $coin]);
        $crypto_balance = $stmt->fetchColumn();
        
        if ($crypto_balance < $amount) {
            throw new Exception("Insufficient $coin balance.");
        }
        
        // 2. Deduct Crypto
        $stmt = $pdo->prepare("UPDATE wallets SET amount = amount - ? WHERE user_id = ? AND coin_name = ?");
        $stmt->execute([$amount, $user_id, $coin]);
        
        // 3. Add USDT
        $stmt = $pdo->prepare("UPDATE users SET balance_usdt = balance_usdt + ? WHERE id = ?");
        $stmt->execute([$total, $user_id]);
    }
    
    // 4. Record Transaction
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, coin, amount, price, total) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $type, $coin, $amount, $price, $total]);
    
    // 5. Record Order (as completed)
    $stmt = $pdo->prepare("INSERT INTO orders (user_id, type, coin, amount, price, status, order_type) VALUES (?, ?, ?, ?, ?, 'completed', 'market')");
    $stmt->execute([$user_id, $type, $coin, $amount, $price]);
    
    $pdo->commit();
    $_SESSION['flash_msg'] = "Order executed successfully!";
    redirect('dashboard.php');
    
} catch (Exception $e) {
    $pdo->rollBack();
    die($e->getMessage());
}
?>
