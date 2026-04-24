<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Generem un token CSRF basat en el secret del .env i la data del dia
// Això ens permet validar-lo sense dependre de la sessió de PHP
$csrf_token = hash_hmac('sha256', date('Y-m-d'), $_ENV['CSRF_TOKEN_SECRET']);
?>

<!doctype html>
<html lang="ca">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>VoraStudio | Estudi Creatiu a Girona</title>

    <!-- Google Fonts: Montserrat y Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&family=Outfit:wght@300;400;600;700&display=swap"
      rel="stylesheet"
    />
    <!-- CSS Principal -->
    <link rel="stylesheet" href="css/style.css" />

    <!-- GSAP Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/TextPlugin.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.13/dist/SplitText.min.js"></script>
    <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>
    
    <!-- Google reCAPTCHA (TEST para Local) -->
    <script src="https://www.google.com/recaptcha/api.js?render=6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></script>
    
    <!-- Google reCAPTCHA (REAL para Producción - Descomentar al subir)
    <script src="https://www.google.com/recaptcha/enterprise.js?render=6Le_-LssAAAAAHRq37HNjmJNUSzOAxgmAhJkOlNL"></script> 
    -->
  </head>
  <body>
    <!-- PRELOADER -->
    <div id="preloader">
      <div class="loader-content">
        <div class="loader-logo">Vora<span>Studio</span></div>
        <div class="loader-percentage" id="loader-percentage">0</div>
        <div class="loader-bar-container">
          <div class="loader-bar"></div>
        </div>
      </div>
    </div>

    <!-- ----- INICIO SECCIÓN HEADER ----- -->
    <header id="main-header">
      <nav class="nav-container">
        <div class="logo">
          <a href="#hero">
            <img src="img/logo.png" alt="VoraStudio Logo" class="logo-img logo-img--default" />
            <img src="img/voraL.png" alt="VoraStudio Logo" class="logo-img logo-img--scrolled" />
          </a>
        </div>

        <!-- Botón de Menú (Responsive) -->
        <button class="menu-toggle" id="menu-toggle" aria-label="Obrir menú de navegació">
          <span class="line line-1"></span>
          <span class="line line-2"></span>
        </button>

        <!-- Menú Desktop (Horizontal) -->
        <ul class="nav-links desktop-only">
          <li class="has-dropdown">
            <a href="#services">Serveis</a>
            <ul class="dropdown-menu">
              <li><a href="#branding">Estratègia i Branding</a></li>
              <li><a href="#web">Projectes Web</a></li>
              <li><a href="#social">Social Media</a></li>
              <li><a href="#disseny">Disseny Gràfic</a></li>
              <li><a href="#marqueting">Màrqueting Digital</a></li>             
            </ul>
          </li>
          <li class="has-dropdown">
            <a href="#projects-grid">Projectes</a>
            <ul class="dropdown-menu">
              <li><a href="projectes/comercialRos">Comercial Ross</a></li>
              <li><a href="projectes/aurex">Aurex Immobles</a></li>
              <!-- <li><a href="html/guaravan.php">Guaravan</a></li>
              <li><a href="html/cfood.php">C-Food</a></li>
              <li><a href="html/innovafp.php">InnovaFP</a></li>
              <li><a href="html/spica.php">Spica</a></li>
              <li><a href="html/raymel.php">Raymel</a></li>
              <li><a href="html/dtast.php">D-Tast</a></li>
              <li><a href="html/wiar.php">Wiar</a></li>
              <li><a href="html/novagal.php">Novagal</a></li>
              <li><a href="html/palmitohouse.php">Palmito House</a></li>
              <li><a href="html/vitoriaTeylor.php">Vitoria Teylor</a></li>
              <li><a href="html/espaiGras.php">Espai Gastronòmic Quim Casellas</a></li> -->
            </ul>
          </li>
          <li class="has-dropdown">
            <a href="#pricing">Packs</a>
          </li>
        </ul>

        <!-- Botón Contacto Derecho -->
        <div class="header__cta desktop-only">
          <a href="#contact" class="btn-cta">Contacte</a>
        </div>
      </nav>
    </header>

    <!-- Menú Overlay (Panel Lateral) -->
    <div class="menu-overlay" id="menu-overlay">
      <!-- Indicador de cierre -->
      <span class="close-label">CLOSE</span>

      <div class="overlay-content">
        <ul class="overlay-links">
          <li class="has-submenu">
            <a href="javascript:void(0)" class="parent-link">Serveis</a>
            <ul class="submenu">
              <li><a href="#branding">Estratègia i Branding</a></li>
              <li><a href="#web">Projectes Web</a></li>
              <li><a href="#social">Social Media</a></li>
              <li><a href="#disseny">Disseny Gràfic</a></li>
              <li><a href="#marqueting">Màrqueting Digital</a></li>            
            </ul>
          </li>
          <li class="has-submenu">
            <a href="javascript:void(0)" class="parent-link">Projectes</a>
             <ul class="dropdown-menu">
              <li><a href="projectes/comercialRoss">Comercial Ross</a></li>
              <li><a href="projectes/aurex">Aurex Immobles</a></li>
              <!-- <li><a href="html/guaravan.php">Guaravan</a></li>
              <li><a href="html/cfood.php">C-Food</a></li>
              <li><a href="html/innovafp.php">InnovaFP</a></li>
              <li><a href="html/spica.php">Spica</a></li>
              <li><a href="html/raymel.php">Raymel</a></li>
              <li><a href="html/dtast.php">D-Tast</a></li>
              <li><a href="html/wiar.php">Wiar</a></li>
              <li><a href="html/novagal.php">Novagal</a></li>
              <li><a href="html/palmitohouse.php">Palmito House</a></li>
              <li><a href="html/vitoriaTeylor.php">Vitoria Teylor</a></li>
              <li><a href="html/espaiGras.php">Espai Gastronòmic Quim Casellas</a></li> -->
            </ul>
          </li>
          <li><a href="#pricing">Packs</a></li>
          <li><a href="#contact">Contacte</a></li>
        </ul>
      </div>
    </div>
    <!-- ----- FIN SECCIÓN HEADER ----- -->

    <main>
      <!-- SECCIÓN HERO COMPOSICIÓN FOTO -->
      <section id="hero" class="hero-section">
        <div class="hero-container">
          <div class="hero-line-p">
            <h1 class="hero-text reveal-left">ESTUDI</h1>
            <p>
              Som un estudi de comunicació i disseny que treballa a la vora del client. Creiem en la comunicació amb propòsit: volem fer les coses bé,
              amb sentit i coherència.
            </p>
          </div>
          <div class="hero-line line-2">
            <div class="hero-pill-block reveal-left">
              <div class="pill-image-container">
                <img src="img/mask.webp" alt="Creativitat" class="pill-image" />
              </div>
              <div class="hero-text-block-2">
                <h1 class="hero-text vora-text">VORA STUDIO</h1>
              </div>
            </div>
            <div class="hero-text-block reveal-right">
              <h1 class="hero-text reveal-up">CREATIU</h1>
              <div class="hero-logo-wrapper">
                <img src="img/logo_blanc.png" alt="VoraStudio Logo Blanc" class="hero-logo-white" />
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- SECCIÓN ESTRATEGIA -->
      <section id="strategy" class="full-section strategy-section">
        <div class="parallax-box p-box-1">
          <img src="img/para1.webp" alt="Detall de disseny Vora Studio" class="p-img" />
        </div>
        <div class="parallax-box p-box-2">
          <img src="img/para2.webp" alt="Espai de treball creatiu" class="p-img" />
        </div>

        <div class="content-wrapper strategy-content">
          <h2 class="strategy-title">Estratègia, comunicació i disseny per construir marques amb criteri i coherència</h2>
          <div class="strategy-body">
            <p>
              Som un <strong>estudi de comunicació i disseny</strong> que treballa a la vora del client. Creiem en la comunicació amb propòsit: volem
              fer les coses bé, amb sentit i coherència.
              <strong>Ajudem marques a deixar el soroll i centrar-se en el que realment les fa créixer.</strong>
            </p>
            <a href="#pricing" class="btn-white-strategy">Veure els packs</a>
          </div>
        </div>
      </section>

      <!-- SECCIÓN MASK REVEAL -->
      <section id="mask-reveal" class="mask-section">
        <div class="mask-container">
          <div class="mask-wrapper">
            <img src="img/mask.webp" alt="Imatge de revelació de visió creativa" class="mask-img" />
          </div>
          <div class="mask-text-overlay">
            <h2 class="mask-title">A vora del <br />que importa</h2>
          </div>
        </div>
      </section>

      <!-- SECCIÓN SERVEIS (STACKING CARDS) -->
      <section id="services" class="full-section services-section">
        <div class="services-header">
          <div class="services-title-mask">
            <h2 class="services-title services-title--black">Els nostres <span class="vora-gradient">serveis</span></h2>
          </div>
        </div>
        <div class="services-container">
          <div class="services-sidebar">
            <div class="services-desc">
              <h2 class="services-title2 services-title--black">
                Tot el que necesita<br />
                la teva <span class="vora-gradient">marca</span>
              </h2>
              <p>
                A Vora Studio ajudem marques a comunicar amb sentit i crèixer amb coherència. Combinem pensament estratègic, disseny i creativitat.
              </p>
              <a href="#contact" class="btn-orange-strategy">Treballem junts?</a>
            </div>
          </div>

          <div class="services-stack">
            <!-- Card 1: Branding -->
            <div class="service-card card-1" id="branding">
              <div class="service-card__header">
                <div class="card-icon-wrapper">
                  <svg
                    width="40"
                    height="40"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="white"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                  </svg>
                </div>
                <h3 class="card-title">Estratègia i branding</h3>
              </div>
              <div class="service-card__body">
                <div class="card-image-wrapper">
                  <img
                    src="img/aurexFinestra.webp"
                    alt="Branding Vora Studio"
                    class="card-inner-img"
                  />
                </div>
                <div class="card-text-content">
                  <p class="card-text">
                    Definim la base de la teva marca: qui ets, què dius i com ho dius. T’ajudem a posicionar-te amb claredat i construir una
                    identitat. amb sentit i recorregut.
                  </p>
                </div>
              </div>
            </div>

            <!-- Card 2: Disseny Web -->
            <div class="service-card card-2" id="web">
              <div class="service-card__header">
                <div class="card-icon-wrapper">
                  <svg
                    width="40"
                    height="40"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="white"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <circle cx="12" cy="12" r="10" />
                    <line x1="2" y1="12" x2="22" y2="12" />
                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
                  </svg>
                </div>
                <h3 class="card-title">Projectes web</h3>
              </div>
              <div class="service-card__body">
                <div class="card-image-wrapper">
                  <img
                    src="img/web.webp"
                    alt="Disseny Web Vora Studio"
                    class="card-inner-img"
                  />
                </div>
                <div class="card-text-content">
                  <p class="card-text">
                    Dissenyem webs pensades per comunicar, connectar i convertir. Cada estructura, text i detall té un objectiu: guiar l’usuari i
                    generar resultats.
                  </p>
                </div>
              </div>
            </div>

            <!-- Card 3: Xarxes Socials -->
            <div class="service-card card-3" id="social">
              <div class="service-card__header">
                <div class="card-icon-wrapper">
                  <svg
                    width="40"
                    height="40"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="white"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                  </svg>
                </div>
                <h3 class="card-title">Social media</h3>
              </div>
              <div class="service-card__body">
                <div class="card-image-wrapper">
                  <img
                    src="img/mobil.webp"
                    alt="Xarxes Socials Vora Studio"
                    class="card-inner-img"
                  />
                </div>
                <div class="card-text-content">
                  <p class="card-text">
                    Gestionem xarxes amb criteri, no per omplir calendari. Creem contingut que connecta, aporta valor i reforça la teva marca.
                  </p>
                </div>
              </div>
            </div>

            <!-- Card 4: Estratègia Digital -->
            <div class="service-card card-4" id="disseny">
              <div class="service-card__header">
                <div class="card-icon-wrapper">
                  <svg
                    width="40"
                    height="40"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="white"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path
                      d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"
                    />
                    <polyline points="3.27 6.96 12 12.01 20.73 6.96" />
                    <line x1="12" y1="22.08" x2="12" y2="12" />
                  </svg>
                </div>
                <h3 class="card-title">Disseny gràfic</h3>
              </div>
              <div class="service-card__body">
                <div class="card-image-wrapper">
                  <img
                    src="img/cfood.webp"
                    alt="Estratègia Digital Vora Studio"
                    class="card-inner-img"
                  />
                </div>
                <div class="card-text-content">
                  <p class="card-text">
                    Creem identitats visuals coherents, reconeixibles i alineades amb la teva marca. No es tracta només d’estètica, sinó de transmetre
                    bé el que ets.
                  </p>
                </div>
              </div>
            </div>

            <!-- Card 5: SEO & Optimització -->
            <div class="service-card card-5" id="marqueting">
              <div class="service-card__header">
                <div class="card-icon-wrapper">
                  <svg
                    width="40"
                    height="40"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="white"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                    <line x1="11" y1="8" x2="11" y2="14" />
                    <line x1="8" y1="11" x2="14" y2="11" />
                  </svg>
                </div>
                <h3 class="card-title">Màrqueting digital</h3>
              </div>
              <div class="service-card__body">
                <div class="card-image-wrapper">
                  <img src="https://images.unsplash.com/photo-1562577309-4932fdd64cd1?w=800&q=80" alt="SEO Vora Studio" class="card-inner-img" />
                </div>
                <div class="card-text-content">
                  <p class="card-text">
                    Planifiquem accions que tenen un objectiu clar i mesurable. Combinem estratègia i execució per fer créixer la teva presència
                    digital.
                  </p>
                </div>
              </div>
            </div>

            <!-- Card 6: Fotografia & Vídeo -->
            <div class="service-card card-6" id="publicitat">
              <div class="service-card__header">
                <div class="card-icon-wrapper">
                  <svg
                    width="40"
                    height="40"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="white"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                    <circle cx="12" cy="13" r="4" />
                  </svg>
                </div>
                <h3 class="card-title">Publicitat i comunicació</h3>
              </div>
              <div class="service-card__body">
                <div class="card-image-wrapper">
                  <img
                    src="img/inova.webp"
                    alt="Fotografia Vora Studio"
                    class="card-inner-img"
                  />
                </div>
                <div class="card-text-content">
                  <p class="card-text">
                    Desenvolupem campanyes i missatges que arriben i deixen marca. T’ajudem a explicar el que fas de manera clara, rellevant i
                    efectiva.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- SECCIÓN PARRILLA DIVERGENTE (WEBIX EFFECT) -->
      <section id="projects-grid" class="divergent-grid">
        <div class="divergent-grid__container">
          <div class="divergent-grid__header">
            <h2 class="divergent-grid__title services-title--black">Projectes <span class="vora-gradient">Destacats</span></h2>
          </div>

          <div class="divergent-grid__wrapper">
            <div class="divergent-grid__row divergent-grid__row--top">
              <div class="divergent-grid__item"><img src="img/band.webp" alt="P1" /></div>
              <div class="divergent-grid__item"><img src="img/cfood.webp" alt="P2" /></div>
              <div class="divergent-grid__item"><img src="img/wiar.webp" alt="P3" /></div>
              <div class="divergent-grid__item"><img src="img/aurexFinestra.webp" alt="P4" /></div>
              <div class="divergent-grid__item"><img src="img/web.webp" alt="P5" /></div>
              <div class="divergent-grid__item"><img src="img/Targeta_.webp" alt="P6" /></div>
            </div>

            <div class="divergent-grid__row divergent-grid__row--bottom">
              <div class="divergent-grid__item"><img src="img/cfood.webp" alt="P7" /></div>
              <div class="divergent-grid__item"><img src="img/web.webp" alt="P8" /></div>
              <div class="divergent-grid__item"><img src="img/aurexFinestra.webp" alt="P9" /></div>
              <div class="divergent-grid__item">
                <img src="img/Targeta_.webp" alt="P10" />
              </div>
              <div class="divergent-grid__item">
                <img src="img/wiar.webp" alt="P11" />
              </div>
              <div class="divergent-grid__item">
                <img src="img/band.webp" alt="P12" />
              </div>
            </div>

            <div class="divergent-grid__badge">
              <img src="img/icone nou.png" alt="VoraStudio Logo" class="gallery-badge-img" />
            </div>

            <!-- Botones de Navegación (Solo Móvil) -->
            <div class="gallery-nav-mobile">
              <button id="gallery-prev" class="gallery-nav-btn" aria-label="Anterior">
                <svg
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <line x1="19" y1="12" x2="5" y2="12"></line>
                  <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
              </button>
              <button id="gallery-next" class="gallery-nav-btn" aria-label="Següent">
                <svg
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                  <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </section>

      <!-- SECCIÓN PACKS (PRICING) -->
      <section id="pricing" class="pricing-section">
        <div class="pricing__header">
          <div class="pricing__header-title">
            <h2 class="pricing__title services-title--black">Tria el teu <span class="pricing__title--highlight">Pack</span></h2>
          </div>
          <div class="pricing__header-faq">
            <!-- FAQ 1 -->
            <div class="faq-item">
              <button class="faq-question">
                Per què triar el pack Essencial? <span class="faq-icon"><img src="img/icone nou.png" alt="Icona" /></span>
              </button>
              <div class="faq-answer">
                <p>
                  És ideal si estàs llançant la teva marca o vols una base sòlida sense una gran inversió inicial. Cobreix tot el que necessites per
                  començar a comunicar de forma professional.
                </p>
              </div>
            </div>
            <!-- FAQ 2 -->
            <div class="faq-item">
              <button class="faq-question">
                Per què triar el pack Integral? <span class="faq-icon"><img src="img/icone nou.png" alt="Icona" /></span>
              </button>
              <div class="faq-answer">
                <p>
                  Perfecte si ja tens una marca, però necessites créixer. Agafem les regnes de tota la teva comunicació perquè tu et puguis centrar en
                  el teu negoci.
                </p>
              </div>
            </div>
            <!-- FAQ 3 -->
            <div class="faq-item">
              <button class="faq-question">
                Per què triar el pack Master? <span class="faq-icon"><img src="img/icone nou.png" alt="Icona" /></span>
              </button>
              <div class="faq-answer">
                <p>
                  La solució definitiva per liderar el teu sector. T'oferim una estratègia contínua, campanyes innovadores i presència a tots els
                  canals amb màxima qualitat.
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="pricing__container">
          <!-- Pack Essencial -->
          <article class="pricing-card">
            <div class="pricing-card__top">
              <h3 class="pricing-card__name">Pack Essencial</h3>
              <p class="pricing-card__slogan">A vora de començar</p>
              <div class="pricing-card__price-wrapper">
                <span class="pricing-card__price">450 €</span>
                <span class="pricing-card__period">/mes</span>
              </div>
              <p class="pricing-card__duration">min 3 mesos</p>
              <p class="pricing-card__description">
                Pensat per a marques que fan els primers passos o necessiten ordre. Definim l’estratègia inicial, el relat de marca i una identitat
                visual bàsica.
              </p>
              <a href="#contact" class="pricing-card__cta" aria-label="Més informació sobre el Pack Essencial">Més informació</a>
            </div>

            <div class="pricing-card__features">
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">ESTRATÈGIA DE MARCA</h4>
                <ul class="pricing-card__list">
                  <li>Anàlisi bàsica de mercat i competència</li>
                  <li>Definició de propòsit i valors</li>
                  <li>Definició del to de marca</li>
                  <li>Storytelling inicial</li>
                  <li>Orientació estratègica per comunicar millor</li>
                </ul>
              </div>
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">IDENTITAT VISUAL</h4>
                <ul class="pricing-card__list">
                  <li>Disseny de logotip</li>
                  <li>Paleta de colors bàsica</li>
                  <li>Templates visuals bàsics</li>
                  <li>1 presentació corporativa de marca</li>
                </ul>
              </div>
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">XARXES SOCIALS I CONTINGUT</h4>
                <ul class="pricing-card__list">
                  <li>Estratègia per 2 xarxes socials (IG + FB)</li>
                  <li>4–6 publicacions mensuals</li>
                  <li>Calendari editorial mensual</li>
                </ul>
              </div>
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">WEB I PRESÈNCIA DIGITAL</h4>
                <ul class="pricing-card__list">
                  <li>Pàgina web bàsica (1–3 seccions)</li>
                  <li>SEO inicial</li>
                  <li>Configuració de xarxes socials i email</li>
                </ul>
              </div>
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">COMUNICACIÓ I SUPORT</h4>
                <ul class="pricing-card__list">
                  <li>1 peça gràfica mensual</li>
                  <li>Assessorament en coherència visual</li>
                </ul>
              </div>
            </div>
          </article>

          <!-- Pack Integral -->
          <article class="pricing-card pricing-card--featured">
            <div class="pricing-card__top">
              <h3 class="pricing-card__name">Pack Integral</h3>
              <p class="pricing-card__slogan">A vora del creixement</p>
              <div class="pricing-card__price-wrapper">
                <span class="pricing-card__price">900 €</span>
                <span class="pricing-card__period">/mes</span>
              </div>
              <p class="pricing-card__duration">min 4 mesos</p>
              <p class="pricing-card__description">
                Per a marques que volen créixer i professionalitzar la seva comunicació. Desenvolupem una estratègia completa i identitat sòlida.
              </p>
              <a href="#contact" class="pricing-card__cta" aria-label="Més informació sobre el Pack Integral">Més informació</a>
            </div>

            <div class="pricing-card__features">
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">ESTRATÈGIA DE MARCA I COMUNICACIÓ</h4>
                <ul class="pricing-card__list">
                  <li>Anàlisi detallada de mercat i competència</li>
                  <li>Definició de propòsit, valors i storytelling</li>
                  <li>Naming i arquitectura de marca (si cal)</li>
                  <li>Brandbook i guia visual</li>
                </ul>
              </div>
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">IDENTITAT VISUAL</h4>
                <ul class="pricing-card__list">
                  <li>Identitat visual completa</li>
                  <li>Materials corporatius (papereria, presentacions)</li>
                  <li>Direcció d’art lleugera</li>
                </ul>
              </div>
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">XARXES SOCIALS I CONTINGUT</h4>
                <ul class="pricing-card__list">
                  <li>Gestió de 2–3 xarxes socials</li>
                  <li>Creació de contingut visual i textual</li>
                  <li>1 sessió de fotografia</li>
                  <li>Calendari editorial i analítica bàsica</li>
                </ul>
              </div>
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">WEB I PRESÈNCIA DIGITAL</h4>
                <ul class="pricing-card__list">
                  <li>Disseny web i SEO bàsic</li>
                  <li>Manteniment i actualitzacions</li>
                  <li>Optimització de velocitat i seguretat</li>
                </ul>
              </div>
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">COMUNICACIÓ I SUPORT</h4>
                <ul class="pricing-card__list">
                  <li>2 peces gràfiques mensuals</li>
                  <li>1 campanya creativa i publicitat segmentada</li>
                  <li>1 newsletter mensual</li>
                  <li>Comunicació corporativa bàsica</li>
                </ul>
              </div>
            </div>
          </article>

          <!-- Pack Master -->
          <article class="pricing-card">
            <div class="pricing-card__top">
              <h3 class="pricing-card__name">Pack Master</h3>
              <p class="pricing-card__slogan">A vora del que importa</p>
              <div class="pricing-card__price-wrapper">
                <span class="pricing-card__price">1.400 €</span>
                <span class="pricing-card__period">/mes</span>
              </div>
              <p class="pricing-card__duration">min 6 mesos</p>
              <p class="pricing-card__description">
                La solució més completa per a marques que volen liderar. Estratègia avançada, branding potent i comunicació multicanal.
              </p>
              <a href="#contact" class="pricing-card__cta" aria-label="Més informació sobre el Pack Master">Més informació</a>
            </div>

            <div class="pricing-card__features">
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">ESTRATÈGIA DE MARCA</h4>
                <ul class="pricing-card__list">
                  <li>Estratègia integral i contínua</li>
                  <li>Rebranding complet (si cal)</li>
                  <li>Storytelling avançat i arquitectura</li>
                  <li>Brandbook complet i optimització constant</li>
                </ul>
              </div>
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">IDENTITAT VISUAL</h4>
                <ul class="pricing-card__list">
                  <li>Sistema visual complet i Motion branding</li>
                  <li>Materials corporatius complets</li>
                  <li>Direcció creativa continuada</li>
                </ul>
              </div>
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">XARXES SOCIALS I CONTINGUT</h4>
                <ul class="pricing-card__list">
                  <li>Gestió de 4 xarxes socials</li>
                  <li>Contingut visual i textual</li>
                  <li>1 sessió de fotografia i Social Ads</li>
                  <li>Planificació de campanyes avanzada</li>
                </ul>
              </div>
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">WEB I PRESÈNCIA DIGITAL</h4>
                <ul class="pricing-card__list">
                  <li>Disseny UX/UI complet</li>
                  <li>Hosting, dominis, seguretat i SEO avançat</li>
                  <li>Blog, analítica i millores contínues</li>
                </ul>
              </div>
              <div class="pricing-card__group">
                <h4 class="pricing-card__group-title">COMUNICACIÓ I CREIXEMENT</h4>
                <ul class="pricing-card__list">
                  <li>4 peces gràfiques mensuals</li>
                  <li>Campanyes creatives i llançaments</li>
                  <li>2 newsletters mensuals</li>
                  <li>Comunicació corporativa integral (PR)</li>
                </ul>
              </div>
            </div>
          </article>
        </div>
      </section>

      <!-- SECCIÓN MARQUESINA (LOGOS) -->
      <section class="logos-marquee">
        <div class="logos-title">Empreses a vora nostra</div>
        <div class="logos-marquee__track">
          <!-- Duplicamos los elementos para el efecto infinito -->
          <div class="logos-marquee__content">
            <img src="img/wiar.png" alt="Logo 1" />
            <img src="img/Comercial-ros_negre.png" alt="Logo 2" />
            <img src="img/A.Izquierdo.png" alt="Logo 3" />
            <img src="img/6.png" alt="Logo 4" />
            <img src="img/raymel_logo.png" alt="Logo 5" />
          </div>
          <div class="logos-marquee__content">
            <img src="img/Cfood_logo BLACK.png" alt="Logo 1" />
            <img src="img/Aurex 1x.png" alt="Logo 2" />
            <img src="img/spica.png" alt="Logo 3" />
            <img src="img/6.png" alt="Logo 4" />
            <img src="img/logo_certical_marror.png" alt="Logo 5" />
          </div>
        </div>
      </section>

      <!-- SECCIÓN FORMULARIO MODERNO -->
      <section id="contact" class="contact-section">
        <div class="contact__container">
          <!-- Columna Izquierda: Información -->
          <div class="contact__info">
            <h2 class="contact__info-title">Fem un cafe?</h2>
            <p class="contact__info-subtitle">Estas a la vora d'alguna cosa gran!</p>
            <a href="mailto:hola@vorastudio.cat" class="contact__info-email">hola@vorastudio.cat</a>

            <div class="contact__socials">
              <a href="https://www.linkedin.com/company/vorastudio" class="social-icon" aria-label="LinkedIn" target="_blank">
                <svg
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                  <rect x="2" y="9" width="4" height="12" />
                  <circle cx="4" cy="4" r="2" />
                </svg>
              </a>
              <a href="https://wa.me/722812139" class="social-icon" aria-label="WhatsApp" target="_blank">
                <svg
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 1 1-7.6-7.6 8.38 8.38 0 0 1 3.8.9L21 4.5z" />
                </svg>
              </a>
              <a href="https://www.instagram.com/vorastudio_/" class="social-icon" aria-label="Instagram" target="_blank">
                <svg
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                  <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                  <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                </svg>
              </a>
            </div>
          </div>

          <!-- Columna Dreta: Formulari -->
          <div class="contact__form-wrapper">
            <form class="modern-form" id="contact-form-element" action="javascript:void(0);" method="POST">
              <!-- SEGURETAT: Token CSRF per evitar atacs de falsificació -->
              <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
              
              <!-- SEGURETAT: Honeypot per evitar spam de bots (invisible per humans) -->
              <div style="display:none;">
                <label>No omplis aquest camp si ets humà:</label>
                <input type="text" name="honeypot" value="">
              </div>

              <!-- SEGURETAT: Token de Google reCAPTCHA -->
              <input type="hidden" name="recaptcha_response" id="recaptcha_response">

              <div class="form-row">
                <div class="form-field">
                  <!-- Anulem amb el action = Javascript, el comportament per defecte del formulari -->
                  <input type="text" id="name" name="name" class="form-field__input" placeholder=" " required />
                  <label for="name" class="form-field__label">Nom i cognoms</label>
                  <div class="form-field__bar"></div>
                </div>
                <div class="form-field">
                  <input type="email" id="email" name="email" class="form-field__input" placeholder=" " required />
                  <label for="email" class="form-field__label">Correu electrònic</label>
                  <div class="form-field__bar"></div>
                </div>
              </div>

              <div class="form-row">
                <div class="form-field">
                  <select id="topic" name="topic" class="form-field__input" required>
                    <option value="" disabled selected hidden></option>
                    <option value="Essencial">Pack Essencial</option>
                    <option value="Integral">Pack Integral</option>
                    <option value="Master">Pack Master</option>
                    <option value="Altre">Un altre motiu / Consulta general</option>
                  </select>
                  <label for="topic" class="form-field__label">Tema de interes</label>
                  <div class="form-field__bar"></div>
                </div>

                <div class="form-field">
                  <input type="text" id="subject" name="subject" class="form-field__input" placeholder=" " required />
                  <label for="subject" class="form-field__label">Assumpte</label>
                  <div class="form-field__bar"></div>
                </div>
              </div>

              <div class="form-field">
                <textarea id="message" name="message" class="form-field__input form-field__textarea" placeholder=" " required></textarea>
                <label for="message" class="form-field__label">Com et podem ayudar?</label>
                <div class="form-field__bar"></div>
              </div>



              <!-- Checkbox Política y reCAPTCHA oculto -->
              <div class="form-checkbox">
                <input type="checkbox" id="privacy" name="privacy" class="form-checkbox__input" required />
                <label for="privacy" class="form-checkbox__label"> He llegit i accepto les <a href="#">condicions d'ús</a>. </label>
              </div>

              <div id="form-status" class="form-status"></div>

              <button type="submit" class="form-btn" aria-label="Enviar formulari de contacte">
                <span class="form-btn__text">Enviar missatge</span>
                <div class="form-btn__icon">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7" />
                  </svg>
                </div>
              </button>
            </form>
          </div>
        </div>
      </section>

      <!-- Contenedor para notificaciones Toast con GSAP -->
      <div id="toast-container" class="toast-hidden"></div>
      <!-- PIE DE PÁGINA -->
      <footer id="main-footer" class="footer-section">
        <div class="content-wrapper">
          <p>© 2026 VoraStudio | Creativitat sense límits.</p>
        </div>
      </footer>
    </main>
    <script src="js/script.js"></script>
  </body>
</html>
