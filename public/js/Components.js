class CardManga extends HTMLElement {

    connectedCallback() {
        this.attachShadow({ mode: 'open' });

        const url = this.getAttribute('url') || '#';
        const title = this.getAttribute('title');
        const image = this.getAttribute('img');
        const dispo = this.getAttribute('dispo') !== "false"; // true par défaut
        const tag = this.getAttribute('tags');
        const info = this.getAttribute('info'); // ex: "13 Tome"
        const price = this.getAttribute('price'); // ex: "7,30€"
        const note = parseFloat(this.getAttribute('note'));
        const reviews = this.getAttribute('reviews'); // ex: "21"
        const type = this.getAttribute('type'); // tome / oeuvre ou rien pour les deux
        const idSerie = this.getAttribute('idserie');

        // On crée la zone interactive différemment selon le type
        let interactiveBoxHTML = '';
        let button = '';

       

        if (type === 'oeuvre') {
            // Pour une oeuvre : afficher uniquement le nombre de tomes, masquer prix/note/dispo et désactiver le hover 
            button = 'Voir plus';
            interactiveBoxHTML = `
                <div class="interactive-info-box no-hover">
                    <div class="view-default">
                        ${info || ''}
                    </div>
                </div>
            `;
        } else if (type === 'tome') {
            // Pour un tome : afficher directement le prix/dispo/note par défaut sans le nombre de tomes
            button = 'Ajouter au panier';
            interactiveBoxHTML = `
                <div class="interactive-info-box no-hover">
                    <div class="view-hover" style="opacity: 1; transform: translateY(0); pointer-events: auto;">
                        <div class="dispo-row">
                            <span class="dot"></span>
                            <span>${dispo ? 'Disponible' : 'Indisponible'}</span>
                        </div>
                        <div class="price-stars-row">
                            <span class="price">${price || 'N/A'}</span>
                            ${!isNaN(note) ? `
                            <div class="stars-container">
                                ${this.genNoteStar(note)}
                                ${reviews ? `<span class="reviews">(${reviews})</span>` : ''}
                            </div>` : ''}
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Comportement par défaut (avec animation au survol)
            interactiveBoxHTML = `
                <div class="interactive-info-box">
                    <div class="view-default">
                        ${info || ''}
                    </div>
                    
                    <div class="view-hover">
                        <div class="dispo-row">
                            <span class="dot"></span>
                            <span>${dispo ? 'Disponible' : 'Indisponible'}</span>
                        </div>
                        <div class="price-stars-row">
                            <span class="price">${price || 'N/A'}</span>
                            ${!isNaN(note) ? `
                            <div class="stars-container">
                                ${this.genNoteStar(note)}
                                ${reviews ? `<span class="reviews">(${reviews})</span>` : ''}
                            </div>` : ''}
                        </div>
                    </div>
                </div>
            `;
        }

        // Construction du HTML interne
        this.shadowRoot.innerHTML = `
        <style>

            * {
                -webkit-user-drag: none;
                user-select: none;
                -moz-user-select: none;
                -webkit-user-select: none;
                -ms-user-select: none;
                user-select: none;
            }

            :host {
                display: block;
                width: 200px;
                height: 282px;
                border-radius: 12px;
                overflow: hidden;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                box-sizing: border-box;
            }

            .card {
                position: relative;
                width: 100%;
                height: 100%;
                overflow: hidden;
                cursor: pointer;
            }

            .bg-image {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.4s ease;
                
            }

            /* Le dégradé qui apparaît au hover global */
            .gradient-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 60%;
                background: linear-gradient(0deg, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.7) 40%, rgba(0,0,0,0) 100%);
                opacity: 0;
                transition: opacity 0.3s ease;
                pointer-events: none;
                z-index: 1;
            }

            .manga-cover {
                position: relative;
                z-index: 1;
            }

            #scroll-container.dragging .manga-cover {
                pointer-events: none;
            }
            

            .card:hover .gradient-overlay {
                opacity: 1;
            }

            /* Conteneur des textes */
            .content {
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                padding: 12px;
                box-sizing: border-box;
                z-index: 2;
                display: flex;
                flex-direction: column;
                justify-content: flex-end;
            }

            /* Titre : Toujours visible, avec un contour noir pour être lisible sans dégradé */
            .title {
                font-size: 16px;
                font-weight: bold;
                color: white;
                margin: 0;
                text-shadow: 
                    -1px -1px 0 #000,  
                     1px -1px 0 #000,
                    -1px  1px 0 #000,
                     1px  1px 0 #000,
                     0px  4px 4px rgba(0,0,0,0.5);
                transition: text-shadow 0.3s ease, transform 0.3s ease;
                
            }

            .card:hover .title {
                text-shadow: none; /* On enlève le contour noir quand le dégradé apparaît */
            }

            /* Contenu caché par défaut (tags, infos, bouton) */
            .hidden-content {
                max-height: 0;
                opacity: 0;
                overflow: hidden;
                transition: max-height 0.4s ease, opacity 0.3s ease;
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .card:hover .hidden-content {
                max-height: 150px; /* Hauteur suffisante pour tout afficher */
                opacity: 1;
                margin-top: 4px;
            }

            .tags {
                font-size: 10px;
                color: #e0e0e0;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* --- LA ZONE INTERACTIVE (Le "Tome") --- */
            .interactive-info-box {
                position: relative;
                height: 35px; /* Hauteur fixe pour éviter les sauts lors du changement */
                width: 100%;
            }

            .view-default, .view-hover {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                display: flex;
                align-items: center;
                transition: opacity 0.2s ease, transform 0.2s ease;
            }

            /* Vue 1 : "13 Tome" */
            .view-default {
                font-size: 14px;
                font-weight: bold;
                color: white;
                opacity: 1;
                transform: translateY(0);
            }

            /* Vue 2 : Dispo, Prix, Etoiles (Cachée par défaut) */
            .view-hover {
                opacity: 0;
                pointer-events: none;
                transform: translateY(10px);
                flex-wrap: wrap;
                align-content: center;
            }

            /* Le hover magique sur la zone d'info (Désactivé si classe .no-hover présente) */
            .interactive-info-box:not(.no-hover):hover .view-default {
                opacity: 0;
                transform: translateY(-10px);
            }

            .interactive-info-box:not(.no-hover):hover .view-hover {
                opacity: 1;
                pointer-events: auto;
                transform: translateY(0);
            }

            /* Details de la Vue 2 */
            .dispo-row {
                width: 100%;
                display: flex;
                align-items: center;
                gap: 5px;
                font-size: 10px;
                color: #ccc;
                margin-bottom: 2px;
            }

            .dot {
                width: 6px;
                height: 6px;
                border-radius: 50%;
                background-color: ${dispo ? '#2ecc71' : '#e74c3c'};
            }

            .price-stars-row {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .price {
                font-size: 14px;
                font-weight: bold;
                color: white;
            }

            .stars-container {
                display: flex;
                align-items: center;
                gap: 2px;
            }

            .stars-container svg {
                width: 10px;
                height: 10px;
            }
            
            .reviews {
                font-size: 9px;
                color: #ccc;
                margin-left: 2px;
            }

            /* Bouton "voir plus" */
            .btn {
                display: block;
                width: 100%;
                padding: 6px 0;
                text-align: center;
                background: rgba(255, 255, 255, 0.6);
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
                color: black;
                text-decoration: none;
                font-weight: bold;
                font-size: 13px;
                border-radius: 20px;
                transition: background 0.2s ease;
                margin-top: 4px;
            }

            .btn:hover {
                background: rgba(255, 255, 255, 0.9);
            }
        </style>

        <div class="card">
            <img class="bg-image" src="${image}" alt="${title}">
            <div class="gradient-overlay"></div>
            
            <div class="content">
                <h3 class="title">${title}</h3>
                
                <div class="hidden-content">
                    ${tag ? `<div class="tags">${tag}</div>` : ''}
                    
                    <!-- Zone dynamique -->
                    ${interactiveBoxHTML}

                    <a href="${url}" class="btn">${button}</a>
                </div>
            </div>
        </div>
        `;
    }

    static SVG_STAR(percent){
        const gradId = `grad-${Math.random().toString(36).substr(2, 9)}`;
        return `
        <svg viewBox="0 0 20 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="${gradId}" x1="0" y1="0" x2="1" y2="0">
                    <stop offset="${percent * 100}%" stop-color="#FFA033"/>
                    <stop offset="${percent * 100}%" stop-color="white"/>
                </linearGradient>
            </defs>
            <path d="M12.0898 7.32324L12.1465 7.49609H19.1787L13.6357 11.5225L13.4893 11.6299L13.5449 11.8018L15.6611 18.3164L10.1201 14.291L9.97363 14.1836L9.82617 14.291L4.28418 18.3164L6.40137 11.8018L6.45801 11.6299L6.31055 11.5225L0.768555 7.49609H7.80078L7.85645 7.32324L9.97266 0.808594L12.0898 7.32324Z" 
                fill="url(#${gradId})" stroke="black" stroke-width="0.5"/>
        </svg>`;
    }

    genNoteStar(note){
        let E = "";
        for(let i = 0; i < 5; i++){
            let percent = Math.min(Math.max(note - i, 0), 1);
            E += CardManga.SVG_STAR(percent);
        }
        return E;
    }
}

customElements.define('card-manga', CardManga);