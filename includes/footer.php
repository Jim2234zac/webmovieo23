<?php
$bannersBottom = getBanners('bottom');
if ($bannersBottom):
?>
<div class="banner-bottom">
    <?php foreach ($bannersBottom as $banner): ?>
        <a href="<?= e($banner['link_url']) ?>" target="_blank" rel="noopener noreferrer">
            <img src="<?= e($banner['image_url']) ?>" alt="Banner" width="728" height="90">
        </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<footer class="site-footer">
    <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?> — ดูอนิเมะออนไลน์ ซับไทย พากย์ไทย HD</p>
</footer>

<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<script src="<?= SITE_URL ?>/assets/js/enhancements.js"></script>
</body>
</html>
