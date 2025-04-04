<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once 'db_connect.php';
//require_once 'otp.php';

// Charger Sentry
require_once 'vendor/autoload.php';

try {
    Sentry\init([
        'dsn' => 'http://ab62b5fb0837424aa4b3a9290c4daa6a@172.16.0.100:8000/1',
    ]);
} catch (Exception $e) {
    error_log("Erreur lors de l'initialisation de Sentry : " . $e->getMessage());
}

// Vérifier la connexion à la base de données
if (!$conn) {
    $errorMessage = "Erreur de connexion à la base de données.";
    error_log($errorMessage);
    Sentry\captureMessage($errorMessage, \Sentry\Severity::fatal());
    die($errorMessage);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    error_log("Tentative de connexion pour l'utilisateur : " . $username);

    try {
        $user = null;

        // Vérifier si l'utilisateur est un administrateur
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($user) {
            error_log("Utilisateur trouvé : " . $username);

            $passwordField = isset($user['password_hash']) ? 'password_hash' : 'password';

            if (password_verify($password, $user[$passwordField])) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                if (isset($user['role'])) {
                    $_SESSION['role'] = $user['role'];
                    error_log("Connexion réussie pour l'administrateur : " . $username);
                    Sentry\captureMessage("Connexion réussie pour l'administrateur : " . $username, \Sentry\Severity::info());
                    header('Location: admin_dashboard.php');
                } else {
                    $_SESSION['shards'] = $user['shards'];
                    error_log(" Connexion réussie pour l'utilisateur : " . $username);
                    Sentry\captureMessage("✅ Connexion réussie pour l'utilisateur : " . $username, \Sentry\Severity::info());
                    header('Location: index.php');
                }
            
                exit;
            } else {
                $errorMessage = "Échec de connexion : Mot de passe incorrect pour l'utilisateur " . $username;
                error_log($errorMessage);
                Sentry\captureMessage("💀Échec de connexion : Mot de passe incorrect pour l'utilisateur " . $username, \Sentry\Severity::warning());
                $error = "Mot de passe incorrect.";
            }
        } else {
            $errorMessage = " Échec de connexion : Nom d'utilisateur non trouvé - " . $username;
            error_log($errorMessage);
            Sentry\captureMessage("💀Échec de connexion : Nom d'utilisateur non trouvé - " . $username, \Sentry\Severity::info());
            $error = "Nom d'utilisateur non trouvé.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Erreur PDO lors de la connexion : " . $e->getMessage();
        error_log($errorMessage);
        Sentry\captureException($e);
        $error = "Erreur lors de la connexion.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arcane | Breaker</title>
    <link rel="icon" href="images/Arcanelogo.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="styles.css" id="theme-link">
</head>
<body>
    <header>
        <h1>Arcane Breaker</h1>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="register.php">Inscription</a></li>
                <li><a href="settings.php"><i class="bi bi-gear nav-icon"></i></a></li>
            </ul>
        </nav>
    </header>
    <h1>Connexion</h1>
    
    <?php if (isset($error)): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" name="username" id="username" required>
        
        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">Se connecter</button>
    </form>

    <p>Pas encore inscrit ? <a href="register.php">Créer un compte</a></p>
</body>
</html>