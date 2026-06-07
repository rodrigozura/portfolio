<?php
/**
 * Authentication guard.
 *
 * Start or resume a session and verify the user is authenticated.
 * Redirects to /login if the session is invalid or expired.
 *
 * @return void
 */
function requiere_autenticacion(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['usuario_id'])) {
        header('Location: /login');
        exit;
    }
}
