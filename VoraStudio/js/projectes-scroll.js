/* ══════════════════════════════════════════════════════════════
   projectes-scroll.js — VoraStudio
   ══════════════════════════════════════════════════════════════
   Carrusel horitzontal de projectes.
   Carrega els projectes des del CMS. Mostra spinner mentre
   carrega i missatge d'error si falla.
   ══════════════════════════════════════════════════════════════ */

document.addEventListener("DOMContentLoaded", function () {
  /* ─── CMS API Config (local per proves) ─── */
  var CMS_API_BASE = 'https://voracms.voradata.cat';
  var CMS_API_TOKEN = 'UJIv45gTpMGckBdJjDg3UmkuqZzOWqHV';

  /* ─── Inici ─── */
  loadCarouselFromCMS();

  /* ─── Animació header (independent del carrusel) ─── */
  animateHeader();

  /* ════════════════════════════════════════════════════════════
     Carregar projectes del CMS i generar carrusel
     ════════════════════════════════════════════════════════════ */

  async function loadCarouselFromCMS() {
    try {
      var res = await fetch(CMS_API_BASE + '/api/public/web/vorastudio-projects?locale=ca', {
        headers: { 'Authorization': 'Bearer ' + CMS_API_TOKEN }
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      var json = await res.json();
      var projects = json.data;
      if (!projects || !projects.length) throw new Error('Sense dades');

      /* Ordenar per camp "ordre" si existeix */
      projects.sort(function (a, b) {
        return (a.ordre || 999) - (b.ordre || 999);
      });
      buildCarousel(projects);

      /* Inicialitzar animacions i scroll horitzontal */
      requestAnimationFrame(function () {
        animateCards();
        initHorizontalScroll();
      });
      return;
    } catch (err) {
      console.error('Carrusel: error al carregar del CMS:', err.message);
      showError("No s'han pogut carregar els projectes");
    }
  }

  function showError(msg) {
    var spinner = document.getElementById('carousel-spinner');
    if (spinner) {
      spinner.classList.add('carousel-spinner--error');
      spinner.querySelector('.carousel-spinner__text').textContent = msg;
    }
  }

  function buildCarousel(projects) {
    var track = document.getElementById('projectes-track');
    if (!track) return;

    var isProjectPage = document.body.classList.contains('page-projecte');
    var basePath = isProjectPage ? '' : '../projectes/';

    track.innerHTML = projects.map(function (p) {
      var title = p.titol || 'Projecte';
      var slug = p.slug_del_projecte || p.project_slug || '';
      var packRaw = p.packs || p.pack_type || 'Essencial';
      var packLabel = packRaw;

      /* Imatge del carrusel: imatge_principal > main_image > primera de galeria */
      var imgSrc = '';
      if (p.imatge_principal && p.imatge_principal.length) {
        imgSrc = p.imatge_principal[0].url || '';
      }
      if (!imgSrc && p.main_image) {
        imgSrc = typeof p.main_image === 'string' ? p.main_image
          : (p.main_image.url || '');
      }
      if (!imgSrc && p.galeria && p.galeria.length) {
        imgSrc = p.galeria[0].url || '';
      }

      return '<div class="projecte-card-wrap">'
        + '<div class="projecte-card__info">'
        + '<h3 class="projecte-card__title">' + escHtml(title) + '</h3>'
        + '<p class="projecte-card__subtitle">' + escHtml(packLabel) + '</p>'
        + '</div>'
        + '<a href="' + basePath + 'projecte.php?project=' + escAttr(slug) + '" class="projecte-card" data-project="' + escAttr(slug) + '">'
        + (imgSrc ? '<img src="' + escAttr(imgSrc) + '" alt="' + escAttr(title) + '" loading="lazy" />' : '')
        + '</a>'
        + '</div>';
    }).join('');
  }

  function escHtml(text) {
    var d = document.createElement('div');
    d.textContent = text;
    return d.innerHTML;
  }

  function escAttr(text) {
    return escHtml(text).replace(/"/g, '&quot;');
  }

  /* ════════════════════════════════════════════════════════════
     Animacions
     ════════════════════════════════════════════════════════════ */

  function animateHeader() {
    var header = document.querySelector("#sub-header");
    if (header) {
      gsap.set(header, { autoAlpha: 0, y: -50 });
      gsap.to(header, { autoAlpha: 1, y: 0, duration: 1, ease: "power3.out" });
    }
  }

  function animateCards() {
    var cards = document.querySelectorAll(".projectes-scroll .projecte-card-wrap");
    var scrollSection = document.querySelector(".projectes-scroll");
    if (cards.length && scrollSection) {
      gsap.set(cards, { autoAlpha: 0, y: 60 });
      gsap.to(cards, {
        autoAlpha: 1,
        y: 0,
        stagger: 0.15,
        duration: 1.2,
        ease: "power3.out",
        scrollTrigger: {
          trigger: scrollSection,
          start: "top 85%",
        },
      });
    }
  }

  /* ════════════════════════════════════════════════════════════
     Scroll horitzontal (GSAP + ScrollTrigger)
     ════════════════════════════════════════════════════════════ */

  function initHorizontalScroll() {
    var track = document.querySelector(".projectes-scroll__track");
    var section = document.querySelector(".projectes-scroll");
    if (!track || !section) return;

    /* Mòbil: scroll natiu + fleques */
    if (window.innerWidth < 768) {
      initMobileArrows(track);
      return;
    }

    /* Escriptori: animació GSAP */
    gsap.to(track, {
      x: function () {
        return -(track.scrollWidth - section.offsetWidth);
      },
      ease: "none",
      scrollTrigger: {
        trigger: section,
        pin: true,
        start: "top 17%",
        end: function () {
          return "+=" + (track.scrollWidth - section.offsetWidth);
        },
        scrub: 1,
        invalidateOnRefresh: true,
      },
    });

    ScrollTrigger.refresh();
  }

  function initMobileArrows(track) {
    var leftBtn = document.querySelector('.carousel-arrow--left');
    var rightBtn = document.querySelector('.carousel-arrow--right');
    if (!leftBtn || !rightBtn) return;

    function scrollBy(amount) {
      track.scrollBy({ left: amount, behavior: 'smooth' });
    }

    function cardWidth() {
      var card = track.querySelector('.projecte-card-wrap');
      if (!card) return 260;
      return card.offsetWidth + 16; /* width + gap */
    }

    leftBtn.addEventListener('click', function () { scrollBy(-cardWidth()); });
    rightBtn.addEventListener('click', function () { scrollBy(cardWidth()); });
  }
});
