document.addEventListener("DOMContentLoaded", function () {
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
