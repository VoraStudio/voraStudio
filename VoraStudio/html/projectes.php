<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!doctype html>
<html lang="ca">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>VoraStudio | Projectes Creatius</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet" />
    
    <!-- CSS Principal -->
    <link rel="stylesheet" href="../css/style.css" />

    <!-- JS Libraries (GSAP & Lenis) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/TextPlugin.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.2/dist/SplitText.min.js"></script>
    <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></script>
  </head>
  <body class="page-projectes">

    <!-- ----- HEADER ----- -->
     <header id="sub-header">
      <nav class="nav-container">
        <div class="logo">
          <a href="../index.php">
            <img src="../img/voraL.png" alt="VoraStudio Logo" class="logo-img logo-img--default" />
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
            <a href="../index.php#services">Serveis</a>
            <ul class="dropdown-menu">
              <li><a href="../index.php#branding">Estratègia i Branding</a></li>
              <li><a href="../index.php#web">Projectes Web</a></li>
              <li><a href="../index.php#social">Social Media</a></li>
              <li><a href="../index.php#disseny">Disseny Gràfic</a></li>
              <li><a href="../index.php#marqueting">Màrqueting Digital</a></li>             
            </ul>
          </li>
          <li class="has-dropdown">
            <a href="#projects-grid">Projectes</a>
            <ul class="dropdown-menu">
              <li><a href="cross.php">Comercial Ross</a></li>
              <li><a href="aurex.php">Aurex Immobles</a></li>
              <!-- <li><a href="guaravan.php">Guaravan</a></li>
              <li><a href="cfood.php">C-Food</a></li>
              <li><a href="innovafp.php">InnovaFP</a></li>
              <li><a href="spica.php">Spica</a></li>
              <li><a href="raymel.php">Raymel</a></li>
              <li><a href="dtast.php">D-Tast</a></li>
              <li><a href="wiar.php">Wiar</a></li>
              <li><a href="novagal.php">Novagal</a></li>
              <li><a href="palmitohouse.php">Palmito House</a></li>
              <li><a href="vitoriaTeylor.php">Vitoria Teylor</a></li>
              <li><a href="espaiGras.php">Espai Gastronòmic Quim Casellas</a></li> -->
            </ul>
          </li>
          <li class="has-dropdown">
            <a href="../index.php#pricing">Packs</a>
          </li>
        </ul>

        <!-- Botón Contacto Derecho -->
        <div class="header__cta desktop-only">
          <a href="../index.php#contact" class="btn-cta" style="border: 2px solid #f5a04e !important;">Contacte</a>
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
              <li><a href="../index.php#branding">Estratègia i Branding</a></li>
              <li><a href="../index.php#web">Projectes Web</a></li>
              <li><a href="../index.php#social">Social Media</a></li>
              <li><a href="../index.php#disseny">Disseny Gràfic</a></li>
              <li><a href="../index.php#marqueting">Màrqueting Digital</a></li>            
            </ul>
          </li>
          <li class="has-submenu">
            <a href="javascript:void(0)" class="parent-link">Projectes</a>
             <ul class="dropdown-menu">
              <li><a href="cross.php">Comercial Ross</a></li>
              <li><a href="aurex.php">Aurex Immobles</a></li>
              <!-- <li><a href="guaravan.php">Guaravan</a></li>
              <li><a href="cfood.php">C-Food</a></li>
              <li><a href="innovafp.php">InnovaFP</a></li>
              <li><a href="spica.php">Spica</a></li>
              <li><a href="raymel.php">Raymel</a></li>
              <li><a href="dtast.php">D-Tast</a></li>
              <li><a href="wiar.php">Wiar</a></li>
              <li><a href="novagal.php">Novagal</a></li>
              <li><a href="palmitohouse.php">Palmito House</a></li>
              <li><a href="vitoriaTeylor.php">Vitoria Teylor</a></li>
              <li><a href="espaiGras.php">Espai Gastronòmic Quim Casellas</a></li> -->
            </ul>
          </li>
          <li><a href="../index.php#pricing">Packs</a></li>
          <li><a href="../index.php#contacte">Contacte</a></li>
        </ul>
      </div>
    </div>

    <!-- ----- MAIN CONTENT (WHITE BLOCK) ----- -->
    <main class="main-projectes">
      
      <!-- HERO -->
      <!-- <section class="portfolio-hero">
        <div class="portfolio-hero__container">
          <h1 class="portfolio-hero__title">
            Projectes amb <br />
            <span class="accent">ànima creativa.</span>
          </h1>
          <p class="portfolio-hero__subtitle">
            Una selecció de treballs on la passió pel detall i la innovació digital es troben.
          </p>
        </div>
      </section> -->

      <!-- GALLERY (ACCORDION) -->
      <section class="portfolio-grid">
        <!-- Project 01 -->
       <article class="portfolio-card">
          <img src="../img/para3.webp" alt="Divergent" class="portfolio-card__img" />
          <div class="portfolio-card__overlay">
             <span class="portfolio-card__number">01</span>
             <h2 class="portfolio-card__title">Comercial Ross</h2>
             <p class="portfolio-card__text">Identitat visual disruptiva i disseny editorial.</p>
             <a href="cross.php" class="portfolio-card__btn">Veure més</a>
          </div>
        </article>

        <!-- Project 02 -->
      <article class="portfolio-card">
          <img src="../img/aurexFinestra.webp" alt="Aurex" class="portfolio-card__img" />
          <div class="portfolio-card__overlay">
            <span class="portfolio-card__number">02</span>
            <h2 class="portfolio-card__title">Aurex Immobles</h2>
            <p class="portfolio-card__text">Branding, Web Design & Social Media.</p>
            <a href="aurex.php" class="portfolio-card__btn">Veure més</a>
          </div>
      </article>
        

        <!-- Project 03 -->
        <article class="portfolio-card">
          <img src="../img/cfood.webp" alt="Graphic" class="portfolio-card__img" />
          <div class="portfolio-card__overlay">
             <span class="portfolio-card__number">03</span>
             <h2 class="portfolio-card__title">C-Food</h2>
             <p class="portfolio-card__text">Packaging corporatiu premium.</p>
             <a href="#" class="portfolio-card__btn">Pròximament</a>
          </div>
        </article>

        <!-- Project 04 -->
        <article class="portfolio-card">
          <img src="../img/Targetes.webp" alt="Web" class="portfolio-card__img" />
          <div class="portfolio-card__overlay">
             <span class="portfolio-card__number">04</span>
             <h2 class="portfolio-card__title">Guardavan</h2>
             <p class="portfolio-card__text">Disseny i optimització web d'alt rendiment.</p>
             <a href="#" class="portfolio-card__btn">Pròximament</a>
          </div>
        </article>
      </section>

      <section class="portfolio-grid2">
        <!-- Project 01 -->
       <article class="portfolio-card">
          <img src="../img/para3.webp" alt="Divergent" class="portfolio-card__img" />
          <div class="portfolio-card__overlay">
             <span class="portfolio-card__number">01</span>
             <h2 class="portfolio-card__title">Comercial Ross</h2>
             <p class="portfolio-card__text">Identitat visual disruptiva i disseny editorial.</p>
             <a href="cross.php" class="portfolio-card__btn">Veure més</a>
          </div>
        </article>

        <!-- Project 02 -->
      <article class="portfolio-card">
          <img src="../img/aurexFinestra.webp" alt="Aurex" class="portfolio-card__img" />
          <div class="portfolio-card__overlay">
            <span class="portfolio-card__number">02</span>
            <h2 class="portfolio-card__title">Aurex Immobles</h2>
            <p class="portfolio-card__text">Branding, Web Design & Social Media.</p>
            <a href="aurex.php" class="portfolio-card__btn">Veure més</a>
          </div>
      </article>
        <!-- Project 03 -->
        <article class="portfolio-card">
          <img src="../img/cfood.webp" alt="Graphic" class="portfolio-card__img" />
          <div class="portfolio-card__overlay">
             <span class="portfolio-card__number">03</span>
             <h2 class="portfolio-card__title">C-Food</h2>
             <p class="portfolio-card__text">Packaging corporatiu premium.</p>
             <a href="#" class="portfolio-card__btn">Pròximament</a>
          </div>
        </article>

        <!-- Project 04 -->
        <article class="portfolio-card">
          <img src="../img/Targetes.webp" alt="Web" class="portfolio-card__img" />
          <div class="portfolio-card__overlay">
             <span class="portfolio-card__number">04</span>
             <h2 class="portfolio-card__title">Guardavan</h2>
             <p class="portfolio-card__text">Disseny i optimització web d'alt rendiment.</p>
             <a href="#" class="portfolio-card__btn">Pròximament</a>
          </div>
        </article>
      </section>

      <!-- Toast Container -->
      <div id="toast-container" class="toast-hidden"></div>

    </main> <!-- CIERRE DEL BLOQUE BLANCO -->

    <!-- ----- CONTACT SECTION (REVEAL ON IMAGE) ----- -->
    <section id="contact" class="contact-section">
      <div class="contact__container">
        <div class="contact__info">
          <h2 class="contact__info-title">Fem un cafe?</h2>
          <p class="contact__info-subtitle">Estas a la vora d'alguna cosa gran!</p>
          <a href="mailto:hola@vorastudio.cat" class="contact__info-email">hola@vorastudio.cat</a>

          <div class="contact__socials">
            <a href="https://www.linkedin.com/company/vorastudio" class="social-icon" aria-label="LinkedIn" target="_blank">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" />
                <rect x="2" y="9" width="4" height="12" />
                <circle cx="4" cy="4" r="2" />
              </svg>
            </a>
            <a href="https://wa.me/722812139" class="social-icon" aria-label="WhatsApp" target="_blank">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 1 1-7.6-7.6 8.38 8.38 0 0 1 3.8.9L21 4.5z" />
              </svg>
            </a>
            <a href="https://www.instagram.com/vorastudio_/" class="social-icon" aria-label="Instagram" target="_blank">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
              </svg>
            </a>
          </div>
        </div>

        <div class="contact__form-wrapper">
          <form class="modern-form" id="contact-form-element" action="javascript:void(0);" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div style="display:none;">
              <label>No omplis aquest camp si ets humà:</label>
              <input type="text" name="honeypot" value="">
            </div>
            <input type="hidden" name="recaptcha_response" id="recaptcha_response">

            <div class="form-row">
              <div class="form-field">
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

    <!-- ----- FOOTER ----- -->
    <footer id="main-footer" class="footer-section">
      <div class="content-wrapper">
        <p>© 2026 VoraStudio | Creativitat sense límits.</p>
      </div>
    </footer>

    <!-- Scripts -->
    <script src="../js/script.js"></script>
  </body>
</html>
