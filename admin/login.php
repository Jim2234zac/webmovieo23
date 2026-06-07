<?php
require_once __DIR__ . '/../includes/functions.php';

if (isAdmin()) redirect(SITE_URL . '/admin/index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $user = fetchOne('SELECT * FROM users WHERE username = ? AND role = ?', [$username, 'admin']);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        redirect(SITE_URL . '/admin/index.php');
    }
    $error = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?= SITE_NAME ?></title>
    <?php require_once __DIR__ . '/../includes/fonts.php'; ?>
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <h1>Admin Panel</h1>
        <p class="subtitle">เข้าสู่ระบบผู้ดูแล</p>
        <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>ชื่อผู้ใช้</label>
                <input type="text" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">เข้าสู่ระบบ</button>
        </form>
        <p style="text-align:center;margin-top:16px;"><a href="<?= SITE_URL ?>/index.php">← กลับหน้าแรก</a></p>
    </div>
</div>
</body>
</html>
