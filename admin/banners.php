<?php
$pageTitle = 'จัดการแบนเนอร์';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_id'])) {
        execute('UPDATE banners SET active = NOT active WHERE id = ?', [(int) $_POST['toggle_id']]);
    }
    if (isset($_POST['delete_id'])) {
        execute('DELETE FROM banners WHERE id = ?', [(int) $_POST['delete_id']]);
        flashMessage('success', 'ลบแบนเนอร์เรียบร้อย');
    }
    redirect(SITE_URL . '/admin/banners.php');
}

require_once __DIR__ . '/includes/sidebar.php';
$flash = getFlashMessage();
$banners = fetchAll('SELECT * FROM banners ORDER BY position, id DESC');
$labels = ['left'=>'ซ้าย 160x600','right'=>'ขวา 160x600','top'=>'บน 728x90','bottom'=>'ล่าง 728x90'];
?>

<div class="admin-top">
    <h1>จัดการแบนเนอร์</h1>
    <a href="banners_add.php" class="btn btn-primary btn-sm">+ เพิ่มแบนเนอร์</a>
</div>
<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div><?php endif; ?>

<div class="table-wrap">
    <table class="data-table">
        <thead><tr><th>รูป</th><th>ลิงก์</th><th>ตำแหน่ง</th><th>เปิดใช้</th><th>หมดอายุ</th><th>จัดการ</th></tr></thead>
        <tbody>
        <?php foreach ($banners as $b): ?>
        <tr>
            <td><img src="<?= e($b['image_url']) ?>" style="max-width:120px;height:60px;object-fit:cover;border-radius:4px;" alt=""></td>
            <td><?= e($b['link_url']) ?></td>
            <td><span class="pos-badge pos-<?= $b['position'] ?>"><?= $labels[$b['position']] ?? $b['position'] ?></span></td>
            <td>
                <form method="POST"><input type="hidden" name="toggle_id" value="<?= $b['id'] ?>">
                <button class="btn btn-sm <?= $b['active'] ? 'btn-success' : 'btn-outline' ?>"><?= $b['active'] ? 'เปิด' : 'ปิด' ?></button></form>
            </td>
            <td><?= $b['expire_date'] ? e($b['expire_date']) : '-' ?></td>
            <td class="actions">
                <a href="banners_add.php?id=<?= $b['id'] ?>" class="btn btn-outline btn-sm">แก้ไข</a>
                <form method="POST" style="display:inline" onsubmit="return confirm('ลบ?')">
                    <input type="hidden" name="delete_id" value="<?= $b['id'] ?>">
                    <button class="btn btn-danger btn-sm">ลบ</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
