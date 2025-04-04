<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Launcher</title>
    <link rel="stylesheet" href="styles.css" id="theme-link">
    <link rel="stylesheet" href="styles_launcher.css">
</head>
<body>
    <div class="launcher-container">
        <img id ="logo"src="images/Arcane logo.png" alt="Logo" class="launcher-logo">
        <button class="launcher-button" onclick="enterSite()">Entrer</button>
    </div>

    <script>
        function enterSite() {
            window.location.href = "index.php"; // Remplacez par l'URL ou la page cible
        }
    </script>
</body>
</html>
