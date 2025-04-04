<?php include('header.php'); ?>
<?php include('lang.php'); // Charger la langue ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/Arcane_logo.png" type="image/x-icon">
    <title>Paramètres | Arcane Breaker</title>
    <!-- Lien vers le fichier CSS dynamique -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="styles.css" id="theme-link">
    <style>
        .settings-container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
        }
        .settings-section {
            margin-bottom: 30px;
        }
        .settings-section h3 {
            margin-bottom: 10px;
        }
        .settings-section select, .settings-section input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .settings-section button:hover {
            opacity: 0.9;
        }
        .theme-option button {
            margin: 10px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .theme-option button.light-theme-btn {
            background-color: #f0be73;
            color: #000;
        }
        .theme-option button.dark-theme-btn {
            background-color: #e94560;
            color: #fff;
        }
        .theme-option button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <header>
        <h1>Arcane Breaker</h1>
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

    <main class="settings-container">
        <h2>Paramètres</h2>
        <p>Changez le thème du site selon vos préférences :</p>
        <div class="theme-option">
            <button class="light-theme-btn" onclick="switchTheme('light')">Thème Clair</button>
            <button class="dark-theme-btn" onclick="switchTheme('dark')">Thème Sombre</button>
        </div>

        <!-- Notifications -->
        <div class="settings-section">
            <h3>Notifications</h3>
            <label>
                <input type="checkbox" id="notifications" checked> Activer les notifications
            </label>
        </div>

        <!-- Langue -->
        <div class="settings-section">
            <h3>Langue</h3>
            <label for="language">Choisissez la langue :</label>
            <h2><?php echo $translations['settings']; ?></h2>
        <form method="POST" action="">
            <label for="language"><?php echo $translations['choose_language']; ?></label>
            <select id="language" name="language" onchange="this.form.submit()">
                <option value="fr" <?php echo ($_SESSION['lang'] == 'fr') ? 'selected' : ''; ?>>Français</option>
                <option value="en" <?php echo ($_SESSION['lang'] == 'en') ? 'selected' : ''; ?>>English</option>
                <option value="es" <?php echo ($_SESSION['lang'] == 'es') ? 'selected' : ''; ?>>Español</option>
            </select>
        </form>
        </div>

        <!-- Modifier le mot de passe -->
        <div class="settings-section">
            <h3>Modifier le mot de passe</h3>
            <form action="change_password.php" method="POST">
                <label for="current-password">Mot de passe actuel :</label>
                <input type="password" id="current-password" name="current_password" required>
                <label for="new-password">Nouveau mot de passe :</label>
                <input type="password" id="new-password" name="new_password" required>
                <label for="confirm-password">Confirmez le mot de passe :</label>
                <input type="password" id="confirm-password" name="confirm_password" required>
                <button type="submit">Modifier</button>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Arcane Breaker. Tous droits réservés.</p>
    </footer>
    
</body>
</html>
