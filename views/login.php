<?php
    $SM = new Serie_Manga();
    function image($SM) : string {
        $listBannedId = [361, 50, 491, 681, 892];
        $id = $SM->getRandomIdWithBannedTag(['Hentai', 'Ecchi', 'Erotica'], $listBannedId);
        $result = $SM->getMangaImageUrl($id);
        return $result ? $result : "https://picsum.photos/300/450";
    }

    $redirect = $_GET['origine'] ?? 'list';
?>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Kavoon&family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body.login-page-body {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            font-family: 'Nunito', sans-serif;
            background-color: #1f3447;
            color: #ffffff;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
        }

        .login-layout {
            --bg-dark-blue: #1f3447;
            --input-grey: #6c7a86;
            --accent-blue: #a4d1d5;
            --border-manga: #244C53;

            display: flex;
            width: 100vw;
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        .login-layout * {
            box-sizing: border-box;
        }

        .login-left-panel {
            width: 40%;
            position: relative;
            background-color: #0d1a24;
            overflow: hidden;
            border-right: 5px solid rgba(0,0,0,0.2);
            box-shadow: 5px 0 15px rgba(0,0,0,0.5);
            z-index: 10;
            transition: all 0.3s ease;
        }

        .login-manga-cover {
            position: absolute;
            width: 55%;
            aspect-ratio: 2 / 3;
            object-fit: cover;
            border: 4px solid var(--border-manga);
            border-radius: 12px;
            box-shadow: 3px 5px 15px rgba(0,0,0,0.6);
            -webkit-user-drag: none;
            user-select: none;
        }

        .login-cover-1 { top: 30%; right: -5%; transform: rotate(<?php echo rand(1,10) ?>deg); z-index: <?php echo rand(3,5) ?>; }
        .login-cover-2 { top: -<?php echo rand(1,30) ?>%; right: -12%; transform: rotate(-<?php echo rand(1,10) ?>deg); z-index: <?php echo rand(3,5) ?>; width: 60%; }
        .login-cover-3 { top: <?php echo rand(10,40) ?>%; left: -<?php echo rand(1,10) ?>%; transform: rotate(-<?php echo rand(1,10) ?>deg); z-index: 3; width: 52%; }
        .login-cover-4 { top: <?php echo rand(1,12) ?>%; right: 18%; transform: rotate(<?php echo rand(1,10) ?>deg); z-index: 4; width: 55%; }
        .login-cover-5 { bottom: -10%; left: -5%; transform: rotate(10deg); z-index: 5; width: 5<?php echo rand(1,9) ?>%; }
        .login-cover-6 { bottom: -2%; right: -10%; transform: rotate(-6deg); z-index: 6; width: <?php echo rand(3,4) ?>0%; }
        .login-cover-7 { top: -<?php echo rand(1,10) ?>%; left: -3%; transform: rotate(<?php echo rand(1,10) ?>deg); z-index: 1; }
        .login-cover-8 { bottom: -<?php echo rand(1,30) ?>%; left: 30%; z-index: 1; }

        .login-right-panel {
            width: 60%;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg-dark-blue);
            padding: 2rem;
            position: relative;
            transition: width 0.3s ease;
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 500px;
            transition: max-width 0.3s ease;
        }

        .login-titles { margin-bottom: 2.5rem; }

        .login-h1 {
            font-family: 'Kavoon', cursive;
            font-size: 3.5rem;
            line-height: 1.1;
            margin: 0;
            letter-spacing: 1px;
            font-weight: normal;
        }

        .login-title-white { color: #ffffff; }
        .login-title-blue { color: var(--accent-blue); }

        .login-input-group { margin-bottom: 1.5rem; }

        .login-label {
            font-family: 'Kavoon', cursive;
            font-size: 1.6rem;
            display: block;
            margin-bottom: 0.5rem;
        }

        .login-input {
            width: 100%;
            padding: 1.2rem 1.5rem;
            background-color: var(--input-grey);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 1.2rem;
            font-weight: 700;
            outline: none;
            -webkit-user-select: text !important;
            user-select: text !important;
        }

        .login-input-password {
            width: 100%;
            border: none;
            color: #ffffff;
            font-size: 1.2rem;
            font-weight: 700;
            outline: none;
            -webkit-user-select: text !important;
            user-select: text !important;
            background: none;
        }

        .login-input-container {
            display: flex;
            width: 100%;
            padding: 1.2rem 1.5rem;
            background-color: var(--input-grey);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            outline: none;
            height: 4.95rem;
            flex-direction: row;
            align-items: center;
            gap: 20px;
        }

        .login-form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            margin-bottom: 2.5rem;
        }

        .login-btn-connect {
            display: block;
            width: fit-content;
            padding: 0.8rem 2.5rem;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 40px;
            font-family: 'Kavoon', cursive;
            font-size: 1.8rem;
            color: #ffffff;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
        }

        .login-btn-connect:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #ffffff;
        }

        .login-terms-link {
            color: var(--accent-blue);
            text-decoration: underline;
        }

        .login-forgot-password {
            color: #ffffff;
            text-decoration: underline;
            cursor: pointer;
        }

        .login-eye-icon {
            width: 28px;
            height: 28px;
            fill: var(--bg-dark-blue);
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.2s;
        }

        #margin-left {
            height: 110%;
            width: 30px;
            position: absolute;
            top: 0;
            left: 0;
            background-color: #1d2f44;
            z-index: 10;
            border-right: #0d1a24 solid 5px;
        }

        .login-register-link {
            margin-top: 15px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .login-register-link a {
            color: var(--accent-blue);
            text-decoration: underline;
        }

        @media screen and (min-width: 1600px) {
            .login-form-wrapper { max-width: 700px; }
            .login-h1 { font-size: 5rem; }
            .login-label { font-size: 2.2rem; }
            .login-input { padding: 1.6rem 2rem; font-size: 1.5rem; border-radius: 18px; }
            .login-btn-connect { font-size: 2.5rem; padding: 1.2rem 4rem; border-radius: 60px; }
            .login-form-options { font-size: 1.2rem; }
        }

        @media screen and (max-width: 1300px) {
            .login-input-container { height: 3.81rem !important; }

            .login-cover-1 { top: 30%; right: -5%; transform: rotate(<?php echo rand(1,10) ?>deg); z-index: <?php echo rand(3,5) ?>; }
            .login-cover-2 { top: -<?php echo rand(1,30) ?>%; right: -12%; transform: rotate(-<?php echo rand(1,10) ?>deg); z-index: <?php echo rand(3,5) ?>; width: 70%; }
            .login-cover-3 { top: <?php echo rand(10,40) ?>%; left: -<?php echo rand(1,10) ?>%; transform: rotate(-<?php echo rand(1,10) ?>deg); z-index: 3; width: 62%; }
            .login-cover-4 { top: <?php echo rand(1,12) ?>%; right: 18%; transform: rotate(<?php echo rand(1,10) ?>deg); z-index: 4; width: 65%; }
            .login-cover-5 { bottom: -10%; left: -5%; transform: rotate(10deg); z-index: 5; width: 6<?php echo rand(1,9) ?>%; }
            .login-cover-6 { bottom: -2%; right: -10%; transform: rotate(-6deg); z-index: 6; width: <?php echo rand(5,7) ?>0%; }
            .login-cover-7 { top: -<?php echo rand(1,10) ?>%; left: -3%; transform: rotate(<?php echo rand(1,10) ?>deg); z-index: 1; }
            .login-cover-8 { bottom: -<?php echo rand(1,30) ?>%; left: 30%; z-index: 1; }
        }

        @media screen and (max-width: 1150px) {
            .login-cover-1 { top: 30%; right: -5%; transform: rotate(<?php echo rand(1,10) ?>deg); z-index: <?php echo rand(3,5) ?>; }
            .login-cover-2 { top: -<?php echo rand(1,30) ?>%; right: -12%; transform: rotate(-<?php echo rand(1,10) ?>deg); z-index: <?php echo rand(3,5) ?>; width: 90%; }
            .login-cover-3 { top: <?php echo rand(10,40) ?>%; left: -<?php echo rand(1,10) ?>%; transform: rotate(-<?php echo rand(1,10) ?>deg); z-index: <?php echo rand(3,5) ?>; width: 82%; }
            .login-cover-4 { top: <?php echo rand(1,12) ?>%; right: 18%; transform: rotate(<?php echo rand(1,10) ?>deg); z-index: <?php echo rand(4,7) ?>; width: 65%; }
            .login-cover-5 { bottom: -10%; left: -5%; transform: rotate(10deg); z-index: 5; width: 8<?php echo rand(1,9) ?>%; }
            .login-cover-6 { bottom: -2%; right: -10%; transform: rotate(-6deg); z-index: 6; width: <?php echo rand(7,9) ?>0%; }
            .login-cover-7 { top: -<?php echo rand(1,10) ?>%; left: -3%; transform: rotate(<?php echo rand(1,10) ?>deg); z-index: <?php echo rand(1,5) ?>; }
            .login-cover-8 { bottom: -<?php echo rand(1,30) ?>%; left: 30%; z-index: <?php echo rand(1,5) ?>; }
        }

        @media screen and (max-width: 900px) {
            .login-left-panel {
                display: none;
            }
            .login-right-panel {
                width: 100%;
                padding: 1.5rem;
            }
            .login-h1 { font-size: 2.5rem; }
            .login-label { font-size: 1.4rem; }
            .login-input { padding: 1rem; }
        }

        @media screen and (max-width: 400px) {
            .login-h1 { font-size: 2rem; }
        }
    </style>
</head>

<body class="login-page-body">

    <div class="login-layout">
        
        <div class="login-left-panel">
            <div id="margin-left"></div>
            <img class="login-manga-cover login-cover-1" src="<?php echo image($SM) ?>" alt="Couverture Manga 1" />
            <img class="login-manga-cover login-cover-2" src="<?php echo image($SM) ?>" alt="Couverture Manga 2" />
            <img class="login-manga-cover login-cover-3" src="<?php echo image($SM) ?>" alt="Couverture Manga 3" />
            <img class="login-manga-cover login-cover-4" src="<?php echo image($SM) ?>" alt="Couverture Manga 4" />
            <img class="login-manga-cover login-cover-5" src="<?php echo image($SM) ?>" alt="Couverture Manga 5" />
            <img class="login-manga-cover login-cover-6" src="<?php echo image($SM) ?>" alt="Couverture Manga 6" />
            <img class="login-manga-cover login-cover-7" src="<?php echo image($SM) ?>" alt="Couverture Manga 7" />
            <img class="login-manga-cover login-cover-8" src="<?php echo image($SM) ?>" alt="Couverture Manga 8" />
        </div>

        <div class="login-right-panel">
            <div class="login-form-wrapper">
                
                <div class="login-titles">
                    <h1 class="login-h1 login-title-white">Heureux de vous</h1>
                    <h1 class="login-h1 login-title-blue">revoir chez nous</h1>
                </div>

                <form action="" method="POST">
                    
                    <div class="login-input-group">
                        <label class="login-label" for="login-identifiant">Identifiant</label>
                        <div class="login-input-wrapper">
                            <input class="login-input" type="text" id="login-identifiant" name="identifiant" required>
                        </div>
                    </div>

                    <div class="login-input-group">
                        <label class="login-label" for="login-password">Mot de passe</label>
                        <div class="login-input-wrapper">
                            <div class="login-input-container">
                                <input class="login-input-password" type="password" id="login-password" name="password" required>
                                <svg class="login-eye-icon" viewBox="0 0 24 24" onclick="togglePassword()">
                                    <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                                </svg>
                            </div>
                            
                        </div>
                    </div>

                    <div class="login-form-options">
                        <a href="#" class="login-forgot-password">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="login-btn-connect">Se Connecter</button>

                    <div class="login-register-link">
                        Pas encore de compte ? <a href="register?redirect=<?php echo $redirect ?>">crée mon compte</a>
                    </div>

                </form>
            </div>
        </div>
    </div>
    <?php if ($message): ?>
        <div id="system-message" data-message="<?= htmlspecialchars($message) ?>"></div>
        <div id="type-message" data-message="<?= htmlspecialchars($typeMessage) ?>"></div>
    <?php endif; ?>
</body>