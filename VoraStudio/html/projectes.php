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
  <body class="page-projectes page-projectes-gallery">

    <!-- ----- HEADER ----- -->
     <header id="sub-header">
      <nav class="nav-container">
        <div class="logo">
          <a href="../index.php">
            <img src="../img/voraL.png" alt="VoraStudio Logo" class="logo-img logo-img--default" />
          </a>
        </div>

        <button class="menu-toggle" id="menu-toggle" aria-label="Obrir menú de navegació">
          <span class="line line-1"></span>
          <span class="line line-2"></span>
        </button>

        <ul class="nav-links desktop-only">
          <li class="has-dropdown">
            <a href="serveis.php">Serveis</a>
            <ul class="dropdown-menu">
              <li><a href="../index.php#branding">Estratègia i Branding</a></li>
              <li><a href="../index.php#web">Projectes Web</a></li>
              <li><a href="../index.php#social">Social Media</a></li>
              <li><a href="../index.php#disseny">Disseny Gràfic</a></li>
              <li><a href="../index.php#marqueting">Màrqueting Digital</a></li>             
            </ul>
          </li>
          <li class="has-dropdown">
            <a href="#">Projectes</a>
          </li>
          <li class="has-dropdown">
            <a href="../index.php#pricing">Packs</a>
          </li>
        </ul>

        <div class="header__cta desktop-only">
          <a href="../index.php#contact" class="btn-cta" style="border: 2px solid #f5a04e !important;">Contacte</a>
        </div>
      </nav>
    </header>

    <!-- Menú Overlay -->
    <div class="menu-overlay" id="menu-overlay">
      <span class="close-label">CLOSE</span>
      <div class="overlay-content">
        <ul class="overlay-links">
          <li><a href="serveis.php">Serveis</a></li>
          <li><a href="#">Projectes</a></li>
          <li><a href="../index.php#pricing">Packs</a></li>
          <li><a href="../index.php#contacte">Contacte</a></li>
        </ul>
      </div>
    </div>

    <!-- ----- MAIN: GALERIA HORITZONTAL ----- -->
    <main class="main-projectes">

      <section class="projectes-scroll" id="projectes-scroll">
        <div class="projectes-scroll__track" id="projectes-track">

          <div class="projecte-card-wrap">
            <div class="projecte-card__info">
              <h3 class="projecte-card__title">Aurex Immobles</h3>
              <p class="projecte-card__subtitle">Pack Master</p>
            </div>
            <a href="../projectes/projecte.php?project=aurex" class="projecte-card" data-project="aurex">
              <img src="../img/aurexFinestra.webp" alt="Aurex Immobles" loading="lazy" />
            </a>
          </div>

          <div class="projecte-card-wrap">
            <div class="projecte-card__info">
              <h3 class="projecte-card__title">Comercial Ross</h3>
              <p class="projecte-card__subtitle">Pack Essencial</p>
            </div>
            <a href="#" class="projecte-card" data-project="comercial-ross">
              <img src="../img/para3.webp" alt="Comercial Ross" loading="lazy" />
            </a>
          </div>

          <div class="projecte-card-wrap">
            <div class="projecte-card__info">
              <h3 class="projecte-card__title">C-Food</h3>
              <p class="projecte-card__subtitle">Pack Integral</p>
            </div>
            <a href="#" class="projecte-card" data-project="cfood">
              <img src="../img/cfood.webp" alt="C-Food" loading="lazy" />
            </a>
          </div>

          <div class="projecte-card-wrap">
            <div class="projecte-card__info">
              <h3 class="projecte-card__title">Guardavan</h3>
              <p class="projecte-card__subtitle">Pack Integral</p>
            </div>
            <a href="#" class="projecte-card" data-project="guardavan">
              <img src="../img/Targetes.webp" alt="Guardavan" loading="lazy" />
            </a>
          </div>

          <div class="projecte-card-wrap">
            <div class="projecte-card__info">
              <h3 class="projecte-card__title">Wiar</h3>
              <p class="projecte-card__subtitle">Pack Essencial</p>
            </div>
            <a href="#" class="projecte-card" data-project="wiar">
              <img src="../img/wiar.webp" alt="Wiar" loading="lazy" />
            </a>
          </div>

          <div class="projecte-card-wrap">
            <div class="projecte-card__info">
              <h3 class="projecte-card__title">Raymel</h3>
              <p class="projecte-card__subtitle">Pack Integral</p>
            </div>
            <a href="#" class="projecte-card" data-project="raymel">
              <img src="../img/band.webp" alt="Raymel" loading="lazy" />
            </a>
          </div>

          <div class="projecte-card-wrap">
            <div class="projecte-card__info">
              <h3 class="projecte-card__title">Spica</h3>
              <p class="projecte-card__subtitle">Pack Essencial</p>
            </div>
            <a href="#" class="projecte-card" data-project="spica">
              <img src="../img/web.webp" alt="Spica" loading="lazy" />
            </a>
          </div>

          <div class="projecte-card-wrap">
            <div class="projecte-card__info">
              <h3 class="projecte-card__title">Palmito House</h3>
              <p class="projecte-card__subtitle">Pack Essencial</p>
            </div>
            <a href="#" class="projecte-card" data-project="palmitohouse">
              <img src="../img/Mockup 2.webp" alt="Palmito House" loading="lazy" />
            </a>
          </div>

          <div class="projecte-card-wrap">
            <div class="projecte-card__info">
              <h3 class="projecte-card__title">InnovaFP</h3>
              <p class="projecte-card__subtitle">Pack Essencial</p>
            </div>
            <a href="#" class="projecte-card" data-project="innovafp">
              <img src="../img/Mokcup.webp" alt="InnovaFP" loading="lazy" />
            </a>
          </div>

          <div class="projecte-card-wrap">
            <div class="projecte-card__info">
              <h3 class="projecte-card__title">Novagal</h3>
              <p class="projecte-card__subtitle">Pack Master</p>
            </div>
            <a href="#" class="projecte-card" data-project="novagal">
              <img src="../img/Targeta_.webp" alt="Novagal" loading="lazy" />
            </a>
          </div>

          <div class="projecte-card-wrap">
            <div class="projecte-card__info">
              <h3 class="projecte-card__title">D-Tast</h3>
              <p class="projecte-card__subtitle">Pack Essencial</p>
            </div>
            <a href="#" class="projecte-card" data-project="dtast">
              <img src="../img/band.webp" alt="D-Tast" loading="lazy" />
            </a>
          </div>

          <div class="projecte-card-wrap">
            <div class="projecte-card__info">
              <h3 class="projecte-card__title">Vitoria Teylor</h3>
              <p class="projecte-card__subtitle">Pack Master</p>
            </div>
            <a href="#" class="projecte-card" data-project="vitoria-teylor">
              <img src="../img/web.webp" alt="Vitoria Teylor" loading="lazy" />
            </a>
          </div>

        </div>

        <h2 class="projectes-scroll__title">Projectes</h2>

        <footer class="projectes-scroll__footer">
          <p>&copy; 2026 VoraStudio | Creativitat sense l&iacute;mits.</p>
        </footer>
      </section>

    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
      var track = document.querySelector('.projectes-scroll__track');
      var section = document.querySelector('.projectes-scroll');
      if (!track || !section) return;

      // Clonar 3 veces para que el recorrido sea muy largo
      var cards = track.querySelectorAll('.projecte-card-wrap');
      for (var i = 0; i < 3; i++) {
        cards.forEach(function (card) {
          track.appendChild(card.cloneNode(true));
        });
      }

      ScrollTrigger.refresh();

      gsap.to(track, {
        x: function () {
          return -(track.scrollWidth - section.offsetWidth);
        },
        ease: 'none',
        scrollTrigger: {
          trigger: section,
          pin: true,
          pinSpacing: true,
          start: 'top 17%',
          end: function () {
            return '+=' + (track.scrollWidth - section.offsetWidth);
          },
          scrub: 1,
          invalidateOnRefresh: true
        }
      });
    });
    </script>

    <script src="../js/script.js"></script>
  </body>
</html>
