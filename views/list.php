<body>
    <main style="justify-content: flex-start; overflow: hidden;">
        <div id="decoration-carousel">

        </div>
        <?php foreach($list as $theme => $mangas): ?>
            <div class="theme-section">
                <h2 class="theme-title"><?php echo $theme ?></h2>          
                    <div class="scroll-container">
                        <div class="scroll-group">
                            <?php foreach($mangas as $manga): ?>
                                <?php 
                                    $img = !empty($manga['chemin_image']) ? $manga['chemin_image'] : ' ';
                                    $titre = $manga['titre'] ?? ' ';
                                    $tomeModel = new Tome();
                                    $caracteriser = new Caracteriser();
                                    $tomeNumber = $tomeModel->getTomeNumberInSerie($manga['id']);

                                    $tagsArray = $caracteriser->getTagsBySerieId($manga['id']);
                                    $tags = array_column($tagsArray, 'nom_tag');
                                    $tagsString = implode(', ', $tags);
                                    $idSerie = $manga['id'];
                                    
                                ?>
                                <card-manga class="scroll"
                                    type="oeuvre"
                                    url="./manga?id=<?= htmlspecialchars($idSerie) ?>" 
                                    img="<?= $img ?>"
                                    title="<?= htmlspecialchars($titre) ?>"
                                    info="<?= $tomeNumber .' Tome(s)' ?>"
                                    tags="<?= htmlspecialchars($tagsString) ?>"
                                    idserie="<?= htmlspecialchars($manga['id']) ?>">
                                </card-manga>
                            <?php endforeach; ?>
                        </div>
                    </div>
            </div>
        <?php endforeach; ?>
    </main>
    <script src="public/js/scroll.js"></script>
</body>