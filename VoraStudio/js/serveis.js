document.addEventListener("DOMContentLoaded", () => {
  // Inicialització Lenis (Smooth Scroll)
  if (typeof Lenis !== "undefined") {
    const lenis = new Lenis();
    lenis.on("scroll", ScrollTrigger.update);
    gsap.ticker.add((time) => {
      lenis.raf(time * 1000);
    });
    gsap.ticker.lagSmoothing(0);
  }

  function split(text) {
    gsap.set(text, {
      perspective: "800px",
      transformStyle: "preserve-3d",
    });
  }

  // ==========================================================================
  // 1. HERO / TOP SECTION
  // ==========================================================================
  const tlHero = gsap.timeline();

  const serveiTitle = new SplitText(".servei-title", { type: "chars, lines", mask: "lines" });
  const serveiText = new SplitText(".servei-text", { type: "lines", mask: "lines" });
  const queFem = new SplitText(".servei-box-title", { type: "chars,lines", mask: "lines" });
  split(".servei-title");
  split(".servei-text");
  split(".servei-box-title");

  tlHero
    .from(serveiTitle.chars, {
      duration: 0.4,
      opacity: 0,
      yPercent: 100,
      clipPath: "inset(100% 0 0 0)",
      stagger: {
        each: 0.05,
        from: "start",
      },
    })
    .from(
      serveiText.lines,
      {
        duration: 1,
        opacity: 0,
        y: 50,
        yPercent: 100,
        clipPath: "inset(100% 0 0 0)",
        stagger: 0.2,
      },
      "-=1.0"
    )
    .from(
      ".servei-images img",
      {
        duration: 1.5,
        clipPath: "inset(0 100% 0 0)",
      },
      "<0.5"
    )
    .from(
      queFem.chars,
      {
        duration: 0.4,
        opacity: 0,
        yPercent: 100,
        clipPath: "inset(100% 0 0 0)",
        stagger: 0.05,
      },
      "<0.5"
    )
    .from(
      ".lista_Servei",
      {
        opacity: 0,
        y: 50,
        duration: 1,
        stagger: 0.2,
      },
      "<0.5"
    )
    .from(
      "#parlem-btn",
      {
        opacity: 0,
        scale: 0.8,
        y: 20,
        duration: 0.8,
        ease: "back.out(1.7)",
      },
      "<0.3"
    );

  // ==========================================================================
  // 2. SECCIÓN: EL NOSTRE PROCÉS
  // ==========================================================================
  const procesTitle = new SplitText(".servei-process-title", { type: "chars, lines", mask: "lines" });
  split(".servei-process-title");

  const tlProces = gsap.timeline({
    scrollTrigger: {
      trigger: ".servei-process",
      start: "top 80%",
      toggleActions: "play none none reverse",
    },
  });

  tlProces
    .from(".servei-process__img img", {
      duration: 1.8,
      clipPath: "inset(0 100% 0 0)",
      scale: 1.1,
      ease: "power3.out",
    })
    .from(
      procesTitle.chars,
      {
        duration: 0.4,
        opacity: 0,
        yPercent: 100,
        clipPath: "inset(100% 0 0 0)",
        stagger: 0.04,
      },
      "<0.3"
    )
    .from(
      ".servei-process-item",
      {
        duration: 0.8,
        opacity: 0,
        y: 40,
        stagger: 0.2,
        ease: "power2.out",
      },
      "<0.4"
    );

  // ==========================================================================
  // 3. SECCIÓN: PER A TU SI...
  // ==========================================================================
  const targetTitle = new SplitText(".servei-target-title", { type: "chars, lines", mask: "lines" });
  split(".servei-target-title");

  const tlTarget = gsap.timeline({
    scrollTrigger: {
      trigger: ".servei-target",
      start: "top 80%",
      toggleActions: "play none none reverse",
    },
  });

  tlTarget
    .from(targetTitle.chars, {
      duration: 0.4,
      opacity: 0,
      yPercent: 100,
      clipPath: "inset(100% 0 0 0)",
      stagger: 0.04,
    })
    .from(
      ".target-list li",
      {
        duration: 0.8,
        opacity: 0,
        x: -30,
        stagger: 0.15,
        ease: "power2.out",
      },
      "<0.3"
    )
    .from(
      "#treballem-btn",
      {
        duration: 0.8,
        opacity: 0,
        scale: 0.8,
        y: 20,
        ease: "back.out(1.7)",
      },
      "<0.4"
    );
});
