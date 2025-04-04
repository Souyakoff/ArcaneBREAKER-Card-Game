<?php
include('header.php');
// Connexion à la base de données
require_once 'db_connect.php';
if (!$conn) {
    die("Erreur de connexion à la base de données.");
}


// Récupérer l'ID de l'utilisateur depuis la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Activer les erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Récupérer l'ID de la saison
$seasonId = isset($_GET['season_id']) ? intval($_GET['season_id']) : 0;

// Vérifier si l'ID est valide
if ($seasonId <= 0) {
    die("Saison non valide.");
}

// Récupérer les informations de la saison
$stmt_season = $conn->prepare("SELECT * FROM seasons WHERE id = :id");
$stmt_season->execute(['id' => $seasonId]);
$season = $stmt_season->fetch(PDO::FETCH_ASSOC);

if (!$season) {
    die("Saison introuvable.");
}

// Récupérer les niveaux associés à la saison
$stmt_levels = $conn->prepare("SELECT * FROM pass_levels WHERE season_id = :season_id ORDER BY level ASC");
$stmt_levels->execute(['season_id' => $seasonId]);
$levels = $stmt_levels->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css" id="theme-link">
    <link rel="stylesheet" href="styles_all_levels.css">
    <title>Arcane Breaker - Saison <?php echo htmlspecialchars($season['name']); ?></title>
</head>
<body>
    <header>
        <h1><strong id="title">Arcane</strong> Breaker</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <?php if ($user_id): ?>
                <li><a id="play" href="javascript:void(0);" onclick="openGameWindow()">Jouer</a></li>
                <?php endif; ?>
                <li><a href="deck.php">Deck</a></li>
                <li><a href="market.php">Boutique</a></li>
                <li><a href="buy_shards.php">Shards</a></li>
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
        </nav>
    </header>
        
    <section class="levels">
        <h2>Paliers de la Saison</h2>
        <div class="carousel-buttons">
            <button id="prevButton" class="carousel-button" onclick="scrollCarousel(-1)" disabled>←</button>
            <button id="nextButton" class="carousel-button" onclick="scrollCarousel(1)">→</button>
        </div>
        <div class="levels-grid" id="carousel">
            <?php if ($levels): ?>
                <?php foreach ($levels as $level): ?>
                    <div class="level-card">
                        <h3>Palier <?php echo htmlspecialchars($level['level']); ?></h3>
                        <p><strong>Récompenses :</strong></p>
                        <p><?php echo nl2br(htmlspecialchars($level['benefits'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun palier disponible pour cette saison.</p>
            <?php endif; ?>
        </div>
    </section>
    <a id="buy_pass" href="buy_pass.php">Acheter le pass</a>
    <footer>
        <a href="saison.php" class="btn-back">Retour à toutes les saisons</a>
    </footer>

    <script>
        const carousel = document.getElementById('carousel');
        const prevButton = document.getElementById('prevButton');
        const nextButton = document.getElementById('nextButton');

        function scrollCarousel(direction) {
            const scrollAmount = 300; // Pixels to scroll
            const maxScrollLeft = carousel.scrollWidth - carousel.clientWidth;

            carousel.scrollBy({
                left: direction * scrollAmount,
                behavior: 'smooth'
            });

            setTimeout(() => {
                // Met à jour les boutons après le défilement
                prevButton.disabled = carousel.scrollLeft <= 0;
                nextButton.disabled = carousel.scrollLeft >= maxScrollLeft;
            }, 300);
        }
    </script>
</body>
</html>

