<?php include('header.php');
// Affiche toutes les erreurs PHP
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

if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: login.php");
    exit();
}
$userId = $_SESSION['user_id'];

// Récupération des informations de l'utilisateur
$userQuery = $conn->prepare("SELECT * FROM users WHERE id = :id");
$userQuery->execute(['id' => $userId]);
$user = $userQuery->fetch(PDO::FETCH_ASSOC);

// Vérifier si l'utilisateur existe
if (!$user) {
    die("Utilisateur introuvable");
}

// Récupération des cartes disponibles dans la boutique du jour
$dateSeed = date('Y-m-d'); // Utilise la date du jour pour générer des cartes différentes chaque jour
srand(strtotime($dateSeed)); // Initialise le générateur aléatoire
$dailyCardsQuery = $conn->query("SELECT * FROM cards");
$allCards = $dailyCardsQuery->fetchAll(PDO::FETCH_ASSOC);
shuffle($allCards); // Mélange les cartes
$dailyCards = array_slice($allCards, 0, 14); // Sélectionne les 14 premières cartes mélangées

// Vérifie si la requête est de type POST et si la variable 'card_id' est bien envoyée via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['card_id'])) {
    // Récupère et nettoie l'identifiant de la carte (supprime les espaces inutiles)
    $cardId = intval($_POST['card_id']);

    // Prépare une requête SQL pour récupérer l'ID et le prix de la carte correspondante
    $query = "SELECT id, price FROM cards WHERE id = :id";
    $priceQuery = $conn->prepare($query);
    $priceQuery->execute(['id' => $cardId]); // Exécute la requête avec le paramètre sécurisé
    $card = $priceQuery->fetch(PDO::FETCH_ASSOC); // Récupère le résultat sous forme de tableau associatif

    // Vérifie si la carte existe bien dans la base de données
    if ($card) {
        // Vérifie si l'utilisateur a assez de shards pour acheter la carte
        if ($user['shards'] >= $card['price']) {
            

            // Déduire le coût de la carte du solde en shards de l'utilisateur
            $updateUser = $conn->prepare("UPDATE users SET shards = shards - :price WHERE id = :id");
            $updateUser->execute(['price' => $card['price'], 'id' => $userId]);

            // Insérer l'achat dans la table 'purchased_cards' (ou mettre à jour si la carte est déjà achetée)
            $insertPurchase = $conn->prepare("INSERT INTO purshased_cards (user_id, card_id, quantite) 
                                              VALUES (:user_id, :card_id, 1) 
                                              ON DUPLICATE KEY UPDATE quantite = quantite + 1");
            $insertPurchase->execute(['user_id' => $userId, 'card_id' => $cardId]);

            // Confirmer l'achat avec une animation JavaScript
            echo "<script>handlePurchaseResult(true);</script>";
        } else {
            // L'utilisateur n'a pas assez de shards : afficher une animation d'échec
            echo "<script>handlePurchaseResult(false);</script>";
        }
    } else {
        // La carte demandée n'existe pas : afficher un message d'erreur en rouge
        echo "<p style='color: red;'>Carte introuvable. Debug :</p>";
        echo "<pre>";
        print_r($priceQuery->errorInfo()); // Affiche les erreurs SQL
        echo "</pre>";
    }
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/Arcane_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="transition.css">
    <link rel="stylesheet" href="styles.css" id="theme-link">
    <link rel="stylesheet" href="styles_market.css">
    <title>Marché des Cartes</title>
</head>
<header>
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
<body>
    <h1>Boutique du Jour</h1>
    <h3>La boutique se mettra à jour dans :</h3>
<div id="countdown" style="font-size: 1.5rem; color:#e94560;"></div>

    <section class="cards">
        <h2>Cartes Disponibles Aujourd'hui</h2>
        <ul class="card-list">
        <?php foreach ($dailyCards as $card): ?>
    <li class="card-item" data-id="<?php echo htmlspecialchars($card['id']); ?>">
        <div class="card" onclick="openPopup(<?php echo htmlspecialchars($card['id']); ?>, '<?php echo htmlspecialchars($card['name']); ?>', <?php echo htmlspecialchars($card['price']); ?>)">
            <div class="card-front" style="background-image: url('<?php echo htmlspecialchars($card['image']); ?>');">
                <h4><?php echo htmlspecialchars($card['name']); ?></h4>
                <p class="card-price"><?php echo htmlspecialchars($card['price']); ?> Shards</p>
            </div>
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
    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close-btn" onclick="closePopup()">&times;</span>
            <h3 id="popup-title">Aperçu de la carte</h3>
            <div id="popup-card-display" class="popup-card-display"></div>
            <div id="popup-price-info" class="popup-price-info"></div>
            <form method="POST" action="">
            <input id="popup-card-id" name="card_id" type="hidden">
            <button type="submit" id="buy-button" class="buy-button" style="display: none;">Acheter la carte</button>
            </form>
            <p id="insufficient-funds" style="color: red; display: none;">Vous n'avez pas assez de fonds pour acheter cette carte.</p>
        </div>
    </div>
    </main>
<div class="overlay transition-overlay"></div>
<script>
function openPopup(cardId, cardName, cardPrice) {
    const popup = document.getElementById('popup');
    if (!popup) {
        console.error("Popup element not found.");
        return;
    }

    const popupTitle = document.getElementById('popup-title');
    const popupCardId = document.getElementById('popup-card-id');
    const popupCardDisplay = document.getElementById('popup-card-display');
    const popupPriceInfo = document.getElementById('popup-price-info');
    const buyButton = document.getElementById('buy-button');
    const insufficientFunds = document.getElementById('insufficient-funds');

    // Assure que tous les éléments nécessaires sont présents avant d'agir
    if (!popupCardId || !buyButton || !popupTitle || !popupPriceInfo || !popupCardDisplay) {
        console.error("One or more elements not found in DOM.");
        return;
    }

    // Mise à jour de la popup avec les informations de la carte
    popupTitle.textContent = `Aperçu de la carte "${cardName}"`;
    popupCardId.value = cardId; // Définir correctement l'ID de la carte
    popupPriceInfo.innerHTML = `<p>Prix : ${cardPrice} Shards</p>`;

    // Récupérer la carte correspondante à l'ID
    const card = document.querySelector(`.card-item[data-id='${cardId}']`);
    if (card) {
        const cardHTML = card.innerHTML;
        popupCardDisplay.innerHTML = `<div class="card-item-popup">${cardHTML}</div>`;
    } else {
        console.error(`Card with ID ${cardId} not found.`);
    }

    // Vérification des fonds de l'utilisateur (en PHP)
    const userFunds = <?php echo isset($user['shards']) ? $user['shards'] : 0; ?>;  // Sécurise la valeur en PHP
    if (userFunds >= cardPrice) {
        buyButton.style.display = 'inline-block';
        insufficientFunds.style.display = 'none';
    } else {
        buyButton.style.display = 'none';
        insufficientFunds.style.display = 'block';
    }

    // Affiche la popup
    popup.style.display = 'flex';
}

// Fonction d'achat
function purchaseCard(cardId, cardPrice) {
    const userFunds = <?php echo isset($user['shards']) ? $user['shards'] : 0; ?>;  // Fond de l'utilisateur

    if (userFunds >= cardPrice) {
        // Vérifier que cardId est valide avant d'envoyer la requête
        if (!cardId || isNaN(cardId)) {
            alert("ID de carte invalide.");
            return;
        }

        // Envoi des données pour enregistrer l'achat
        fetch('add_purchased_card.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `card_id=${cardId}&card_price=${cardPrice}`
        })
        .then(response => response.text())
        .then(data => {
            if (data === "Carte achetée avec succès !") {
                alert(data); // Affiche la réponse du serveur
                // Actualiser les fonds de l'utilisateur
                // Mettre à jour l'interface utilisateur si nécessaire
            } else {
                alert("Erreur lors de l'achat : " + data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Une erreur est survenue.");
        });
    } else {
        alert("Fonds insuffisants.");
    }
}

// Fonction pour fermer la popup
function closePopup() {
    const popup = document.getElementById('popup');
    popup.style.display = 'none';
}

// Fonction pour ouvrir la fenêtre du jeu
function openGameWindow() {
    window.open('game.php', 'GameWindow', 'width=800,height=600');
}

</script>

<script>
// Fonction pour calculer et afficher le temps restant jusqu'à la mise à jour de la boutique
function updateCountdown() {
    const now = new Date();
    const nextUpdate = new Date();
    nextUpdate.setDate(now.getDate() + 1); // Passe au jour suivant
    nextUpdate.setHours(0, 0, 0, 0); // Définit minuit comme l'heure de mise à jour

    const timeDiff = nextUpdate - now; // Différence en millisecondes
    if (timeDiff > 0) {
        const hours = Math.floor((timeDiff / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((timeDiff / (1000 * 60)) % 60);
        const seconds = Math.floor((timeDiff / 1000) % 60);

        // Affichage formaté du compte à rebours
        document.getElementById('countdown').textContent = 
            `${hours}h ${minutes}m ${seconds}s`;
    } else {
        document.getElementById('countdown').textContent = "Mise à jour en cours...";
        clearInterval(timer); // Arrête le timer lorsque le temps est écoulé
        location.reload(); // Recharge la page pour afficher les nouvelles cartes
    }
}

// Met à jour le compte à rebours toutes les secondes
const timer = setInterval(updateCountdown, 1000);

// Initialisation immédiate au chargement de la page
updateCountdown();
</script>
<script>
    function showPurchaseConfirmation(success) {
    const popup = document.getElementById('popup');
    const body = document.body;

    // Active ou désactive le flou du corps
    body.classList.toggle('modal-open', true);

    // Gérer les classes selon le succès ou l'échec
    popup.classList.remove('success', 'error');
    popup.classList.add(success ? 'success' : 'error');

    // Afficher la popup avec animation
    popup.classList.add('show');
    
    // Fermer la popup après 3 secondes
    setTimeout(() => {
        popup.classList.remove('show');
        body.classList.remove('modal-open');
    }, 3000);
}

function handlePurchaseResult(success) {
    // Si l'achat est réussi
    if (success) {
        showPurchaseConfirmation(true);
    } else {
        showPurchaseConfirmation(false);
    }
}
</script>
</body>
</html>