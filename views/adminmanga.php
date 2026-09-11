<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration Manga</title>
    <link href="https://fonts.googleapis.com/css2?family=Kavoon&family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <style>
        body.admin-page-body {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            font-family: 'Nunito', sans-serif;
            background-color: #1f3447;
            color: #ffffff;
            min-height: 100vh;
            width: 100vw;
            overflow-x: hidden;
        }

        .admin-layout {
            --bg-dark-blue: #1f3447;
            --input-grey: #6c7a86;
            --accent-blue: #a4d1d5;
            --border-manga: #244C53;

            display: flex;
            flex-wrap: wrap;
            gap: 3rem;
            width: 100%;
            max-width: 1300px;
            margin: 0 auto;
            padding: 4rem 2rem;
            justify-content: center;
            box-sizing: border-box;
        }

        .admin-panel {
            background-color: #0d1a24;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            flex: 1 1 450px;
            max-width: 600px;
            border-top: 5px solid var(--accent-blue);
        }

        .admin-h1 {
            font-family: 'Kavoon', cursive;
            font-size: 2.5rem;
            color: var(--accent-blue);
            text-align: center;
            margin-top: 0;
            margin-bottom: 2rem;
            letter-spacing: 1px;
            font-weight: normal;
        }

        .admin-input-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .admin-label {
            font-family: 'Kavoon', cursive;
            font-size: 1.4rem;
            display: block;
            margin-bottom: 0.5rem;
            color: #ffffff;
        }

        .admin-input {
            width: 100%;
            padding: 1.2rem 1.5rem;
            background-color: var(--input-grey);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 700;
            outline: none;
            box-sizing: border-box;
            transition: box-shadow 0.3s ease;
        }

        .admin-input:focus {
            box-shadow: 0 0 0 3px rgba(164, 209, 213, 0.4);
        }

        .admin-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
            font-weight: 400;
        }

        textarea.admin-input {
            resize: vertical;
            min-height: 100px;
        }

        .admin-btn {
            display: block;
            width: 100%;
            padding: 1rem 2rem;
            background: transparent;
            border: 2px solid var(--accent-blue);
            border-radius: 40px;
            font-family: 'Kavoon', cursive;
            font-size: 1.8rem;
            color: #ffffff;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 2rem;
        }

        .admin-btn:hover {
            background-color: var(--accent-blue);
            color: #0d1a24;
        }

        .divider {
            text-align: center;
            margin: 1rem 0;
            font-size: 1.2rem;
            color: rgba(255,255,255,0.5);
            position: relative;
        }
        .divider::before, .divider::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: rgba(255,255,255,0.2);
        }
        .divider::before { left: 0; }
        .divider::after { right: 0; }

        /* Styles Menu Autocomplete */
        .search-results-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: #0d1a24;
            border: 1px solid var(--accent-blue);
            border-radius: 0 0 12px 12px;
            max-height: 250px;
            overflow-y: auto;
            z-index: 100;
            display: none;
            box-shadow: 0 8px 15px rgba(0,0,0,0.6);
        }
        
        .search-results-menu.active {
            display: block;
        }

        .search-result-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            transition: background 0.2s;
        }

        .search-result-item:last-child {
            border-bottom: none;
        }

        .search-result-item:hover {
            background-color: rgba(164, 209, 213, 0.15);
        }

        .search-result-item img {
            width: 45px;
            height: 65px;
            object-fit: cover;
            margin-right: 15px;
            border-radius: 4px;
        }

        @media screen and (max-width: 900px) {
            .admin-layout {
                padding: 2rem 1rem;
            }
            .admin-h1 { font-size: 2rem; }
            .admin-label { font-size: 1.2rem; }
        }
    </style>
</head>
<body class="admin-page-body">

    <main class="admin-layout">
        
        <!-- ================== AJOUT OEUVRE ================== -->
        <div class="admin-panel">
            <h1 class="admin-h1">Ajouter une œuvre</h1>

            <form id="form-oeuvre" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="addOeuvre">

                <div class="admin-input-group">
                    <label class="admin-label" for="titre_oeuvre">Titre de l’œuvre</label>
                    <input class="admin-input" id="titre_oeuvre" type="text" name="titre_oeuvre" placeholder="Titre de l’œuvre" required>
                </div>

                <div class="admin-input-group">
                    <label class="admin-label" for="synopsis">Synopsis</label>
                    <textarea class="admin-input" id="synopsis" name="synopsis" placeholder="Résumé de l'histoire..." required></textarea>
                </div>

                <div class="admin-input-group">
                    <label class="admin-label" for="date_debut">Date de début</label>
                    <input class="admin-input" id="date_debut" type="date" name="date_debut">
                </div>

                <div class="admin-input-group">
                    <label class="admin-label" for="date_fin">Date de fin</label>
                    <input class="admin-input" id="date_fin" type="date" name="date_fin">
                </div>

                <div class="admin-input-group">
                    <label class="admin-label" for="image_oeuvre">Image (Upload local)</label>
                    <input class="admin-input" id="image_oeuvre" type="file" name="image_oeuvre" accept="image/*">
                </div>

                <div class="divider">OU</div>

                <div class="admin-input-group">
                    <label class="admin-label" for="image_url_oeuvre">Lien de l'image (URL)</label>
                    <input class="admin-input" id="image_url_oeuvre" type="url" name="image_url_oeuvre" placeholder="https://site.com/image.jpg">
                </div>

                <button class="admin-btn" type="submit">Ajouter l’œuvre</button>
            </form>
        </div>


        <div class="admin-panel">
            <h1 class="admin-h1">Ajouter un tome</h1>

            <form id="form-tome" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="addTome">

                <!-- Lien avec oeuvre : Autocomplete -->
                <div class="admin-input-group">
                    <label class="admin-label" for="oeuvre_search">Œuvre associée</label>
                    <input class="admin-input" id="oeuvre_search" type="text" placeholder="Recherchez une œuvre ..." autocomplete="off" required>
                    <input type="hidden" id="oeuvre_id" name="oeuvre_id" required>
                    
                    <!-- Menu déroulant des résultats -->
                    <div id="adminResearchMenu" class="search-results-menu"></div>
                </div>

                <div class="admin-input-group">
                    <label class="admin-label" for="numero_tome">Numéro du tome</label>
                    <input class="admin-input" id="numero_tome" type="number" name="numero_tome" placeholder="Numéro du tome" required>
                </div>

                <div class="admin-input-group">
                    <label class="admin-label" for="quantite">Quantité en magasin</label>
                    <input class="admin-input" id="quantite" type="number" name="quantite" placeholder="Quantité" required>
                </div>

                <div class="admin-input-group">
                    <label class="admin-label" for="date_publication">Date de publication</label>
                    <input class="admin-input" id="date_publication" type="date" name="date_publication">
                </div>

                <div class="admin-input-group">
                    <label class="admin-label" for="image_tome">Image (Upload local)</label>
                    <input class="admin-input" id="image_tome" type="file" name="image_tome" accept="image/*">
                </div>

                <div class="divider">OU</div>

                <div class="admin-input-group">
                    <label class="admin-label" for="image_url_tome">Lien de l'image (URL)</label>
                    <input class="admin-input" id="image_url_tome" type="url" name="image_url_tome" placeholder="https://site.com/image.jpg">
                </div>

                <button class="admin-btn" type="submit">Ajouter le tome</button>
            </form>
        </div>

    </main>

    <script type="module" src="public/js/adminmanga.js" defer></script>
</body>
</html>