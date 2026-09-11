const SCROLL_GROUPE = document.querySelectorAll(".scroll-group")

var allLoadedd = false

window.addEventListener("load", () => {
  allLoadedd = true
})

SCROLL_GROUPE.forEach(GROUPE => {

  const VIEW_SCROLL = GROUPE.parentElement
  const GROUPE_ELEMENT = GROUPE.querySelectorAll("*")

  // la touche qui est apuyer par l'utilisateur (if shift)
  let keyPressNow;

  let isDown = false;
  let lastX = 0
  let startX = 0;
  let scrollX = 0;
  let velocity = 0;
  let momentumID
  let elementVisible = [];


  ///////////////
  //      infinyte sysapplyTransform
  ///////////////

  function refreshVisibleList() {
    //clear
    elementVisible = []

    //refill
    GROUPE.querySelectorAll("*").forEach(x => {
      if (x.classList.contains('visible')) {
        elementVisible.push(x);
      }
    });
  }

  function test() {
    console.log("petit verife pour nvim")
    //sa a laire de marcher

    return {
      "caca": 12
    }
  }

  function elAsHiden(el) {
    if (elementVisible[0] == el) {
      let e = el.cloneNode()
      GROUPE.appendChild(e)
      scrollX -= 220
      applyTransform()
      el.remove()
      observer.observe(e)
    }


    refreshVisibleList()
  }


  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {

      const el = entry.target;

      //crée la liste des el visible
      if (!allLoadedd && entry.isIntersecting) {
        elementVisible.push(el);
        el.classList.add('visible')
        return;
      }

      //ci visible
      if (entry.isIntersecting) {
        el.classList.add('visible')
        return;
      }
      el.classList.remove('visible')




      const rect = el.getBoundingClientRect();
      const parentRect = VIEW_SCROLL.getBoundingClientRect();

      elAsHiden(el)

    });
  }, {
    root: VIEW_SCROLL,
    threshold: 0
  });

  GROUPE_ELEMENT.forEach(x => observer.observe(x));


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



  ///////////////
  //      MOMENTOM
  ///////////////

  function startMomentum() {
    function momentum() {
      scrollX -= velocity;
      velocity *= 0.90; // friction

      applyTransform();

      if (Math.abs(velocity) > 0.5) {
        momentumID = requestAnimationFrame(momentum);
      }
    }

    momentum();
  }

  function cancelMomentum() {
    if (momentumID) cancelAnimationFrame(momentumID);
  }


  ///////////////
  //      FUNCTION SCROLL
  ///////////////

  function applyTransform() {
    if (scrollX <= -20) scrollX = -20
    GROUPE.style.transform = `translateX(${-scrollX}px)`;
  }

  function doScrolle(target, duration = 500) {
    const start = scrollX;
    const change = target - start;
    const startTime = performance.now();

    function animate(time) {
      const t = Math.min((time - startTime) / duration, 1);

      const ease = 1 - Math.pow(1 - t, 3);

      scrollX = start + change * ease;
      applyTransform();

      if (t < 1) requestAnimationFrame(animate);
    }

    requestAnimationFrame(animate);
  }

  ///////////////
  //      HANDLER EVENT
  ///////////////

  function startDrag(x) {
    isDown = true;
    startX = x;
    lastX = x;
    velocity = 0;

    cancelMomentum();
    GROUPE.classList.add("dragging");
  }

  function moveDrag(x) {
    if (!isDown) return;
    if (elementVisible.length == GROUPE.querySelectorAll("*").length) return

    const dx = x - lastX;
    scrollX -= dx;

    velocity = dx;

    lastX = x;
    applyTransform();
  }

  function endDrag() {
    isDown = false;
    GROUPE.classList.remove("dragging");

    startMomentum();
  }



  ///////////////
  //      EVENTS bureaux
  ///////////////

  GROUPE.addEventListener("mousedown", (e) => startDrag(e.pageX));
  GROUPE.addEventListener("mousemove", (e) => moveDrag(e.pageX));
  GROUPE.addEventListener("mouseup", endDrag);
  GROUPE.addEventListener("mouseleave", endDrag);

  GROUPE.addEventListener('wheel', (e) => {
    if (keyPressNow === 'Shift' || navigator.platform.includes('Mac')) {

      e.preventDefault();
      const delta = e.deltaX || e.deltaY;

      scrollX += delta;
      if (scrollX < 0) scrollX = 0;

      velocity = delta;
      applyTransform();
    }
  }, { passive: false });

  ///////////////
  //      EVENTS mobil
  ///////////////

  GROUPE.addEventListener("touchstart", (e) => startDrag(e.touches[0].clientX));
  GROUPE.addEventListener("touchmove", (e) => moveDrag(e.touches[0].clientX));
  GROUPE.addEventListener("touchend", endDrag);
});
