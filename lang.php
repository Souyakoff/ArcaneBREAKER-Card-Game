<?php
session_start();

// Langue par défaut
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'fr'; // Français par défaut
}

// Si une langue est sélectionnée
if (isset($_POST['language'])) {
    $lang = $_POST['language'];
    $_SESSION['lang'] = $lang; // Mettez à jour la langue dans la session
}

// Charger le fichier de traduction correspondant
$langFile = __DIR__ . "/lang/{$_SESSION['lang']}.php";
if (file_exists($langFile)) {
    $translations = include $langFile;
} else {
    // Si le fichier de traduction n'existe pas, utiliser la langue par défaut
    $translations = include __DIR__ . "/lang/fr.php";
}
