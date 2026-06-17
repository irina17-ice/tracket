<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/tracket/');
define('UPLOAD_DIR', __DIR__ . '/../uploads/');

// Messages d'erreur génériques
define('MSG_ACCESS_DENIED', 'Accès refusé. Vous n\'avez pas les permissions requises.');