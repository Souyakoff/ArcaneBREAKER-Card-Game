<?php
// db_connect.php
$servername = "localhost";  // Hôte de la base de données
$username = "hugo";         // Nom d'utilisateur de la base de données
$password = "Hugo290210120510!";             // Mot de passe de la base de données
$dbname = "arcane_game";    // Nom de la base de données

// Connexion à la base de données avec PDO (PHP Data Objects)
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // Configure le mode de gestion des erreurs
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connexion réussie";  // Décommenter pour vérifier la connexion
} catch (PDOException $e) {
    echo "Erreur de connexion: " . $e->getMessage();
    die();  // Arrêter l'exécution du script si la connexion échoue
}
?>
