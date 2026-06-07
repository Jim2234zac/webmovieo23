<?php
$pageTitle = 'เพิ่มแบนเนอร์';
require_once __DIR__ . '/includes/auth.php';

$id = (int) ($_GET['id'] ?? 0);
$banner = $id ? fetchOne('SELECT * FROM banners WHERE id = ?', [$id]) : null;
if ($id && !$banner) redirect(SITE_URL . '/admin/banners.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $linkUrl = trim($_POST['link_url'] ?? 'https://example.com');
    $position = in_array($_POST['position'] ?? '', ['left','right','top','bottom']) ? $_POST['position'] : 'top';
    $expireDate = trim($_POST['expire_date'] ?? '') ?: null;
    $imageUrl = '';
    
    // ลองอัปโหลดรูปก่อน
    if (!empty($_FILES['image_file']['name'])) {
        $up = uploadImage($_FILES['image_file'], 'banner');
        if ($up) {
            $imageUrl = $up;
        } else {
            flashMessage('error', '❌ อัปโหลดรูปไม่สำเร็จ (ประเภทไฟล์หรือขนาดไม่ถูกต้อง)');
            redirect(SITE_URL . '/admin/banners_add.php' . ($id ? '?id=' . $id : ''));
        }
    }
    
    // ถ้าไม่มีการอัปโหลด ลองใช้ URL จาก input
    if (!$imageUrl) {
        $imageUrl = trim($_POST['image_url'] ?? ($banner['image_url'] ?? ''));
    }
    
    // ต้องมี image_url ถึงจะบันทึก
    if (empty($imageUrl)) {
        flashMessage('error', '❌ ต้องอัปโหลดรูป หรือใส่ URL รูป');
        redirect(SITE_URL . '/admin/banners_add.php' . ($id ? '?id=' . $id : ''));
    }
    
    // แบนเนอร์ใหม่ให้เปิดใช้งานโดยค่าเริ่มต้น
    // แบนเนอร์เดิม ถ้าไม่ได้ติ๊ก checkbox ให้คงค่าเดิม
    if ($banner) {
        $active = isset($_POST['active']) ? 1 : ($banner['active'] ?? 1);
        execute('UPDATE banners SET image_url=?, link_url=?, position=?, active=?, expire_date=? WHERE id=?',
            [$imageUrl, $linkUrl, $position, $active, $expireDate, $id]);
        flashMessage('success', '✓ อัปเดตแบนเนอร์เรียบร้อย');
    } else {
        $active = 1;
        execute('INSERT INTO banners (image_url, link_url, position, active, expire_date) VALUES (?,?,?,?,?)',
            [$imageUrl, $linkUrl, $position, $active, $expireDate]);
        flashMessage('success', '✓ เพิ่มแบนเนอร์เรียบร้อย (เปิดใช้งานแล้ว)');
    }
    redirect(SITE_URL . '/admin/banners.php');
}

$pageTitle = $banner ? 'แก้ไขแบนเนอร์' : 'เพิ่มแบนเนอร์';
require_once __DIR__ . '/includes/sidebar.php';
$flash = getFlashMessage();
?>

<div class="admin-top">
    <h1><?= $banner ? 'แก้ไขแบนเนอร์' : 'เพิ่มแบนเนอร์' ?></h1>
    <a href="banners.php" class="btn btn-outline btn-sm">← กลับ</a>
</div>
<?php if ($flash): ?><div class="alert alert-<?= $flash['type'] ?>"><?= e($flash['message']) ?></div><?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="admin-form">
    <div class="form-group">
        <label><strong>🔴 อัปโหลดรูป (บังคับ)</strong></label>
        <input type="file" name="image_file" accept="image/*" id="image_file" required>
        <small style="color: var(--text-secondary);">jpg, png, gif, webp (สูงสุด 5MB)</small>
        <?php if ($banner): ?><img src="<?= e($banner['image_url']) ?>" id="image_preview" class="banner-preview" alt=""><?php endif; ?>
    </div>
    <div class="form-group">
        <label>หรือวาง URL รูป</label>
        <input type="url" name="image_url" placeholder="https://..." value="<?= e($banner['image_url'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>ลิงก์ (เปิด tab ใหม่)</label>
        <input type="url" name="link_url" value="<?= e($banner['link_url'] ?? 'https://example.com') ?>">
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>ตำแหน่ง</label>
            <select name="position">
                <option value="top" <?= ($banner['position'] ?? '') === 'top' ? 'selected' : '' ?>>บนสุด (728x90)</option>
                <option value="bottom" <?= ($banner['position'] ?? '') === 'bottom' ? 'selected' : '' ?>>ล่างสุด (728x90)</option>
                <option value="left" <?= ($banner['position'] ?? '') === 'left' ? 'selected' : '' ?>>ซ้าย (160x600)</option>
                <option value="right" <?= ($banner['position'] ?? '') === 'right' ? 'selected' : '' ?>>ขวา (160x600)</option>
            </select>
        </div>
        <div class="form-group">
            <label>วันหมดอายุ (ปล่อยว่างเพื่อไม่มีวันสิ้นสุด)</label>
            <input type="date" name="expire_date" value="<?= e($banner['expire_date'] ?? '') ?>">
        </div>
    </div>
    <div class="form-group">
        <label>
            <input type="checkbox" name="active" value="1" <?= ($banner['active'] ?? 1) ? 'checked' : '' ?>>
            <strong>✓ เปิดใช้งาน</strong> (ต้องติ๊กเพื่อให้แบนเนอร์ขึ้นหน้าเว็บ)
        </label>
    </div>
    <div class="form-group">
        <small style="color: var(--text-secondary); line-height: 1.6;">
            📋 <strong>วิธีการ:</strong><br>
            1. เลือกรูป (จำเป็น)<br>
            2. ใส่ลิงก์ (ทำให้แบนเนอร์เป็นปุ่มได้)<br>
            3. เลือกตำแหน่ง (เลือก 1 ใน 4 ตำแหน่ง)<br>
            4. ติ๊ก "เปิดใช้งาน" (บังคับ)<br>
            5. กด "บันทึก"
        </small>
    </div>
    <button type="submit" class="btn btn-primary">บันทึก</button>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
