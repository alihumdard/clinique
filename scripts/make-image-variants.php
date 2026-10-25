<?php

/**
 * Generates narrow variants of the placeholder images, so a card does not
 * download a hero.
 *
 * After optimise-images.php every placeholder is 1600px wide, which is
 * right for a full-width hero and about four times what a card needs. The
 * same file is used for both — news-caravan is a hero on one page and a
 * card on another — so one width cannot serve both. x-picture emits a
 * srcset over these variants and lets the browser choose.
 *
 * Run with: php scripts/make-image-variants.php [--dry-run] [--force]
 *
 * Idempotent: a variant that already exists and is newer than its source
 * is left alone, so this is safe to run on every deploy.
 */

$dryRun = in_array('--dry-run', $argv, true);
$force = in_array('--force', $argv, true);

$root = dirname(__DIR__).'/public/images/placeholders';

/*
 * 200 covers a 48px sidebar thumbnail at twice the density, 400 a gallery
 * tile, 800 a card or a half-width figure. The 1600 original stays as the
 * largest step, so a hero is unaffected.
 */
$widths = [200, 400, 800];

$quality = ['jpg' => 72, 'jpeg' => 72, 'webp' => 74];

$made = 0;
$skipped = 0;
$bytes = 0;

foreach (glob("{$root}/*.{jpg,jpeg,webp}", GLOB_BRACE) as $path) {
    $name = pathinfo($path, PATHINFO_FILENAME);
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    // A variant is not itself a source.
    if (preg_match('/-(\d{3,4})$/', $name)) {
        continue;
    }

    [$width, $height] = getimagesize($path);

    foreach ($widths as $target) {
        if ($width <= $target) {
            continue;
        }

        $out = "{$root}/{$name}-{$target}.{$extension}";

        if (! $force && is_file($out) && filemtime($out) >= filemtime($path)) {
            $skipped++;

            continue;
        }

        $targetHeight = (int) round($height * ($target / $width));

        if ($dryRun) {
            printf("  would write %s  (%dx%d)\n", basename($out), $target, $targetHeight);
            $made++;

            continue;
        }

        $source = match ($extension) {
            'jpg', 'jpeg' => imagecreatefromjpeg($path),
            'webp' => imagecreatefromwebp($path),
            default => null,
        };

        if (! $source) {
            continue;
        }

        $resized = imagecreatetruecolor($target, $targetHeight);

        if ($extension === 'webp') {
            // Without this a transparent source gets an opaque black
            // background, which GD fills a new canvas with.
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
        }

        imagecopyresampled($resized, $source, 0, 0, 0, 0, $target, $targetHeight, $width, $height);

        match ($extension) {
            'jpg', 'jpeg' => imagejpeg($resized, $out, $quality[$extension]),
            'webp' => imagewebp($resized, $out, $quality['webp']),
        };

        imagedestroy($source);
        imagedestroy($resized);

        $bytes += filesize($out);
        $made++;
    }
}

printf(
    "\n%s %d variants (%d already current), %.1f Mo written\n",
    $dryRun ? 'Would write' : 'Wrote',
    $made,
    $skipped,
    $bytes / 1048576
);
