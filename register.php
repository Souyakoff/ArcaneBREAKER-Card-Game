<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>

<?php
// Inclure le fichier de connexion à la base de données
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation des champs
    if ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        // Hachage du mot de passe
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insertion dans la base de données
        try {
            // Préparer et exécuter la requête d'insertion
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password]);

            // Message de confirmation
            $success_message = "Inscription réussie ! Vous allez être redirigé pour compléter votre profil.";

            // Rediriger après 3 secondes
            header('refresh:3; url=profile.php');
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
?>
<?php include('header.php'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcane | Breaker</title>
    <link rel="icon" href="images/Arcane_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="styles.css" id="theme-link">
</head>
<body>
    <header>
        <h1>Arcane Breaker</h1>
        <nav>
            <ul>
                <li><a href="index.php">Acceuil</a></li>
                <li><a href="login.php">Connexion</a></li>
                <li><a href="settings.php"><i class="bi bi-gear nav-icon"></i></a></li>
            </ul>
            <?php if ($user_id): ?>
                <!-- Afficher l'image de profil à droite de la navbar si l'utilisateur est connecté -->
                <div class="profile-container">
                    <a href="profile.php">
                        <img src="<?php echo $profile_picture; ?>" alt="Photo de profil">
                    </a>
                </div>
            <?php endif; ?>
    </header>
    <h1>Inscription</h1>

    <?php if (isset($success_message)): ?>
        <p style="color: green;"><?= htmlspecialchars($success_message) ?></p>
    <?php elseif (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" name="username" id="username" required>
        <label for="email">Email :</label>
        <input type="email" name="email" id="email" required>
        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" required>
        <label for="confirm_password">Confirmez le mot de passe :</label>
        <input type="password" name="confirm_password" id="confirm_password" required>
        <button type="submit">S'inscrire</button>
    </form>

</body>
</html>
