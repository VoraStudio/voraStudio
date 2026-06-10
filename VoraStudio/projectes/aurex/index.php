<?php
//Token de seguretat per evitar atacs CSRF ->
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
    <title>VoraStudio | Estudi Creatiu a Girona</title>

    <!-- Google Fonts: Montserrat y Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&family=Outfit:wght@300;400;600;700&display=swap"
      rel="stylesheet"
    />
    <!-- CSS Principal -->
    <link rel="stylesheet" href="../../css/style.css" />

    <!-- GSAP Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/TextPlugin.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.2/dist/SplitText.min.js"></script>
    <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>

    <!-- Google reCAPTCHA (TEST para Local) -->
    <script src="https://www.google.com/recaptcha/api.js?render=6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></script>
  </head>
  <body class="page-aurex">
      <!-- ----- HEADER ----- -->
     <header id="sub-header">
      <nav class="nav-container">
        <div class="logo">
          <a href="../../index.php">
            <img src="../../img/voraL.png" alt="VoraStudio Logo" class="logo-img logo-img--default" />
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
            <a href="../../html/serveis.php">Serveis</a>
            <ul class="dropdown-menu">
              <li><a href="../../index.php#branding">Estratègia i Branding</a></li>
              <li><a href="../../index.php#web">Projectes Web</a></li>
              <li><a href="../../index.php#social">Social Media</a></li>
              <li><a href="../../index.php#disseny">Disseny Gràfic</a></li>
              <li><a href="../../index.php#marqueting">Màrqueting Digital</a></li>             
            </ul>
          </li>
          <li class="has-dropdown">
            <a href="../../html/projectes.html">Projectes</a>
          </li>
          <li class="has-dropdown">
            <a href="../../index.php#pricing">Packs</a>
          </li>
        </ul>

        <!-- Botón Contacto Derecho -->
        <div class="header__cta desktop-only">
          <a href="../../index.php#contact" class="btn-cta" style="border: 2px solid #f5a04e !important;">Contacte</a>
        </div>
      </nav>
    </header>

    <!-- Menú Overlay (Panel Lateral) -->
    <div class="menu-overlay" id="menu-overlay">
      <!-- Indicador de cierre -->
      <span class="close-label">CLOSE</span>

      <div class="overlay-content">
        <ul class="overlay-links">
          <li><a href="../../html/serveis.php">Serveis</a></li>
          <li><a href="../../html/projectes.html">Projectes</a></li>
          <li><a href="../../index.php#pricing">Packs</a></li>
          <li><a href="../../index.php#contact">Contacte</a></li>
        </ul>
      </div>
    </div>
    <!-- ----- FIN SECCIÓN HEADER ----- -->

    <main class="main-projectes">
      <!-- SECCIÓN HERO PROJECT -->
      <section id="aurex-hero" class="aurex-hero">
        <div class="aurex-hero__container">
          <div class="aurex-hero__left">
            <img src="../../img/Aurex 1x.png" alt="Aurex Inmobles" class="aurex-hero__logo" />
            <a href="https://aureximmobles.com" target="_blank" class="aurex-hero__link">WEBISTE: <span>aureximmobles.com</span></a>
          </div>

          <div class="aurex-hero__right">
            <p class="aurex-hero__description">
              En aquest projecte s’ha desenvolupat la identitat visual, la pàgina web i la presència a xarxes socials per a Aurex Immobles. 
              L’objectiu ha estat crear una imatge de marca coherent i professional, alineada amb els valors del sector immobiliari, 
              com la confiança, la proximitat i la transparència.
            </p>

            <div class="aurex-hero__tags">
              <span class="aurex-hero__tag">Desenvolupament</span>
              <span class="aurex-hero__tag">Pack Essencial</span>
              <span class="aurex-hero__tag">Branding</span>
            </div>
          </div>
        </div>
      </section>

      <!-- SECCIÓN ESTRATEGIA / BRIEFING -->
      <section id="aurex-strategy" class="aurex-strategy">
        <div class="aurex-strategy__container">
          <!-- Block 1: Briefing -->
          <div class="aurex-strategy__block">
            <div class="aurex-strategy__header">
              <span class="aurex-strategy__dot"></span>
              <h2 class="aurex-strategy__label">Briefing</h2>
            </div>
            <p class="aurex-strategy__text">
              Definició d’objectius, públic i valors de marca (confiança, proximitat i professionalitat) per crear una identitat clara i coherent.
            </p>
          </div>

          <!-- Block 2: Desenvolupament -->
          <div class="aurex-strategy__block">
            <div class="aurex-strategy__header">
              <span class="aurex-strategy__dot"></span>
              <h2 class="aurex-strategy__label">Desenvolupament</h2>
            </div>
            <p class="aurex-strategy__text">
              Disseny de la web i xarxes socials amb una estructura intuïtiva i una imatge unificada centrada en l’usuari.
            </p>
          </div>

          <!-- Block 3: Resultat -->
          <div class="aurex-strategy__block">
            <div class="aurex-strategy__header">
              <span class="aurex-strategy__dot"></span>
              <h2 class="aurex-strategy__label">Resultat</h2>
            </div>
            <p class="aurex-strategy__text">
              Marca digital sòlida, coherent i funcional que millora la presència online i l’experiència d’usuari.
            </p>
          </div>
        </div>
      </section>

      <!-- SECCIÓN GALERÍA -->
      <section id="aurex-gallery-main" class="aurex-gallery">
        <div class="aurex-gallery__item">
          <img src="../../img/aurexFinestra.webp" alt="Aurex Branding Mockup" class="aurex-gallery__img" />
        </div>

        <!-- REJILLA DE 4 FOTOS (2x2) -->
        <div class="aurex-gallery__grid" id="aurex-gallery-grid">
          <div class="aurex-gallery__grid-item">
            <img src="../../img/Aurex-scaled.webp" alt="Showcase 1" class="aurex-gallery__grid-img" />
          </div>
          <div class="aurex-gallery__grid-item">
            <img src="../../img/aurex-web2.webp" alt="Showcase 2" class="aurex-gallery__grid-img" />
          </div>
          <div class="aurex-gallery__grid-item">
            <img src="../../img/Mockup 2.webp" alt="Showcase 3" class="aurex-gallery__grid-img" />
          </div>
          <div class="aurex-gallery__grid-item">
            <img src="../../img/Targeta_.webp" alt="Showcase 4" class="aurex-gallery__grid-img" />
          </div>
        </div>
      </section>

      <div id="toast-container" class="toast-hidden"></div>
    </main>

    <!-- SECCIÓN FORMULARIO MODERNO (REVEAL) -->
    <section id="contact" class="contact-section">
        <div class="contact__container">
          <!-- Columna Izquierda: Información -->
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
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.396.015 12.03a11.782 11.782 0 001.592 5.955L0 24l6.111-1.605a11.765 11.765 0 005.935 1.636h.005c6.634 0 12.032-5.396 12.035-12.03a11.81 11.81 0 00-3.486-8.484z" />
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

          <!-- Columna Derecha: Formulario -->
          <div class="contact__form-wrapper">
            <form class="modern-form" id="contact-form-element" action="../../contacte.php" method="POST">
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

    <!-- PIE DE PÁGINA -->
    <footer id="main-footer" class="footer-section">
      <div class="content-wrapper">
        <p>© 2026 VoraStudio | Creativitat sense límits.</p>
      </div>
    </footer>


    <!-- Botó flotant de WhatsApp -->
    <a href="https://wa.me/722812139" class="whatsapp-float" target="_blank" aria-label="Contacta'ns per WhatsApp">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.396.015 12.03a11.782 11.782 0 001.592 5.955L0 24l6.111-1.605a11.765 11.765 0 005.935 1.636h.005c6.634 0 12.032-5.396 12.035-12.03a11.81 11.81 0 00-3.486-8.484z" />
      </svg>
    </a>

    <script src="../../js/script.js"></script>
  </body>
</html>
