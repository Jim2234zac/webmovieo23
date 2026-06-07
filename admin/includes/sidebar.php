<?php $adminPage = basename($_SERVER['PHP_SELF'], '.php'); ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?>Admin - <?= SITE_NAME ?></title>
    <?php require_once __DIR__ . '/../../includes/fonts.php'; ?>
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="<?= SITE_URL ?>/admin/index.php" class="admin-brand"><?= SITE_NAME ?></a>
        <nav class="admin-nav">
            <a href="<?= SITE_URL ?>/admin/index.php" class="<?= $adminPage === 'index' ? 'active' : '' ?>">แดชบอร์ด</a>
            <a href="<?= SITE_URL ?>/admin/movies.php" class="<?= in_array($adminPage, ['movies','movies_add','movies_edit']) ? 'active' : '' ?>">จัดการหนัง</a>
            <a href="<?= SITE_URL ?>/admin/banners.php" class="<?= in_array($adminPage, ['banners','banners_add']) ? 'active' : '' ?>">จัดการแบนเนอร์</a>
            <a href="<?= SITE_URL ?>/admin/categories.php" class="<?= $adminPage === 'categories' ? 'active' : '' ?>">จัดการหมวดหมู่</a>
            <a href="<?= SITE_URL ?>/admin/users.php" class="<?= in_array($adminPage, ['users']) ? 'active' : '' ?>">จัดการผู้ใช้</a>
            <a href="<?= SITE_URL ?>/index.php" target="_blank">ดูเว็บไซต์</a>
            <a href="<?= SITE_URL ?>/admin/logout.php">ออกจากระบบ</a>
        </nav>
    </aside>
    <main class="admin-content">
