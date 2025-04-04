<?php
// Connexion à la base de données
// Connexion à la base de données
require_once 'db_connect.php';
if (!$conn) {
    die("Erreur de connexion à la base de données.");
}


// Récupérer l'ID de l'utilisateur depuis la session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Récupérer les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Gérer la mise à jour de la photo de profil et de la bio
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bio = $_POST['bio'] ?? '';
    $profile_picture = $_FILES['profile_picture']['name'] ?? '';

    // Traiter le téléchargement de la photo de profil
    if ($profile_picture) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Vérification du type d'image
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowed_types)) {
            $error = "Seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
        } else {
            // Déplacer l'image téléchargée dans le dossier approprié
            if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
                // Mettre à jour l'utilisateur avec la nouvelle photo de profil
                $stmt = $conn->prepare("UPDATE users SET bio = ?, profile_picture = ? WHERE id = ?");
                $stmt->execute([$bio, $target_file, $user_id]);

                // Message de succès
                $success_message = "Votre profil a été mis à jour avec succès!";
            } else {
                $error = "Erreur lors du téléchargement de la photo.";
            }
        }
    } else {
        // Si aucune photo n'est téléchargée, mettre à jour seulement la bio
        $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE id = ?");
        $stmt->execute([$bio, $user_id]);

        // Message de succès
        $success_message = "Votre profil a été mis à jour avec succès!";
    }
}

// Si l'image de profil existe, on l'affiche, sinon on utilise l'image par défaut
$profile_picture = (!empty($user['profile_picture']) && file_exists($user['profile_picture'])) 
    ? $user['profile_picture'] 
    : 'images/default_profile_picture.jpg'; // Image par défaut

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcane | Profil</title>
    <link rel="icon" href="images/Arcane_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="styles.css" id="theme-link">
    <link rel="stylesheet" href="styles_profile.css">
</head>
<body>
    <header>
        <h1>Bienvenue, <?= htmlspecialchars($user['username']) ?>!</h1>
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

    <h2>Mon Profil</h2>

    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?= htmlspecialchars($success_message) ?></p>
    <?php elseif (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="profile-info">
    <img src="<?php echo $profile_picture; ?>" alt="Photo de profil" width="150">
        <p><strong>Nom d'utilisateur :</strong> <?= htmlspecialchars($user['username']) ?></p>
        <p><strong>Date de création :</strong> <?= htmlspecialchars($user['created_at']) ?></p>
        <p><strong>Bio :</strong> <?= htmlspecialchars($user['bio']) ?: 'Aucune bio renseignée.' ?></p>
        <p id="user_currency"><strong id="user_shards">Shards :</strong><?= htmlspecialchars($user['shards']); ?></p>
    </div>

    <h3>Modifier mon profil</h3>
    <form method="POST" enctype="multipart/form-data">
        <label for="bio">Bio (facultatif) :</label>
        <textarea name="bio" id="bio"><?= htmlspecialchars($user['bio']) ?></textarea>

        <label for="profile_picture">Photo de profil (facultatif) :</label>
        <input type="file" name="profile_picture" id="profile_picture">

        <button type="submit">Mettre à jour le profil</button>
    </form>
    <script>
    function openGameWindow() {
        window.open('game.php', 'GameWindow', 'width=800,height=600');  // Ouvre le jeu dans une nouvelle fenêtre
    }
</script>

</body>
</html>
