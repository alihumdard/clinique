<?php

/**
 * Resizes the bundled images to the sizes the pages actually display them
 * at, and re-encodes them.
 *
 * Why this exists: the performance plan budgets the homepage at under
 * 500 Ko and inner pages at under 400 Ko. The logo shipped at 1080×1080
 * and 680 Ko, and is on every page three times over — in the header, as
 * the favicon and as the default sharing image. The six partner marks
 * shipped at up to 1536×1024 and 847 Ko each, all six on one page. No
 * amount of caching or compression rescues that; the files have to be the
 * right size.
 *
 * Run with: php scripts/optimise-images.php [--dry-run]
 *
 * Idempotent: an image already at or below its target is left alone, so
 * running it twice does not degrade anything.
 */

$dryRun = in_array('--dry-run', $argv, true);
$root = dirname(__DIR__).'/public/images';

/*
 * Each rule is a glob and the widest the image is ever displayed at,
 * doubled for high-density screens. The logo is square and shown at 48px
 * in the header, but it is also the favicon and the default Open Graph
 * image, so it keeps enough resolution to serve as one.
 */
$rules = [
    ['glob' => 'brand/logo.png', 'width' => 256, 'quality' => 9],
    ['glob' => 'partners/*.png', 'width' => 320, 'quality' => 9],
    ['glob' => 'placeholders/*.jpg', 'width' => 1600, 'quality' => 74],
    ['glob' => 'placeholders/*.webp', 'width' => 1600, 'quality' => 76],
];

$before = 0;
$after = 0;
$touched = 0;

foreach ($rules as $rule) {
    foreach (glob("{$root}/{$rule['glob']}") as $path) {
        $size = filesize($path);
        $before += $size;

        [$width, $height] = getimagesize($path);

        if ($width <= $rule['width']) {
            // Already small enough to display; re-encoding a PNG that is
            // already lean can make it bigger, so leave it.
            $after += $size;
            continue;
        }

        $targetWidth = $rule['width'];
        $targetHeight = (int) round($height * ($targetWidth / $width));

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $source = match ($extension) {
            'png' => imagecreatefrompng($path),
            'jpg', 'jpeg' => imagecreatefromjpeg($path),
            'webp' => imagecreatefromwebp($path),
            default => null,
        };

        if (! $source) {
            $after += $size;
            continue;
        }

        $resized = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($extension === 'png' || $extension === 'webp') {
            // Without this a transparent logo gets a black background.
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            imagefill($resized, 0, 0, imagecolorallocatealpha($resized, 0, 0, 0, 127));
        }

        imagecopyresampled($resized, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $label = str_replace($root.'/', '', $path);

        if ($dryRun) {
            printf("  would resize %-46s %dx%d -> %dx%d  (%d Ko)\n",
                $label, $width, $height, $targetWidth, $targetHeight, $size / 1024);
            $after += $size;
            imagedestroy($source);
            imagedestroy($resized);

            continue;
        }

        match ($extension) {
            'png' => imagepng($resized, $path, $rule['quality']),
            'jpg', 'jpeg' => imagejpeg($resized, $path, $rule['quality']),
            'webp' => imagewebp($resized, $path, $rule['quality']),
        };

        imagedestroy($source);
        imagedestroy($resized);

        clearstatcache(true, $path);
        $newSize = filesize($path);
        $after += $newSize;
        $touched++;

        printf("  %-46s %dx%d -> %dx%d   %d Ko -> %d Ko\n",
            $label, $width, $height, $targetWidth, $targetHeight,
            $size / 1024, $newSize / 1024);
    }
}

printf(
    "\n%s: %d files, %.1f Mo -> %.1f Mo (%.0f%% smaller)\n",
    $dryRun ? 'Would change' : 'Changed',
    $touched,
    $before / 1048576,
    $after / 1048576,
    $before > 0 ? (1 - $after / $before) * 100 : 0
);
