<?php
// Connexion à la base de données
require_once 'db_connect.php';
if (!$conn) {
    die("Erreur de connexion à la base de données.");
}


// Récupérer l'ID de l'utilisateur depuis la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    die('Accès non autorisé');
}

$userId = $_SESSION['user_id']; // L'ID de l'utilisateur connecté
$cardId = $_POST['card_id']; // L'ID de la carte à acheter
$cardPrice = $_POST['card_price']; // Le prix de la carte (à ajuster en fonction du front-end)

if (!isset($cardId) || !isset($cardPrice) || !is_numeric($cardId) || !is_numeric($cardPrice)) {
    echo('Données invalides.');
}

// Vérification des fonds de l'utilisateur
$stmt = $conn->prepare("SELECT shards FROM users WHERE id = :userId");
$stmt->bindParam(':userId', $userId);
$stmt->execute();
$userFunds = $stmt->fetchColumn();

if ($userFunds >= $cardPrice) {
    // L'utilisateur a suffisamment de fonds pour acheter la carte
    try {
        // Commencer une transaction pour éviter des incohérences en cas d'erreur
        $conn->beginTransaction();

        // Ajouter la carte à la table des cartes achetées
        $stmt = $conn->prepare("INSERT INTO purshased_cards (user_id, card_id, date_achat) VALUES (:userId, :cardId, NOW())");
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':cardId', $cardId);
        $stmt->execute();

        // Retirer le montant du prix de la carte du solde de l'utilisateur
        $stmt = $conn->prepare("UPDATE users SET shards = shards - :cardPrice WHERE id = :userId");
        $stmt->bindParam(':cardPrice', $cardPrice);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        // Commit de la transaction
        $conn->commit();

        echo "Carte achetée avec succès !";

    } catch (Exception $e) {
        // En cas d'erreur, annuler la transaction
        $conn->rollBack();
        echo "Erreur lors de l'achat : " . $e->getMessage();
    }
} else {
    // L'utilisateur n'a pas assez de fonds
    echo "Fonds insuffisants.";
}
?>
