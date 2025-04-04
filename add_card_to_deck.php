<?php
ini_set('display_errors', 1);
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
    header("Location: login.php"); // Rediriger vers la page de login si non connecté
    exit();
}

// Récupérer l'ID de l'utilisateur
$user_id = $_SESSION['user_id'];

// Vérifier si les données du formulaire sont envoyées
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_card'])) {
    $card_id = $_POST['card_id']; // ID de la carte sélectionnée
    $deck_id = $_POST['deck_id']; // ID du deck sélectionné

    // Vérifier si la carte et le deck existent dans la base de données
    $query_check = "SELECT * FROM decks WHERE deck_id = ? AND user_id = ?";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->execute([$deck_id, $user_id]);

    if ($stmt_check->rowCount() > 0) {
        // Ajouter la carte au deck
        $query_add_card = "INSERT INTO deck_cards (deck_id, card_id) VALUES (?, ?)";
        $stmt_add_card = $conn->prepare($query_add_card);
        
        if ($stmt_add_card->execute([$deck_id, $card_id])) {
            // Si l'ajout est réussi, rediriger vers la page du deck
            header("Location: view_deck.php?deck_id=$deck_id");
            exit();
        } else {
            // Si une erreur se produit lors de l'exécution de la requête
            echo "Erreur lors de l'ajout de la carte au deck.";
        }
    } else {
        echo "Le deck sélectionné n'existe pas ou n'appartient pas à cet utilisateur.";
    }
} else {
    // Si les données du formulaire ne sont pas envoyées, rediriger vers la page des decks
    header("Location: deck.php");
    exit();
}
?>
