const playerPV = document.getElementById("player-pv");
const botPV = document.getElementById("bot-pv");
const playerBoard = document.getElementById("player-board");
const botBoard = document.getElementById("bot-board");
const cards = document.querySelectorAll(".card");

let currentPlayerTurn = true; // True = Player, False = Bot

cards.forEach(card => {
    card.addEventListener("click", () => {
        if (!currentPlayerTurn) return;
        playCard(card);
    });
});

function playCard(card) {
    const attack = parseInt(card.dataset.attack);
    const defense = parseInt(card.dataset.defense);

    // Add card to player's board
    const clone = card.cloneNode(true);
    playerBoard.appendChild(clone);

    // Deal damage to bot
    const botHealth = parseInt(botPV.textContent);
    botPV.textContent = botHealth - attack;

    // End player turn
    currentPlayerTurn = false;

    // Bot's turn
    setTimeout(botTurn, 1000);
}

function botTurn() {
    const botHealth = parseInt(botPV.textContent);
    if (botHealth <= 0) {
        alert("Vous avez gagné !");
        return;
    }

    const playerHealth = parseInt(playerPV.textContent);
    if (playerHealth <= 0) {
        alert("Le bot a gagné !");
        return;
    }

    // Bot randomly attacks
    const damage = Math.floor(Math.random() * 10) + 5;
    playerPV.textContent = playerHealth - damage;

    // Back to player's turn
    currentPlayerTurn = true;
}

function applyAnimation(element, animationClass) {
    element.classList.add(animationClass);
    setTimeout(() => {
        element.classList.remove(animationClass);
    }, 500);
}

function handleEffect(effect) {
    switch (effect) {
        case 'heal':
            const currentHealth = parseInt(playerPV.textContent);
            playerPV.textContent = Math.min(currentHealth + 20, 100);
            applyAnimation(playerPV, 'heal-effect');
            alert("Vous avez récupéré 20 PV !");
            break;
        case 'boost':
            alert("Votre prochaine attaque infligera +10 dégâts !");
            boostedAttack = 10;
            break;
        case 'stun':
            alert("Le bot est paralysé pour un tour !");
            botStunned = true;
            break;
    }
}

let playerScore = 0;

function updateScore(points) {
    playerScore += points;
    document.getElementById("score-display").textContent = `Score : ${playerScore}`;
}

function handleEffect(effect) {
    switch (effect) {
        case 'heal':
            const currentHealth = parseInt(playerPV.textContent);
            playerPV.textContent = Math.min(currentHealth + 20, 100);
            applyAnimation(playerPV, 'heal-effect');
            updateScore(10); // Ajouter des points pour l'utilisation stratégique
            break;
        case 'boost':
            alert("Votre prochaine attaque infligera +10 dégâts !");
            boostedAttack = 10;
            updateScore(15);
            break;
        case 'stun':
            alert("Le bot est paralysé pour un tour !");
            botStunned = true;
            updateScore(20);
            break;
    }
}

function useItem(itemId) {
    fetch(`/use-item/${itemId}`, { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                applyItemEffect(data.item);
                updateInventoryDisplay();
            } else {
                alert(data.message);
            }
        });
}

function applyItemEffect(item) {
    switch (item.effect) {
        case 'heal':
            const currentHealth = parseInt(playerPV.textContent);
            playerPV.textContent = Math.min(currentHealth + item.value, 100);
            applyAnimation(playerPV, 'heal-effect');
            alert(`Vous avez récupéré ${item.value} PV !`);
            break;
        case 'boost':
            alert(`Votre prochaine attaque infligera +${item.value} dégâts !`);
            boostedAttack = item.value;
            break;
        case 'stun':
            alert("Le bot est paralysé pour un tour !");
            botStunned = true;
            break;
    }
}
function updateInventoryDisplay() {
    fetch('/get-inventory')
        .then(response => response.json())
        .then(data => {
            const itemsList = document.getElementById('items-list');
            itemsList.innerHTML = '';

            data.inventory.forEach(item => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'item';
                itemDiv.innerHTML = `
                    <img src="${item.image}" alt="${item.name}">
                    <p>${item.name} (${item.quantity})</p>
                `;
                itemDiv.onclick = () => useItem(item.id);
                itemsList.appendChild(itemDiv);
            });
        });
}
