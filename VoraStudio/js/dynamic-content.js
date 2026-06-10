document.addEventListener('DOMContentLoaded', function () {
  const isProjectePage = document.body.classList.contains("page-projecte");
  if (!isProjectePage) return;

  loadProjectData();

  async function loadProjectData() {
    const projectId = document.body.dataset.project;
    if (!projectId) return;

    try {
      const res = await fetch(`../data/projects.json`);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const projects = await res.json();
      
      const data = projects.find(p => p.id === projectId);
      if (!data) throw new Error("Projecte no trobat");

      buildProjectHero(data);
      buildProjectStrategy(data);
      buildProjectGallery(data);

      // Refresh ScrollTrigger dels elements dinàmics inicials
      setTimeout(() => {
        ScrollTrigger.refresh();
      }, 300);

      // Animar hero
      gsap.set([".project-hero__left", ".project-hero__right"], { autoAlpha: 0, y: 50 });
      const tlHero = gsap.timeline({ defaults: { duration: 1, ease: "power3.out" } });
      tlHero.to(".project-hero__left", { autoAlpha: 1, y: 0 }).to(".project-hero__right", { autoAlpha: 1, y: 0 }, "-=0.8");

      // Animar strategy blocks
      gsap.set(".project-strategy__block", { autoAlpha: 0, y: 50 });
      gsap.to(".project-strategy__block", {
        autoAlpha: 1,
        y: 0,
        stagger: 0.4,
        duration: 2,
        ease: "power2.out",
        scrollTrigger: { trigger: ".project-strategy", start: "top 80%" },
      });

      // Animar galeria items
      gsap.from(".project-gallery__item", {
        autoAlpha: 0,
        y: 120,
        duration: 1.5,
        stagger: 0.35,
        ease: "power3.out",
        scrollTrigger: { trigger: "#project-gallery", start: "top 55%" },
      });

    } catch (err) {
      console.error("Error carregant dades del projecte:", err);
    }
  }

  function buildProjectHero(data) {
    const section = document.getElementById("project-hero");
    const hero = data.hero;
    document.title = `VoraStudio | ${data.name}`;
    section.innerHTML = `
      <div class="project-hero__container">
        <div class="project-hero__left">
          <img src="${hero.logo}" alt="${hero.logoAlt}" />
          <a href="${hero.website}" target="_blank" class="project-hero__link">WEBSITE: <span>${hero.websiteLabel}</span></a>
        </div>
        <div class="project-hero__right">
          <p class="project-hero__description">${hero.description}</p>
          <div class="project-hero__tags">
            ${hero.tags.map((tag) => `<span class="project-hero__tag">${tag}</span>`).join("")}
          </div>
        </div>
      </div>`;
  }

  function buildProjectStrategy(data) {
    const section = document.getElementById("project-strategy");
    if (!data.strategy || !data.strategy.length) {
      section.style.display = "none";
      return;
    }
    section.innerHTML = `
      <div class="project-strategy__container">
        ${data.strategy
          .map(
            (block) => `
          <div class="project-strategy__block">
            <div class="project-strategy__header">
              <span class="project-strategy__dot"></span>
              <h2 class="project-strategy__label">${block.label}</h2>
            </div>
            <p class="project-strategy__text">${block.text}</p>
          </div>`
          )
          .join("")}
      </div>`;
  }

  function buildProjectGallery(data) {
    const section = document.getElementById("project-gallery");
    if (!data.gallery || !data.gallery.length) {
      section.style.display = "none";
      return;
    }
    section.innerHTML = `
      <div class="project-gallery__grid">
        ${data.gallery
          .map(
            (src) => `
          <div class="project-gallery__item">
            <img src="${src}" alt="" class="project-gallery__img" loading="lazy" />
          </div>`
          )
          .join("")}
      </div>`;
  }
});
