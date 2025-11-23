<?php
// Simple script to generate a circular transparent favicon PNG from existing logo.
// Usage (from project root): php scripts/generate_favicon_circle.php

function output($msg) { echo $msg."\n"; }

$publicDir = __DIR__ . '/../public';
$candidates = [
    $publicDir . '/hmblsc-logo.png',
    $publicDir . '/hmblsc-logo.jpg',
];

$sourcePath = null;
foreach ($candidates as $c) {
    if (file_exists($c)) { $sourcePath = $c; break; }
}

if (!$sourcePath) {
    output('No source logo found (expected hmblsc-logo.png or hmblsc-logo.jpg in public/)');
    exit(1);
}

$ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
switch ($ext) {
    case 'png':
        $src = imagecreatefrompng($sourcePath); break;
    case 'jpg':
    case 'jpeg':
        $src = imagecreatefromjpeg($sourcePath); break;
    default:
        output('Unsupported source extension: ' . $ext);
        exit(1);
}

if (!$src) { output('Failed to load source image.'); exit(1); }

$width = imagesx($src);
$height = imagesy($src);
$size = min($width, $height); // square crop

// Center crop to square if needed
$offsetX = (int)(($width - $size) / 2);
$offsetY = (int)(($height - $size) / 2);

$square = imagecreatetruecolor($size, $size);
imagealphablending($square, true);
imagesavealpha($square, true);
$transparent = imagecolorallocatealpha($square, 0, 0, 0, 127);
imagefilledrectangle($square, 0, 0, $size, $size, $transparent);
imagecopy($square, $src, 0, 0, $offsetX, $offsetY, $size, $size);

// Create circular mask
$circle = imagecreatetruecolor($size, $size);
imagealphablending($circle, false);
imagesavealpha($circle, true);
$transparentCircle = imagecolorallocatealpha($circle, 0, 0, 0, 127);
imagefilledrectangle($circle, 0, 0, $size, $size, $transparentCircle);
$white = imagecolorallocatealpha($circle, 255, 255, 255, 0);
imagefilledellipse($circle, (int)($size/2), (int)($size/2), $size, $size, $white);

// Apply mask: keep pixels inside circle, make outside transparent
for ($y = 0; $y < $size; $y++) {
    for ($x = 0; $x < $size; $x++) {
        $alpha = (imagecolorat($circle, $x, $y) & 0xFF000000) === 0 ? 0 : 127; // inside circle alpha=0 else 127
        if ($alpha === 127) {
            // outside circle
            imagesetpixel($square, $x, $y, $transparent);
        }
    }
}

$destPath = $publicDir . '/hmblsc-logo-circle.png';
if (imagepng($square, $destPath)) {
    output('Generated circular favicon: ' . $destPath);
    output('Add/update <link rel="icon" href="' . basename($destPath) . '"> in your layouts (already handled if conditional logic exists).');
} else {
    output('Failed to save circular favicon.');
    exit(1);
}

imagedestroy($src);
imagedestroy($square);
imagedestroy($circle);

output('Done.');