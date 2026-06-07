<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/db.php';

requiere_autenticacion(true);

if (empty($_SESSION['requiere_cambio_password'])) {
    header('Location: /dashboard');
    exit;
}

$error = '';

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token'])
        || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Solicitud inválida. Intente nuevamente.';
    } else {
        $currentPassword = $_POST['password_actual'] ?? '';
        $newPassword     = $_POST['password_nueva'] ?? '';
        $confirmPassword = $_POST['password_confirmacion'] ?? '';

        if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {
            $error = 'Complete todos los campos.';
        } elseif (strlen($newPassword) < 8) {
            $error = 'La nueva contraseña debe tener al menos 8 caracteres.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'La confirmación no coincide con la nueva contraseña.';
        } else {
            try {
                ensurePasswordChangeColumn();

                $pdo  = getDB();
                $stmt = $pdo->prepare('SELECT password_hash FROM usuarios WHERE id = ?');
                $stmt->execute([$_SESSION['usuario_id']]);
                $user = $stmt->fetch();

                if (!$user || !password_verify($currentPassword, $user['password_hash'])) {
                    $error = 'La contraseña actual es incorrecta.';
                } elseif (password_verify($newPassword, $user['password_hash'])) {
                    $error = 'La nueva contraseña debe ser diferente a la contraseña actual.';
                } else {
                    $hash   = password_hash($newPassword, PASSWORD_DEFAULT);
                    $update = $pdo->prepare('UPDATE usuarios SET password_hash = ?, requiere_cambio_password = 0 WHERE id = ?');
                    $update->execute([$hash, $_SESSION['usuario_id']]);

                    $_SESSION['requiere_cambio_password'] = false;
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                    header('Location: /dashboard?success=password_changed');
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'Error de base de datos. Intente nuevamente.';
            }
        }
    }
}
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contraseña — Blog Personal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="login-body">

<main class="login-wrapper">
    <div class="login-panel">
        <h1 class="login-title">Cambiar contraseña</h1>
        <p class="login-helper">Por seguridad, antes de entrar al panel tenés que reemplazar la contraseña inicial.</p>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/cambiar_password.php" class="login-form" novalidate>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="password_actual" class="form-label">Contraseña actual</label>
                <input type="password" id="password_actual" name="password_actual"
                       class="form-input"
                       required
                       autocomplete="current-password">
            </div>

            <div class="form-group">
                <label for="password_nueva" class="form-label">Nueva contraseña</label>
                <input type="password" id="password_nueva" name="password_nueva"
                       class="form-input"
                       minlength="8"
                       required
                       autocomplete="new-password">
            </div>

            <div class="form-group">
                <label for="password_confirmacion" class="form-label">Confirmar nueva contraseña</label>
                <input type="password" id="password_confirmacion" name="password_confirmacion"
                       class="form-input"
                       minlength="8"
                       required
                       autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary btn-full">Guardar contraseña</button>
        </form>

        <a href="/logout.php" class="login-back">Cerrar sesión</a>
    </div>
</main>

</body>
</html>
