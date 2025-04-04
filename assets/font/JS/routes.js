app.post('/use-item/:id', async (req, res) => {
    const userId = req.session.userId; // ID du joueur connecté
    const itemId = req.params.id;

    // Vérifier si le joueur possède l'item
    const inventory = await db.query(`
        SELECT i.*, inv.quantity 
        FROM inventory inv
        JOIN items i ON i.id = inv.item_id
        WHERE inv.user_id = ? AND inv.item_id = ? AND inv.quantity > 0
    `, [userId, itemId]);

    if (inventory.length === 0) {
        return res.json({ success: false, message: "Vous ne possédez pas cet item." });
    }

    const item = inventory[0];

    // Réduire la quantité dans l'inventaire
    await db.query(`
        UPDATE inventory
        SET quantity = quantity - 1
        WHERE user_id = ? AND item_id = ?
    `, [userId, itemId]);

    // Retourner les détails de l'item pour appliquer l'effet
    res.json({ success: true, item });
});
app.get('/get-inventory', async (req, res) => {
    const userId = req.session.userId;

    const inventory = await db.query(`
        SELECT i.*, inv.quantity 
        FROM inventory inv
        JOIN items i ON i.id = inv.item_id
        WHERE inv.user_id = ?
    `, [userId]);

    res.json({ inventory });
});
