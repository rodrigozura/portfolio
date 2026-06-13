<?php
/**
 * NAP workaround: serve main.js through PHP.
 *
 * The NAP reverse proxy blocks requests with .css / .js extensions.
 * This endpoint delivers the same file with correct headers so the
 * public deployment works without touching internal Apache config.
 */

$file = __DIR__ . '/js/main.js';

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
    http_response_code(304);
    exit;
}

header('Content-Type: application/javascript; charset=UTF-8');
header('Cache-Control: public, max-age=86400');
header('ETag: ' . $etag);
header('Last-Modified: ' . $lastMod);

readfile($file);
