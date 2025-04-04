<?php
// Démarrer la session et récupérer les informations de l'utilisateur
// LOG !!!!
// Récupérer l'ID de l'utilisateur depuis la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
// LOG !!!!
$user_id = $_SESSION['user_id'] ?? null;
$user = null;


if ($user_id) {
    // Récupérer les informations de l'utilisateur
    // LOG !!!!
    include('db_connect.php');
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $_SESSION['username'] = $user['username'];
    $_SESSION['level'] = $user['level'];
    // Définir l'image de profil
    $profile_picture = (!empty($user['profile_picture']) && file_exists($user['profile_picture'])) 
        ? $user['profile_picture'] 
        : 'images/default_profile_picture.jpg'; // Image par défaut
}
?>
<script src="assets/font/JS/theme.js"></script>