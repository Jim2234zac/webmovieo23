<?php
$pageTitle = 'เพิ่มหนัง';
require_once __DIR__ . '/includes/auth.php';

$categories = getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrfToken)) {
        flashMessage('error', 'ข้อความรักษาความปลอดภัยไม่ถูกต้อง');
        redirect(SITE_URL . '/admin/movies_add.php');
    }
    $title = trim($_POST['title'] ?? '');
    $titleTh = trim($_POST['title_th'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0) ?: null;
    $status = in_array($_POST['status'] ?? '', ['ongoing','completed']) ? $_POST['status'] : 'ongoing';
    $year = (int) ($_POST['year'] ?? 0) ?: null;
    $thumbnail = trim($_POST['thumbnail'] ?? '');
    if (!empty($_FILES['thumbnail_file']['name'])) {
        $up = uploadImage($_FILES['thumbnail_file'], 'movie');
        if ($up) $thumbnail = $up;
        else {
            flashMessage('error', 'ไม่สามารถอัปโหลดรูปภาพได้');
            redirect(SITE_URL . '/admin/movies_add.php');
        }
    }
    if ($title && $titleTh) {
        execute(
            'INSERT INTO movies (title, title_th, description, thumbnail, category_id, status, year) VALUES (?,?,?,?,?,?,?)',
            [$title, $titleTh, $description, $thumbnail, $categoryId, $status, $year]
        );
        flashMessage('success', 'เพิ่มหนังเรียบร้อย');
        redirect(SITE_URL . '/admin/movies.php');
    }
    flashMessage('error', 'กรุณากรอกชื่อหนัง');
}

require_once __DIR__ . '/includes/sidebar.php';
$flash = getFlashMessage();
?>

<div class="admin-top"><h1>เพิ่มหนังใหม่</h1><a href="movies.php" class="btn btn-outline btn-sm">← กลับ</a></div>
<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div><?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    <div class="form-row">
        <div class="form-group"><label>ชื่อ (EN)</label><input name="title" required></div>
        <div class="form-group"><label>ชื่อ (ไทย)</label><input name="title_th" required></div>
    </div>
    <div class="form-group"><label>เรื่องย่อ</label><textarea name="description"></textarea></div>
    <div class="form-row">
        <div class="form-group">
            <label>หมวดหมู่</label>
            <select name="category_id"><option value="">-- เลือก --</option>
            <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name_th']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>สถานะ</label>
            <select name="status"><option value="ongoing">กำลังฉาย</option><option value="completed">จบแล้ว</option></select>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group"><label>ปี</label><input type="number" name="year" min="1900" max="2099"></div>
        <div class="form-group"><label>URL รูป</label><input type="url" name="thumbnail" placeholder="https://..."></div>
    </div>
    <div class="form-group"><label>อัปโหลดรูป</label><input type="file" name="thumbnail_file" accept="image/*"></div>
    <button type="submit" class="btn btn-primary">บันทึก</button>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
