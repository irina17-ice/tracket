<?php
header('Content-Type: application/json');
require_once '../config/constants.php';
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    echo json_encode(['success' => false, 'message' => 'Accès interdit.']);
    exit;
}

$student_id = $_SESSION['user_id'];
$evaluation_id = intval($_POST['evaluation_id'] ?? 0);
$answers = $_POST['answers'] ?? []; // Tableau [question_id => 'A/B/C/D']

// 1. Récupérer l'évaluation et le module associé
$stmt = $pdo->prepare("SELECT * FROM evaluations WHERE id = ?");
$stmt->execute([$evaluation_id]);
$evaluation = $stmt->fetch();

if (!$evaluation) {
    echo json_encode(['success' => false, 'message' => 'Évaluation introuvable.']);
    exit;
}

$module_id = $evaluation['module_id'];

// 2. Compter le nombre de tentatives passées
$stmt = $pdo->prepare("SELECT COUNT(*) FROM evaluation_results WHERE student_id = ? AND evaluation_id = ?");
$stmt->execute([$student_id, $evaluation_id]);
$attempts = $stmt->fetchColumn();

if ($attempts >= 3) {
    echo json_encode(['success' => false, 'message' => 'Vous avez épuisé vos 3 tentatives pour ce module.']);
    exit;
}

$current_attempt = $attempts + 1;

// 3. Calculer le score
$stmt = $pdo->prepare("SELECT id, correct_option FROM evaluation_questions WHERE evaluation_id = ?");
$stmt->execute([$evaluation_id]);
$questions = $stmt->fetchAll();

$total_questions = count($questions);
$correct_answers = 0;

foreach ($questions as $q) {
    $q_id = $q['id'];
    if (isset($answers[$q_id]) && $answers[$q_id] === $q['correct_option']) {
        $correct_answers++;
    }
}

$score_percent = ($total_questions > 0) ? round(($correct_answers / $total_questions) * 100) : 0;
$passed = ($score_percent >= $evaluation['passing_score']) ? 1 : 0;

// 4. Enregistrer le résultat
$stmt = $pdo->prepare("INSERT INTO evaluation_results (student_id, evaluation_id, score, passed, attempt_number) VALUES (?, ?, ?, ?, ?)");
$stmt->execute([$student_id, $evaluation_id, $score_percent, $passed, $current_attempt]);

$response = [
    'success' => true,
    'score' => $score_percent,
    'passed' => (bool)$passed,
    'attempt_number' => $current_attempt
];

if ($passed) {
    // Valider la progression du module à 100%
    $stmt = $pdo->prepare("INSERT INTO module_progress (student_id, module_id, progress_percent, is_completed) 
                           VALUES (?, ?, 100, 1) 
                           ON DUPLICATE KEY UPDATE progress_percent = 100, is_completed = 1");
    $stmt->execute([$student_id, $module_id]);
    
    // Logique de déclenchement du certificat (Appel interne Dompdf ou script dédié)
    $response['message'] = "Félicitations ! Vous avez validé ce module. Votre certificat est disponible.";
} else {
    if ($current_attempt >= 3) {
        // RÈGLE MÉTIER : 3 échecs -> Retour à la première leçon du module
        $stmt = $pdo->prepare("SELECT l.id FROM lessons l 
                               JOIN courses c ON l.course_id = c.id 
                               WHERE c.module_id = ? ORDER BY c.sort_order ASC, l.sort_order ASC LIMIT 1");
        $stmt->execute([$module_id]);
        $first_lesson_id = $stmt->fetchColumn();
        
        // Réinitialiser la progression du module
        $stmt = $pdo->prepare("INSERT INTO module_progress (student_id, module_id, progress_percent, is_completed) 
                               VALUES (?, ?, 0, 0) 
                               ON DUPLICATE KEY UPDATE progress_percent = 0, is_completed = 0");
        $stmt->execute([$student_id, $module_id]);

        $response['message'] = "3ème échec. Votre progression a été réinitialisée. Vous devez reprendre à la première leçon.";
        $response['redirect_lesson'] = $first_lesson_id;
    } else {
        $remaining = 3 - $current_attempt;
        $response['message'] = "Échec de l'évaluation. Il vous reste {$remaining} tentative(s).";
    }
}

echo json_encode($response);