<?php
require_once __DIR__ . '/../db.php';

session_start();

// Redirect to dashboard if already authenticated
if (isset($_SESSION['usuario_id'])) {
    if (!empty($_SESSION['requiere_cambio_password'])) {
        app_redirect('/cambiar_password.php');
    }

    app_redirect('/dashboard');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF validation
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Solicitud inválida. Intente nuevamente.';
    } else {
        $username = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($username === '' || $password === '') {
            $error = 'Complete todos los campos.';
        } else {
            try {
                $pdo  = getDB();
                ensurePasswordChangeColumn();

                $stmt = $pdo->prepare('SELECT id, usuario, password_hash, requiere_cambio_password FROM usuarios WHERE usuario = ?');
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, $user['password_hash'])) {
                    $requiresPasswordChange = (bool)$user['requiere_cambio_password'];

                    if ($user['usuario'] === 'admin' && password_verify('admin123', $user['password_hash'])) {
                        $requiresPasswordChange = true;
                        $forceChange = $pdo->prepare('UPDATE usuarios SET requiere_cambio_password = 1 WHERE id = ?');
                        $forceChange->execute([$user['id']]);
                    }

                    session_regenerate_id(true);
                    $_SESSION['usuario_id']   = $user['id'];
                    $_SESSION['usuario_nombre'] = $user['usuario'];
                    $_SESSION['requiere_cambio_password'] = $requiresPasswordChange;

                    if ($_SESSION['requiere_cambio_password']) {
                        app_redirect('/cambiar_password.php');
                    }

                    app_redirect('/dashboard');
                }

                $error = 'Usuario o contraseña incorrectos.';
            } catch (PDOException $e) {
                $error = 'Error de conexión. Intente nuevamente.';
            }
        }
    }
}

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — Blog Personal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= app_url('/assets/css/style.css') ?>">
</head>
<body class="login-body">

<main class="login-wrapper">
    <div class="login-panel">
        <a href="<?= app_url('/') ?>" class="login-back">&larr; Volver al inicio</a>
        <h1 class="login-title">Iniciar sesión</h1>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= app_url('/login/') ?>" class="login-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="usuario" class="form-label">Usuario</label>
                <input type="text" id="usuario" name="usuario"
                       class="form-input"
                       placeholder="Tu nombre de usuario"
                       required
                       autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password"
                       class="form-input"
                       placeholder="Tu contraseña"
                       required
                       autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary btn-full">Ingresar</button>
        </form>
    </div>
</main>

</body>
</html>
