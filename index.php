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
}?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/Arcanelogo.png" type="image/x-icon">
    <title>Arcane | Breaker</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.2.7/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="transition.css">
    <link rel="stylesheet" href="styles.css" id="theme-link">
    <link rel="stylesheet" href="styles_index.css">
    <link rel="manifest" href="manifest.json">

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
    <section class="intro hidden md:block">
    <div class="logo">
        <img src="images/Arcanebg__3_-removebg (1).png" alt="Logo Arcane">
    </div>
</section>

<section class="game-rules py-12">
    <h2 class="text-4xl font-semibold text-center mb-6">Les Règles du Jeu</h2>
    <div class="rule-container flex flex-wrap justify-between mb-8">
        <div class="rule-text w-full md:w-1/2 px-4">
            <h3 class="text-2xl font-semibold">Composez votre Deck</h3>
            <p>Créez votre deck avec des cartes représentant vos personnages, attaques, et capacités spéciales. Votre stratégie commence ici.</p>
        </div>
        <div class="rule-image w-full md:w-1/2 px-4">
            <img src="images/deck.PNG" alt="Exemple de Deck" class="w-full h-auto rounded-lg">
        </div>
    </div>
    <div class="rule-container flex flex-wrap justify-between mb-8">
        <div class="rule-image w-full md:w-1/2 px-4">
            <img src="images/roles-example.jpg" alt="Exemple de Rôles" class="w-full h-auto rounded-lg">
        </div>
        <div class="rule-text w-full md:w-1/2 px-4">
            <h3 class="text-2xl font-semibold">Choisissez votre Rôle</h3>
            <p>Le jeu se joue entre deux rôles : l'attaquant tente de réduire les PV de l'adversaire, tandis que le défenseur protège son arcane.</p>
        </div>
    </div>

    <div class="rule-container flex flex-wrap justify-between mb-8">
        <div class="rule-text w-full md:w-1/2 px-4">
            <h3>Attaquez et Défendez-vous</h3>
            <p>Jouez à tour de rôle en choisissant une carte pour attaquer ou défendre. Utilisez des stratégies pour renverser la partie.</p>
        </div>
        <div class="rule-image">
            <img src="images/attack-example.jpg" alt="Exemple d'Attaque">
        </div>
    </div>

    <div class="rule-container flex flex-wrap justify-between mb-8">
    <div class="rule-image w-full md:w-1/2 px-4">
            <img src="images/victory-example.jpg" alt="Exemple de Victoire">
        </div>
        <div class="rule-text w-full md:w-1/2 px-4">
            <h3>Gagnez la Partie</h3>
            <p>Réduisez les PV de l'arcane de votre adversaire à zéro pour remporter la victoire. Protégez votre propre arcane à tout prix.</p>
        </div>
    </div>
</section>


<section class="cards py-12 bg-gray-100">
    <h2 class="text-4xl font-semibold text-center mb-6">Champions Disponibles</h2>
    <p>Dans Arcane Breaker vous pouvez récupérez des champions tous unique en leurs genre avec leurs propre capacité spéciale !</Datag></p>
    <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
    
        <?php
        // Inclure le fichier de connexion à la base de données


        // Récupération des cartes depuis la base de données
        $sql = "SELECT * FROM cards ORDER BY RAND() LIMIT 7";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
  <li class="card-item" data-id="<?php echo htmlspecialchars($row['id']); ?>">
                    <div class="card shadow-lg rounded-lg overflow-hidden cursor-pointer" onclick="openPopup(<?php echo htmlspecialchars($row['id']); ?>, '<?php echo htmlspecialchars($row['name']); ?>')">
                        <div class="card-front bg-cover bg-center h-48 flex items-center justify-center text-white" style="background-image: url('<?php echo htmlspecialchars($row['image']); ?>');">
                            <h4 class="text-xl font-bold"><?php echo htmlspecialchars($row['name']); ?></h4>
                        </div>
                        <div class="card-back bg-cover bg-center h-48 flex items-center justify-center text-white" style="background-image: url('<?php echo htmlspecialchars($row['city_image']); ?>');">
                            <div class="p-4">
                            <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                            <p><strong>Points de Vie :</strong> <?php echo htmlspecialchars($row['health_points']); ?></p>
                            <p><strong>Attaque :</strong> <?php echo htmlspecialchars($row['attack']); ?></p>
                            <p><strong>Défense :</strong> <?php echo htmlspecialchars($row['defense']); ?></p>
                            <p><strong>Capacité Spéciale :</strong> <?php echo htmlspecialchars($row['special_ability']); ?></p>
                        </div>
                    </div>
                </li>
                <?php
            }
        } else {
            echo "<p>Aucune carte trouvée.</p>";
        }

        $conn = null; // Fermer la connexion PDO
        ?>
    </ul>
</section>

<section class="patch-note">
    <h2>Patch Note</h2>
    <p>Regardez les derniéres notes de mise à jour d'Arcane BREAKER ici :</p>
    <a href="patch_note.php" class="cta-button">Patch note</a>
</section>


<section class="call-to-action">
    <h2>Prêt à jouer ?</h2>
    <?php if ($user_id): ?>
        <p>Alors plonger dés maintenant dans l'univers d'Arcane Breaker !</p>
        <a id="game-launch" href="javascript:void(0);" class="cta-button" onclick="openGameWindow()">Jouer</a>
        <?php else: ?>
        <p>Inscrivez-vous maintenant et commencez à créer votre deck de cartes pour plonger dans l'univers d'Arcane Breaker !</p>
            <a href="register.php" class="cta-button">S'inscrire</a>
                <h2>Vous avez deja un compte ?</h2>
                <p>Alors connectez-vous et plonger dés maintenant dans l'univers d'Arcane Breaker !</p>
                <a href="login.php" class="cta-button">Connexion</a>
                <?php endif; ?>
        </section>
</main>
<div class="overlay transition-overlay"></div>
    <footer>
        <p>&copy; 2024 Arcane Card Game. Tous droits réservés.</p>
    </footer>
    <script>
    function openGameWindow() {
        window.open('lobby.php', 'GameWindow', 'width=6000,height=2000');  // Ouvre le jeu dans une nouvelle fenêtre
    }
</script>
</body>
</html>

