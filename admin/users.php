<?php
$pageTitle = 'จัดการผู้ใช้';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/sidebar.php';

$flash = getFlashMessage();

// ลบผู้ใช้
if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $csrfToken = $_GET['csrf_token'] ?? '';
    
    if (!verifyCsrfToken($csrfToken)) {
        flashMessage('error', 'ข้อความรักษาความปลอดภัยไม่ถูกต้อง');
        redirect(SITE_URL . '/admin/users.php');
    }
    
    // ป้องกันไม่ให้ลบตัวเอง
    if ($deleteId === $_SESSION['user_id']) {
        flashMessage('error', 'ไม่สามารถลบตัวเองได้');
        redirect(SITE_URL . '/admin/users.php');
    }
    
    $user = fetchOne('SELECT * FROM users WHERE id = ?', [$deleteId]);
    if (!$user) {
        flashMessage('error', 'ไม่พบผู้ใช้');
        redirect(SITE_URL . '/admin/users.php');
    }
    
    $ok = execute('DELETE FROM users WHERE id = ?', [$deleteId]);
    if ($ok) {
        flashMessage('success', 'ลบผู้ใช้เรียบร้อยแล้ว');
    } else {
        flashMessage('error', 'ไม่สามารถลบผู้ใช้ได้');
    }
    redirect(SITE_URL . '/admin/users.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrfToken)) {
        flashMessage('error', 'ข้อความรักษาความปลอดภัยไม่ถูกต้อง');
        redirect(SITE_URL . '/admin/users.php');
    }
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = in_array($_POST['role'] ?? 'user', ['user','admin']) ? $_POST['role'] : 'user';

    if ($username === '' || $email === '' || $password === '') {
        flashMessage('error', 'กรุณากรอกชื่อผู้ใช้ อีเมล และรหัสผ่าน');
        redirect(SITE_URL . '/admin/users.php');
    }

    $existing = fetchOne('SELECT * FROM users WHERE username = ? OR email = ?', [$username, $email]);
    if ($existing) {
        flashMessage('error', 'ชื่อผู้ใช้หรืออีเมลนี้มีอยู่แล้ว');
        redirect(SITE_URL . '/admin/users.php');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $ok = execute('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)', [$username, $email, $hash, $role]);
    if ($ok) {
        flashMessage('success', 'เพิ่มผู้ใช้เรียบร้อยแล้ว');
    } else {
        flashMessage('error', 'ไม่สามารถเพิ่มผู้ใช้ได้');
    }
    redirect(SITE_URL . '/admin/users.php');
}

$users = fetchAll('SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC');
?>

<div class="admin-top">
    <h1>จัดการผู้ใช้</h1>
    <span>สวัสดี, <?= e($_SESSION['username']) ?></span>
</div>

<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div><?php endif; ?>

<div class="panel">
    <h2>เพิ่มผู้ใช้ใหม่</h2>
    <form method="POST" class="form-inline">
        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
        <div class="form-group">
            <label>ชื่อผู้ใช้</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>อีเมล</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>รหัสผ่าน</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>บทบาท</label>
            <select name="role">
                <option value="user">ผู้ใช้</option>
                <option value="admin">ผู้ดูแล</option>
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">เพิ่มผู้ใช้</button>
        </div>
    </form>
</div>

<h2 class="admin-subtitle">รายการผู้ใช้</h2>
<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>ชื่อผู้ใช้</th><th>อีเมล</th><th>บทบาท</th><th>วันที่สร้าง</th><th>จัดการ</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= e($u['username']) ?></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e($u['role']) ?></td>
            <td><?= e($u['created_at']) ?></td>
            <td>
                <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                <a href="?delete=<?= $u['id'] ?>&csrf_token=<?= generateCsrfToken() ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('ยืนยันที่จะลบผู้ใช้: <?= e($u['username']) ?>?')">ลบ</a>
                <?php else: ?>
                <span class="text-muted">ตัวเอง</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
