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

// Récupérer la date actuelle
$currentDate = date('Y-m-d');

// Récupérer la saison en cours
$stmt_current_season = $conn->prepare("SELECT * FROM seasons WHERE datede_debut <= :current_date AND datede_fin >= :current_date LIMIT 1");
$stmt_current_season->execute(['current_date' => $currentDate]);
$currentSeason = $stmt_current_season->fetch(PDO::FETCH_ASSOC);

if (!$currentSeason) {
    die("Aucune saison en cours.");
}

// Récupérer les passes associés à la saison en cours
$stmt_passes = $conn->prepare("SELECT * FROM passes WHERE season_id = :season_id");
$stmt_passes->execute(['season_id' => $currentSeason['id']]);
$passes = $stmt_passes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Achat de Pass - Saison <?php echo htmlspecialchars($currentSeason['name']); ?></title>
</head>
<body>
    <header>
        <h1><strong id="title">Arcane</strong> Breaker</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <?php if ($user_id): ?>
                    <li><a href="javascript:void(0);" onclick="openGameWindow()">Jouer</a></li>
                <?php endif; ?>
                <li><a href="deck.php">Deck</a></li>
                <li><a href="market.php">Boutique</a></li>
                <li><a href="buy_shards.php">Shards</a></li>
                <div class="profile-container">
                    <?php if ($user_id): ?>
                        <a href="profile.php">
                            <img src="<?php echo $profile_picture; ?>" alt="Photo de profil" class="profile-img">
                        </a>
                    <?php endif; ?>
                </div>
                <div class="menu-links">
                    <?php if ($user_id): ?>
                        <li><a href="logout.php">Se déconnecter</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Connexion</a></li>
                    <?php endif; ?>
                    <li><a href="settings.php"><i class="bi bi-gear nav-icon"></i></a></li>
                </div>
            </ul>
        </nav>
    </header>

    <section class="buy-pass">
        <h2>Pass de la Saison <?php echo htmlspecialchars($currentSeason['name']); ?></h2>

        <?php if ($passes): ?>
            <div class="pass-list">
                <?php foreach ($passes as $pass): ?>
                    <div class="pass-card">
                        <h3><?php echo htmlspecialchars($pass['name']); ?></h3>
                        <p><strong>Prix :</strong> <?php echo htmlspecialchars($pass['price']); ?> Shards</p>
                        <p><?php echo nl2br(htmlspecialchars($pass['description'])); ?></p>
                        <form action="buy_pass_action.php" method="POST">
                            <input type="hidden" name="pass_id" value="<?php echo $pass['id']; ?>">
                            <button type="submit" class="btn-buy">Acheter ce Pass</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Aucun pass disponible pour cette saison.</p>
        <?php endif; ?>
    </section>

    <footer>
        <a href="index.php" class="btn-back">Retour à l'accueil</a>
    </footer>
</body>
</html>
