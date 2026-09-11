<head>
    <link href="public/css/main.css" rel="stylesheet"/>
    <style>
        * {
            box-sizing: border-box;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .status-en_attente_de_livraison {
            background-color: #60acf4;
            color: white;
        }

        .status-livrée {
            background-color: #43a062;
            color: white;
        }

        .status-annulée {
            background-color: #c85a5a;
            color: white;
        }

        .status-en_attente_de_paiement {
            background-color: #df6b0c;
            color: white;
        }
        
        .order-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 15px;
        }

        .order-items h4 {
            margin-top: 0;
            color: #a4d1d5;
        }

        .order-total {
            font-size: 1.5em;
            font-weight: bold;
            color: #a4d1d5;
            margin: 0;
        }

        .order-status {
            text-align: right;
        }

        .orders-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #3a5a6e;
        }
        
        .order-card {
            display: block;
            border-radius: 12px;
            border: 1px solid #ffffff57;
            padding: 20px;
            box-shadow: 7px 11px 12px 0px rgb(0 0 0 / 23%);
            margin-bottom: 16px;
            font-family: 'Inter', system-ui, sans-serif;
            height: auto;
            background: linear-gradient(135deg, #2c455c, #1f3447);
            transition: all 0.3s ease;
        }

        .order-info h3 {
            font-size: 2.3em;
            margin: 0 0 5px 0;
            color: #ffffff;
        }

        .order-date {
            color: #8b9aa3;
            margin: 0;
        }

        .cart-container {
            display: flex;
            justify-content: flex-start;
            flex-direction: column;
            gap: 20px;
            margin: 0 auto;
            padding: 20px;
            max-width: 1200px;
            width: 100%;
            margin-top: 70px;
        }

        .cart-header {
            display: flex;
            padding: 20px;
            margin-bottom: 16px;
            flex-direction: column;
        }
        
        .empty-cart { text-align: center; padding: 50px; color: #666; }

        #command-emply {
            display: flex;
            align-items: center;
            flex-direction: column;
        }

        #command-card-container {
            display: block;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            font-family: 'Inter', system-ui, sans-serif;
            transition: transform 0.2s ease;
            height: auto;
        }

        .btn-commande {
            text-decoration: none;
            color: white;
            font-weight: 100;
            font-family: cursive;
            font-size: 20px;
            padding: 10px;
            width: 12rem;
            background-color: #17b0e39f;
            border-radius: 20px;
            display: flex;
            justify-content: center;
            transition: background-color 0.2s;
        }

        .btn-primary {
            background-color: #a4d1d5;
            color: #0f1419;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #7fb5bf;
        }

        .btn-secondary {
            background-color: #5a7c8e;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.95em;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #7a9cb0;
        }

        .btn-cancel,
        .btn-delete {
            background-color: #c85a5a;
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.95em;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover,
        .btn-delete:hover {
            background-color: #a74646;
        }

        .ligne-horizontal {
            height: 2px;
            width: 100%;
            border-radius: 1px;
            margin-top: 20px;
            margin-bottom: 20px;
            background: #ffffff38;
            border: none;
        }

        #top-content {
            display: flex;
            justify-content: space-between;
            margin: 0 1vw;
            color: white;
            font-weight: 100;
            font-family: 'Kavoon', cursive;
            font-size: 20px;
        }

        #cart-top {
            display: flex;
            justify-content: space-between;
        }

        #cart-top > h1 {
            margin: 0 1vw;
            color: white;
            font-weight: 100;
            font-family: 'Kavoon', cursive;
            font-size: 20px;
        }

        @media (max-width: 768px) {
            .cart-container {
                padding: 10px;
            }
            .cart-header {
                padding: 15px;
            }
            #top-content {
                display: none;
            }
            #cart-top > h1 {
                font-size: 18px;
                margin: 0;
            }
            .btn-commande {
                width: 100%;
            }
        }
        @media (max-width: 600px) {
            .status-badge {
                display: none;
            }
        }
    </style>
</head>
<body>
    <main class="cart-container">
        <section class="cart-header">
            <div id="cart-top">
                <h1>Vos Commande(s)</h1>
            </div>

            <hr class="ligne-horizontal"></hr>

            <?php if (empty($orders)): ?>
                <div id="command-emply">
                    <h3 class="empty-cart">Vous n'avez aucune commande</h3>
                    <a href="./list" class="btn-primary">Continuer vers l'achat</a>
                </div>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>Commande N°<?= htmlspecialchars($order['id'] ?? '') ?></h3>
                                    <p class="order-date">
                                        <?= htmlspecialchars(date('d/m/Y', strtotime($order['date_commande'] ?? ''))) ?>
                                    </p>
                                </div>
                                <div class="order-status">
                                    <span class="status-badge status-<?= strtolower(str_replace(' ', '_', $order['statut'] ?? '')) ?>">
                                        <?= htmlspecialchars($order['statut'] ?? 'En attente') ?>
                                    </span>
                                    <p class="order-total"><?= htmlspecialchars($order['montant_total'] ?? '0') ?> €</p>
                                </div>
                            </div>

                            <div class="order-items">
                                <h4>Articles commandés:</h4>
                                <div id="command-card-container">
                                    <?php foreach ($order['items'] as $item): ?>
                                        <card-panier 
                                            id-serie="<?= htmlspecialchars($item['id_serie']) ?>"
                                            id-tome="<?= htmlspecialchars($item['id_tome']) ?>"
                                            title="<?= htmlspecialchars($item['titre']) ?>"
                                            image="<?= htmlspecialchars($item['image']) ?>"
                                            price="<?= htmlspecialchars($item['price']) ?>"
                                            quantite="<?= htmlspecialchars($item['quantite']) ?>"
                                            tome="<?= htmlspecialchars($item['numero_volume']) ?>"
                                            type="commandtracker"
                                            note="<?= htmlspecialchars($item['note']) ?>"
                                            status="<?= htmlspecialchars($order['statut']) ?>"
                                            reviews="<?= htmlspecialchars($item['reviews']) ?>">
                                        </card-panier>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="order-actions">
                                <?php if ($order['statut'] === 'En attente de paiement'): ?>
                                    <a href="commandtracker?action=cancel&id=<?= htmlspecialchars($order['id']) ?>" class="btn-cancel">Annuler la commande</a>
                                <?php endif; ?>
                                <?php if ($order['statut'] === 'En attente de livraison' || $order['statut'] === 'Livrée'): ?>
                                    <a href="facture?action=facture&id=<?= htmlspecialchars($order['id']) ?>" class="btn-secondary">Télécharger facture</a>
                                <?php endif; ?>
                                <?php if ($order['statut'] === 'Annulée'): ?>
                                    <a href="commandtracker?action=delete&id=<?= htmlspecialchars($order['id']) ?>" class="btn-delete">Supprimé</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    
    <?php if (!empty($message)): ?>
        <div id="system-message" data-message="<?= htmlspecialchars($message) ?>"></div>
        <div id="type-message" data-message="<?= htmlspecialchars($typeMessage ?? '') ?>"></div>
    <?php endif; ?>
    
    <script type="module" src="public/js/panierComponents.js" defer></script>
    <script type="module" src="public/js/commandtracker.js" defer></script>
</body>