<?php
$pageTitle = 'จัดการหมวดหมู่';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrfToken)) {
        flashMessage('error', 'ข้อความรักษาความปลอดภัยไม่ถูกต้อง');
        redirect(SITE_URL . '/admin/categories.php');
    }
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $nameTh = trim($_POST['name_th'] ?? '');
        if ($name && $nameTh) {
            execute('INSERT INTO categories (name, name_th) VALUES (?,?)', [$name, $nameTh]);
            flashMessage('success', 'เพิ่มหมวดหมู่เรียบร้อย');
        }
    }
    if ($action === 'edit') {
        execute('UPDATE categories SET name=?, name_th=? WHERE id=?',
            [trim($_POST['name']), trim($_POST['name_th']), (int) $_POST['id']]);
        flashMessage('success', 'แก้ไขหมวดหมู่เรียบร้อย');
    }
    if ($action === 'delete') {
        execute('DELETE FROM categories WHERE id = ?', [(int) $_POST['id']]);
        flashMessage('success', 'ลบหมวดหมู่เรียบร้อย');
    }
    redirect(SITE_URL . '/admin/categories.php');
}

require_once __DIR__ . '/includes/sidebar.php';
$flash = getFlashMessage();
$categories = fetchAll('SELECT c.*, (SELECT COUNT(*) FROM movies WHERE category_id=c.id) AS cnt FROM categories c ORDER BY name_th');
$editId = (int) ($_GET['edit'] ?? 0);
$editCat = $editId ? fetchOne('SELECT * FROM categories WHERE id=?', [$editId]) : null;
?>

<div class="admin-top"><h1>จัดการหมวดหมู่</h1></div>
<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div><?php endif; ?>

<form method="POST" class="admin-form" style="margin-bottom:24px;">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    <input type="hidden" name="action" value="<?= $editCat ? 'edit' : 'add' ?>">
    <?php if ($editCat): ?><input type="hidden" name="id" value="<?= $editCat['id'] ?>"><?php endif; ?>
    <h2><?= $editCat ? 'แก้ไขหมวดหมู่' : 'เพิ่มหมวดหมู่' ?></h2>
    <div class="form-row">
        <div class="form-group"><label>ชื่อ EN</label><input name="name" value="<?= e($editCat['name'] ?? '') ?>" required></div>
        <div class="form-group"><label>ชื่อไทย</label><input name="name_th" value="<?= e($editCat['name_th'] ?? '') ?>" required></div>
    </div>
    <button type="submit" class="btn btn-primary">บันทึก</button>
    <?php if ($editCat): ?><a href="categories.php" class="btn btn-outline">ยกเลิก</a><?php endif; ?>
</form>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>EN</th><th>ไทย</th><th>จำนวนหนัง</th><th>จัดการ</th></tr></thead>
        <tbody>
        <?php foreach ($categories as $c): ?>
        <tr>
            <td><?= e($c['name']) ?></td>
            <td><?= e($c['name_th']) ?></td>
            <td><?= $c['cnt'] ?></td>
            <td class="actions">
                <a href="?edit=<?= $c['id'] ?>" class="btn btn-outline btn-sm">แก้ไข</a>
                <form method="POST" style="display:inline" onsubmit="return confirm('ลบหมวดหมู่นี้ (จำนวนหนัง: <?= $c['cnt'] ?>)?')">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                    <button class="btn btn-danger btn-sm">ลบ</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
