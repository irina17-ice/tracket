<?php
require_once '../config/constants.php';
require_once '../config/database.php';
// Inclure autoload de Dompdf installé via Composer ou manuellement
require_once '../vendor/autoload.php'; 

use Dompdf\Dompdf;
use Dompdf\Options;

if (!isset($_SESSION['user_id'])) {
    die("Non autorisé.");
}

$student_id = intval($_GET['student_id'] ?? 0);
$module_id = intval($_GET['module_id'] ?? 0);

// Vérifier la complétion à 100%
$stmt = $pdo->prepare("SELECT is_completed FROM module_progress WHERE student_id = ? AND module_id = ?");
$stmt->execute([$student_id, $module_id]);
$completed = $stmt->fetchColumn();

if (!$completed) {
    die("Le module n'est pas encore complété à 100%.");
}

// Récupérer les informations complémentaires
$stmt = $pdo->prepare("SELECT u.full_name, m.title as module_title FROM users u, modules m WHERE u.id = ? AND m.id = ?");
$stmt->execute([$student_id, $module_id]);
$info = $stmt->fetch();

$certificate_code = "CERT-" . strtoupper(uniqid());

// Contenu HTML du Certificat stylisé
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: "Helvetica", sans-serif; text-align: center; padding: 50px; border: 10px solid #6B5B95; }
        h1 { color: #6B5B95; font-size: 40px; }
        h2 { color: #2D2D2D; }
        .code { margin-top: 50px; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <h1>Certificat de Réussite</h1>
    <p>Décerné à :</p>
    <h2>' . htmlspecialchars($info['full_name']) . '</h2>
    <p>Pour avoir validé avec succès l\'ensemble des cours du module :</p>
    <h3>' . htmlspecialchars($info['module_title']) . '</h3>
    <br><br>
    <p>Fait le ' . date('d/m/Y') . '</p>
    <div class="code">Code d\'authenticité : ' . $certificate_code . '</div>
</body>
</html>
';

// Initialisation Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Sauvegarde sur le serveur
$filename = "cert_" . $student_id . "_" . $module_id . ".pdf";
$output = $dompdf->output();
file_put_contents(UPLOAD_DIR . "certificates/" . $filename, $output);

// Enregistrer en BDD
$stmt = $pdo->prepare("INSERT INTO certificates (student_id, module_id, certificate_code, file_path) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE file_path=VALUES(file_path)");
$stmt->execute([$student_id, $module_id, $certificate_code, "uploads/certificates/" . $filename]);

// Téléchargement direct
$dompdf->stream($filename, ["Attachment" => true]);