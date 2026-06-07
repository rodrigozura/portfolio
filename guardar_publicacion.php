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
    header('Location: /dashboard');
    exit;
}

// CSRF validation
if (!isset($_POST['csrf_token'], $_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    header('Location: /dashboard?error=csrf');
    exit;
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
    header('Location: /dashboard?error=' . implode(',', $errors));
    exit;
}

try {
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'INSERT INTO publicaciones (titulo, contenido, categoria) VALUES (?, ?, ?)'
    );
    $stmt->execute([$titulo, $contenido, $categoria !== '' ? $categoria : null]);

    header('Location: /dashboard?success=creado');
    exit;
} catch (PDOException $e) {
    header('Location: /dashboard?error=db');
    exit;
}
