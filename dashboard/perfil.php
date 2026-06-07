<?php
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../db.php';

requiere_autenticacion();

// Fetch current perfil data
$perfil = getPerfil();

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Determine status message from GET params
$success = $_GET['success'] ?? '';
$error   = $_GET['error'] ?? '';

$successMessages = [
    'guardado' => 'Perfil guardado correctamente.',
];

$errorMessages = [
    'csrf'            => 'Error de validación. Intente nuevamente.',
    'db'              => 'Error de base de datos. Intente nuevamente.',
    'nombre'          => 'El nombre completo es obligatorio.',
    'nombre_max'      => 'El nombre completo no puede superar los 150 caracteres.',
    'descripcion'     => 'La descripción del hero es obligatoria.',
    'legajo_max'      => 'El legajo no puede superar los 20 caracteres.',
    'ubicacion_max'   => 'La ubicación no puede superar los 100 caracteres.',
    'github_invalido' => 'La URL de GitHub no es válida.',
    'linkedin_invalido' => 'La URL de LinkedIn no es válida.',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil — Blog Personal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="dashboard-body">

<nav class="navbar dashboard-nav">
    <div class="container nav-container">
        <a href="/dashboard" class="nav-logo" aria-label="Volver al panel">RZ</a>
        <div class="nav-links">
            <a href="/" class="nav-link-icon" target="_blank">Ver sitio</a>
            <a href="/logout.php" class="nav-link-icon nav-logout">Cerrar sesión</a>
        </div>
    </div>
</nav>

<main class="dashboard-main container">

    <a href="/dashboard" class="btn btn-secondary btn-sm" style="margin-bottom: 1rem;">&larr; Volver al panel</a>
    <h1 class="dashboard-headline">Editar Perfil</h1>

    <!-- ===== Status Messages ===== -->
    <?php if ($success !== '' && isset($successMessages[$success])): ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($successMessages[$success], ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php elseif ($error !== ''): ?>
        <?php
        $errorKeys = explode(',', $error);
        foreach ($errorKeys as $ek):
            if (isset($errorMessages[$ek])):
        ?>
            <div class="alert alert-error" role="alert">
                <?= htmlspecialchars($errorMessages[$ek], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php
            endif;
        endforeach;
        ?>
    <?php endif; ?>

    <section class="dashboard-section">
        <form method="POST" action="/guardar_perfil.php" class="pub-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="nombre_completo" class="form-label">Nombre completo *</label>
                <input type="text" id="nombre_completo" name="nombre_completo"
                       class="form-input"
                       maxlength="150"
                       required
                       value="<?= htmlspecialchars($perfil['nombre_completo'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="hero_descripcion" class="form-label">Descripción del hero *</label>
                <textarea id="hero_descripcion" name="hero_descripcion"
                          class="form-textarea"
                          rows="3"
                          required><?= htmlspecialchars($perfil['hero_descripcion'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-group">
                <label for="sobre_mi_texto" class="form-label">Texto "Sobre mí"</label>
                <textarea id="sobre_mi_texto" name="sobre_mi_texto"
                          class="form-textarea"
                          rows="5"><?= htmlspecialchars($perfil['sobre_mi_texto'], ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="form-group">
                <label for="legajo" class="form-label">Legajo</label>
                <input type="text" id="legajo" name="legajo"
                       class="form-input"
                       maxlength="20"
                       value="<?= htmlspecialchars($perfil['legajo'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="ubicacion" class="form-label">Ubicación</label>
                <input type="text" id="ubicacion" name="ubicacion"
                       class="form-input"
                       maxlength="100"
                       value="<?= htmlspecialchars($perfil['ubicacion'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="github_url" class="form-label">URL de GitHub</label>
                <input type="url" id="github_url" name="github_url"
                       class="form-input"
                       maxlength="255"
                       placeholder="https://github.com/usuario"
                       value="<?= htmlspecialchars($perfil['github_url'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label for="linkedin_url" class="form-label">URL de LinkedIn</label>
                <input type="url" id="linkedin_url" name="linkedin_url"
                       class="form-input"
                       maxlength="255"
                       placeholder="https://linkedin.com/in/usuario"
                       value="<?= htmlspecialchars($perfil['linkedin_url'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                <a href="/dashboard" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </section>

</main>

</body>
</html>
