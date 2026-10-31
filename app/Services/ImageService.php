<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class ImageService
{
    /**
     * Convierte y comprime una imagen subida en un Data URI Base64 persistente
     * para que nunca se pierda en servidores con disco efímero (como Render).
     *
     * @param UploadedFile $file
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $quality
     * @return string Data URI (ej: "data:image/webp;base64,...")
     */
    public static function processToBase64(
        UploadedFile $file,
        int $maxWidth = 400,
        int $maxHeight = 400,
        int $quality = 82
    ): string {
        $realPath = $file->getRealPath();

        // Si GD no está disponible, devolver directamente base64 del archivo original
        if (! extension_loaded('gd') || ! file_exists($realPath)) {
            $mime = $file->getMimeType() ?: 'image/jpeg';
            $data = file_get_contents($realPath);
            return 'data:' . $mime . ';base64,' . base64_encode($data);
        }

        try {
            $imageInfo = @getimagesize($realPath);
            if (! $imageInfo) {
                $mime = $file->getMimeType() ?: 'image/jpeg';
                $data = file_get_contents($realPath);
                return 'data:' . $mime . ';base64,' . base64_encode($data);
            }

            [$origWidth, $origHeight, $imageType] = $imageInfo;

            $srcImage = null;
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $srcImage = @imagecreatefromjpeg($realPath);
                    break;
                case IMAGETYPE_PNG:
                    $srcImage = @imagecreatefrompng($realPath);
                    break;
                case IMAGETYPE_WEBP:
                    if (function_exists('imagecreatefromwebp')) {
                        $srcImage = @imagecreatefromwebp($realPath);
                    }
                    break;
                case IMAGETYPE_GIF:
                    $srcImage = @imagecreatefromgif($realPath);
                    break;
            }

            if (! $srcImage) {
                $mime = $file->getMimeType() ?: 'image/jpeg';
                $data = file_get_contents($realPath);
                return 'data:' . $mime . ';base64,' . base64_encode($data);
            }

            // Calcular dimensiones respetando aspect ratio
            $ratio = min($maxWidth / max($origWidth, 1), $maxHeight / max($origHeight, 1));
            if ($ratio < 1) {
                $newWidth = (int) max(round($origWidth * $ratio), 1);
                $newHeight = (int) max(round($origHeight * $ratio), 1);
            } else {
                $newWidth = $origWidth;
                $newHeight = $origHeight;
            }

            $dstImage = imagecreatetruecolor($newWidth, $newHeight);

            // Manejar canales alfa para PNG y WebP
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);

            imagecopyresampled(
                $dstImage,
                $srcImage,
                0, 0, 0, 0,
                $newWidth,
                $newHeight,
                $origWidth,
                $origHeight
            );

            ob_start();
            // Si soporta WebP, usamos WebP por su altísima compresión y calidad
            if (function_exists('imagewebp')) {
                imagewebp($dstImage, null, $quality);
                $outputData = ob_get_clean();
                $outputMime = 'image/webp';
            } else {
                imagejpeg($dstImage, null, $quality);
                $outputData = ob_get_clean();
                $outputMime = 'image/jpeg';
            }

            imagedestroy($srcImage);
            imagedestroy($dstImage);

            if ($outputData) {
                return 'data:' . $outputMime . ';base64,' . base64_encode($outputData);
            }
        } catch (\Throwable $e) {
            // Fallback silencioso en caso de excepción GD
        }

        $mime = $file->getMimeType() ?: 'image/jpeg';
        $data = file_get_contents($realPath);
        return 'data:' . $mime . ';base64,' . base64_encode($data);
    }
}
