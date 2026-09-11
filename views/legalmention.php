<head>
    <style>
        .legal > address {
            color: #f5f5f5;
        }

        .legal > h1 {
            font-size: 2.2rem;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
            color: #f5f5f5;
        }

        .legal > section {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 15px;
        }

        .legal > h2 {
            font-size: 1.3rem;
            color: #f5f5f5;
            border-left: 4px solid #888;
            padding-left: 10px;
            margin-bottom: 20px;
        }

        .legal > h3 {
            font-size: 1.1rem;
            margin-top: 10px;
            color: #ccc;
        }

        .legal > p {
            font-size: 0.95rem;
            color: #ddd;
        }

        .legal > ul {
            padding-left: 20px;
        }

        .legal > li {
            margin-bottom: 5px;
        }

        .legal > address {
            font-style: normal;
            line-height: 1.5;
        }

        .legal > a {
            color: #ffffff;
            text-decoration: none;
        }

        .legal > a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {

            .legal > h1 {
                font-size: 1.6rem;
            }

            .legal > h2 {
                font-size: 1.1rem;
            }
        }

    </style>
</head>

<body>
    <main style="display: flex; justify-content: flex-start; flex-direction: column; align-items: center;">
        <h1 class="legal" style="color: #ffffff; font-size: 2.2rem;">Mentions légales</h1>
        <div style="display: flex; flex-direction: column; gap:30px; align-items: flex-start;">
            <section class="legal">
                <h2 class="legal">Propriétaire</h2>
                <p class="legal">
                    <strong>Raison sociale :</strong> MILLE SABORDS SARL<br>
                    <strong>Nom commercial :</strong> MILLE SABORDS<br>
                    <strong>Gérant :</strong> M. Olivier POIRIER<br>
                    <strong>Code APE :</strong> 4762Z
                </p>
            </section class="legal">
            <section class="legal">
                <h2 class="legal">Informations légales</h2>
                <p class="legal">
                    <strong>SIRET :</strong> 380 468 413 00022<br>
                    <strong>RCS :</strong> La Rochelle B 380 468 413<br>
                    <strong>N° TVA intracommunautaire :</strong> FR 43 380468413<br>
                    <strong>Capital social :</strong> 60 980,00 €<br>
                    <strong>Date d'immatriculation :</strong> 15/01/1991<br>
                    <strong>Nationalité :</strong> Française
                </p>
            </section>
            <section class="legal">
                <h2 class="legal">Coordonnées</h2>
                <address class="legal">
                    <strong>Adresse :</strong><br>
                    20 Rue du Palais<br>
                    17000 LA ROCHELLE<br><br>

                    <strong>Email :</strong><br>
                    <a class="legal" href="mailto:contact@1000-sabords.fr">contact@1000-sabords.fr</a>
                </address>
            </section>
        </div>
    </main>
</body>