<?php
/**
 * Create publication handler.
 *
 * POST only. Validates CSRF token, sanitizes inputs, and inserts a new
 * publication into the database. Redirects to /dashboard with status.
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

requiere_autenticacion();

// Ensure this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    app_redirect('/dashboard');
}

// CSRF validation
if (!isset($_POST['csrf_token'], $_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    app_redirect('/dashboard?error=csrf');
}

// Validate inputs
$titulo    = trim($_POST['titulo'] ?? '');
$contenido = trim($_POST['contenido'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');

$errors = [];

if ($titulo === '') {
    $errors[] = 'titulo';
} elseif (mb_strlen($titulo) > 150) {
    $errors[] = 'titulo_max';
}

if ($contenido === '') {
    $errors[] = 'contenido';
}

if (mb_strlen($categoria) > 80) {
    $errors[] = 'categoria_max';
}

if (!empty($errors)) {
    app_redirect('/dashboard?error=' . implode(',', $errors));
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'INSERT INTO publicaciones (titulo, contenido, categoria) VALUES (?, ?, ?)'
    );
    $stmt->execute([$titulo, $contenido, $categoria !== '' ? $categoria : null]);

    app_redirect('/dashboard?success=creado');
} catch (PDOException $e) {
    app_redirect('/dashboard?error=db');
}
