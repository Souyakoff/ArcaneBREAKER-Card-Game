<?php include('header.php'); ?>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
include 'db_connect.php'; // Assurez-vous que ce fichier contient les informations nécessaires

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Rediriger vers la page de login si non connecté
    exit();
}

// Vérifier si l'ID du deck est passé dans l'URL
if (!isset($_GET['deck_id'])) {
    echo "Deck non trouvé.";
    exit();
}

// Récupérer l'ID du deck depuis l'URL
$deck_id = $_GET['deck_id'];

// Récupérer les informations du deck
$query_deck = "SELECT * FROM decks WHERE deck_id = ? AND user_id = ?";
$stmt_deck = $conn->prepare($query_deck);
$stmt_deck->execute([$deck_id, $_SESSION['user_id']]);
$deck = $stmt_deck->fetch();

// Vérifier si le deck existe et appartient à l'utilisateur
if (!$deck) {
    echo "Ce deck n'existe pas ou vous n'avez pas accès à ce deck.";
    exit();
}

// Récupérer les cartes associées au deck
$query_cards_in_deck = "
    SELECT cards.* 
    FROM cards 
    INNER JOIN deck_cards ON cards.id = deck_cards.card_id 
    WHERE deck_cards.deck_id = ?";
$stmt_cards_in_deck = $conn->prepare($query_cards_in_deck);
$stmt_cards_in_deck->execute([$deck_id]);
$cards_in_deck = $stmt_cards_in_deck->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voir le Deck - Mon jeu de cartes</title>
    <link rel="icon" href="images/Arcane_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="styles.css" id="theme-link"> <!-- Assurez-vous que ce fichier existe -->
    <link rel="stylesheet" href="styles_viewdeck.css">
</head>
<body>
<header>
        <h1>Mes Decks</h1>
        <nav>
            <ul>
               <li><a href="deck.php">Retour</a></li>
                <div class="profile-container" style="display: flex; align-items: center;">
            <?php if ($user_id): ?>
                <a href="profile.php">
                    <img src="<?php echo $profile_picture; ?>" alt="Photo de profil" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                </a>
            <?php endif; ?>
        </div>
        <div class="menu-links" style="display: flex; align-items: center;">
            <?php if ($user_id): ?>
                <li><a href="logout.php">Se déconnecter</a></li>
            <?php else: ?>
                <li><a href="login.php">Connexion</a></li>
            <?php endif; ?>
            <li><a href="settings.php" style="margin-left: 20px;"><i class="bi bi-gear nav-icon"></i></a></li>
        </div>
    </ul>
    </header>

    <div class="container">
        <section class="deck-details">
            <h2>Détails du Deck</h2>
            <p><strong>Nom du Deck :</strong> <?php echo htmlspecialchars($deck['deck_name']); ?></p>
            <?php if (count($cards_in_deck) > 0): ?>
    <section class="cards">
    <h2>Cartes dans ce Deck</h2>
    <ul class="card-list">
        <?php foreach ($cards_in_deck as $card): ?>
            <li class="card-item" data-id="<?php echo htmlspecialchars($card['id']); ?>">
                <div class="card" onclick="openPopup(<?php echo htmlspecialchars($card['id']); ?>, '<?php echo htmlspecialchars($card['name']); ?>')">
                    <!-- Face avant de la carte -->
                    <div class="card-front" style="background-image: url('<?php echo htmlspecialchars($card['image']); ?>');">
                        <h4><?php echo htmlspecialchars($card['name']); ?></h4>
                    </div>
                    <!-- Face arrière de la carte (détails) -->
                    <div class="card-back" style="background-image: url('<?php echo htmlspecialchars($card['city_image']); ?>');">
                        <h4><?php echo htmlspecialchars($card['name']); ?></h4>
                        <p><strong>Points de Vie :</strong> <?php echo htmlspecialchars($card['health_points']); ?></p>
                        <p><strong>Attaque :</strong> <?php echo htmlspecialchars($card['attack']); ?></p>
                        <p><strong>Défense :</strong> <?php echo htmlspecialchars($card['defense']); ?></p>
                        <p><strong>Capacité Spéciale :</strong> <?php echo htmlspecialchars($card['special_ability']); ?></p>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
            <?php else: ?>
                <p>Ce deck ne contient aucune carte pour le moment.</p>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
