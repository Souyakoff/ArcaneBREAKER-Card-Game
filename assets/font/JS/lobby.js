document.addEventListener("DOMContentLoaded", () => {
    const deckCards = document.querySelectorAll(".deck-card");
    const popup = document.getElementById("deck-popup");
    const popupDeckName = document.getElementById("popup-deck-name");
    const popupDeckCards = document.getElementById("popup-deck-cards");
    const selectDeckButton = document.getElementById("select-deck-button");
    const closePopupButton = document.getElementById("close-popup");
    const deckForm = document.getElementById("deck-form");
    const selectedDeckId = document.getElementById("selected-deck-id");

    // Mock data for deck cards (Replace with AJAX calls or backend rendering)
    const mockCards = {
        1: ["Carte 1", "Carte 2", "Carte 3"],
        2: ["Carte A", "Carte B", "Carte C"],
        3: ["Carte X", "Carte Y", "Carte Z"]
    };

    // Ouvrir la popup avec les informations du deck
    deckCards.forEach(card => {
        card.addEventListener("click", () => {
            const deckId = card.dataset.id;
            const deckName = card.querySelector("h3").innerText;

            // Remplir la popup
            popupDeckName.innerText = deckName;
            popupDeckCards.innerHTML = "";

            if (mockCards[deckId]) {
                mockCards[deckId].forEach(cardName => {
                    const li = document.createElement("li");
                    li.innerText = cardName;
                    popupDeckCards.appendChild(li);
                });
            }

            selectedDeckId.value = deckId;

            // Afficher la popup
            popup.style.display = "flex";
        });
    });

    // Fermer la popup
    closePopupButton.addEventListener("click", () => {
        popup.style.display = "none";
    });

    // Valider le choix du deck
    selectDeckButton.addEventListener("click", () => {
        deckForm.submit();
    });
});
