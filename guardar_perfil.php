<?php
/**
 * Update perfil handler.
 *
 * POST only. Validates CSRF token, sanitizes inputs, and updates the
 * perfil row (id=1) in the database. Redirects to /dashboard/perfil.php
 * with status.
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

requiere_autenticacion();

// Ensure this is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    app_redirect('/dashboard/perfil.php');
}

// CSRF validation
if (!isset($_POST['csrf_token'], $_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    app_redirect('/dashboard/perfil.php?error=csrf');
}

// Validate inputs
$nombre_completo  = trim($_POST['nombre_completo'] ?? '');
$hero_descripcion = trim($_POST['hero_descripcion'] ?? '');
$sobre_mi_texto   = trim($_POST['sobre_mi_texto'] ?? '');
$legajo           = trim($_POST['legajo'] ?? '');
$ubicacion        = trim($_POST['ubicacion'] ?? '');
$github_url       = trim($_POST['github_url'] ?? '');
$linkedin_url     = trim($_POST['linkedin_url'] ?? '');

$errors = [];

if ($nombre_completo === '') {
    $errors[] = 'nombre';
} elseif (mb_strlen($nombre_completo) > 150) {
    $errors[] = 'nombre_max';
}

if ($hero_descripcion === '') {
    $errors[] = 'descripcion';
}

if (mb_strlen($legajo) > 20) {
    $errors[] = 'legajo_max';
}

if (mb_strlen($ubicacion) > 100) {
    $errors[] = 'ubicacion_max';
}

if ($github_url !== '' && filter_var($github_url, FILTER_VALIDATE_URL) === false) {
    $errors[] = 'github_invalido';
}

if ($linkedin_url !== '' && filter_var($linkedin_url, FILTER_VALIDATE_URL) === false) {
    $errors[] = 'linkedin_invalido';
}

if (!empty($errors)) {
    app_redirect('/dashboard/perfil.php?error=' . implode(',', $errors));
}

try {
    $pdo = getDB();

    // Ensure row exists (idempotent upsert)
    $check = $pdo->query('SELECT id FROM perfil LIMIT 1');
    if (!$check->fetch()) {
        $stmt = $pdo->prepare(
            'INSERT INTO perfil (nombre_completo, hero_descripcion, sobre_mi_texto, legajo, ubicacion, github_url, linkedin_url)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
    } else {
        $stmt = $pdo->prepare(
            'UPDATE perfil SET
                nombre_completo = ?, hero_descripcion = ?, sobre_mi_texto = ?,
                legajo = ?, ubicacion = ?, github_url = ?, linkedin_url = ?
             WHERE id = 1'
        );
    }

    $stmt->execute([
        $nombre_completo,
        $hero_descripcion,
        $sobre_mi_texto,
        $legajo !== '' ? $legajo : null,
        $ubicacion !== '' ? $ubicacion : null,
        $github_url !== '' ? $github_url : null,
        $linkedin_url !== '' ? $linkedin_url : null,
    ]);

    app_redirect('/dashboard/perfil.php?success=guardado');
} catch (PDOException $e) {
    app_redirect('/dashboard/perfil.php?error=db');
}
