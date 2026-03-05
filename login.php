<?php
require_once 'db.php';
 
if (is_logged_in()) {
    redirect('dashboard.php');
}
 
$error = '';
$step = 1; // 1 for login, 2 for OTP
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf($_POST['csrf_token']);
 
    // LOGIN STEP
    if (isset($_POST['login'])) {
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
 
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
 
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['pending_user_id'] = $user['id'];
            $_SESSION['pending_otp'] = '123456'; // Simulated static OTP for demo
            $step = 2; // Move to OTP step
        } else {
            $error = "Invalid email or password.";
        }
    }
 
    // VERIFY OTP STEP
    if (isset($_POST['verify_otp'])) {
        $otp = sanitize($_POST['otp']);
        if ($otp === $_SESSION['pending_otp']) {
            $_SESSION['user_id'] = $_SESSION['pending_user_id'];
 
            // Get user data
            $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user_data = $stmt->fetch();
            $_SESSION['user_name'] = $user_data['name'];
            $_SESSION['user_email'] = $user_data['email'];
 
            unset($_SESSION['pending_user_id']);
            unset($_SESSION['pending_otp']);
 
            redirect('dashboard.php');
        } else {
            $error = "Invalid OTP code. Try 123456 for demo.";
            $step = 2;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | CryptoX Pro Exchange</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: var(--bg-darker);">
 
    <div class="auth-container">
        <?php if ($step === 1): ?>
            <form class="auth-card glass fade-in" method="POST">
                <h2 style="margin-bottom: 25px; text-align: center; color: var(--primary);">Welcome Back</h2>
 
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
 
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
 
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="Email" required>
                </div>
 
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
 
                <button type="submit" name="login" class="btn btn-primary" style="width: 100%; padding: 15px; margin-top: 10px;">Login</button>
 
                <p style="text-align: center; margin-top: 20px; color: var(--text-muted);">
                    Don't have an account? <a href="signup.php" style="color: var(--primary); font-weight: 500;">Sign Up</a>
                </p>
            </form>
        <?php else: ?>
            <form class="auth-card glass fade-in" method="POST">
                <h2 style="margin-bottom: 10px; text-align: center; color: var(--primary);">Two-Factor Auth</h2>
                <p style="text-align: center; margin-bottom: 25px; color: var(--text-muted);">We've sent a 6-digit code to your email.</p>
 
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
 
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
 
                <div class="form-group">
                    <label style="text-align: center;">Enter OTP Code</label>
                    <input type="text" name="otp" class="form-control" placeholder="123456" maxlength="6" style="text-align: center; letter-spacing: 5px; font-size: 1.5rem;" required>
                </div>
 
                <button type="submit" name="verify_otp" class="btn btn-primary" style="width: 100%; padding: 15px; margin-top: 10px;">Verify & Login</button>
            </form>
        <?php endif; ?>
    </div>
 
</body>
</html>
 
