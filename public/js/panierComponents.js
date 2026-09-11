class CardPanier extends HTMLElement {

    showStockModal(onConfirm, onCancel, title, tome, currentQty, first=false) {
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8); display: flex; align-items: center;
            justify-content: center; z-index: 10000; font-family: sans-serif;
        `;

        const modal = document.createElement('div');
        modal.style.cssText = `
            background: #2c455c; padding: 25px; border-radius: 12px;
            border: 1px solid #ffffff57; text-align: center; max-width: 400px; color: white;
        `;

        modal.innerHTML = `
            <h2 style="margin-top:0; color: #fff3cd;">Stock limité</h2>
            <p>Le tome ${tome} de ${title} n'est plus disponible en stock, Le délai de livraison pourrait être plus long.</p>
            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
                <button id="modal-cancel" style="padding: 10px 20px; border-radius: 5px; cursor: pointer; background: #666; color: white; border: none;">Annuler</button>
                <button id="modal-confirm" style="padding: 10px 20px; border-radius: 5px; cursor: pointer; background: #17b0e3; color: white; border: none;">Ajouter quand même</button>
            </div>
        `;

        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        overlay.querySelector('#modal-cancel').onclick = () => {
            overlay.remove();
            if (onCancel) onCancel();
        };
        overlay.querySelector('#modal-confirm').onclick = () => {
            overlay.remove();
            onConfirm();
        };
    }

    updateRemoveButton() {
        const quantite = parseInt(this.getAttribute('quantite'));
        const btn = this.shadowRoot.querySelector('.btn-remove');

        if (!btn) return;

        if (quantite <= 1) {
            btn.classList.remove('btn-minus');
            btn.classList.add('btn-delete');
            btn.innerHTML = `
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            `;
        } else {
            btn.classList.remove('btn-delete');
            btn.classList.add('btn-minus');
            btn.textContent = '-';
        }
    }

    connectedCallback() {
        this.attachShadow({ mode: 'open' });
        
        const idSerie = this.getAttribute('id-serie');
        const idTome = this.getAttribute('id-tome');
        const title = this.getAttribute('title');
        const image = this.getAttribute('image');
        const price = parseFloat(this.getAttribute('price')).toFixed(2);
        const quantite = this.getAttribute('quantite');
        const tome = this.getAttribute('tome');
        const isDispo = this.getAttribute('dispo') !== "false";
        const stock = parseInt(this.getAttribute('stock') || 0);

        const type = this.getAttribute('type') || "panier" ; //panier ou commandtraker
        const note = parseFloat(this.getAttribute('note'));
        const reviews = this.getAttribute('reviews');
        const currentStatus = this.getAttribute('status') || "En attente de paiement";

        

        let style = `
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Kavoon&display=swap');

                .galss-morphism {
                    background: rgba(217, 217, 217, 0.16);
                    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1), inset 0px 0px 8px 0px rgba(255, 255, 255, 0.4);
                    backdrop-filter: blur(6.4px);
                    -webkit-backdrop-filter: blur(6.4px);
                    overflow: hidden;
                }

                .ligne-vertical {
                    height: 3vh;
                    width: 2px;
                    border-radius: 1px;
                    margin-left: 20px;
                    margin-right: 20px;
                    background: #ffffff38;
                    border: none;
                }

                :host {
                    display: block;
                    padding: 16px 0;
                    font-family: 'Inter', system-ui, sans-serif;
                    transition: transform 0.2s ease;
                }
                
                .card {
                    display: flex;
                    align-items: flex-start;
                    justify-content: space-between;
                    gap: 20px;
                }

                .left-content {
                    display: flex;
                    gap: 24px;
                    flex: 1;
                }

                #left-bottom-content {
                    display: flex;
                    align-items: flex-start;
                    gap: 12px;
                    font-size: 0.9rem;
                    color: #ffffff;
                    margin-top: 4px;
                    flex-direction: column;
                }

                #left-container {
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                    width: 100%;
                }

                .manga-image {
                    border: 1px solid #ffffff57;
                    width: 10rem;
                    height: auto;
                    border-radius: 6px;
                    object-fit: cover;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
                }

                .manga-info {
                    display: flex;
                    flex-direction: column;
                    gap: 6px;
                    margin-top: 2vh;
                }

                .manga-info > h1{
                    color: white;
                    font-weight: 100;
                    font-family: 'Kavoon', cursive;
                    font-size: 30px;
                    margin: 0;
                }

                .manga-title {
                    font-size: 1.25rem;
                    font-weight: 700;
                    color: #1f2937;
                    margin: 0;
                }

                .manga-tome {
                    font-size: 1rem;
                    color: #6b7280;
                    margin: 0;
                }

                .status {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 0.9rem;
                    color: #ffffff;
                }

                .status-dot {
                    width: 10px;
                    height: 10px;
                    border-radius: 50%;
                    background-color: ${isDispo ? '#10b981' : '#ef4444'};
                }

                .right-content {
                    display: flex;
                    align-items: center;
                    gap: 32px;
                    margin-top: 2vh;
                }

                #quantity-selector {
                    display: flex;
                    align-items: center;
                    border: #2f435a solid 1px;
                    border-radius: 999px;
                    padding: 4px;
                }

                #quantity-container {
                    display: flex;
                    gap: 10px;
                    align-items: center;
                    width: 100%;
                }

                #voir-article-btn {
                    text-decoration: none;
                    color: #BFBFBF;
                    font-family: 'Kavoon', cursive;
                    font-size: 18px;
                    white-space: nowrap;
                }
                
                #bottom-link-style {
                    text-decoration: none;
                    color: #BFBFBF;
                    font-family: 'Kavoon', cursive;
                    font-size: 18px;
                    white-space: nowrap;
                }

                .btn-qty {
                    background: transparent;
                    border: none;
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.2rem;
                    color: #ffffff;
                    cursor: pointer;
                    transition: background 0.2s;
                }

                .qty-val {
                    min-width: 30px;
                    text-align: center;
                    font-weight: 600;
                    font-size: 1.1rem;
                    color: #ffffff;
                }

                .price {
                    font-size: 1.4rem;
                    font-weight: 700;
                    color: #ffffff;
                    min-width: 80px;
                    text-align: right;
                }

                .rating-container {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    margin-top: 2px;
                }
                .stars { display: flex; align-items: center; }
                .reviews { font-size: 0.8rem; color: #8b9aa3; }

                .btn-delete {
                    background: transparent;
                    border: none;
                    cursor: pointer;
                    padding: 8px;
                    border-radius: 18px;
                    color: #9ca3af;
                    transition: color 0.2s, background 0.2s;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .btn-delete:hover {
                    color: #ef4444;
                    background: #fee2e2;
                }

                svg {
                    width: 24px;
                    height: 24px;
                }

                #responsive {
                    display: none;
                }

                .ligne-horizontal {
                    height: 2px;
                    width: 100%;
                    border-radius: 1px;
                    margin-top: 30px;
                    background: #ffffff38;
                    border: none;
                }
                                
                @media (max-width: 768px) {
                    .right-content { 
                        display: none;
                    }
                    .manga-image {
                        width: 7rem;
                    }
                    .manga-info > h1 {
                        font-size: 24px;
                    }
                    #responsive {
                        display: block;
                        margin-left: auto;
                    }
                    .ligne-vertical {
                        display: none;
                    }
                    #quantity-container {
                        flex-wrap: wrap;
                        gap: 15px;
                    }
                }

                @media (max-width: 480px) {
                    .card {
                        flex-direction: column;
                        align-items: center;
                    }
                    .left-content {
                        flex-direction: column;
                        align-items: center;
                        text-align: center;
                        gap: 15px;
                        width: 100%;
                    }
                    .manga-image {
                        width: 100%;
                        max-width: 180px;
                    }
                    .manga-info {
                        flex-direction: column-reverse;
                        margin-top: 5px;
                        margin-bottom: 5px;
                    }
                    .status {
                        justify-content: center;
                    }
                    #quantity-container {
                        justify-content: center;
                        flex-direction: column-reverse;
                    }

                    #responsive {
                        margin-left: 0;
                        margin-top: 10px;
                        font-size: 1.6rem;
                    }

                    #left-bottom-content {
                        align-items: center;
                    }
                }
            </style>
            `;

            if (type === "panier"){
                this.shadowRoot.innerHTML = `${style}
                    <div class="card">
                        <div class="left-content">
                            <img src="${image}" alt="Couverture de ${title}" class="manga-image">
                            <div id="left-container">
                                <div id="left-top-content">
                                    <div class="manga-info">
                                        <h1 class="manga-tome">Tome ${tome}</h1>
                                        <h1 class="manga-title">${title}</h1>
                                    </div>
                                </div>
                                <div id="left-bottom-content">
                                    <div class="status">
                                        <span class="status-dot"></span>
                                        <span>${isDispo ? 'Disponible' : 'Indisponible'}</span>
                                    </div>
                                    <div id="left-bottom-container">
                                        <div id="quantity-container">
                                            <div id="quantity-selector" class="galss-morphism">
                                                <button class="btn-qty btn-remove"></button>
                                                <span class="qty-val">${quantite}</span>
                                                <button class="btn-qty btn-plus">+</button>
                                            </div>
                                            <hr class="ligne-vertical"></hr>
                                            <a id="voir-article-btn" href="manga?id=${idSerie}">Voir l'article</a>
                                            <hr class="ligne-vertical"></hr>
                                            <div class="price" id="responsive">${price}€</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="right-content">
                            <div class="price">${price}€</div>
                        </div>
                    </div>
                    <hr class="ligne-horizontal"></hr>
                `;
            }
            else {

                let statusHTML = '';
                if (currentStatus === 'Livrée') {
                    statusHTML = `
                        <div>
                            <div id="bottom-link-style" class="avis-btn" style="cursor:pointer;" data-id="${idTome}">
                                Ajouter un avis
                            </div>
                        </div>
                        <hr class="ligne-vertical"></hr>
                    `;
                }

                this.shadowRoot.innerHTML = `${style}
                    <div class="card">
                        <div class="left-content">
                            <img src="${image}" alt="Couverture de ${title}" class="manga-image">
                            <div id="left-container">
                                <div id="left-top-content">
                                    <div class="manga-info">
                                        <h1 class="manga-tome">Tome ${tome}</h1>
                                        <h1 class="manga-title">${title}</h1>
                                    </div>
                                </div>
                                <div id="left-bottom-content">
                                    <div class="price" id="responsive">${price}€</div>
                                        ${!isNaN(note) ? `
                                        <div class="rating-container">
                                            <div class="stars">${this.genNoteStar(note)}</div>
                                            ${reviews ? `<span class="reviews">(${reviews})</span>` : ''}
                                        </div>` : ''}
                                    <div id="left-bottom-container">
                                        <div id="quantity-container">
                                            ${statusHTML}
                                            <a id="bottom-link-style" href="manga?id=${idSerie}">Voir l'article</a>
                                            <hr class="ligne-vertical"></hr>
                                            <h1 id="bottom-link-style">Quantité : <span class="qty-val">${quantite}</span></h1>
                                            <hr class="ligne-vertical"></hr>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="right-content">
                            <div class="price">${price}€</div>
                        </div>
                    </div>
                    <hr class="ligne-horizontal"></hr>
                `;
            }
        this.updateRemoveButton();
        this.addEventListeners();

        const currentQty = parseInt(this.getAttribute('quantite'));
        const stockInitial = parseInt(this.getAttribute('stock') || 0);
        const chemin = window.location.pathname;

        const params = new URLSearchParams(window.location.search);
        const isCommandePage = chemin.includes("/command");
        const isAddingAction = params.get('action') === 'add';
        const isTargetedTome = params.get('id') === this.getAttribute('id-tome');

        if (type === "panier" && !isCommandePage && currentQty > stockInitial && isAddingAction && isTargetedTome) {
            setTimeout(() => {
                this.showStockModal(
                    () => {},
                    () => { 
                        if (currentQty === 1) {
                            const idTome = this.getAttribute('id-tome');
                            fetch(`?action=delete&id=${idTome}`);
                            this.remove();
                            window.dispatchEvent(new CustomEvent('cart-updated'));
                        } else {
                            const btnRemove = this.shadowRoot.querySelector('.btn-remove');
                            if (btnRemove) btnRemove.click();
                        }
                        window.history.replaceState({}, document.title, window.location.pathname);
                    },
                    title, 
                    tome, 
                    currentQty, 
                    true
                );
            }, 100);
        }
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
            E += CardPanier.SVG_STAR(percent);
        }
        return E;
    }
    
    addEventListeners() {
        const btnPlus = this.shadowRoot.querySelector('.btn-plus');
        const btnMinus = this.shadowRoot.querySelector('.btn-minus');
        const btnDeletes = this.shadowRoot.querySelector('.btn-delete');
        const qtyVal = this.shadowRoot.querySelector('.qty-val');
        const btnRemove = this.shadowRoot.querySelector('.btn-remove');
        
        const updateQuantity = async (action) => {
            let currentQty = parseInt(this.getAttribute('quantite'));
            const idTome = this.getAttribute('id-tome');

            if (action === 'add') {
                currentQty++;
            } else if (action === 'remove' && currentQty > 1) {
                currentQty--;
            } else if (action === 'delete') {
                currentQty = 0;
            }

            if (currentQty === 0) {
                await fetch(`?action=delete&id=${idTome}`);
                this.remove();
            } else {
                this.setAttribute('quantite', currentQty);
                qtyVal.textContent = currentQty;
                fetch(`?action=${action}&id=${idTome}`);
            }

            this.updateRemoveButton();

            window.dispatchEvent(new CustomEvent('cart-updated'));
        };

        if (btnPlus) {
            btnPlus.addEventListener('click', () => {
                const currentQty = parseInt(this.getAttribute('quantite'));
                const title = this.getAttribute('title');
                const tome = this.getAttribute('tome');
                const stockInitial = parseInt(this.getAttribute('stock') || 0);
                
                if (currentQty >= stockInitial) {
                    this.showStockModal(
                        () => { updateQuantity('add'); },
                        () => { console.log("Ajout annulé"); },
                        title, 
                        tome, 
                        currentQty,
                        false
                    );
                } else {
                    updateQuantity('add');
                }
            });
        }

        if (btnRemove) {
            btnRemove.addEventListener('click', () => {
                const qty = parseInt(this.getAttribute('quantite'));
                if (qty <= 1) {
                    updateQuantity('delete');
                } else {
                    updateQuantity('remove');
                }
            });
        }
        
    }
}

customElements.define('card-panier', CardPanier);

document.addEventListener('DOMContentLoaded', () => {

    const recalculateTotals = () => {
        const items = document.querySelectorAll('card-panier');
        let totalItems = 0;
        let totalPrice = 0;

        items.forEach(item => {
            const qty = parseInt(item.getAttribute('quantite'));
            const price = parseFloat(item.getAttribute('price'));
            
            totalItems += qty;
            totalPrice += (qty * price);
        });

        document.getElementById('total-items').textContent = totalItems;
        document.getElementById('total-price').textContent = totalPrice.toFixed(2) + '€';

        if (items.length === 0) {
            const url = window.location.pathname;
            if(url === "/AP/MSmangaT/command"){
                window.location = "/AP/MSmangaT/pannier?notification=Erreur : Votre panier est vide.&type=error";
            }
            const cartList = document.getElementById('cart-list');
            if (cartList) {
                cartList.innerHTML = '<h3 class="empty-cart">Votre panier est actuellement vide.</h3>';
            }
        }
    };

    recalculateTotals();

    window.addEventListener('cart-updated', recalculateTotals);
});