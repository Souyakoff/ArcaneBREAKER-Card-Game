<?php
// Inclusion des dépendances
require_once dirname(__FILE__).'/vendor/autoload.php';
use OTPHP\TOTP;

/***********************
 * Génération d'un secret
***********************/
$otp = TOTP::create();
$secret = $otp->getSecret();


// Utilisation d'un secret déjà généré
$secret = "LZVY6T3JBNWCEORXKJAVM2LDPFZGQ53Y7XKHONSRF2V5UTWPCYLQNKTZGA";
$secretOutput = "The OTP secret is: {$secret}\n";


/***********************
 * Création du TOTP avec des informations précises
 ***********************/
$otp = TOTP::create(
    $secret,                   // secret utilisé (généré plus haut)
    30,                 // période de validité
    'sha256',           // Algorithme utilisé
    6                   // 6 digits
);
$otp->setLabel('Hugo CAFFENNE'); // The label
$otp->setIssuer('tout est incroyable (sauf ce con de clovis)');
$otp->setParameter('image', 'https://cdn.discordapp.com/attachments/761594647944233010/1353725876289933407/IMG_20250324_144256.jpg?ex=67e2b2f7&is=67e16177&hm=de6397bb5aaf13f2c47aa3d5ee8f5ae58300dfafbcf716d2e509f8930183b032&'); // FreeOTP can display image

$otpOutput = "The current OTP is: {$otp->now()}\n";

/***********************
 * Affichage du temps pour information
 ***********************/
// Définition de la zone de temps
date_default_timezone_set('Europe/Paris');
$maintenant = time() ;

// Affichage de maintenant
$dateOutput = date('Y-m-d H:i:s',$maintenant);


/***********************
 * Génération du QrCode
 ***********************/
// Note: You must set label before generating the QR code
$grCodeUri = $otp->getQrCodeUri(
    'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
    '[DATA]'
);
$qrCodeOutput = "<img src='{$grCodeUri}'>";



/***********************
 * Fonction de vérification du formulaire
 ***********************/
// Fonction qui renvoie true si login et mot de passe sont corrects
function checkLoginPassword($login, $password)
{
    if ($login==':user' && $password==':password') return true;
    return false;
}

// Vérifie la valeur OTP
function checkOTP($otp_form): bool
{
    global $otp;

    return $otp->verify($otp_form);
}

$formOutput = '';
// Traitement du formulaire de login:
if (!empty($_POST['login']))
{
    if ( checkLoginPassword($_POST['login'], $_POST['password'] ) && checkOTP( $_POST['otp'] ) )
        $formOutput = "Login OK !";
    else
        $formOutput = "Echec login";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification OTP</title>
</head>
<body>
    <h1>Entrez le code OTP envoyé à votre email</h1>

    <form method="POST">
        <label for="otp">Code OTP :</label>
        <input type="text" name="otp" id="otp" required>
        <button type="submit">Vérifier</button>
    </form>
</body>
</html>
