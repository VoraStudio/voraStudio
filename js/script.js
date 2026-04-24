/* ==========================================================================
   ANIMACIONS VoraStudio (v26 Slide-up Mask Reveal & Intro Transitions)
   ========================================================================== */
try {
  gsap.registerPlugin(ScrollTrigger, TextPlugin, SplitText);
} catch (e) {
  gsap.registerPlugin(ScrollTrigger, TextPlugin); // Fallback segur
}
const lenis = new Lenis();
lenis.on("scroll", ScrollTrigger.update);
gsap.ticker.add((time) => lenis.raf(time * 1000));
gsap.ticker.lagSmoothing(0);

window.addEventListener("DOMContentLoaded", () => {
  // Selectores Globales
  const preloader = document.getElementById("preloader");
  const percent = document.getElementById("loader-percentage");
  const logoImg = document.querySelector(".logo-img");
  const galleryLogo = document.querySelector(".gallery-badge-img");
  const logoMobil = innerWidth < 800;

  // --- Sets de Gsap para definiciones Iniciales ---
  gsap.set(".hero-logo-white", { autoAlpha: 0, xPercent: logoMobil ? 0 : -300 });

  /*==========================================================================
                    SECCIÓN: SPINNER DEL INICIO
    ========================================================================== */
  if (preloader) {
    let count = 0;
    const interval = setInterval(() => {
      count += Math.floor(Math.random() * 10) + 1;
      if (count >= 100) {
        count = 100;
        clearInterval(interval);
        hidePreloader();
      }
      if (percent) percent.innerText = count;
    }, 40);
  } else {
    // Si no hi ha preloader (pàgines internes), iniciem amb un petit retard per seguretat
    setTimeout(startAnimations, 500);
  }

  function hidePreloader() {
    if (preloader) {
      gsap.to(preloader, { duration: 0.8, opacity: 0, visibility: "hidden", onComplete: startAnimations });
    } else {
      startAnimations();
    }
  }

  function startAnimations() {
    const isProjectesPage = document.body.classList.contains("page-projectes");
    const isAurexPage = document.body.classList.contains("page-aurex");

    /* --- ANIMACIONS D'ENTRADA (Aurex i Projectes) --- */
    if (isAurexPage) {
      gsap.set([".aurex-hero__left", ".aurex-hero__right"], { autoAlpha: 0, y: 50 });
      const tlAurex = gsap.timeline({ defaults: { duration: 1, ease: "power3.out" } });
      tlAurex.to(".aurex-hero__left", { autoAlpha: 1, y: 0 }).to(".aurex-hero__right", { autoAlpha: 1, y: 0 }, "-=0.8");

      gsap.set(".aurex-strategy__block", { autoAlpha: 0, y: 50 });

      gsap.to(".aurex-strategy__block", {
        autoAlpha: 1,
        y: 0,
        stagger: 0.4,
        duration: 2,
        ease: "power2.out",
        scrollTrigger: { trigger: ".aurex-strategy", start: "top 80%" },
      });

      gsap.fromTo(
        ".aurex-gallery__item",
        { autoAlpha: 0, y: 450 },
        {
          autoAlpha: 1,
          y: 0,
          duration: 3,
          ease: "power3.out",
          scrollTrigger: { trigger: "#aurex-gallery-main", start: "top 85%" },
        },
      );

      gsap.from(".aurex-gallery__grid-item", {
        autoAlpha: 0,
        x: (i) => (i % 2 === 0 ? -300 : 300),
        duration: 2,
        stagger: 0.8,
        ease: "power3.out",
        scrollTrigger: { trigger: "#aurex-gallery-grid", start: "top 85%" },
      });
    }

    if (isProjectesPage) {
      const tlPortfolio = gsap.timeline({ defaults: { duration: 1.2, ease: "power4.out" } });
      tlPortfolio.to(".portfolio-hero__title", { autoAlpha: 1, y: 0, delay: 0.5 }).to(".portfolio-hero__subtitle", { autoAlpha: 1, y: 0 }, "-=0.8");
    }

    //#region HERO

    //#region HEADER

    /* ==========================================================================
       SECCIÓN: DINAMISMO DE HEADER (Cambio de color Negro/Blanco)
       Descripción: Cambia el color del logo y enlaces al salir del bloque blanco.
       ========================================================================== */
    const isInternalPage = !document.getElementById("hero");

    if (isInternalPage) {
      // 1. Transición de Fondo (Imagen -> Blanco)
      gsap.to("body", {
        "--bg-opacity": 1,
        ease: "none",
        scrollTrigger: {
          trigger: ".portfolio-hero",
          start: "bottom center",
          end: "bottom top",
          scrub: true,
        },
      });

      // 2. Transición de Color de Texto y Header
      // Animamos el color del body (texto) para que pase de blanco a negro
      gsap.to("body", {
        color: "#1a1a1a",
        scrollTrigger: {
          trigger: ".portfolio-hero, .aurex-hero",
          start: "bottom center",
          end: "bottom top",
          scrub: true,
        },
      });
    }

    // ----- MENÚ HAMBURGUESA -----
    const menuToggle = document.getElementById("menu-toggle");
    const menuOverlay = document.getElementById("menu-overlay");
    const overlayLinks = document.querySelectorAll(".overlay-links li");
    const closeLabel = document.querySelector(".close-label");

    const menuTl = gsap.timeline({ paused: true });
    if (menuOverlay) {
      menuTl.to(menuOverlay, { duration: 0.5, opacity: 1, visibility: "visible", ease: "power2.inOut" });
      menuTl.from(overlayLinks, { duration: 0.4, y: 20, opacity: 0, stagger: 0.1, ease: "power2.out" }, "-=0.2");
    }

    let isMenuOpen = false;
    const toggleMenu = () => {
      isMenuOpen = !isMenuOpen;
      if (menuToggle) menuToggle.classList.toggle("active");
      if (isMenuOpen) {
        if (menuOverlay) gsap.set(menuOverlay, { pointerEvents: "auto" });
        menuTl.play();
        document.body.style.overflow = "hidden";
      } else {
        if (menuOverlay) gsap.set(menuOverlay, { pointerEvents: "none" });
        menuTl.reverse();
        document.body.style.overflow = "auto";
      }
    };

    if (menuToggle) menuToggle.addEventListener("click", toggleMenu);
    if (closeLabel) closeLabel.addEventListener("click", toggleMenu);

    // Tancar el menú en clicar qualsevol enllaç (que no sigui un desplegable)
    document.querySelectorAll(".overlay-links a:not(.parent-link)").forEach((link) => {
      link.addEventListener("click", () => {
        if (isMenuOpen) toggleMenu();
      });
    });

    document.querySelectorAll(".parent-link").forEach((link) => {
      link.addEventListener("click", (e) => {
        e.preventDefault();
        const parentLi = link.closest(".has-submenu");
        if (parentLi) parentLi.classList.toggle("active");
      });
    });

    // =====================LOGICA SCROLL HEADER=================================
    window.addEventListener("scroll", () => {
      const mainHeader = document.getElementById("main-header");
      const btnCta = document.querySelector(".btn-cta");
      if (!mainHeader) return;

      if (window.scrollY > 50) {
        mainHeader.classList.add("scrolled");
        if (btnCta) btnCta.classList.add("btn-cta-white");
      } else {
        mainHeader.classList.remove("scrolled");
        if (btnCta) btnCta.classList.remove("btn-cta-white");
        // Reseteamos altura por si acaso se quedó pillado el hover
        mainHeader.style.height = "65px";
      }
    });
    //#endregion HEADER
    /* ==========================================================================
       SECCIÓN: HERO
       ========================================================================== */
    const tlHero = gsap.timeline({ defaults: { duration: 1.5, ease: "power4.out" } });
    tlHero
      //Entrada de "ESTUDI"
      .fromTo(".hero-line-p .hero-text", { scale: 0.8, autoAlpha: 0, xPercent: -100 }, { scale: 1, autoAlpha: 1, xPercent: 0 }, 0.2)
      //Entrada de "CREATIU"
      .fromTo(".hero-text-block .hero-text", { x: 100, autoAlpha: 0 }, { x: 0, autoAlpha: 1 }, 0.8)
      //Entrada de "VORA STUDIO"
      .fromTo(".vora-text", { autoAlpha: 0, xPercent: -100 }, { autoAlpha: 1, xPercent: 0 }, 1.2)
      //Entrada de "Som un estudi de comunicació i disseny"
      .fromTo(".hero-line-p p", { y: 30, autoAlpha: 0 }, { y: 0, autoAlpha: 1 }, 1.6)
      //Entrada de la imatge
      .fromTo(".pill-image-container", { xPercent: -20, autoAlpha: 0, rotate: -3 }, { xPercent: 0, autoAlpha: 1, rotate: 0 }, 2.2)
      // --- ANIMACIÓN INFINITA DEL LOGO (Aparicion) ---
      .fromTo(
        ".hero-logo-white",
        { autoAlpha: 0, xPercent: logoMobil ? 0 : -300, yPercent: logoMobil ? 0 : 70 },
        { autoAlpha: 1, duration: 1.5 },
        2.2,
      )
      // Fase de Salida (Scroll Trigger) - Rotando hacia la derecha fuera de la pantalla
      .to(".hero-logo-white", {
        x: 1200,
        rotate: 360,
        duration: logoMobil ? 1 : 5,
        ease: "power1.in",
        scrollTrigger: {
          trigger: "#hero",
          start: "top top", // Comienza el movimiento al empezar a scrollear
          end: "bottom top",
          scrub: 1,
        },
      });
    //#endregion HERO

    //#region STRATEGY & PARALLAX
    /*==========================================================================
                  SECCIÓN: STRATEGY INTRO & PARALLAX
    ========================================================================== */
    const splitBody = new SplitText(".strategy-body p", { type: "words" });
    const splitTitle = new SplitText(".strategy-title", { type: "lines, words" });
    gsap.set(splitTitle.lines, { overflow: "hidden" });
    gsap.set(splitBody.words, { opacity: 0.1 });
    gsap.set(".btn-white-strategy", { opacity: 0 });
    //Descripción ->
    const tlStrategyText = gsap.timeline({
      scrollTrigger: {
        trigger: ".strategy-section",
        start: window.innerWidth < 800 ? "top 95%" : "top 45%", // Comença quan la secció entra un 25% a la pantalla
        end: "center 60%", // Acaba quan la secció està més amunt
        scrub: 0.8, // L'animació segueix el scroll (el 0.5 li dóna suavitat)
      },
    });

    //Titulo ->
    gsap.from(splitTitle.words, {
      opacity: 0,
      stagger: 2,
      yPercent: 100,
      duration: 2.5,
      scrollTrigger: {
        trigger: ".strategy-section",
        start: "top 70%", // Comença quan el títol entra per baix
        end: "top 20%", // Acaba a mitja pantalla
        scrub: 0.5, // Més responsiu que el títol
      },
    });
    //Entra la descripción ->
    tlStrategyText
      .to(
        splitBody.words,
        {
          opacity: 1, // Recupera l'opacitat total
          stagger: 1, // Les paraules s'encenen una rere l'altra
          ease: "none", // "none" és millor per a efectes de scrub
        },
        "-=0.5",
      )
      //Entra el botón ->
      .to(
        ".btn-white-strategy",
        {
          opacity: 1,
          duration: 2,
          ease: "power2.out",
          delay: 1.5,
        },
        ">-1",
      );
    /*-==========================================================================
                           PARALLAX CAJAS
    ========================================================================== */

    // --- CAJA 1 ---
    // 1. Establecemos la posición inicial y ocultamos
    gsap.set(".p-box-1", { x: "100vw", xPercent: -100, y: "30vh", autoAlpha: 0 });

    // 2. Animación de aparición (Aparece donde ya está "setead")
    gsap.to(".p-box-1", {
      autoAlpha: 1,
      duration: 2,
      ease: "expo.out",
      scrollTrigger: {
        trigger: ".strategy-section",
        start: "top bottom",
        toggleActions: "play none none none",
      },
    });

    gsap.to(".p-box-1", {
      y: "+=200",
      ease: "none",
      scrollTrigger: {
        trigger: ".strategy-section",
        start: "top bottom",
        end: "bottom top",
        scrub: 1,
        immediateRender: false,
      },
    });

    // --- CAJA 2 ---
    gsap.set(".p-box-2", { x: "0vw", y: "70vh", autoAlpha: 0 });
    gsap.to(".p-box-2", {
      autoAlpha: 1,
      duration: 2,
      ease: "expo.out",
      scrollTrigger: {
        trigger: ".strategy-section",
        start: "top bottom",
        toggleActions: "play none none none",
      },
    });

    gsap.to(".p-box-2", {
      y: "-=250",
      ease: "none",
      scrollTrigger: {
        trigger: ".strategy-section",
        start: "top bottom",
        end: "bottom top",
        scrub: 1.5,
        immediateRender: false,
      },
    });
    //#endregion STRATEGY INTRO & PARALLAX

    //#region MASCARA
    /* ==========================================================================
     SECCION 03 -  Revelado por Máscara -> (Servicios)
     ========================================================================== */
    let media = gsap.matchMedia();

    // 2. Definimos las condiciones (igual que en CSS)
    media.add(
      {
        isMobile: "(max-width: 991px)",
        isDesktop: "(min-width: 992px)",
      },
      (context) => {
        // context.conditions extrae qué media query está activa en este momento
        let { isMobile } = context.conditions;
        gsap.fromTo(
          ".mask-container",
          { y: isMobile ? 250 : 100 },
          {
            y: 0,
            ease: "none",
            scrollTrigger: {
              trigger: ".strategy-section",
              start: "top bottom",
              end: "bottom top",
              scrub: 1.5,
              onLeave: () => gsap.set(".mask-container", { y: 0 }),
            },
          },
        );

        // 2. EXPANSIÓN DE LA MÁSCARA
        const maskTl = gsap.timeline({
          scrollTrigger: {
            trigger: ".mask-section",
            start: "top top",
            end: "+=150%",
            scrub: 1, // Un poco más rápido para que no haya "lag" al llegar al 100%
            pin: true,
            anticipatePin: 1,
            pinSpacing: true,
          },
        });

        maskTl
          .to(".mask-wrapper", {
            // Usamos 100vw/vh para asegurar que ignore cualquier padding accidental
            width: "100vw",
            height: "100vh",
            maxWidth: "none",
            maxHeight: "none",
            borderRadius: "0px",
            y: 0, // Reseteamos cualquier y relativo a 0 absoluto
            ease: "none",
          })
          .fromTo(".mask-img", { scale: 1.4 }, { scale: 1, ease: "none" }, 0)
          .to(".mask-title", { opacity: 1, y: -20, duration: 0.5, ease: "power2.out" }, "-=0.2");
      },
    );
    //#endregion MASCARA

    //#region SERVICIOS
    /* ==========================================================================
     SECCCION 04: SERVICIOS
     ========================================================================== */
    //ENTRADA DEL TITULO
    const splitServicios = new SplitText(".services-title2", { type: "lines" });
    const tlServicios = gsap.timeline({
      scrollTrigger: {
        trigger: ".services-title-mask",
        start: "top 90%",
        toggleActions: "play none none reverse",
      },
    });
    tlServicios
      .from(".services-title", {
        yPercent: 110,
        duration: 3,
        ease: "power4.out",
        clearProps: "all",
      })
      .from(
        splitServicios.lines,
        {
          autoAlpha: 0,
          stagger: 0.5,
          yPercent: 100,
          duration: 1,
          ease: "power4.out",
        },
        "<0.5",
      )
      .from(
        ".services-desc p",
        {
          autoAlpha: 0,
          duration: 0.5,
        },
        "<1",
      )
      .from(
        ".btn-orange-strategy",
        {
          autoAlpha: 0,
          duration: 0.5,
          scale: 0.8,
        },
        ">",
      );
    // 1. STACKING CARDS (Solo en Desktop)
    const mm = gsap.matchMedia();
    mm.add("(min-width: 992px)", () => {
      const cards = gsap.utils.toArray(".service-card");
      cards.forEach((card, i) => {
        gsap.to(card, {
          scrollTrigger: {
            trigger: card,
            start: () => `top ${2 + i * 2}%`,
            endTrigger: ".services-stack",
            //Evitem el stacking flow de les tarjes asegurant 1 scroll enter de paginació per tarja
            end: () => `+=${window.innerHeight * 6}`,
            pin: true,
            pinSpacing: false,
            scrub: true,
            invalidateOnRefresh: true,
            onEnter: () => {
              console.log("Card " + i + " completada");
            },
          },
        });
      });
      gsap.to(".service-card", {
        autoAlpha: 0,
        scrollTrigger: {
          trigger: ".divergent-grid", // Cuando entras en la siguiente sección (proyectos)
          start: "top center",
          toggleActions: "play none none reverse",
        },
      });

      //Fin de las cards
      //#endregion SERVICIOS

      //#region PROYECTOS
      /*==========================================================================
      SECCION 05 -> PROYECTOS
     ========================================================================== */
      const divergentTitle = document.querySelector(".divergent-grid__title");
      if (divergentTitle) {
        const originalHTML = divergentTitle.innerHTML;
        divergentTitle.innerHTML = `<span class="mask-inner">${originalHTML}</span>`;

        gsap.from(".divergent-grid__title .mask-inner", {
          scrollTrigger: {
            trigger: ".divergent-grid__title",
            start: "top 85%",
            toggleActions: "play none none reverse",
          },
          yPercent: 100,
          duration: 1.5,
          ease: "power4.out",
        });
      }
      // 2. PARRILLA DIVERGENTE (Scroll Largo)
      const tlDivergent = gsap.timeline({
        scrollTrigger: {
          trigger: ".divergent-grid",
          start: "center 35%",
          end: "+=200%",
          pin: true,
          scrub: 1.2,
          onUpdate: (self) => {
            gsap.to(logoSpin, { timeScale: self.direction, duration: 0.5 });
          },
          onToggle: (self) => {
            // Lógica del LOGO
            if (self.isActive) logoSpin.play();
            else logoSpin.pause();
          },
        },
      });
      tlDivergent
        .to(".divergent-grid__row--top", { xPercent: -40, ease: "none" }, 0)
        .fromTo(".divergent-grid__row--bottom", { xPercent: -40 }, { xPercent: 0, ease: "none" }, 0);
    });
    const logoSpin = gsap.to(".gallery-badge-img", {
      rotation: 360,
      duration: 15,
      repeat: -1,
      ease: "none",
      paused: true,
      transformOrigin: "50% 50%",
    });
    // ----- GALERIA PER MOBILS -----
    mm.add("(max-width: 991px)", () => {
      const nextBtn = document.getElementById("gallery-next");
      const prevBtn = document.getElementById("gallery-prev");
      const rowTop = document.querySelector(".divergent-grid__row--top");

      if (nextBtn && prevBtn && rowTop) {
        const items = Array.from(rowTop.querySelectorAll(".divergent-grid__item")); //Creem el array de les imatges
        let currentIndex = 0;
        let isAnimating = false;

        const goTo = (newIndex, direction) => {
          // Funció de seguretat per avançar correctament
          if (isAnimating || newIndex === currentIndex) return;
          isAnimating = true;

          const current = items[currentIndex]; //Imatge actual
          const next = items[newIndex]; //Imatge següent

          const inClass = direction === "next" ? "slide-in-right" : "slide-in-left"; //Direcció de l'animació
          const outClass = direction === "next" ? "slide-out-left" : "slide-out-right"; //Direcció de l'animació

          // Anima la sortida de l'actual
          current.classList.add(outClass);

          // Anima l'entrada de la nova
          next.classList.add(inClass);

          setTimeout(() => {
            current.classList.remove("active", outClass);
            next.classList.remove(inClass);
            next.classList.add("active");
            currentIndex = newIndex;
            isAnimating = false;
          }, 450);
        };

        // Mostra la primera imatge sense animació
        items[0].classList.add("active");

        nextBtn.addEventListener("click", () => {
          const newIndex = (currentIndex + 1) % items.length;
          goTo(newIndex, "next");
        });

        prevBtn.addEventListener("click", () => {
          const newIndex = (currentIndex - 1 + items.length) % items.length;
          goTo(newIndex, "prev");
        });
      }
    });

    /* ==========================================================================
     ANIMACIÓN: 02. Revelado por Máscara (Proyectos)
     Descripción: Revelado ascendente a través de un contenedor overflow:hidden.
     ========================================================================== */

    /* ==========================================================================
     SECCIÓN: PRICING ANIMATION
     Descripción: Entrada premium escalonada de packs y sus detalles.
     ========================================================================== */
    const pricingTitle = document.querySelector(".pricing__title");
    if (pricingTitle) {
      const originalHTML = pricingTitle.innerHTML;
      pricingTitle.innerHTML = `<span class="mask-inner">${originalHTML}</span>`;

      gsap.from(".pricing__title .mask-inner", {
        scrollTrigger: {
          trigger: ".pricing__title",
          start: "top 90%",
          toggleActions: "play none none reverse",
        },
        yPercent: 100,
        duration: 1.2,
        ease: "power4.out",
      });
    }

    const tlPricing = gsap.timeline({
      scrollTrigger: {
        trigger: ".pricing-section",
        start: "top 70%",
        toggleActions: "play none none reverse",
      },
    });

    tlPricing.from(
      ".pricing-card",
      {
        y: 80,
        opacity: 0,
        duration: 1.2,
        stagger: 0.15,
        ease: "expo.out",
        clearProps: "all",
      },
      0.2, // Empieza un poco después del título
    );
    /* ==========================================================================
     FIN ANIMACIÓN: 02. Revelado por Máscara
     ========================================================================== */
    /* ==========================================================================
     TRANSICIÓN DE FONDO GLOBAL
     ========================================================================== */
    // Al llegar a la sección de servicios, tapamos la imagen con un gradiente blanco de forma suave
    gsap.to("body", {
      "--bg-opacity": 1,
      scrollTrigger: {
        trigger: ".services-section",
        start: "top 75%", // Empieza un poco antes de ver las tarjetas
        end: "top 50%",
        scrub: 1, // Transición suave atada al scroll
        invalidateOnRefresh: true,
      },
    });

    // 1. Preparación para Loop Infinito (Solo en Desktop, no en móvil/slider)
    if (window.innerWidth >= 992) {
      const rows = document.querySelectorAll(".divergent-grid__row");
      rows.forEach((row) => {
        const content = row.innerHTML;
        row.innerHTML = content + content + content;
      });
    }
    /* ==========================================================================
     SECCIÓN: CONTACT ANIMATION
     Descripción: Revelación del formulario moderno.
     ========================================================================== */
    const tlContact = gsap.timeline({
      scrollTrigger: {
        trigger: ".contact-section",
        start: "top 85%",
        end: "bottom 95%",
        scrub: 1,
      },
    });

    // 1. Fondo y Elementos entran juntos
    tlContact
      .to("body", {
        "--bg-opacity": 0,
        duration: 1.5,
        ease: "power2.inOut",
      })
      .from(
        ".contact__info",
        {
          y: 40,
          opacity: 0,
          duration: 1,
          ease: "power2.out",
        },
        0,
      )
      .from(
        ".form-field",
        {
          y: 20,
          opacity: 0,
          stagger: 0.1,
          duration: 0.8,
          ease: "power2.out",
        },
        0.5,
      );

    // CRÍTICO: Recalcular posiciones
    ScrollTrigger.refresh();

    /* ==========================================================================
       SECCIÓN: PORTFOLIO (PROJECTES) ANIMATIONS
       ========================================================================== */
    if (isProjectesPage) {
      // 2. ScrollTrigger para las tarjetas del catálogo
      const portfolioItems = document.querySelectorAll(".portfolio-card");
      portfolioItems.forEach((item) => {
        gsap.to(item, {
          autoAlpha: 1,
          y: 0,
          duration: 1,
          ease: "power2.out",
          scrollTrigger: {
            trigger: item,
            start: "top 90%", // Empieza a mostrarse cuando entra por abajo
          },
        });
      });
    }

    /* ==========================================================================
       FIN SECCIÓN: ANIMACIONES
       ========================================================================== */
  }

  /* ==========================================================================
     SECCIÓ: ENVÍO DE FORMULARIO (Segur i amb GSAP)
     Descripció: Gestió d'enviament via AJAX, reCAPTCHA i notificacions Toast.
     ========================================================================== */
  const contactForm = document.getElementById("contact-form-element");

  if (contactForm) {
    contactForm.addEventListener("submit", (e) => {
      e.preventDefault();

      // UI: Cambiamos el texto del botón inmediatamente
      const btnText = contactForm.querySelector(".form-btn__text");
      const originalText = btnText.textContent;
      btnText.textContent = "Enviant...";
      // Seleccionamos el element pare que conté el text del botó (el botó en sí)
      btnText.parentElement.disabled = true;

      // Execució de reCAPTCHA
      grecaptcha.ready(() => {
        // CLAU PARA TEST (Local)
        const siteKey = "6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI";
        // CLAU PARA PRODUCCIÓ (Descomentar i fer servir grecaptcha.enterprise si cal)
        // const siteKey = "6Le_-LssAAAAAHRq37HNjmJNUSzOAxgmAhJkOlNL";

        grecaptcha.execute(siteKey, { action: "submit" }).then((token) => {
          enviarFormulari(token, originalText);
        });
      });
    });
  }

  /**
   * Envia les dades del formulari via AJAX
   * @param {string} token - Token de reCAPTCHA v3
   * @param {string} originalText - Text original del botó per restaurar-lo
   */
  async function enviarFormulari(token, originalText) {
    const btnText = contactForm.querySelector(".form-btn__text");

    // Inserim el token al camp ocult PRIMER
    const recaptchaInput = document.getElementById("recaptcha_response");
    if (recaptchaInput) recaptchaInput.value = token;

    // Creem el FormData DESPRÉS d'inserir el token
    const data = new FormData(contactForm);

    try {
      // Ruta relativa per evitar problemes si el projecte està en una subcarpeta
      const response = await fetch("php/contacte.php", {
        method: "POST",
        body: data,
        headers: {
          Accept: "application/json",
        },
      });

      const result = await response.json();

      if (response.ok && result.ok) {
        mostrarToast(result.message || "Missatge enviat correctament!", "success");
        contactForm.reset();
      } else {
        mostrarToast(result.error || "Hi ha hagut un problema.", "error");
      }
    } catch (error) {
      mostrarToast("Error de connexió. Intenta-ho més tard.", "error");
    } finally {
      // Restore UI
      btnText.textContent = originalText;
      btnText.parentElement.disabled = false;
    }
  }

  /**
   * Mostra una notificació Toast a la pantalla amb GSAP
   * @param {string} mensaje - El text a mostrar
   * @param {string} tipo - 'success' o 'error'
   */
  function mostrarToast(mensaje, tipo) {
    const toast = document.getElementById("toast-container");
    if (!toast) return;

    toast.innerText = mensaje;
    toast.className = `toast-visible toast-${tipo}`;

    // Animació GSAP: Entrada suau des de sota i sortida progressiva
    const tlToast = gsap.timeline();

    tlToast.fromTo(toast, { y: 100, opacity: 0 }, { y: 0, opacity: 1, duration: 0.6, ease: "power3.out" }).to(toast, {
      opacity: 0,
      y: -20,
      delay: 4.5,
      duration: 0.5,
      onComplete: () => {
        toast.className = "toast-hidden";
      },
    });
  }

  /* ==========================================================================
     FI SECCIÓ: ENVÍO DE FORMULARIO
     ========================================================================== */

  // --- LÓGICA DE EXPANSIÓN DE PACKS (Exclusiva) ---
  const pricingCards = document.querySelectorAll(".pricing-card");

  pricingCards.forEach((card) => {
    const btn = card.querySelector(".pricing-card__cta");
    const features = card.querySelector(".pricing-card__features");

    if (btn && features) {
      btn.addEventListener("click", (e) => {
        // Si ya está expandida, dejamos que actúe el link al contacto
        if (card.classList.contains("pricing-card--expanded")) {
          return;
        }

        e.preventDefault();
        e.stopPropagation();

        // 1. Cerrar cualquier OTRA tarjeta abierta
        pricingCards.forEach((otherCard) => {
          if (otherCard !== card && otherCard.classList.contains("pricing-card--expanded")) {
            const otherFeatures = otherCard.querySelector(".pricing-card__features");
            const otherBtn = otherCard.querySelector(".pricing-card__cta");

            otherCard.classList.remove("pricing-card--expanded");
            gsap.to(otherFeatures, {
              height: 0,
              opacity: 0,
              duration: 0.5,
              ease: "power3.inOut",
            });
            if (otherBtn) otherBtn.textContent = "Més informació";
          }
        });

        // 2. Abrir ESTA tarjeta
        card.classList.add("pricing-card--expanded");
        gsap.to(features, {
          height: "auto",
          opacity: 1,
          duration: 0.8,
          ease: "power3.out",
        });
        btn.textContent = "Vull aquest pack";
      });
    }
  });

  // Cerrar al clicar fuera de cualquier tarjeta
  document.addEventListener("click", (e) => {
    pricingCards.forEach((card) => {
      if (card.classList.contains("pricing-card--expanded") && !card.contains(e.target)) {
        const features = card.querySelector(".pricing-card__features");
        const btn = card.querySelector(".pricing-card__cta");

        card.classList.remove("pricing-card--expanded");
        gsap.to(features, {
          height: 0,
          opacity: 0,
          duration: 0.5,
          ease: "power3.inOut",
        });
        if (btn) btn.textContent = "Més informació";
      }
    });
  });

  // --- LÓGICA DEL ACORDEÓN DE PRECIOS (FAQ) ---
  const faqQuestions = document.querySelectorAll(".faq-question");

  faqQuestions.forEach((question) => {
    question.addEventListener("click", () => {
      const parent = question.closest(".faq-item");
      const answer = parent.querySelector(".faq-answer");

      // Primero, cerramos los demás
      document.querySelectorAll(".faq-item").forEach((item) => {
        if (item !== parent && item.classList.contains("active")) {
          item.classList.remove("active");
          gsap.to(item.querySelector(".faq-answer"), { height: 0, opacity: 0, duration: 0.4 });
        }
      });

      // Luego abrimos/cerramos el clicado
      if (parent.classList.contains("active")) {
        parent.classList.remove("active");
        gsap.to(answer, { height: 0, opacity: 0, duration: 0.4 });
      } else {
        parent.classList.add("active");
        gsap.set(answer, { height: "auto" });
        const targetHeight = answer.offsetHeight;
        gsap.set(answer, { height: 0 });
        gsap.to(answer, {
          height: targetHeight,
          opacity: 1,
          duration: 0.4,
          onComplete: () => {
            gsap.set(answer, { height: "auto" });
          },
        });
      }
    });
  });

  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const targetId = this.getAttribute("href");

      // Usamos el objeto "lenis" que declaraste al principio
      lenis.scrollTo(targetId, {
        duration: 2, // Aumenta este número (en segundos) para que el viaje sea más pausado y no tan brusco
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // Movimiento suave
      });
    });
  });
});
