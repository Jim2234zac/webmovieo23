<?php
$pageTitle = 'จัดการหนัง';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    execute('DELETE FROM movies WHERE id = ?', [(int) $_POST['delete_id']]);
    flashMessage('success', 'ลบหนังเรียบร้อย');
    redirect(SITE_URL . '/admin/movies.php');
}

require_once __DIR__ . '/includes/sidebar.php';
$flash = getFlashMessage();

// รับค่าการค้นหาและกรอง
$search = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';

// สร้าง query พื้นฐาน
$sql = 'SELECT m.*, c.name_th AS category_name,
        (SELECT COUNT(*) FROM episodes WHERE movie_id = m.id) AS ep_count
        FROM movies m LEFT JOIN categories c ON m.category_id = c.id';
$params = [];

// เพิ่มเงื่อนไขการค้นหา
$where = [];
if ($search !== '') {
    $where[] = '(m.title_th LIKE ? OR m.title_en LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// เพิ่มเงื่อนไขกรองสถานะ
if ($statusFilter !== '' && in_array($statusFilter, ['ongoing', 'completed'])) {
    $where[] = 'm.status = ?';
    $params[] = $statusFilter;
}

// รวมเงื่อนไข WHERE
if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY m.id DESC';

// Debug: แสดง query และ params
if ($search !== '' || $statusFilter !== '') {
    error_log("Search: " . $search);
    error_log("Status: " . $statusFilter);
    error_log("SQL: " . $sql);
    error_log("Params: " . print_r($params, true));
}

$movies = fetchAll($sql, $params);
?>

<div class="admin-top">
    <h1>จัดการหนัง</h1>
    <a href="movies_add.php" class="btn btn-primary btn-sm">+ เพิ่มหนัง</a>
</div>
<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div><?php endif; ?>

<?php if ($search !== '' || $statusFilter !== ''): ?>
<div class="alert alert-info">
    <strong>Debug:</strong> ค้นหา: "<?= e($search) ?>" | สถานะ: "<?= e($statusFilter) ?>" | พบ <?= count($movies) ?> รายการ
</div>
<?php endif; ?>

<div class="panel">
    <form method="GET" action="movies.php" class="form-inline">
        <div class="form-group">
            <label>ค้นหาหนัง</label>
            <input type="text" name="search" value="<?= e($search) ?>" placeholder="ชื่อไทย หรือ ชื่ออังกฤษ">
        </div>
        <div class="form-group">
            <label>สถานะ</label>
            <select name="status">
                <option value="">ทั้งหมด</option>
                <option value="ongoing" <?= $statusFilter === 'ongoing' ? 'selected' : '' ?>>กำลังฉาย</option>
                <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>จบแล้ว</option>
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">ค้นหา</button>
            <a href="movies.php" class="btn btn-outline">รีเซ็ต</a>
        </div>
    </form>
</div>

<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr><th>รูป</th><th>ชื่อไทย</th><th>หมวด</th><th>ตอน</th><th>สถานะ</th><th>ยอดดู</th><th>จัดการ</th></tr>
        </thead>
        <tbody>
        <?php foreach ($movies as $m): ?>
        <tr>
            <td><img src="<?= e($m['thumbnail']) ?>" class="thumb-sm" alt=""></td>
            <td><?= e($m['title_th']) ?></td>
            <td><?= e($m['category_name'] ?? '-') ?></td>
            <td><?= $m['ep_count'] ?></td>
            <td><?= statusBadge($m['status']) ?></td>
            <td><?= number_format($m['views']) ?></td>
            <td class="actions">
                <a href="episodes.php?movie_id=<?= $m['id'] ?>" class="btn btn-outline btn-sm">ตอน</a>
                <a href="movies_edit.php?id=<?= $m['id'] ?>" class="btn btn-outline btn-sm">แก้ไข</a>
                <form method="POST" style="display:inline" onsubmit="return confirm('ลบหนังนี้?')">
                    <input type="hidden" name="delete_id" value="<?= $m['id'] ?>">
                    <button class="btn btn-danger btn-sm">ลบ</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
