<?php
$pageTitle = 'แก้ไขหนัง';
require_once __DIR__ . '/includes/auth.php';

$id = (int) ($_GET['id'] ?? 0);
$movie = getMovieById($id);
if (!$movie) redirect(SITE_URL . '/admin/movies.php');

$categories = getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCsrfToken($csrfToken)) {
        flashMessage('error', 'ข้อความรักษาความปลอดภัยไม่ถูกต้อง');
        redirect(SITE_URL . '/admin/movies_edit.php?id=' . $id);
    }
    $title = trim($_POST['title'] ?? '');
    $titleTh = trim($_POST['title_th'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0) ?: null;
    $status = in_array($_POST['status'] ?? '', ['ongoing','completed']) ? $_POST['status'] : 'ongoing';
    $year = (int) ($_POST['year'] ?? 0) ?: null;
    $thumbnail = trim($_POST['thumbnail'] ?? $movie['thumbnail']);
    if (!empty($_FILES['thumbnail_file']['name'])) {
        $up = uploadImage($_FILES['thumbnail_file'], 'movie');
        if ($up) $thumbnail = $up;
        else {
            flashMessage('error', 'ไม่สามารถอัปโหลดรูปภาพได้');
            redirect(SITE_URL . '/admin/movies_edit.php?id=' . $id);
        }
    }
    execute(
        'UPDATE movies SET title=?, title_th=?, description=?, thumbnail=?, category_id=?, status=?, year=? WHERE id=?',
        [$title, $titleTh, $description, $thumbnail, $categoryId, $status, $year, $id]
    );
    flashMessage('success', 'แก้ไขหนังเรียบร้อย');
    redirect(SITE_URL . '/admin/movies.php');
}

require_once __DIR__ . '/includes/sidebar.php';
$flash = getFlashMessage();
?>

<div class="admin-top"><h1>แก้ไขหนัง</h1><a href="movies.php" class="btn btn-outline btn-sm">← กลับ</a></div>
<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div><?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    <div class="form-row">
        <div class="form-group"><label>ชื่อ (EN)</label><input name="title" value="<?= e($movie['title']) ?>" required></div>
        <div class="form-group"><label>ชื่อ (ไทย)</label><input name="title_th" value="<?= e($movie['title_th']) ?>" required></div>
    </div>
    <div class="form-group"><label>เรื่องย่อ</label><textarea name="description"><?= e($movie['description'] ?? '') ?></textarea></div>
    <div class="form-row">
        <div class="form-group">
            <label>หมวดหมู่</label>
            <select name="category_id"><option value="">-- เลือก --</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $movie['category_id'] == $c['id'] ? 'selected' : '' ?>><?= e($c['name_th']) ?></option>
            <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>สถานะ</label>
            <select name="status">
                <option value="ongoing" <?= $movie['status'] === 'ongoing' ? 'selected' : '' ?>>กำลังฉาย</option>
                <option value="completed" <?= $movie['status'] === 'completed' ? 'selected' : '' ?>>จบแล้ว</option>
            </select>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group"><label>ปี</label><input type="number" name="year" value="<?= e($movie['year'] ?? '') ?>"></div>
        <div class="form-group"><label>URL รูป</label><input type="url" name="thumbnail" value="<?= e($movie['thumbnail'] ?? '') ?>"></div>
    </div>
    <div class="form-group"><label>อัปโหลดรูปใหม่</label><input type="file" name="thumbnail_file" accept="image/*"></div>
    <button type="submit" class="btn btn-primary">บันทึก</button>
    <a href="episodes.php?movie_id=<?= $id ?>" class="btn btn-outline">จัดการตอน</a>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
