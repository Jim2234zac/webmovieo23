<?php
require_once __DIR__ . '/functions.php';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$categories = getCategories();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?><?= SITE_NAME ?></title>
    <?php require_once __DIR__ . '/fonts.php'; ?>
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/responsive.css">
</head>
<body>

<header class="site-header">
    <button class="menu-toggle" aria-label="เมนู">☰</button>
    <a href="<?= SITE_URL ?>/index.php" class="logo">
        <span class="logo-icon">🎬</span> <?= SITE_NAME ?>
    </a>

    <ul class="nav-menu">
        <li><a href="<?= SITE_URL ?>/index.php" class="<?= $currentPage === 'index' ? 'active' : '' ?>">หน้าแรก</a></li>
        <li><a href="<?= SITE_URL ?>/index.php?status=ongoing">กำลังฉาย</a></li>
        <li><a href="<?= SITE_URL ?>/index.php?status=completed">จบแล้ว</a></li>
    </ul>

    <form class="search-form" action="<?= SITE_URL ?>/search.php" method="GET" autocomplete="off">
        <span class="search-icon">🔍</span>
        <input type="text" name="q" id="search-input" placeholder="ค้นหาอนิเมะ..." value="<?= e($_GET['q'] ?? '') ?>">
        <div class="search-suggest" id="search-suggest"></div>
    </form>

    <div class="header-actions">
        <?php if (isLoggedIn()): ?>
            <span class="user-greeting">สวัสดี, <?= e($_SESSION['username']) ?></span>
            <?php if (isAdmin()): ?>
                <a href="<?= SITE_URL ?>/admin/index.php" class="btn btn-outline btn-sm">Admin</a>
            <?php endif; ?>
            <a href="<?= SITE_URL ?>/logout.php" class="btn btn-outline btn-sm">ออกจากระบบ</a>
        <?php else: ?>
            <?php if ($currentPage !== 'watch' && $currentPage !== 'index'): ?>
                <a href="<?= SITE_URL ?>/login.php" class="btn btn-primary btn-sm">เข้าสู่ระบบ</a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</header>

<?php
$bannersTop = getBanners('top');
if ($bannersTop):
?>
<div class="banner-top">
    <button class="banner-close" data-close="top" aria-label="ปิดแบนเนอร์">✕</button>
    <?php foreach ($bannersTop as $banner): ?>
        <a href="<?= e($banner['link_url']) ?>" target="_blank" rel="noopener noreferrer">
            <img src="<?= e($banner['image_url']) ?>" alt="Banner" width="728" height="90">
        </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php foreach (['left', 'right'] as $side):
    $banners = getBanners($side);
    if ($banners):
?>
<div class="banner-side <?= $side ?>" id="banner-<?= $side ?>" data-side="<?= $side ?>">
    <button class="banner-close" data-close="<?= $side ?>" aria-label="ปิดแบนเนอร์">✕</button>
    <?php foreach ($banners as $banner): ?>
        <a href="<?= e($banner['link_url']) ?>" target="_blank" rel="noopener noreferrer">
            <img src="<?= e($banner['image_url']) ?>" alt="Banner" width="160" height="600">
        </a>
    <?php endforeach; ?>
</div>
<?php endif; endforeach; ?>

<?php
$bannersBottom = getBanners('bottom');
if ($bannersBottom):
?>
<div class="banner-bottom" id="banner-bottom" data-side="bottom">
    <button class="banner-close" data-close="bottom" aria-label="ปิดแบนเนอร์">✕</button>
    <?php foreach ($bannersBottom as $banner): ?>
        <a href="<?= e($banner['link_url']) ?>" target="_blank" rel="noopener noreferrer">
            <img src="<?= e($banner['image_url']) ?>" alt="Banner" width="728" height="90">
        </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>
