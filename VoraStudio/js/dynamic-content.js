/* ══════════════════════════════════════════════════════════════
   dynamic-content.js — VoraStudio
   ══════════════════════════════════════════════════════════════
   Les dades del projecte ja estan renderitzades al HTML pel
   servidor (SSR). Aquest script només aplica animacions GSAP
   a les seccions pre-existents.
   ══════════════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function () {
  const isProjectePage = document.body.classList.contains("page-projecte");
  if (!isProjectePage) return;

  animateSections();
});

/* ══════════════════════════════════════════════════════════════
   Animacions (GSAP)
  ══════════════════════════════════════════════════════════════ */

function animateSections() {
  if (typeof ScrollTrigger !== 'undefined') {
    var images = document.querySelectorAll('img');
    var loadedCount = 0;
    var totalImages = images.length;

    function onImageLoad() {
      loadedCount++;
      if (loadedCount >= totalImages) {
        ScrollTrigger.refresh();
      }
    }

    if (totalImages > 0) {
      images.forEach(function (img) {
        if (img.complete) {
          onImageLoad();
        } else {
          img.addEventListener('load', onImageLoad);
          img.addEventListener('error', onImageLoad);
        }
      });
    } else {
      ScrollTrigger.refresh();
    }

    setTimeout(function () {
      ScrollTrigger.refresh();
    }, 1500);
  }

  gsap.set(".project-hero__left, .project-hero__right", { autoAlpha: 0, y: 50 });
  const tlHero = gsap.timeline({ defaults: { duration: 1, ease: "power3.out" } });
  tlHero.to(".project-hero__left", { autoAlpha: 1, y: 0 })
        .to(".project-hero__right", { autoAlpha: 1, y: 0 }, "-=0.8");

  gsap.set(".project-strategy__block", { autoAlpha: 0, y: 50 });
  gsap.to(".project-strategy__block", {
    autoAlpha: 1, y: 0, stagger: 0.4, duration: 2, ease: "power2.out",
    scrollTrigger: { trigger: ".project-strategy", start: "top 80%" },
  });

  gsap.from(".project-gallery__item", {
    autoAlpha: 0, y: 120, duration: 1.5, stagger: 0.35, ease: "power3.out",
    scrollTrigger: { trigger: "#project-gallery", start: "top 55%" },
  });
}
