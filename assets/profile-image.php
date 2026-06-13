<?php
/**
 * NAP workaround: serve the profile image through PHP.
 *
 * The NAP reverse proxy may block or mishandle direct .png requests.
 * This endpoint delivers the same file with correct Content-Type and
 * cache headers, consistent with assets/style.php and assets/main.php.
 */

$file = __DIR__ . '/img/foto_perfil.png';

if (!is_file($file)) {
    http_response_code(404);
    exit;
}

$etag    = '"' . md5_file($file) . '"';
$lastMod = gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT';

// Conditional-request support (browser / CDN cache revalidation)
if (
    (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) ||
    (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] === $lastMod)
) {
    header('Cache-Control: public, max-age=86400');
    header('ETag: ' . $etag);
    header('Last-Modified: ' . $lastMod);
    http_response_code(304);
    exit;
}

header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');
header('ETag: ' . $etag);
header('Last-Modified: ' . $lastMod);

readfile($file);
