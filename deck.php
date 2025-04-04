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

// Récupérer les decks de l'utilisateur connecté
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM decks WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$user_id]);
$decks = $stmt->fetchAll();

// Récupérer uniquement les cartes achetées par l'utilisateur
$query_cards = "SELECT cards.*, classe.icone
                FROM cards 
                INNER JOIN classe ON cards.id_class = classe.id_class
                INNER JOIN purshased_cards ON cards.id = purshased_cards.card_id
                WHERE purshased_cards.user_id = ?";
$stmt_cards = $conn->prepare($query_cards);
$stmt_cards->execute([$user_id]);
$cards = $stmt_cards->fetchAll();


// Ajouter un nouveau deck
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_deck'])) {
    // Assurez-vous que le formulaire a bien envoyé les données
    $deck_name = htmlspecialchars($_POST['deck_name']);
    
    // Vérifiez si le nom du deck n'est pas vide
    if (!empty($deck_name)) {
        $query_insert = "INSERT INTO decks (user_id, deck_name) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($query_insert);
        $stmt_insert->execute([$user_id, $deck_name]);

        // Assurez-vous que la redirection a bien lieu après l'exécution de la requête
        header("Location: deck.php"); // Recharger la page après la création du deck
        exit();
    } else {
        echo "<p>Le nom du deck ne peut pas être vide.</p>";
    }
}
?>
<?php include('header.php'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="images/Arcanelogo.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcane | Breaker - Mes Decks</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="transition.css">
    <link rel="stylesheet" href="styles.css" id="theme-link">
    <link rel="stylesheet" href="styles_deck.css">
</head>
<body>
<header class="bg-gray-900 text-white py-4">
<img src="images/ArcaneLogoMain.png" alt="LogoMainArcane" style="width: 8%; height: auto;">

<nav class="mt-4">
        <ul class="flex justify-center space-x-4">
            <li><a href="index.php" class="hover:text-blue-400 nav-link active" >Accueil</a></li>
            <?php if ($user_id): ?>
            <li><a href="javascript:void(0);" onclick="openGameWindow()" class="hover:text-blue-400" id="game-launch">Jouer</a></li>
            <?php endif; ?>
            <li><a href="deck.php" class="hover:text-blue-400">Deck</a></li>
            <li><a href="saison.php" class="hover:text-blue-400">Saison 1</a></li>
            <li><a href="market.php" class="hover:text-blue-400">Boutique</a></li>
            <li><a href="buy_shards.php" class="hover:text-blue-400">Shards</a></li>
            <div class="profile-container flex items-center">
                <?php if ($user_id): ?>
                    <a href="profile.php">
                        <img src="<?php echo $profile_picture; ?>" alt="Photo de profil" class="w-10 h-10 rounded-full mr-3">
                    </a>
                <?php endif; ?>
            </div>
            <div class="menu-links flex items-center">
           
                <?php if ($user_id): ?>
                    <span id="username"class="text-white font-semibold"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <span id="level"class="text-white font-semibold">Lvl: <?php echo htmlspecialchars($_SESSION['level']); ?></span>
                    <li><a href="logout.php" class="hover:text-blue-400">Se déconnecter</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="hover:text-blue-400">Connexion</a></li>
                <?php endif; ?>
                <li><a href="settings.php" class="ml-5 text-xl hover:text-blue-400"><i class="bi bi-gear"></i></a></li>
            </div>
        </ul>
    </nav>
</header>
    <main id="swup" class="transition-fade">
    <div= class="container">
    <section class="decks">
    <h2>Mes Decks</h2>
    <?php if (count($decks) > 0): ?>
    <ul class="deck-list">
        <?php foreach ($decks as $deck): ?>
            <li class="deck-item">
                <div class="deck-card">
                    <h3><?php echo htmlspecialchars($deck['deck_name']); ?></h3>
                    <a href="view_deck.php?deck_id=<?php echo $deck['deck_id']; ?>" class="btn-view">Voir le Deck</a>
                    <!-- Bouton de suppression -->
                    <form action="delete_deck.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce deck ?');">
                        <input type="hidden" name="deck_id" value="<?php echo $deck['deck_id']; ?>">
                        <button type="submit" name="delete_deck" class="btn-delete">Supprimer le Deck</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <p>Vous n'avez encore créé aucun deck. Créez-en un maintenant !</p>
<?php endif; ?>

    <form action="deck.php" method="POST" class="form-create-deck">
        <label for="deck_name">Nom du deck :</label>
        <input type="text" id="deck_name" name="deck_name" required>
        <button type="submit" name="create_deck" class="btn-create">Créer le Deck</button>
    </form>
</section>


<section class="cards">
    <h2>Cartes Disponibles</h2>
    <ul class="card-list">
        <?php foreach ($cards as $card): ?>
            <li class="card-item" data-id="<?php echo htmlspecialchars($card['id']); ?>">
                <div class="card" onclick="openPopup(<?php echo htmlspecialchars($card['id']); ?>, '<?php echo htmlspecialchars($card['name']); ?>')">
                    <div class="class-icon absolute top-0 right-0 p-2">
                    </div>
                    <!-- Face avant de la carte -->
                    <div class="card-front" style="background-image: url('<?php echo htmlspecialchars($card['image']); ?>');">
                        <img src="<?php echo htmlspecialchars($card['icone']); ?>" alt="Classe" class="w-8 h-8">
                        <h4><?php echo htmlspecialchars($card['name']); ?></h4>
                        <!-- L'image est maintenant gérée par le background CSS -->
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
</div>
<!--Popup modale-->
<div id="popup" class="popup">
    <div class="popup-content">
        <span class="close-btn" onclick="closePopup()">&times;</span>
        <h3 id="popup-title">Ajouter la carte à un deck</h3>
        <div id="popup-card-display" class="popup-card-display">
        </div>
        <form action="add_card_to_deck.php" method="POST" onsubmit="return handleCardAddition(event)">
            <input type="hidden" id="popup-card-id" name="card_id">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <label for="deck_select_popup">Choisir un deck :</label>
            <select name="deck_id" id="deck_select_popup" required>
                <?php foreach ($decks as $deck): ?>
                    <option value="<?php echo $deck['deck_id']; ?>"><?php echo htmlspecialchars($deck['deck_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="add_card">Ajouter au Deck</button>
        </form>
    </div>
</div>
</main>
<div class="overlay transition-overlay"></div>
<script>
function openPopup(cardId, cardName) {
    const popup = document.getElementById('popup');
    const popupTitle = document.getElementById('popup-title');
    const popupCardId = document.getElementById('popup-card-id');
    const popupCardDisplay = document.getElementById('popup-card-display');

    // Met à jour les informations de la popup
    popupTitle.textContent = `Ajouter la carte "${cardName}" à un deck`;
    popupCardId.value = cardId;  // ID de la carte sélectionnée

    // Récupère les détails de la carte à afficher (exemple de données dynamiques)
    const card = document.querySelector(`.card-item[data-id='${cardId}']`);
    const cardHTML = card.innerHTML;

    // Affiche la carte dans le modal
    popupCardDisplay.innerHTML = `<div class="card-item-popup">${cardHTML}</div>`;

    // Affiche la popup
    popup.style.display = 'block';
}

// Fonction pour fermer la popup
function closePopup() {
    const popup = document.getElementById('popup');
    popup.style.display = 'none';
}

</script>
<script>
    function openGameWindow() {
        window.open('game.php', 'GameWindow', 'width=800,height=600');  // Ouvre le jeu dans une nouvelle fenêtre
    }
</script>
</body>
</html>
