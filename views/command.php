<head>
    <style>
        body {
            background-color: #0f2233;
            margin: 0;
            font-family: 'Inter', system-ui, sans-serif;
            color: #ffffff;
        }

        .checkout-wrapper {
            padding: 90px 10px;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            box-sizing: border-box;
            margin: 0px;
        }

        .checkout-container {
            display: flex;
            flex-direction: row;
            gap: 30px;
            max-width: 1100px;
            width: 100%;
            align-items: flex-start;
            justify-content: center;
        }

        .checkout-card {
            background-color: #2c455c;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        .form-card { 
            flex: 1.1; 
        }
        .summary-card { 
            flex: 0.9; 
        }

        .card-title {
            color: #ffffff;
            font-family: 'Kavoon', cursive;
            font-size: 28px;
            margin: 0 0 10px 0;
            text-align: center;
        }

        .section-subtitle {
            color: #ffffff;
            font-family: 'Kavoon', cursive;
            font-size: 22px;
            margin: 30px 0 10px 0;
        }

        .divider {
            border: 0;
            height: 1px;
            background-color: rgba(255, 255, 255, 0.15);
            margin: 15px 0 25px 0;
        }

        .input-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .input-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .input-group.full-width {
            margin-bottom: 20px;
        }

        .input-group label {
            color: #ffffff;
            font-family: 'Kavoon', cursive;
            font-size: 15px;
            letter-spacing: 0.5px;
        }

        .input-group input {
            background-color: #6f7f8c;
            border: none;
            border-radius: 10px;
            padding: 14px 16px;
            color: #ffffff;
            font-family: 'Inter', system-ui, sans-serif;
            font-size: 16px;
            outline: none;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
            transition: outline 0.2s ease;
        }

        .input-group input:focus {
            outline: 2px solid #2f8bbd;
            background-color: #7b8b98;
        }

        #cart-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .total-label {
            color: #ffffff;
            font-family: 'Kavoon', cursive;
            font-size: 22px;
        }

        #total-price, #price {
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            font-size: 24px;
        }

        .submit-row {
            display: flex;
            justify-content: center;
        }

        .btn-submit {
            background-color: #2f8bbd;
            color: #ffffff;
            font-family: 'Kavoon', cursive;
            font-size: 18px;
            border: none;
            border-radius: 50px;
            padding: 14px 35px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            text-decoration: none;
            display: inline-block;
        }

        .btn-submit:hover {
            background-color: #3bb2ed;
            transform: translateY(-2px);
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.4);
            padding: 15px;
            border-radius: 8px;
            color: #ffc107;
            margin-bottom: 20px;
            text-align: center;
        }

        @media (max-width: 900px) {
            .checkout-container {
                flex-direction: column;
            }
            .checkout-card {
                width: 100%;
                box-sizing: border-box;
            }
        }

        @media (max-width: 550px) {
            .input-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="checkout-wrapper">
        <div class="checkout-container">
            <div class="checkout-card form-card">
                <h2 class="card-title">Commande de produit</h2>
                <hr class="divider">

                <form action="" method="POST">
                    
                    <div class="input-grid">
                        <div class="input-group">
                            <label>Nom</label>
                            <input type="text" name="nom" required>
                        </div>
                        <div class="input-group">
                            <label>Prénom</label>
                            <input type="text" name="prenom" required>
                        </div>
                    </div>

                    <div class="input-group full-width">
                        <label>Numéro de Téléphone</label>
                        <input type="number" name="telephone" required>
                    </div>

                    <h3 class="section-subtitle">Adresse de livraison</h3>
                    <hr class="divider">


                    <div class="input-grid">
                        <div class="input-group">
                            <label>Numéro de rue</label>
                            <input type="number" name="numero_rue" min="0" required>
                        </div>
                        <div class="input-group">
                            <label>Nom de rue</label>
                            <input type="text" name="nom_rue" required>
                        </div>
                    </div>

                    <div class="input-group full-width">
                        <label>Complément d'adresse</label>
                        <input type="text" name="complement">
                    </div>

                    <div class="input-grid">
                        <div class="input-group">
                            <label>Ville</label>
                            <input type="text" name="ville" required>
                        </div>
                        <div class="input-group">
                            <label>Code Postal</label>
                            <input type="number" name="code_postal" required>
                        </div>
                    </div>

                    <hr class="divider" style="margin-top: 35px;">
                    
                    <div id="cart-info">
                        <h1 id="price"><span id="total-items"> 0 </span> Article(s)</h1>
                        <h1 id="total-price">0.00€</h1>
                    </div>

                    <div class="submit-row">
                        <button type="submit" class="btn-submit">Soumettre la commande</button>
                    </div>
                </form>
            </div>

            <div class="checkout-card summary-card">
                <h2 class="card-title">Votre commande</h2>
                <hr class="divider">

                <div id="cart-list" class="summary-items">
                    <?php 
                        if (empty($contenuePanier)){
                            header("Location: pannier?notification=Erreur : Votre panier est vide.&type=error");
                            exit();
                        }
                    ?>
                        <?php if(!empty($contenuePanier)): ?>
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
                                stock="<?= htmlspecialchars($tome['quantite_stock']) ?>"
                                dispo="<?= $dispo ?>">
                            </card-panier>
                            
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>

    <div id="system-message" data-message="<?= htmlspecialchars($message ?? '') ?>"></div>
    <div id="type-message" data-message="<?= htmlspecialchars($typeMessage ?? '') ?>"></div>
    <script type="module" src="public/js/panierComponents.js" defer></script>
</body>