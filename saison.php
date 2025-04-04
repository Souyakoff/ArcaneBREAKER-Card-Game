<?php include('header.php'); ?>
<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connexion à la base de données
require 'db_connect.php';

// Récupérer l'ID de l'utilisateur depuis la session
session_start();
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

$current_date = date('Y-m-d');

// Requête SQL pour obtenir la saison la plus proche de la date actuelle
$stmt = $conn->prepare("
    SELECT id,
        COALESCE(datede_debut, '1970-01-01') AS datede_debut, 
        COALESCE(datede_fin, '1970-01-01') AS datede_fin,
        name, description, theme, image, pass_benefits 
    FROM seasons 
    WHERE datede_debut <= ? AND datede_fin >= ?
    ORDER BY datede_debut ASC
    LIMIT 1
");
$stmt->execute([$current_date, $current_date]);

$current_season = $stmt->fetch(PDO::FETCH_ASSOC);

// Vérifier si une saison a été trouvée
if (!$current_season) {
    echo "Aucune saison trouvée.";
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saison actuelle | Arcane Breaker</title>
    <link rel="stylesheet" href="styles.css" id="theme-link">
    <style>

        .season-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .season-image {
            max-width: 100%;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .season-details h1 {
            font-size: 2.5em;
            color: #333;
            margin-bottom: 15px;
        }

        .season-details p {
            font-size: 1.1em;
            color: #555;
            margin-bottom: 15px;
        }

        .season-pass {
            margin-top: 20px;
            padding: 20px;
            background-color: #e94560;
            color: #fff;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .season-pass h2 {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .season-pass p {
            font-size: 1.1em;
            margin-bottom: 20px;
        }

        .season-pass a {
            background-color: #fff;
            color: #e94560;
            padding: 10px 20px;
            font-size: 1.2em;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .season-pass a:hover {
            background-color: #f0be73;
        }

        .pass-preview {
    padding: 20px;
    background-color: #f4f4f4;
    border-radius: 10px;
    text-align: center;
}

.pass-preview h3 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #333;
}

.levels-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.level-card {
    background-color: #1a1a2e;
    border-radius: 15px;
    padding: 15px;
    color: #fff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.level-card:hover {
    background-color: #e94560;
    transform: translateY(-5px);
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
    color: #1a1a2e;
}

.level-card h4 {
    font-size: 18px;
    margin-bottom: 10px;
}

.level-name {
    font-size: 16px;
    font-weight: bold;
    margin-bottom: 10px;
}

.level-benefits {
    font-size: 14px;
    line-height: 1.5;
}
.view-pass-btn {
    display: inline-block;
    margin-top: 20px;
    padding: 10px 20px;
    background: #ff63a8;
    color: #fff;
    text-decoration: none;
    font-weight: bold;
    border-radius: 25px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: background 0.3s ease, box-shadow 0.3s ease;
}

.view-pass-btn:hover {
    background: #6b63ff;
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
}

    </style>
</head>
<body>
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

    <main class="season-container">
        <img src="<?php echo $current_season['image']; ?>" alt="Image de la saison" class="season-image">
        <div class="season-details">
            <h1><?php echo htmlspecialchars($current_season['name']); ?></h1>
            <p><strong>Dates :</strong> <?php echo date('d M Y', strtotime($current_season['datede_debut'])) . ' - ' . date('d M Y', strtotime($current_season['datede_fin'])); ?></p>
            <p><?php echo nl2br(htmlspecialchars($current_season['description'])); ?></p>
            <?php if ($current_season['theme']): ?>
                <p><strong>Thème :</strong> <?php echo htmlspecialchars($current_season['theme']); ?></p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Arcane Breaker. Tous droits réservés.</p>
    </footer>
    <script src="assets/font/JS/theme.js"></script>
</body>
</html>
