// Fonction pour changer le thème
function switchTheme(theme) {
    const themeLink = document.getElementById("theme-link");
    if (themeLink) { // Vérifier si l'élément existe
        if (theme === "dark") {
            themeLink.setAttribute("href", "styles.css");
        } else if (theme === "light") {
            themeLink.setAttribute("href", "light.css");
        }
    } else {
        console.error("L'élément #theme-link est introuvable.");
    }

    // Enregistrer le thème dans le stockage local
    localStorage.setItem("siteTheme", theme);
}

// Charger le thème depuis le stockage local au chargement de la page
document.addEventListener("DOMContentLoaded", function () {
    const savedTheme = localStorage.getItem("siteTheme");
    if (savedTheme) {
        switchTheme(savedTheme);
    } else {
        // Si aucun thème n'est enregistré, appliquer un thème par défaut (par exemple, "light")
        switchTheme("light");
    }
});
