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

$user_id = $_SESSION['user_id'];

// Récupérer les decks de l'utilisateur
$sql = "SELECT * FROM decks WHERE user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcane - Lobby</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="styles_lobby.css">
</head>
<body>
    <header>
        <h1>Arcane Breaker</h1>
    </header>

    <main>
        <h2>Choisissez votre Deck</h2>
        <section class="decks-container">
            <?php foreach ($decks as $deck): ?>
                <div class="deck-card" data-id="<?= $deck['deck_id'] ?>">
                    <h3><?= htmlspecialchars($deck['deck_name']) ?></h3>
                    <p><?= htmlspecialchars($deck['deck_description'] ?? "Description non disponible") ?></p>
                </div>
            <?php endforeach; ?>
        </section>

        <!-- Popup -->
        <div class="deck-popup" id="deck-popup">
            <div class="popup-content">
                <h3 id="popup-deck-name">Nom du deck</h3>
                <ul id="popup-deck-cards">
                    <!-- Les cartes du deck seront ajoutées dynamiquement ici -->
                </ul>
                <button id="select-deck-button" class="choose-deck-button">Choisir ce deck</button>
                <button id="close-popup" class="close-popup-button">Fermer</button>
            </div>
        </div>

        <form action="loading.php" method="post" id="deck-form">
            <input type="hidden" name="deck_id" id="selected-deck-id" required>
        </form>
    </main>

    <footer>
        <p>&copy; 2024 Arcane Card Game. Tous droits réservés.</p>
    </footer>

    <script src="assets/font/JS/lobby.js"></script>
</body>
</html>
