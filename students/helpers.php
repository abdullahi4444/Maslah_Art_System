<?php
// Common helper functions for the Maslax Arts admin project.

/**
 * Sanitize input data by trimming whitespace and stripping HTML tags.
 *
 * @param string|null $value
 * @return string|null
 */
function sanitize(?string $value): ?string
{
    if ($value === null) {
        return null;
    }
    return trim(strip_tags($value));
}

/**
 * Redirect to a given URL and exit.
 *
 * @param string $url
 * @return void
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Handle file upload and return the stored file path or null if no file was uploaded.
 * Ensures the target directory exists and generates a unique filename.
 *
 * @param string $inputName
 * @param string $targetDir
 * @param array $allowedTypes
 * @param int $maxSize
 * @return string|null
 */
function upload_file(string $inputName, string $targetDir, array $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'], int $maxSize = 2097152): ?string
{
    if (!isset($_FILES[$inputName]) || $_FILES[$inputName]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    $file = $_FILES[$inputName];
    if ($file['size'] > $maxSize) {
        return null; // file too large
    }
    if (!in_array($file['type'], $allowedTypes, true)) {
        return null; // invalid file type
    }
    // Ensure target directory exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $uniqueName = uniqid('file_', true) . '.' . $ext;
    $destination = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $uniqueName;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return null;
    }
    // Return a web-relative path (relative to project root) so it can be used in <img src="...">.
    // If $destination is inside the project directory, strip the project dir prefix.
    $projectRoot = __DIR__;
    $rel = str_replace('\\', '/', $destination);
    $proj = str_replace('\\', '/', $projectRoot);
    if (strpos($rel, $proj) === 0) {
        $relativePath = ltrim(substr($rel, strlen($proj)), '/');
        return $relativePath;
    }
    // Fallback: return the filename under the target directory
    return trim(str_replace('\\', '/', $uniqueName), '/');
}

/**
 * CSRF helpers
 */
function csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    $token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="_csrf" value="' . $token . '">';
}

function verify_csrf(?string $token): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // If token wasn't provided, try common locations: POST body or X-CSRF-Token header
    if (empty($token)) {
        $token = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
    }
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Create a resized thumbnail for an image using GD.
 * Returns true on success, false on failure.
 *
 * @param string $srcFsPath Full filesystem path to source image
 * @param string $destFsPath Full filesystem path to destination thumbnail
 * @param int $maxWidth
 * @param int $maxHeight
 * @return bool
 */
function create_image_thumbnail(string $srcFsPath, string $destFsPath, int $maxWidth = 400, int $maxHeight = 300): bool
{
    if (!file_exists($srcFsPath)) {
        return false;
    }
    $info = @getimagesize($srcFsPath);
    if (!$info) return false;
    [$width, $height, $type] = [$info[0], $info[1], $info[2]];
    if ($width <= 0 || $height <= 0) return false;

    // If GD extension is not available, avoid calling imagecreatefrom* functions which are undefined
    if (!function_exists('gd_info')) {
        return false;
    }

    // compute new size preserving aspect ratio
    $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
    $newW = (int)max(1, round($width * $ratio));
    $newH = (int)max(1, round($height * $ratio));

    switch ($type) {
        case IMAGETYPE_JPEG:
            if (!function_exists('imagecreatefromjpeg')) return false;
            $srcImg = @imagecreatefromjpeg($srcFsPath);
            break;
        case IMAGETYPE_PNG:
            if (!function_exists('imagecreatefrompng')) return false;
            $srcImg = @imagecreatefrompng($srcFsPath);
            break;
        case IMAGETYPE_GIF:
            if (!function_exists('imagecreatefromgif')) return false;
            $srcImg = @imagecreatefromgif($srcFsPath);
            break;
        case IMAGETYPE_WEBP:
            if (!function_exists('imagecreatefromwebp')) return false;
            $srcImg = @imagecreatefromwebp($srcFsPath);
            break;
        default:
            return false;
    }
    if (!$srcImg) return false;

    $thumb = imagecreatetruecolor($newW, $newH);
    // preserve transparency for PNG/GIF
    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
        imagecolortransparent($thumb, imagecolorallocatealpha($thumb, 0, 0, 0, 127));
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
    }
    imagecopyresampled($thumb, $srcImg, 0, 0, 0, 0, $newW, $newH, $width, $height);

    // ensure destination directory exists
    $destDir = dirname($destFsPath);
    if (!is_dir($destDir)) mkdir($destDir, 0755, true);

    $saved = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            if (function_exists('imagejpeg')) $saved = imagejpeg($thumb, $destFsPath, 85);
            break;
        case IMAGETYPE_PNG:
            if (function_exists('imagepng')) $saved = imagepng($thumb, $destFsPath);
            break;
        case IMAGETYPE_GIF:
            if (function_exists('imagegif')) $saved = imagegif($thumb, $destFsPath);
            break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagewebp')) $saved = imagewebp($thumb, $destFsPath, 85);
            break;
    }
    imagedestroy($srcImg);
    imagedestroy($thumb);
    return (bool)$saved;
}

/**
 * Simple session flash message helpers. Use set_flash_message() before a
 * redirect and get_flash_message() on the next request to retrieve and clear
 * the message.
 */
function set_flash_message(string $message, string $type = 'success'): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message,
    ];
}

/**
 * Returns array|null with keys 'type' and 'message', and clears the flash.
 */
function get_flash_message(): ?array
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['flash_message'])) {
        return null;
    }
    $m = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
    return $m;
}