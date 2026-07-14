/* ══════════════════════════════════════════════════════════════
   projectes-scroll.js — VoraStudio
   ══════════════════════════════════════════════════════════════
   Carrusel horitzontal de projectes.
   Les cards ja estan renderitzades al HTML pel servidor (SSR).
   Aquest script només aplica animacions i scroll horitzontal.
   ══════════════════════════════════════════════════════════════ */

document.addEventListener("DOMContentLoaded", function () {
  /* ─── Inici ─── */
  animateHeader();

  var track = document.getElementById('projectes-track');
  var cards = track ? track.querySelectorAll('.projecte-card-wrap') : [];

  if (cards.length) {
    animateCards();
    initHorizontalScroll();
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
    var section = document.querySelector(".projectes-scroll");
    if (!track || !section) return;

    var mm = gsap.matchMedia();

    /* Escriptori: animació GSAP (>= 1024px) */
    mm.add("(min-width: 1024px)", function () {
      var scrollTween = gsap.to(track, {
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

      return function () {};
    });

    /* Mòbil i Tablet: scroll natiu + fletxes (< 1024px) */
    mm.add("(max-width: 1023px)", function () {
      var leftBtn = document.querySelector('.carousel-arrow--left');
      var rightBtn = document.querySelector('.carousel-arrow--right');
      if (!leftBtn || !rightBtn) return;

      function scrollLeft() {
        var card = track.querySelector('.projecte-card-wrap');
        var width = card ? (card.offsetWidth + 16) : 276;
        track.scrollBy({ left: -width, behavior: 'smooth' });
      }

      function scrollRight() {
        var card = track.querySelector('.projecte-card-wrap');
        var width = card ? (card.offsetWidth + 16) : 276;
        track.scrollBy({ left: width, behavior: 'smooth' });
      }

      leftBtn.addEventListener('click', scrollLeft);
      rightBtn.addEventListener('click', scrollRight);

      return function () {
        leftBtn.removeEventListener('click', scrollLeft);
        rightBtn.removeEventListener('click', scrollRight);
      };
    });

    refreshScrollTriggerOnImagesLoad();
  }

  function refreshScrollTriggerOnImagesLoad() {
    if (typeof ScrollTrigger === 'undefined') return;
    var images = document.querySelectorAll('img');
    var loadedCount = 0;
    var totalImages = images.length;

    function onImageLoad() {
      loadedCount++;
      if (loadedCount >= totalImages) {
        ScrollTrigger.refresh();
      }
    }

    if (totalImages === 0) {
      ScrollTrigger.refresh();
      return;
    }

    images.forEach(function (img) {
      if (img.complete) {
        onImageLoad();
      } else {
        img.addEventListener('load', onImageLoad);
        img.addEventListener('error', onImageLoad);
      }
    });

    setTimeout(function () {
      ScrollTrigger.refresh();
    }, 1500);
  }
});
