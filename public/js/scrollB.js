/**
 * Code créé par TIAGO le 2 avril
 * 
 * Retravaillé le même jour par Maël pour :
 * 
 *      - le commenter
 *      - faire un système infini
 *      - créer la fonction doScroll() (car trop peu de neurones pour créer une fonction quand il y a un truc qui se répète 4 fois)
 *      - une dépression
 *      - un dodo
 *      - à l'aide
 *      - le saviez-vous ? Un ventilateur à 3 pales est plus efficient qu'avec 4 pales
 * 
 *      - 22h40 : trop tard pour passer d’un système de scroll physique à un système théorique sans cloner les élement
 * 
 *      je stock
 *      ((x % n) + n) % n
 */


const scrollContainers = document.querySelectorAll('.scroll-container');
var allLoadedd = false
/**
 * Met le système de scroll sur les éléments avec la classe 'scroll-container'
 */

scrollContainers.forEach(scroll => {




    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible'); 
            } else {
                entry.target.classList.remove('visible'); 
            }
            if(!allLoadedd)return;

            console.log(scroll,scroll.querySelectorAll(':scope > .scroll-group > card-manga'))
        });
    }, {
        root: document,
        rootMargin: '0px',
        threshold: 0.1
    });

    scroll.querySelectorAll(':scope > .scroll-group > card-manga').forEach(child => {
        observer.observe(child);
    });


    // click préser 
    let isDown = false;

    // valeur du debut de mousedown
    let startX = 0;

    // valeur de transition actuel
    let currentTranslate = 0;
    let prevTranslate = 0;

    // la touche qui est apuyer par l'utilisateur (if shift)
    let keyPressNow;

    //met une animation de merde (mon avie est un fait)
    scroll.style.transition = `transform 0.5s`;


    /**
     * la logic du scrolle 
     */
    function doScrolle(dx) {
        if (!Array.from(scroll.querySelectorAll(".scroll-group > card-manga")).find(x=>x.classList.contains('visible') == false) ) return;
        if (currentTranslate >= -7 && dx > -7) {
            scroll.style.transition = `transform 0.5s ease`;
            scroll.style.transform = `translateX(-5px)`;
            currentTranslate = -5;
            return;
        }
        currentTranslate = prevTranslate + dx;
        scroll.style.transform = `translateX(${currentTranslate}px)`;

    }

    /**
     * swhitch isDown a true 
     * add la class dragging a la zone de scroll
     */
    scroll.addEventListener('mousedown', (e) => {
        isDown = true;
        scroll.classList.add('dragging');
        startX = e.pageX;

    });

    /**
     * ci le curseur est clicker
     * add la class a la zone de scroll
     */
    scroll.addEventListener('mousemove', (e) => {
        if (!isDown) return;

        const dx = e.pageX - startX;
        doScrolle(dx)

    });

    /**
     * swhitch isDown a false 
     * add la class dragging a la zone de scroll
     */
    scroll.addEventListener('mouseup', () => {
        isDown = false;
        scroll.classList.remove('dragging');
        prevTranslate = currentTranslate;
    });

    /**
     * swhitch isDown a false 
     * add la class dragging a la zone de scroll
     */
    scroll.addEventListener('mouseleave', () => {
        isDown = false;
        scroll.classList.remove('dragging');
        prevTranslate = currentTranslate;
    });

    ////////
    //      lisent key press 
    ////////

    /**
     * regarde l'event 
     */
    window.addEventListener('keydown', (e) => {
        if (!keyPressNow) {
            keyPressNow = e.key;
        }
    })

    /**
     * regarde l'event 
     */
    window.addEventListener('keyup', (e) => {
        if (keyPressNow) {
            keyPressNow = null;
        }
    })

    /**
     * fait le scrolle avec la molet
     * 
     * ci t sur mac ou Shift press
     */
    scroll.addEventListener('wheel', (e) => {
        if (keyPressNow === 'Shift' || navigator.platform.includes('Mac')) {

            const dx = e.deltaX || e.deltaY;

            doScrolle(dx)
            prevTranslate = currentTranslate;

            e.preventDefault();
        }
    }, { passive: false });

    /////////
    //      pour mobile
    /////////
    /**
     * écoute le doit 
     */
    scroll.addEventListener('touchstart', (e) => {
        isDown = true;
        scroll.classList.add('dragging');
        startX = e.touches[0].clientX;
    });


    /**
     * écoute le doit 
     */
    scroll.addEventListener('touchend', () => {
        isDown = false;
        scroll.classList.remove('dragging');
        prevTranslate = currentTranslate;
    });




    /**
     * scrolle ci le doit bouge
     */
    scroll.addEventListener('touchmove', (e) => {
        if (!isDown) return;

        const dx = e.touches[0].clientX - startX;
        doScrolle(dx)

    });


});
addEventListener("load",()=>{
    allLoadedd = true
})
