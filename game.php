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

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT c.* 
    FROM cards c
    JOIN deck_cards dc ON c.id = dc.card_id
    JOIN decks d ON dc.deck_id = d.deck_id
    WHERE d.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcane Breaker</title>
    <link rel="stylesheet" href="styles.css" id="theme-link">
    <link rel="stylesheet" href="styles_games.css">
</head>
<body>
    <div id="game-container">
        <div id="player-health">
            <p>Vos PV : <span id="player-pv">100</span></p>
        </div>
        <div id="bot-health">
            <p>PV Bot : <span id="bot-pv">100</span></p>
        </div>
        <div id="game-board">
            <div id="player-board"></div>
            <div id="bot-board"></div>
        </div>
        <div id="deck">
            <?php foreach ($cards as $card): ?>
                <div class="card" data-id="<?php echo htmlspecialchars($card['id']); ?>" 
                     data-attack="<?php echo htmlspecialchars($card['attack']); ?>" 
                     data-defense="<?php echo htmlspecialchars($card['defense']); ?>">
                    <img src="<?php echo htmlspecialchars($card['image']); ?>" alt="<?php echo htmlspecialchars($card['name']); ?>">
                    <p><?php echo htmlspecialchars($card['name']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="inventory">
    <h3>Inventaire</h3>
    <div id="items-list">

    </div>
</div>

    </div>

    <script src="assets/font/JS/game.js"></script>
</body>
</html>
