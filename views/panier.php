<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <link href="public/css/main.css" rel="stylesheet"/>
    <style>
        * {
            box-sizing: border-box;
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
            border-radius: 12px;
            background: #39628221;
            border: 1px solid #ffffff57;
            box-shadow: 7px 11px 12px 0px rgb(0 0 0 / 23%);
            margin-bottom: 16px;
            flex-direction: column;
        }
        
        .empty-cart { text-align: center; padding: 50px; color: #666; }

        #card-container {
            display: block;
            background: #39628221;
            border-radius: 12px;
            border: 1px solid #ffffff57;
            padding: 20px;
            box-shadow: 7px 11px 12px 0px rgb(0 0 0 / 23%);
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

        .btn-commande:hover {
            background-color: #17b0e3df;
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

        #cart-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0 1vw;
        }

        #cart-info > h1 {
            color: white;
            font-family: 'Inter', system-ui, sans-serif;
            font-weight: 700;
            font-size: 1.4rem;
            margin: 10px 0;
        }

        #cart-bottom {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
            margin-right: 1vw;
        }

        @media (max-width: 768px) {
            .cart-container {
                padding: 10px;
            }
            .cart-header, #card-container {
                padding: 15px;
            }
            #top-content {
                display: none;
            }
            #card-container > .ligne-horizontal:first-of-type {
                display: none;
            }
            #cart-top > h1 {
                font-size: 18px;
                margin: 0;
            }
            #cart-info {
                margin: 0;
            }
            #cart-info > h1 {
                font-size: 1.2rem;
            }
            #cart-bottom {
                margin-right: 0;
                justify-content: center;
            }
            .btn-commande {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            #cart-info {
                flex-direction: column;
                gap: 5px;
            }
            #cart-top {
                display: none;
            }
        }
    </style>
</head>
<body>
    <main class="cart-container">
        
        <?php if (!$isLogged): ?>
            <div style="background: #fff3cd; padding: 15px; border-radius: 8px; color: #856404; margin-bottom: 20px;">
                Vous n'êtes pas connecté. Connectez-vous pour sauvegarder votre panier de façon permanente.
            </div>
        <?php endif; ?>

        <section class="cart-header">
            <div id="cart-top">
                <h1>Total</h1>
                <h1>Prix Total</h1>
            </div>

            <hr class="ligne-horizontal"></hr>

            <div id="cart-info">
                <h1 id="price"><span id="total-items"> 0 </span> Article(s)</h1>
                <h1 id="total-price">0.00€</h1>
            </div>
            <div id="cart-bottom">
                <?php if (!$isLogged): ?>
                    <a href="./login?origine=pannier" class="btn-commande">Passer Commande</a>
                <?php else: ?>
                    <a href="./command" class="btn-commande">Passer Commande</a>
                <?php endif; ?>
            </div>
        </section>

        <section id="cart-list">
            <?php if (empty($contenuePanier)): ?>
                <h3 class="empty-cart">Votre panier est actuellement vide.</h3>
            <?php else: ?>
                <div id="card-container">
                    <div id="top-content">
                        <h2>Votre panier</h2>
                        <h2>Prix</h2>
                    </div>
                    <hr class="ligne-horizontal"></hr>
                    <?php 
                        $SM = new Serie_Manga();
                        foreach ($contenuePanier as $tome): 
                            $idTome = $tome['id'] ?? $tome['idTome'] ?? $tome['id_tome'];
                            $quantite = $tome['quantite'];
                            $tomePrice = $tome['price'];
                            $tomeNumber = $tome['numero_volume'];
                            
                            $serieInfo = $SM->getAllByTomeId($idTome);
                            $serieTitre = $serieInfo['titre'] ?? 'Titre inconnu';
                            $idSerie = $serieInfo['id_serie'];
                            $image_tome = $serieInfo['chemin_image'];
                            
                            $dispo = "true";
                            if ($tome['quantite_stock'] <= 0) {
                                $dispo = "false";
                            }
                    ?>
                        <card-panier 
                            id-serie="<?= htmlspecialchars($idSerie) ?>"
                            id-tome="<?= htmlspecialchars($idTome) ?>"
                            title="<?= htmlspecialchars($serieTitre) ?>"
                            image="<?= htmlspecialchars($image_tome) ?>"
                            price="<?= htmlspecialchars($tomePrice) ?>"
                            quantite="<?= htmlspecialchars($quantite) ?>"
                            tome="<?= htmlspecialchars($tomeNumber) ?>"
                            stock="<?= htmlspecialchars($tome['quantite_stock'] ?? 0) ?>"
                            dispo="<?= $dispo ?>">
                        </card-panier>
                        
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <?php if ($message): ?>
        <div id="system-message" data-message="<?= htmlspecialchars($message) ?>"></div>
        <div id="type-message" data-message="<?= htmlspecialchars($typeMessage) ?>"></div>
    <?php endif; ?>
    <script type="module" src="public/js/panierComponents.js" defer></script>
</body>
</html>