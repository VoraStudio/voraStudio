<?php
/* ─── CSRF per al formulari ─── */
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../includes/CmsClient.php';

/* ─── Configuració CMS ─── */
$cmsUrl = getenv('CMS_URL') ?: 'https://voracms.voradata.cat';
$origin = getenv('SSR_ORIGIN') ?: 'https://vorastudio.cat';
$cms = new CmsClient($cmsUrl, $origin);

/* ─── Slug del projecte ─── */
$projectSlug = $_GET['project'] ?? '';
if (!$projectSlug) {
    header('Location: /html/projectes.php');
    exit;
}
$projectSlug = htmlspecialchars($projectSlug);

/* ─── SSR: carregar projectes des del CMS ─── */
$projectData = null;
$allProjects = [];

try {
    $result = $cms->fetch('/api/public/web-principal/vorastudio-projects?locale=ca');
    if ($result && isset($result['data'])) {
        $allProjects = $result['data'];
        foreach ($allProjects as $p) {
            $slug = $p['slug_del_projecte'] ?? $p['project_slug'] ?? '';
            if ($slug === $projectSlug) {
                $projectData = $p;
                break;
            }
        }
    }
} catch (Exception $e) {
    // CMS offline — renderitzar buit, JS farà fallback
}

/* ─── Helper per a URLs d'imatges ─── */
function imgUrl($path, $base) {
    if (!$path) return '';
    if (str_starts_with($path, 'http')) return $path;
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

/* ─── Dades del projecte (o buit) ─── */
$titol    = $projectData['titol'] ?? ucfirst($projectSlug);
$logo     = imgUrl($projectData['logo'][0]['url'] ?? '', $cmsUrl);
$website  = ($projectData['website'] ?? '#') ?: '#';
$websiteLabel = preg_replace('#^https?://#', '', rtrim($website, '/'));
$desc     = $projectData['descripcio'] ?? '';
$tagsRaw  = $projectData['tags'] ?? '';
$tags     = $tagsRaw ? array_map('trim', explode(',', $tagsRaw)) : [];
$repte    = $projectData['repte'] ?? '';
$estrategia = $projectData['estrategia'] ?? '';
$resultat = $projectData['resultat'] ?? '';
$galeria  = $projectData['galeria'] ?? [];

$hasStrategy = $repte || $estrategia || $resultat;
?>
<!doctype html>
<html lang="ca">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>VoraStudio | <?= $titol ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../css/style.css" />

    <!-- GSAP Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/TextPlugin.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.12.2/dist/SplitText.min.js"></script>
    <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>

    <script src="https://www.google.com/recaptcha/api.js?render=6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI"></script>
  </head>
  <body class="page-projecte" data-project="<?= $projectSlug ?>">

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
            <a href="../html/serveis.php">Serveis</a>
            <ul class="dropdown-menu">
              <li><a href="../html/servei.php?id=59">Estratègia i Branding</a></li>
              <li><a href="../html/servei.php?id=60">Projectes Web</a></li>
              <li><a href="../html/servei.php?id=61">Social Media</a></li>
              <li><a href="../html/serveis.php">Disseny Gràfic</a></li>
              <li><a href="../html/serveis.php">Màrqueting Digital</a></li>
              <li><a href="https://voradata.cat/" target="_blank" rel="noopener noreferrer">voraData</a></li>
            </ul>
          </li>
          <li class="has-dropdown">
            <a href="../html/projectes.php">Projectes</a>
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
          <li><a href="../html/serveis.php">Serveis</a></li>
          <li><a href="../html/projectes.php">Projectes</a></li>
          <li><a href="../index.php#pricing">Packs</a></li>
          <li><a href="../index.php#contact">Contacte</a></li>
        </ul>
      </div>
    </div>

    <main class="main-projecte">

      <!-- ═══════════ HERO (SSR) ═══════════ -->
      <section id="project-hero" class="project-hero">
        <div class="project-hero__container">
          <div class="project-hero__left">
            <?php if ($logo): ?>
              <img src="<?= $logo ?>" alt="" />
            <?php endif; ?>
            <?php if ($website !== '#'): ?>
              <a href="<?= $website ?>" target="_blank" class="project-hero__link">WEBSITE: <span><?= $websiteLabel ?></span></a>
            <?php endif; ?>
          </div>
          <div class="project-hero__right">
            <p class="project-hero__description"><?= htmlspecialchars($desc) ?></p>
            <?php if ($tags): ?>
              <div class="project-hero__tags">
                <?php foreach ($tags as $tag): ?>
                  <span class="project-hero__tag"><?= htmlspecialchars(trim($tag)) ?></span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </section>

      <!-- ═══════════ ESTRATÈGIA (SSR) ═══════════ -->
      <section id="project-strategy" class="project-strategy"<?= $hasStrategy ? '' : ' style="display:none"' ?>>
        <div class="project-strategy__container">
          <?php if ($repte): ?>
          <div class="project-strategy__block">
            <div class="project-strategy__header">
              <span class="project-strategy__dot"></span>
              <h2 class="project-strategy__label">EL REPTE</h2>
            </div>
            <p class="project-strategy__text"><?= htmlspecialchars($repte) ?></p>
          </div>
          <?php endif; ?>
          <?php if ($estrategia): ?>
          <div class="project-strategy__block">
            <div class="project-strategy__header">
              <span class="project-strategy__dot"></span>
              <h2 class="project-strategy__label">L'ESTRATÈGIA</h2>
            </div>
            <p class="project-strategy__text"><?= htmlspecialchars($estrategia) ?></p>
          </div>
          <?php endif; ?>
          <?php if ($resultat): ?>
          <div class="project-strategy__block">
            <div class="project-strategy__header">
              <span class="project-strategy__dot"></span>
              <h2 class="project-strategy__label">EL RESULTAT</h2>
            </div>
            <p class="project-strategy__text"><?= htmlspecialchars($resultat) ?></p>
          </div>
          <?php endif; ?>
        </div>
      </section>

      <!-- ═══════════ GALERIA (SSR) ═══════════ -->
      <section id="project-gallery" class="project-gallery"<?= $galeria ? '' : ' style="display:none"' ?>>
        <div class="project-gallery__grid">
          <?php foreach ($galeria as $img): $url = imgUrl($img['url'] ?? '', $cmsUrl); if ($url): ?>
            <div class="project-gallery__item">
              <img src="<?= $url ?>" alt="" class="project-gallery__img" loading="lazy" />
            </div>
          <?php endif; endforeach; ?>
        </div>
      </section>

      <!-- ═══════════ ALTRES PROJECTES (SSR) ═══════════ -->
      <section class="projectes-scroll" id="projectes-scroll">
        <h2 class="projectes-scroll__heading">Altres projectes:</h2>
        <button class="carousel-arrow carousel-arrow--left" aria-label="Anterior">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <div class="projectes-scroll__track" id="projectes-track">
          <?php if ($allProjects): ?>
            <?php foreach ($allProjects as $p):
              $slug = $p['slug_del_projecte'] ?? $p['project_slug'] ?? '';
              if ($slug === $projectSlug) continue; /* ometre el projecte actual */
              $title = $p['titol'] ?? 'Projecte';
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
              <a href="projecte.php?project=<?= urlencode($slug) ?>" class="projecte-card" data-project="<?= htmlspecialchars($slug) ?>">
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
      </section>

      <div id="toast-container" class="toast-hidden"></div>
    </main>

    <!-- ═══════════ CONTACTE ═══════════ -->
    <section id="contact" class="contact-section">
        <div class="contact__container">
          <div class="contact__info">
            <h2 class="contact__info-title">Fem un cafe?</h2>
            <p class="contact__info-subtitle">Estas a la vora d'alguna cosa gran!</p>
            <a href="mailto:hola@vorastudio.cat" class="contact__info-email">hola@vorastudio.cat</a>
            <div class="contact__socials">
              <a href="https://www.linkedin.com/company/vorastudio" class="social-icon" aria-label="LinkedIn" target="_blank">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z" /><rect x="2" y="9" width="4" height="12" /><circle cx="4" cy="4" r="2" /></svg>
              </a>
              <a href="https://wa.me/722812139" class="social-icon" aria-label="WhatsApp" target="_blank">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.396.015 12.03a11.782 11.782 0 001.592 5.955L0 24l6.111-1.605a11.765 11.765 0 005.935 1.636h.005c6.634 0 12.032-5.396 12.035-12.03a11.81 11.81 0 00-3.486-8.484z" /></svg>
              </a>
              <a href="https://www.instagram.com/vorastudio_/" class="social-icon" aria-label="Instagram" target="_blank">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5" /><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" /><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" /></svg>
              </a>
            </div>
          </div>
          <div class="contact__form-wrapper">
            <form class="modern-form" id="contact-form-element" action="../contacte.php" method="POST">
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
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7" /></svg>
                </div>
              </button>
            </form>
          </div>
        </div>
      </section>

    <footer id="main-footer" class="footer-section">
      <div class="content-wrapper">
        <p>© 2026 VoraStudio | Creativitat sense límits.</p>
      </div>
    </footer>

    <a href="https://wa.me/722812139" class="whatsapp-float" target="_blank" aria-label="Contacta'ns per WhatsApp">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.396.015 12.03a11.782 11.782 0 001.592 5.955L0 24l6.111-1.605a11.765 11.765 0 005.935 1.636h.005c6.634 0 12.032-5.396 12.035-12.03a11.81 11.81 0 00-3.486-8.484z" />
      </svg>
    </a>

    <script src="../js/script.js"></script>
    <script src="../js/dynamic-content.js"></script>
    <script src="../js/projectes-scroll.js"></script>
  </body>
</html>
