import Notyf from "/AP/MSmangaT/public/js/module/notify/notify.js";
const notyf = new Notyf({duration: 3000});

let avisMenu = null;
let overlay = null;
let note = 0;

function notification(type, message){
    notyf.open({
        type: type,
        message: message
    });
}

function closeAvisMenu() {
    if (avisMenu) avisMenu.remove();
    if (overlay) overlay.remove();
    avisMenu = null;
    overlay = null;
    note = 0;
}

document.addEventListener('click', (e) => {
    const btn = e.composedPath().find(el => el.classList && el.classList.contains('avis-btn'));

    if (btn) {
        if (avisMenu) {
            closeAvisMenu();
        } else {
            overlay = document.createElement('div');
            overlay.className = 'modal-overlay';
            overlay.addEventListener('click', closeAvisMenu); 
            
            avisMenu = document.createElement('div');
            avisMenu.id = 'avisMenu'; 

            fetch(`../MSmangaT/API/getImageAndNameByIdTome.php?id=${encodeURIComponent(btn.dataset.id)}`)
                .then(response => {
                    if (!response.ok) throw new Error('Erreur réseau');
                    return response.json();
                })
                .then(resultData => {
                    if (resultData.success) {
                        const mangaInfo = resultData.data;
                        let starsHtml = ``;

                        for (let i = 1; i <= 5; i++) {
                            starsHtml += `
                                <svg style="cursor: pointer;" class="star-rating" data-index="${i}" width="3rem" height="3rem" viewBox="0 0 20 19">
                                    <defs>
                                        <linearGradient id="grad-${i}" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop class="stop-yellow" offset="0%" stop-color="#FFA033"/>
                                            <stop class="stop-white" offset="0%" stop-color="white"/>
                                        </linearGradient>
                                    </defs>
                                    <path d="M12.0898 7.32324L12.1465 7.49609H19.1787L13.6357 11.5225L13.4893 11.6299L13.5449 11.8018L15.6611 18.3164L10.1201 14.291L9.97363 14.1836L9.82617 14.291L4.28418 18.3164L6.40137 11.8018L6.45801 11.6299L6.31055 11.5225L0.768555 7.49609H7.80078L7.85645 7.32324L9.97266 0.808594L12.0898 7.32324Z" 
                                    fill="url(#grad-${i})" stroke="black" stroke-width="0.5"/>
                                </svg>
                            `;
                        }

                        avisMenu.innerHTML = `
                            <div style="display: flex; flex-direction: column; gap: 15px;">
                                <p style="font-size: 1.6em; color: #8b9aa3;">Tome ${mangaInfo['numero_volume']} de ${mangaInfo['titre']}</p>

                                <img class="manga-cover-avis" src="${mangaInfo['chemin_image']}" alt="Couverture de ${mangaInfo['titre']}" />

                                <div class="stars-container-note">
                                    ${starsHtml}
                                </div>
                                
                                <textarea class="commentaire"
                                    placeholder="Votre avis sur le tome ${mangaInfo['numero_volume']} de ${mangaInfo['titre']}" 
                                    style="width: 100%; min-height: 120px; border-radius: 8px; padding: 10px; background: rgba(0,0,0,0.2); color: white; border: 1px solid #3a5a6e; resize: none;"
                                ></textarea>
                                
                                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                    <button id="cancel-avis" class="btn-secondary">Annuler</button>
                                    <button id="send-avis" class="btn-primary">Envoyer l'avis</button>
                                </div>
                            </div>
                        `;
                        
                        document.body.appendChild(overlay);
                        document.body.appendChild(avisMenu);

                        document.getElementById('cancel-avis').addEventListener('click', closeAvisMenu);

                        const stars = document.querySelectorAll('.star-rating');
                        stars.forEach(star => {
                            star.addEventListener('click', () => {
                                const selectedIndex = parseInt(star.dataset.index);
                                note = selectedIndex;

                                for (let i = 1; i <= 5; i++) {
                                    const gradient = document.getElementById(`grad-${i}`);
                                    const stopYellow = gradient.querySelector('.stop-yellow');
                                    const stopWhite = gradient.querySelector('.stop-white');

                                    if (i <= selectedIndex) {
                                        stopYellow.setAttribute('offset', '100%');
                                        stopWhite.setAttribute('offset', '100%');
                                    } else {
                                        stopYellow.setAttribute('offset', '0%');
                                        stopWhite.setAttribute('offset', '0%');
                                    }
                                }
                            });
                        });

                        const sendAvis = document.querySelector("#send-avis");
                        const textarea = document.querySelector(".commentaire");

                        sendAvis.addEventListener('click', () => {
                            const commentaire = textarea.value;
                            
                            const formData = new FormData();
                            formData.append('id', btn.dataset.id);
                            formData.append('commentaire', commentaire);
                            formData.append('note', note);

                            fetch('../MSmangaT/API/sendAvis.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => {
                                if (!response.ok) throw new Error('Erreur réseau');
                                return response.json();
                            })
                            .then(resultData => {
                                if (resultData.success) {
                                    closeAvisMenu();
                                    notification("success", "Avis enregistré avec succès !");
                                } else {
                                    notification("error", "Erreur lors de l'enregistrement de l'avis.");
                                }
                            })
                            .catch(error => {
                                console.error('Erreur lors de l\'envoi de l\'avis:', error);
                                notification("error", "Erreur : Impossible d'envoyer l'avis.");
                            });
                        });
                    } else {
                        notification("error", "Impossible de charger les informations du tome.");
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche:', error);
                    notification("error", "Erreur : problème de connexion au serveur.");
                });
        }
    }
});