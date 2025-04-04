<?php
// Démarrer la session
session_start();//LOG !!!

// Détruire toutes les variables de session
session_unset();//LOG !!!

// Détruire la session
session_destroy();//LOG !!!

// Rediriger l'utilisateur vers la page de connexion
header('Location: login.php');
exit;
?>
