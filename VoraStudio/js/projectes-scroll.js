document.addEventListener("DOMContentLoaded", function () {
  // 1. Animar el header al cargar la página
  var header = document.querySelector("#sub-header");
  if (header) {
    gsap.set(header, { autoAlpha: 0, y: -50 });
    gsap.to(header, { autoAlpha: 1, y: 0, duration: 1, ease: "power3.out" });
  }

  // 2. Animar las cards de la galería horizontal al entrar en vista (ScrollTrigger)
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

  initHorizontalScroll();

  function initHorizontalScroll() {
    var track = document.querySelector(".projectes-scroll__track");
    var section = document.querySelector(".projectes-scroll");
    if (!track || !section) return;

    ScrollTrigger.refresh();

    gsap.to(track, {
      x: function () {
        return -(track.scrollWidth - section.offsetWidth);
      },
      ease: "none",
      scrollTrigger: {
        trigger: section,
        pin: true,
        start: function () {
          //mobils
          if (window.innerWidth < 768) {
            return document.body.classList.contains("page-projectes-gallery")
              ? //Mobils
                "top 15%"
              : //Escritori
                "top 40%";
          }
          return "top 17%";
        },
        end: function () {
          return "+=" + (track.scrollWidth - section.offsetWidth);
        },
        scrub: 1,
        invalidateOnRefresh: true,
      },
    });
  }
});
