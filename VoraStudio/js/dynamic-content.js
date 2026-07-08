/* ══════════════════════════════════════════════════════════════
   dynamic-content.js — VoraStudio
   ══════════════════════════════════════════════════════════════
   Carrega les dades del projecte des de VoraCMS (local per proves).
   Si el CMS no està disponible, fa fallback al JSON local.

   CMS API: https://voracms.voradata.cat/api/public/web/vorastudio-projects?locale=ca
   ══════════════════════════════════════════════════════════════ */

const CMS_API_BASE = 'https://voracms.voradata.cat';
const CMS_API_TOKEN = 'UJIv45gTpMGckBdJjDg3UmkuqZzOWqHV';

document.addEventListener('DOMContentLoaded', function () {
  const isProjectePage = document.body.classList.contains("page-projecte");
  if (!isProjectePage) return;

  loadProjectData();

  async function loadProjectData() {
    const projectSlug = document.body.dataset.project;
    if (!projectSlug) return;

    /* ─── Intentar CMS primer ─── */
    try {
      const res = await fetch(`${CMS_API_BASE}/api/public/web/vorastudio-projects?locale=ca`, {
        headers: { 'Authorization': 'Bearer ' + CMS_API_TOKEN }
      });
      if (!res.ok) throw new Error(`CMS HTTP ${res.status}`);
      const json = await res.json();
      const projects = json.data;

      /* Buscar per slug_del_projecte o project_slug */
      const data = projects.find(p => (p.slug_del_projecte || p.project_slug) === projectSlug);
      if (!data) throw new Error("Projecte no trobat al CMS");

      renderProjectFromCMS(data);
      return;
    } catch (err) {
      console.warn('CMS no disponible, usant dades locals:', err.message);
    }

    /* ─── Fallback al JSON local ─── */
    try {
      const res = await fetch(`../data/projects.json`);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const projects = await res.json();
      const data = projects.find(p => slugify(p.name) === projectSlug || p.id === projectSlug);
      if (!data) throw new Error("Projecte no trobat");

      renderProjectFromLocal(data);
    } catch (err) {
      console.error('Error carregant dades del projecte:', err);
    }
  }

  /* ─── Helpers ─── */

  function slugify(text) {
    return text.toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-|-$/g, '');
  }

  function imgUrl(path) {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    return CMS_API_BASE + path;
  }

  /* ══════════════════════════════════════════════════════════════
      Render des de CMS
     ══════════════════════════════════════════════════════════════ */

  function renderProjectFromCMS(data) {
    document.title = `VoraStudio | ${data.titol}`;

    const logo = data.logo?.[0]?.url ? imgUrl(data.logo[0].url) : '';
    const tags = data.tags ? data.tags.split(',').map(t => t.trim()).filter(Boolean) : [];
    const website = data.website && data.website !== '#' ? data.website : '#';
    const websiteLabel = data.website && data.website !== '#'
      ? data.website.replace(/^https?:\/\//, '').replace(/\/$/, '') : '#';

    buildProjectHero(logo, website, websiteLabel, data.descripcio || '', tags);
    buildProjectStrategy(data.repte, data.estrategia, data.resultat);
    buildProjectGallery(data.galeria || []);

    animateSections();
  }

  function buildProjectHero(logo, website, websiteLabel, description, tags) {
    const section = document.getElementById("project-hero");
    section.innerHTML = `
      <div class="project-hero__container">
        <div class="project-hero__left">
          ${logo ? `<img src="${logo}" alt="" />` : ''}
          <a href="${website}" target="_blank" class="project-hero__link">WEBSITE: <span>${websiteLabel}</span></a>
        </div>
        <div class="project-hero__right">
          <p class="project-hero__description">${description}</p>
          <div class="project-hero__tags">
            ${tags.map(tag => `<span class="project-hero__tag">${tag}</span>`).join("")}
          </div>
        </div>
      </div>`;
  }

  function buildProjectStrategy(repte, estrategia, resultat) {
    const section = document.getElementById("project-strategy");
    const blocks = [
      { label: 'EL REPTE', text: repte },
      { label: "L'ESTRATÈGIA", text: estrategia },
      { label: 'EL RESULTAT', text: resultat },
    ].filter(b => b.text);

    if (!blocks.length) {
      section.style.display = "none";
      return;
    }

    section.innerHTML = `
      <div class="project-strategy__container">
        ${blocks.map(b => `
          <div class="project-strategy__block">
            <div class="project-strategy__header">
              <span class="project-strategy__dot"></span>
              <h2 class="project-strategy__label">${b.label}</h2>
            </div>
            <p class="project-strategy__text">${b.text}</p>
          </div>`
        ).join("")}
      </div>`;
  }

  function buildProjectGallery(galeria) {
    const section = document.getElementById("project-gallery");
    if (!galeria.length) {
      section.style.display = "none";
      return;
    }

    section.innerHTML = `
      <div class="project-gallery__grid">
        ${galeria.map(img => `
          <div class="project-gallery__item">
            <img src="${imgUrl(img.url)}" alt="" class="project-gallery__img" loading="lazy" />
          </div>`
        ).join("")}
      </div>`;
  }

  /* ══════════════════════════════════════════════════════════════
      Render des de JSON local (compatible ambrere)
     ══════════════════════════════════════════════════════════════ */

  function renderProjectFromLocal(data) {
    document.title = `VoraStudio | ${data.name}`;
    const hero = data.hero;

    buildProjectHero(
      hero.logo || '',
      hero.website || '#',
      hero.websiteLabel || '#',
      hero.description || '',
      hero.tags || []
    );
    buildProjectStrategy(
      data.strategy?.find(s => s.label === 'EL REPTE')?.text,
      data.strategy?.find(s => s.label === "L'ESTRATÈGIA")?.text,
      data.strategy?.find(s => s.label === 'EL RESULTAT')?.text
    );
    buildProjectGallery(
      (data.gallery || []).map(src => ({ url: src }))
    );

    animateSections();
  }

  /* ══════════════════════════════════════════════════════════════
      Animacions (GSAP)
     ══════════════════════════════════════════════════════════════ */

  function animateSections() {
    setTimeout(() => {
      if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();
    }, 300);

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
});
