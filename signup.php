<?php
require_once 'db.php';
 
if (is_logged_in()) {
    redirect('dashboard.php');
}
 
$error = '';
$success = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    check_csrf($_POST['csrf_token']);
 
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
 
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, balance_usdt) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $hashed_password, 10000.00])) {
                $user_id = $pdo->lastInsertId();
 
                // Initialize wallets
                $coins = ['BTC', 'ETH', 'BNB', 'SOL', 'XRP'];
                foreach ($coins as $coin) {
                    $stmt = $pdo->prepare("INSERT INTO wallets (user_id, coin_name, amount) VALUES (?, ?, ?)");
                    $stmt->execute([$user_id, $coin, 0]);
                }
 
                $success = "Registration successful! You can now <a href='login.php' style='color:var(--primary); text-decoration:underline;'>Login</a>.";
            } else {
                $error = "Registration failed. Try again later.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | CryptoX Pro</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body style="background: var(--bg-darker);">
 
    <div class="auth-container">
        <form class="auth-card glass fade-in" method="POST">
            <h2 style="margin-bottom: 25px; text-align: center; color: var(--primary);">Join CryptoX Pro</h2>
 
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
 
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
 
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
            </div>
 
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
            </div>
 
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
 
            <button type="submit" name="signup" class="btn btn-primary" style="width: 100%; padding: 15px; margin-top: 10px;">Create Account</button>
 
            <p style="text-align: center; margin-top: 20px; color: var(--text-muted);">
                Already have an account? <a href="login.php" style="color: var(--primary); font-weight: 500;">Login</a>
            </p>
        </form>
    </div>
 
</body>
</html>
 
