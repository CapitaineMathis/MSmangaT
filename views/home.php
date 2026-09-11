<?php
    $SM = new Serie_Manga();
    function image($SM) : string {
        $listBannedId = [361, 50, 491, 681, 892];
        $id = $SM->getRandomIdWithBannedTag(['Hentai', 'Ecchi'], $listBannedId);
        $result = $SM->getMangaImageUrl($id);
        if ($result){
            return $result;
        }
        else {
            while(!$result){
                $result = $SM->getMangaImageUrl($id);
            }
            return $result;
        }
    }
?>

<link href="https://fonts.googleapis.com/css2?family=Kavoon&display=swap" rel="stylesheet">

<main class="hero-container">
    <div class="hero-content">
        <div class="texte-decoration">
            <h1>Plongez</h1>
            <h1>dans</h1>
            <h1>l’univers</h1>
            <h1 class="text-gradient-1">infini du</h1>
            <h1 class="text-gradient-2">Manga</h1>
        </div>
        
        <div class="button-container">
            <a href="/AP/MSmangaT/list" id="button" class="glass-morphism">Nos Produits</a>
        </div>
    </div>

    <div class="hero-images-grid">
        <div class="img-wrapper col-1">
            <img class="manga-cover" src="<?php echo image($SM) ?>" alt="Couverture Manga 1" />
        </div>
        <div class="img-wrapper col-2">
            <img class="manga-cover" src="<?php echo image($SM) ?>" alt="Couverture Manga 2" />
        </div>
        <div class="img-wrapper col-3">
            <img class="manga-cover" src="<?php echo image($SM) ?>" alt="Couverture Manga 3" />
        </div>
        <div class="img-wrapper col-1">
            <img class="manga-cover" src="<?php echo image($SM) ?>" alt="Couverture Manga 4" />
        </div>
        <div class="img-wrapper col-2">
            <img class="manga-cover" src="<?php echo image($SM) ?>" alt="Couverture Manga 5" />
        </div>
        <div class="img-wrapper col-3">
            <img class="manga-cover" src="<?php echo image($SM) ?>" alt="Couverture Manga 6" />
        </div>
    </div>
</main>