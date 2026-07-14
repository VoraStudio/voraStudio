<?php
require_once __DIR__ . '/../includes/CmsClient.php';

$cmsUrl = getenv('CMS_URL') ?: 'https://voracms.voradata.cat';
$origin = getenv('SSR_ORIGIN') ?: 'https://vorastudio.cat';
$cms = new CmsClient($cmsUrl, $origin);

/* SSR: carregar projectes des del CMS */
$allProjects = [];

try {
    $result = $cms->fetch('/api/public/web/vorastudio-projects?locale=ca');
    if ($result && isset($result['data'])) {
        $allProjects = $result['data'];
        usort($allProjects, function ($a, $b) {
            return ($a['ordre'] ?? 999) - ($b['ordre'] ?? 999);
        });
    }
} catch (Exception $e) {
    // CMS offline
}

function imgUrl($path, $base) {
    if (!$path) return '';
    if (str_starts_with($path, 'http')) return $path;
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}
?><!doctype html>
<html lang="ca">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>VoraStudio | Projectes Creatius</title>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-5D9ZE8SPNG"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-5D9ZE8SPNG');
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="../css/style.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/TextPlugin.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.2/dist/SplitText.min.js"></script>
    <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js?render=6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></script>
  </head>
  <body class="page-projectes page-projectes-gallery">

    <!-- ═══════════ HEADER ═══════════ -->
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

    <!-- ═══════════ MAIN: GALERIA HORITZONTAL ═══════════ -->
    <main class="main-projectes">
      <section class="projectes-scroll" id="projectes-scroll">
        <button class="carousel-arrow carousel-arrow--left" aria-label="Anterior">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <div class="projectes-scroll__track" id="projectes-track">
          <?php if ($allProjects): ?>
            <?php foreach ($allProjects as $p):
              $title = $p['titol'] ?? 'Projecte';
              $slug  = $p['slug_del_projecte'] ?? $p['project_slug'] ?? '';
              $packRaw = $p['packs'] ?? $p['pack_type'] ?? 'Essencial';
              $imgSrc = '';
              if (!empty($p['imatge_principal'][0]['url'])) $imgSrc = imgUrl($p['imatge_principal'][0]['url'], $cmsUrl);
              if (!$imgSrc && !empty($p['main_image'])) $imgSrc = is_string($p['main_image']) ? imgUrl($p['main_image'], $cmsUrl) : imgUrl($p['main_image']['url'] ?? '', $cmsUrl);
              if (!$imgSrc && !empty($p['galeria'][0]['url'])) $imgSrc = imgUrl($p['galeria'][0]['url'], $cmsUrl);
            ?>
            <div class="projecte-card-wrap">
              <div class="projecte-card__info">
                <h3 class="projecte-card__title"><?= htmlspecialchars($title) ?></h3>
                <p class="projecte-card__subtitle"><?= htmlspecialchars($packRaw) ?></p>
              </div>
              <a href="../projectes/projecte.php?project=<?= urlencode($slug) ?>" class="projecte-card" data-project="<?= htmlspecialchars($slug) ?>">
                <?php if ($imgSrc): ?>
                  <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($title) ?>" loading="lazy" />
                <?php endif; ?>
              </a>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="carousel-spinner" id="carousel-spinner">
              <img src="../img/icone nou.png" alt="" class="carousel-spinner__logo" />
              <p class="carousel-spinner__text">No s'han pogut carregar els projectes</p>
            </div>
          <?php endif; ?>
        </div>
        <button class="carousel-arrow carousel-arrow--right" aria-label="Següent">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
        </button>
        <h2 class="projectes-scroll__title">Projectes</h2>
      </section>

      <footer class="gallery-footer">
        <p>&copy; 2026 VoraStudio | Creativitat sense l&iacute;mits.</p>
      </footer>
    </main>

    <script src="../js/script.js"></script>
    <script src="../js/projectes-scroll.js"></script>
    <script>
    if (typeof Lenis !== 'undefined') {
      var lenis = new Lenis();
      lenis.on('scroll', ScrollTrigger.update);
      gsap.ticker.add(function (time) { lenis.raf(time * 1000); });
      gsap.ticker.lagSmoothing(0);
    }
    </script>
  </body>
</html>
