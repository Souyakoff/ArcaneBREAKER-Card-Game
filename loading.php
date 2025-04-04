<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
require_once 'db_connect.php';
if (!$conn) {
    die("Erreur de connexion à la base de données.");
}


// Récupérer l'ID de l'utilisateur depuis la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Vérifier si un deck est sélectionné
if (!isset($_POST['deck_id'])) {
    header('Location: lobby.php');
    exit();
}

$deck_id = $_POST['deck_id'];
$user_id = $_SESSION['user_id'];

// Récupérer les cartes du deck sélectionné
$sql = "
    SELECT c.*
    FROM cards c
    JOIN deck_cards dc ON c.id = dc.card_id
    WHERE dc.deck_id = :deck_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':deck_id', $deck_id);
$stmt->execute();
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcane - Jeu</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .loading-screen {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #000;
            color: #fff;
            font-size: 24px;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
    <div class="loading-screen" id="loading-screen">
        Chargement en cours...
    </div>

    <script>
        setTimeout(() => {
            document.getElementById('loading-screen').style.display = 'none';
            // Charger le contenu principal
            window.location.href = "game.php";
        }, 2000); // Durée de chargement en millisecondes
    </script>
</body>
</html>
