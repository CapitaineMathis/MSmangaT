import Notyf from "/AP/MSmangaT/public/js/module/notify/notify.js";
const notyf = new Notyf({
    duration: 3000,
    position: { x: 'right', y: 'top' },
});

document.addEventListener('DOMContentLoaded', () => {

    const searchInput = document.getElementById('oeuvre_search');
    const hiddenIdInput = document.getElementById('oeuvre_id');
    const resultsContainer = document.getElementById('adminResearchMenu');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const text = e.target.value.trim();
            clearTimeout(searchTimeout);

            if (text.length === 0) {
                resultsContainer.innerHTML = '';
                resultsContainer.classList.remove('active');
                hiddenIdInput.value = '';
                return;
            }

            searchTimeout = setTimeout(() => {
                fetch(`../MSmangaT/API/search.php?search=${encodeURIComponent(text)}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Erreur réseau');
                        return response.json();
                    })
                    .then(resultData => {
                        if (resultData.success) {
                            const mangaIds = resultData.data;
                            resultsContainer.innerHTML = '';

                            if (mangaIds.length === 0) {
                                resultsContainer.innerHTML = '<div style="padding: 15px; color: #fff;">Aucun manga trouvé...</div>';
                                resultsContainer.classList.add('active');
                                return;
                            }

                            mangaIds.forEach((mangaId) => {
                                fetch(`../MSmangaT/API/getImageAndNameById.php?id=${encodeURIComponent(mangaId)}`)
                                    .then(response => response.json())
                                    .then(mangaInfoRes => {
                                        if (mangaInfoRes.success) {
                                            const mangaInfo = mangaInfoRes.data;

                                            // Création de l'élément de liste
                                            const item = document.createElement('div');
                                            item.className = 'search-result-item';
                                            item.innerHTML = `
                                                <img src="${mangaInfo['chemin_image']}" alt="Couverture" />
                                                <span>${mangaInfo['titre']}</span>
                                            `;

                                            // Comportement au clic
                                            item.addEventListener('click', () => {
                                                searchInput.value = mangaInfo['titre'];
                                                hiddenIdInput.value = mangaId;
                                                resultsContainer.innerHTML = '';
                                                resultsContainer.classList.remove('active');
                                            });

                                            resultsContainer.appendChild(item);
                                            resultsContainer.classList.add('active');
                                        }
                                    })
                                    .catch(err => console.error(err));
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la recherche:', error);
                    });
            }, 500);
        });

        // Cacher le menu si on clique en dehors
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !resultsContainer.contains(e.target)) {
                resultsContainer.classList.remove('active');
            }
        });
    }

    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);
            const actionUrl = window.location.href; // Envoie sur la même page du contrôleur

            try {
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    body: formData
                });

                // On récupère d'abord en texte brut pour éviter un crash si PHP renvoie une erreur HTML
                const responseText = await response.text();
                let data;

                try {
                    data = JSON.parse(responseText);
                } catch (jsonError) {
                    console.error("Le serveur n'a pas renvoyé du JSON. Réponse brute :", responseText);
                    throw new Error("Erreur du serveur Mathis na pas donner la permission que le serveur upload");
                }

                if (data.success) {
                    notyf.success(data.message);
                    form.reset(); // Vider le formulaire après succès

                    if (form.id === 'form-tome') {
                        hiddenIdInput.value = ''; // Bien reset l'ID caché aussi
                    }
                } else {
                    notyf.error("Erreur : " + data.message);
                }

            } catch (error) {
                console.error("Erreur lors de l'envoi du formulaire :", error);
                notyf.error(error.message || "Une erreur serveur s'est produite lors de l'ajout.");
            }
        });
    });

});