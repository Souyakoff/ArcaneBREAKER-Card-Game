<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
include 'db_connect.php'; // Assurez-vous que ce fichier contient les informations nécessaires

session_start();//LOG !!!

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Vérifier si un deck a été soumis pour suppression
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_deck'])) {
    $deck_id = $_POST['deck_id'];

    // Vérifier que le deck appartient à l'utilisateur connecté
    $user_id = $_SESSION['user_id'];
    $query_check = "SELECT * FROM decks WHERE deck_id = ? AND user_id = ?";
    $stmt_check = $conn->prepare($query_check);
    $stmt_check->execute([$deck_id, $user_id]);
    $deck = $stmt_check->fetch();

    if ($deck) {
        // Supprimer le deck
        $query_delete = "DELETE FROM decks WHERE deck_id = ?";
        $stmt_delete = $conn->prepare($query_delete);
        $stmt_delete->execute([$deck_id]);

        // Redirection après suppression
        header("Location: deck.php"); // Recharger la page des decks
        exit();
    } else {
        echo "<p>Le deck n'existe pas ou vous n'avez pas l'autorisation de le supprimer.</p>";
    }
}
?>
