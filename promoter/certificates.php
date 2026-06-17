<?php
require_once '../config/constants.php';
require_once '../config/database.php';

// Sécurité : Vérification stricte du rôle de Promoteur
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'promoter') {
    header('Location: ../login.php');
    exit;
}

// Récupération de tous les certificats émis avec les détails de l'étudiant et du module
$query = "SELECT c.id, c.certificate_code, c.file_path, c.issued_at, 
                 u.full_name as student_name, u.username as student_id_code,
                 m.title as module_title 
          FROM certificates c
          JOIN users u ON c.student_id = u.id
          JOIN modules m ON c.module_id = m.id
          ORDER BY c.issued_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$certificates = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Certificats - trAcket</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Styles spécifiques à la table de gestion */
        .table-container {
            background: var(--light-card);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-top: 1.5rem;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th, td {
            padding: 1rem;
            border-bottom: 1px solid #E0E0E0;
        }
        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
        }
        tr:hover {
            background-color: #F9F9F9;
        }
        .badge-code {
            background-color: var(--dark-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-family: monospace;
            font-size: 0.9rem;
        }
        .btn-action {
            background-color: var(--success-color);
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-block;
            transition: opacity 0.2s;
        }
        .btn-action:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <aside class="sidebar">
            <h2 style="color: var(--primary-color); margin-bottom: 2rem;">trAcket</h2>
            <nav>
                <p style="color: #AAA; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 0.5rem;">Menu Directeur</p>
                <ul style="list-style: none;">
                    <li style="margin-bottom: 1rem;"><a href="dashboard.php" style="color: white; text-decoration: none;">Tableau de bord</a></li>
                    <li style="margin-bottom: 1rem;"><a href="students_list.php" style="color: white; text-decoration: none;">Gestion Étudiants</a></li>
                    <li style="margin-bottom: 1rem;"><a href="certificates.php" style="color: var(--primary-color); font-weight: bold; text-decoration: none;">Certificats émis</a></li>
                    <li style="margin-top: 3rem;"><a href="../logout.php" style="color: #FF6B6B; text-decoration: none;">Déconnexion</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <div>
                    <h1>Gestion des Certificats</h1>
                    <p style="color: #666;">Supervision globale des titres et certifications délivrés automatiquement à 100% de progression.</p>
                </div>
                <div style="text-align: right;">
                    <strong><?php echo htmlspecialchars($_SESSION['full_name']); ?></strong><br>
                    <span style="font-size: 0.85rem; color: var(--primary-color);">Promoteur</span>
                </div>
            </header>

            <div id="alert-box" class="alert hidden"></div>

            <div class="table-container">
                <?php if (empty($certificates)): ?>
                    <p style="text-align: center; color: #888; padding: 2rem;">Aucun certificat n'a été généré pour le moment.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Code Unique</th>
                                <th>Étudiant</th>
                                <th>Module validé</th>
                                <th>Date d'émission</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($certificates as $cert): ?>
                                <tr id="row-<?php echo $cert['id']; ?>">
                                    <td><span class="badge-code"><?php echo htmlspecialchars($cert['certificate_code']); ?></span></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($cert['student_name']); ?></strong><br>
                                        <small style="color: #666;"><?php echo htmlspecialchars($cert['student_id_code']); ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($cert['module_title']); ?></td>
                                    <td><?php echo date('d/m/Y à H:i', strtotime($cert['issued_at'])); ?></td>
                                    <td>
                                        <a href="../<?php echo htmlspecialchars($cert['file_path']); ?>" class="btn-action" target="_blank" download>
                                            Télécharger le PDF
                                        </a>
                                    </td>
                                </tr>
                            <?php endindex; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>