function openPopup(cardId, cardName, cardPrice) {
    const popup = document.getElementById('popup');
    if (!popup) {
        console.error("Popup element not found.");
        return;
    }

    const popupTitle = document.getElementById('popup-title');
    const popupCardId = document.getElementById('popup-card-id');
    const popupCardDisplay = document.getElementById('popup-card-display');
    const popupPriceInfo = document.getElementById('popup-price-info');
    const buyButton = document.getElementById('buy-button');
    const insufficientFunds = document.getElementById('insufficient-funds');

    // Assure-toi que tous les éléments nécessaires sont présents avant d'agir
    if (!popupCardId || !buyButton || !popupTitle || !popupPriceInfo || !popupCardDisplay) {
        console.error("One or more elements not found in DOM.");
        return;
    }

    // Mise à jour de la popup avec les informations de la carte
    popupTitle.textContent = `Aperçu de la carte "${cardName}"`;
    popupCardId.value = cardId; // Définir correctement l'ID de la carte
    popupPriceInfo.innerHTML = `<p>Prix : ${cardPrice} Shards</p>`;

    // Récupérer la carte correspondante à l'ID
    const card = document.querySelector(`.card-item[data-id='${cardId}']`);
    if (card) {
        const cardHTML = card.innerHTML;
        popupCardDisplay.innerHTML = `<div class="card-item-popup">${cardHTML}</div>`;
    } else {
        console.error(`Card with ID ${cardId} not found.`);
    }

    // Vérification des fonds de l'utilisateur
    const userFunds = <?php echo isset($user['shards']) ? $user['shards'] : 0; ?>;  // Sécurise la valeur en PHP
    if (userFunds >= cardPrice) {
        buyButton.style.display = 'inline-block';
        insufficientFunds.style.display = 'none';
    } else {
        buyButton.style.display = 'none';
        insufficientFunds.style.display = 'block';
    }

    // Affiche la popup
    popup.style.display = 'flex';
}

function closePopup() {
    const popup = document.getElementById('popup');
    popup.style.display = 'none';
}

function openGameWindow() {
    window.open('game.php', 'GameWindow', 'width=800,height=600');
}