<?php
/**
 * Generate placeholder banner images
 * Usage: http://localhost:8080/placeholder.php?w=728&h=90&text=Banner
 */

// ตรวจสอบ cache
if (!empty($_GET['w']) && !empty($_GET['h'])) {
    header('Cache-Control: public, max-age=31536000'); // cache 1 ปี
}

$width = (int)($_GET['w'] ?? 728);
$height = (int)($_GET['h'] ?? 90);
$text = $_GET['text'] ?? 'Banner';
$bg = $_GET['bg'] ?? '1a1a2e';
$fg = $_GET['fg'] ?? '00d4ff';

// สร้างรูป
$image = imagecreatetruecolor($width, $height);

// Parse colors
$bgColor = imagecolorallocate($image, 
    hexdec(substr($bg, 0, 2)), 
    hexdec(substr($bg, 2, 2)), 
    hexdec(substr($bg, 4, 2))
);

$fgColor = imagecolorallocate($image, 
    hexdec(substr($fg, 0, 2)), 
    hexdec(substr($fg, 2, 2)), 
    hexdec(substr($fg, 4, 2))
);

// Fill background
imagefilledrectangle($image, 0, 0, $width, $height, $bgColor);

// Add border
$borderColor = imagecolorallocate($image, 
    hexdec(substr($fg, 0, 2)) / 2, 
    hexdec(substr($fg, 2, 2)) / 2, 
    hexdec(substr($fg, 4, 2)) / 2
);
imagerectangle($image, 0, 0, $width - 1, $height - 1, $borderColor);

// Add text
$fontsize = 5; // 1-5 built-in font size
$textWidth = strlen($text) * imagefontwidth($fontsize);
$textHeight = imagefontheight($fontsize);
$x = max(0, ($width - $textWidth) / 2);
$y = max(0, ($height - $textHeight) / 2);

imagestring($image, $fontsize, $x, $y, $text, $fgColor);

// Output
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
