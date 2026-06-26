<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

/**
 * ImageService
 *
 * Centralized image upload, resize, and delete service.
 *
 * Improvements:
 * - Lower memory usage by re-reading the file per operation instead of cloning many copies
 * - SVG files are stored directly without Intervention processing
 * - Deletes share image variant too
 * - Keeps the same filename pattern currently used in the project: time().ext
 */
class ImageService
{
    /** @var array Allowed MIME types for uploads */
    protected array $allowedMimes = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/svg+xml',
    ];

    /**
     * Store uploaded image in multiple sizes.
     *
     * Output:
     * - images/filename.webp
     * - images/filename_share.jpg
     * - thumb_400/filename.webp
     * - thumb_100/filename.webp
     *
     * For HowWeHelp type:
     * - use contain() instead of cover() for thumbnails
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $type
     * @return string|null
     */
    public function store($file, string $type = ''): ?string
    {
        // Reject unsupported MIME types early
        if (! in_array($file->getMimeType(), $this->allowedMimes)) {
            return null;
        }

        // Optional safety net for large images
        ini_set('memory_limit', '256M');

        $disk = Storage::disk('images');

        // Ensure target directories exist
        $disk->makeDirectory('images');
        $disk->makeDirectory('thumb_400');
        $disk->makeDirectory('thumb_100');

        $mime = $file->getMimeType();
        $path = $file->getRealPath();

        // Keep the same naming style already used in your project
        $baseName   = (string) time();
        $isLogoType = ($type === 'HowWeHelp');

        /**
         * SVG handling:
         * Do not process with GD/Intervention.
         * Store directly as-is.
         */
        if ($mime === 'image/svg+xml') {
            $fileName = $baseName . '.svg';

            $disk->put('images/' . $fileName, file_get_contents($path));

            return $fileName;
        }

        // Optional dimension guard for extremely large images
        $imageInfo = @getimagesize($path);
        if ($imageInfo && isset($imageInfo[0], $imageInfo[1])) {
            $width  = $imageInfo[0];
            $height = $imageInfo[1];

            // Reject absurdly large images that may crash GD
            if ($width > 8000 || $height > 8000) {
                return null;
            }
        }

        $manager  = new ImageManager(new Driver());
        $fileName = $baseName . '.webp';

        // ── Full-size image (max width 1200) ────────────────────────────────
        $original = $manager->read($path);
        $original->scale(width: 1200);

        $disk->put(
            'images/' . $fileName,
            (string) $original->toWebp(90)
        );

        unset($original);

        // ── Social/share image 1200x630 JPG ─────────────────────────────────
        $share = $manager->read($path);
        $share->cover(1200, 630);

        $disk->put(
            'images/' . str_replace('.webp', '_share.jpg', $fileName),
            (string) $share->toJpeg(90)
        );

        unset($share);

        // ── Thumbnail 400x400 ────────────────────────────────────────────────
        $thumb400 = $manager->read($path);

        if ($isLogoType) {
            // Good for logos / transparent assets
            $thumb400->trim()->contain(400, 400);

            $disk->put(
                'thumb_400/' . $fileName,
                (string) $thumb400->toWebp(100)
            );
        } else {
            $thumb400->cover(400, 400);

            $disk->put(
                'thumb_400/' . $fileName,
                (string) $thumb400->toWebp(85)
            );
        }

        unset($thumb400);

        // ── Thumbnail 100x100 ────────────────────────────────────────────────
        $thumb100 = $manager->read($path);

        if ($isLogoType) {
            $thumb100->trim()->contain(100, 100);

            $disk->put(
                'thumb_100/' . $fileName,
                (string) $thumb100->toWebp(100)
            );
        } else {
            $thumb100->cover(100, 100);

            $disk->put(
                'thumb_100/' . $fileName,
                (string) $thumb100->toWebp(85)
            );
        }

        unset($thumb100);

        // Ask PHP to free memory cycles aggressively
        gc_collect_cycles();

        return $fileName;
    }

    /**
     * Delete all stored variants of an image.
     *
     * Supported:
     * - images/file.webp
     * - images/file_share.jpg
     * - thumb_400/file.webp
     * - thumb_100/file.webp
     * - images/file.svg
     *
     * @param string|null $fileName
     * @return void
     */
    public function delete(string $fileName = null): void
    {
        if (empty($fileName)) {
            return;
        }

        $deletePaths = [
            'images/' . $fileName,
            'thumb_400/' . $fileName,
            'thumb_100/' . $fileName,
        ];

        // If original is webp, also delete the generated share jpg
        if (str_ends_with($fileName, '.webp')) {
            $deletePaths[] = 'images/' . str_replace('.webp', '_share.jpg', $fileName);
        }

        Storage::disk('images')->delete($deletePaths);
    }
}