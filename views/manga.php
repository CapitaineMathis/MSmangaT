<?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    
    $tag = new Tag();
    $SM = new Serie_Manga();
    $caracteriser = new Caracteriser();
    $tagsArray = $caracteriser->getTagsBySerieId($idSerie) ?? [];

    $idSerie = $_GET['id'] ?? null;
    $mangaDetail = $SM->getAllById($idSerie);

    $title = $mangaDetail['titre'];

    $pageTitle = $title ?? 'Manga Inconnu';
    $mangaTitle = trim(str_replace("MILLE SABORDS - ", "", $pageTitle));

    $coverImage = $mangaDetail['chemin_image'] ?? ($tome[0]['chemin_image'] ?? './default.jpg');

    $tags = array_column($tagsArray, 'nom_tag');
    $genres = implode(', ', $tags);

    $author = $mangaDetail['auteur'] ?? 'Auteur inconnu';
    $synopsis = $mangaDetail['synopsis'] ?? 'Pas de synopsis disponible.';
    
    foreach($tome as $manga){
        $banner[] = $manga['chemin_image'];
    }
?>

<style>
    body {
        overflow-x: hidden !important;
    }

    .manga-page-wrapper {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
    }

    .manga-hero-banner {
        position: absolute;
        top: 0;
        left: 0;
        width: 100vw;
        height: 300px;

        background: url(<?= htmlspecialchars($banner[rand(0, count($banner) - 1)]) ?>) center 20%/cover no-repeat;
        z-index: 0;

        mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%);
        -webkit-mask-image: linear-gradient(to bottom, rgba(0,0,0,1) 40%, rgba(0,0,0,0) 100%);
        opacity: 0.2;
    }

    .manga-hero-content {
        position: relative;
        z-index: 10;
        display: flex;
        gap: 50px;
        width: 100%;
        max-width: 1400px;
        margin-top: 150px;
        padding: 0 5%;
        box-sizing: border-box;
    }

    .manga-hero-cover {
        flex-shrink: 0;
    }

    .manga-hero-cover img {
        width: 320px;
        aspect-ratio: 2 / 3;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
        border: 2px solid rgba(255, 255, 255, 0.1);
        background-color: #2f435a;
    }

    .manga-hero-text {
        color: white;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        padding-top: 20px;
    }

    .manga-hero-title {
        font-family: 'Oswald', 'Segoe UI', Tahoma, sans-serif;
        font-size: clamp(2.5rem, 4vw, 4rem);
        font-weight: 800;
        text-transform: uppercase;
        margin: 0;
        line-height: 1.1;
        letter-spacing: 1px;
        text-shadow: 2px 4px 10px rgba(0,0,0,0.4);
    }

    .manga-hero-genres {
        font-size: 1.1rem;
        color: #e5e5e5;
        font-weight: 500;
        margin: 5px 0 40px 0;
    }

    .manga-hero-author {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0 0 10px 0;
    }

    .manga-hero-synopsis {
        font-size: 1.2rem;
        line-height: 1.6;
        max-width: 800px;
        color: #e0e0e0;
        margin: 0;
    }

    .manga-tomes-section {
        width: 100%;
        max-width: 1400px;
        padding: 0 5%;
        margin-top: 60px;
        box-sizing: border-box;
        position: relative;
        z-index: 10;
    }

    .tomes-toolbar {
        display: flex;
        justify-content: flex-end;
        padding-bottom: 15px;
        margin-bottom: 25px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    }

    .sort-btn {
        color: white;
        text-decoration: none;
        font-size: 1.2rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: opacity 0.2s;
    }

    .sort-btn:hover {
        opacity: 0.8;
        text-decoration: underline;
    }

    @media screen and (max-width: 900px) {
        .manga-hero-content {
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 30px;
            margin-top: 100px;
        }

        .manga-hero-cover img {
            width: 250px;
        }

        .manga-hero-text {
            align-items: center;
            padding-top: 0;
        }

        .tomes-toolbar {
            justify-content: center;
        }
    }

    @media screen and (max-width: 768px) {
        /* Règle d'or pour stopper l'overflow global */
        html, body {
            overflow-x: hidden;
            width: 100%;
            position: relative;
        }

        .manga-container {
            padding: 10px; /* Réduit la marge sur les côtés */
        }

        .manga-header {
            flex-direction: column;
            align-items: center;
            padding: 20px 15px; /* Réduit le padding qui poussait le contenu dehors */
            gap: 20px;
            width: 100%;
            box-sizing: border-box; /* Crucial pour que le padding ne s'ajoute pas à la largeur */
        }

        .manga-cover {
            width: 100%;
            max-width: 250px; /* Force l'image à rétrécir si besoin */
            height: auto;
        }

        /* Si tu as une barre de recherche dans la navbar, elle est souvent trop large */
        .search-container {
            width: 100% !important;
            max-width: 200px;
        }
    }

    /* ===== FIX SCROLL (adapté à TON JS) ===== */
/* 
    .scroll-container {
        width: 100%;
        overflow: visible
        position: relative;
    }

    .scroll-group {
        display: flex;
        gap: 20px;
        width: max-content;
    }

    .manga-page-wrapper,
    .manga-tomes-section,
    main {
        overflow: visible !important;
    }

    .scroll-container.dragging {
        cursor: grabbing;
    } */
</style>

<body>
    <div class="manga-hero-banner"></div>
    <main style="display: block; padding-top: 0;">
        <div class="manga-page-wrapper">
            <section class="manga-hero-content">
                <div class="manga-hero-cover">
                    <img src="<?= htmlspecialchars($coverImage) ?>" alt="Couverture de <?= htmlspecialchars($mangaTitle) ?>" />
                </div>
                <div class="manga-hero-text">
                    <h1 class="manga-hero-title"><?= htmlspecialchars($mangaTitle) ?></h1>
                    <p class="manga-hero-genres"><?= htmlspecialchars($genres) ?></p>
                    
                    <h2 class="manga-hero-author"><?= htmlspecialchars($author) ?></h2>
                    <p class="manga-hero-synopsis"><?= htmlspecialchars($synopsis) ?></p>
                </div>
            </section>
            <section class="manga-tomes-section">
                </div>
                    <div class="scroll-container">
                        <div class="scroll-group">
                            <?php foreach($tome as $manga): ?>
                                
                                <?php 
                                    $Avis = new Avis();

                                    $img = !empty($manga['chemin_image']) ? $manga['chemin_image'] : ' ';
                                    $tomeNumber = "Tome " . ($manga['numero_volume'] ?? 'N/A');
                                    $price = $manga['price'] ?? '7';
                                    $quantite_stock = $manga['quantite_stock'] ?? null;
                                    if ($quantite_stock > 0){
                                        $dispo = "true";
                                    }
                                    else {
                                        $dispo = "false";
                                    }
                                    
                                    $notes = $Avis->getNoteByTome($manga['id']) ?? [];
                                    $i=0;
                                    $totalnote=0;
                                    foreach($notes as $uneNote){
                                        if (isset($uneNote['note'])) {
                                            $i++;
                                            $totalnote += (int)$uneNote['note'];
                                        }
                                    }
                                    if ($i>0){
                                        $note = $totalnote / $i;
                                    }
                                    else {
                                        $note = 0;
                                    }
                                    

                                    $reviews = $i;
                                ?>
                                <card-manga class="scroll"
                                    type="tome"
                                    url="./pannier?id=<?= htmlspecialchars($manga['id']) ?>&action=add" 
                                    img="<?= htmlspecialchars($img) ?>"
                                    title="<?= htmlspecialchars($tomeNumber) ?>"
                                    price="<?= htmlspecialchars($price) ?>€"
                                    dispo="<?= htmlspecialchars($dispo) ?>"
                                    note="<?= htmlspecialchars($note) ?>"
                                    reviews="<?= htmlspecialchars($reviews) ?>">
                                </card-manga>
                            <?php endforeach; ?>
                        </div>
                    </div>
            </section>
        </div>
    </main>
    <script src="public/js/scroll.js" defer></script>
</body>