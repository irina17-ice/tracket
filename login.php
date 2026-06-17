<?php
require_once 'config/constants.php';
if (isset($_SESSION['user_id'])) {
    header('Location: ' . $_SESSION['role'] . '/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - trAcket</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">
    <div class="login-container">
        <h2>Connexion à <span class="brand">trAcket</span></h2>
        <div id="login-message" class="alert hidden"></div>
        <form id="login-form">
            <div class="form-group">
                <label for="username">Identifiant (Ex: 20T1234, TCH-001)</label>
                <input type="text" id="username" name="username" required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn-primary">Se connecter</button>
        </form>
    </div>

    <script>
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const msgDiv = document.getElementById('login-message');

        fetch('ajax/auth_process.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                msgDiv.className = "alert alert-success";
                msgDiv.textContent = "Connexion réussie ! Redirection...";
                msgDiv.classList.remove('hidden');
                setTimeout(() => { window.location.href = data.redirect; }, 1000);
            } else {
                msgDiv.className = "alert alert-danger";
                msgDiv.textContent = data.message;
                msgDiv.classList.remove('hidden');
            }
        });
    });
    </script>
</body>
</html>