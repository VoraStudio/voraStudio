<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once __DIR__ . '/../includes/CmsClient.php';

$cmsUrl = getenv('CMS_URL') ?: 'https://voracms.voradata.cat';
$origin = getenv('SSR_ORIGIN') ?: 'https://vorastudio.cat';
$cms = new CmsClient($cmsUrl, $origin);

$allProjects = [];

try {
    $result = $cms->fetch('/api/public/web-principal/vorastudio-projects?locale=ca');
    if ($result && isset($result['data'])) {
        $allProjects = $result['data'];
        usort($allProjects, function ($a, $b) {
            return ($a['ordre'] ?? 999) - ($b['ordre'] ?? 999);
        });
    }
} catch (Exception $e) {
    // CMS offline
}

/* ─── Carregar servei individual ─── */
$serveiId = (int) ($_GET['id'] ?? 0);
$serveiData = null;

if ($serveiId > 0) {
    try {
        $result = $cms->fetch("/api/public/web-principal/serveis_vorastudio/{$serveiId}?locale=ca");
        if ($result && isset($result['data'])) {
            $serveiData = $result['data'];
        }
    } catch (Exception $e) {}
}

if (!$serveiData) {
    http_response_code(404);
    echo '<h1>Servei no trobat</h1><p><a href="serveis.php">Tornar als serveis</a></p>';
    exit;
}

function imgUrl($path, $base) {
    if (!$path) return '';
    if (str_starts_with($path, 'http')) return $path;
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function serveiImg($imgArr, $base) {
    if (!$imgArr || !isset($imgArr[0]['url'])) return '';
    $url = $imgArr[0]['url'];
    return str_starts_with($url, 'http') ? $url : rtrim($base, '/') . '/' . ltrim($url, '/');
}

function serveiGallery($imgArr, $base) {
    $urls = [];
    if (!$imgArr) return $urls;
    foreach ($imgArr as $item) {
        $url = $item['url'] ?? '';
        if ($url) {
            $urls[] = str_starts_with($url, 'http') ? $url : rtrim($base, '/') . '/' . ltrim($url, '/');
        }
    }
    return $urls;
}

function parseRepeaterText($items) {
    if (!$items || !is_array($items)) return [];
    return array_map(function($item) {
        return $item['texto'] ?? $item['valor'] ?? '';
    }, $items);
}

function parseRepeaterPasos($items) {
    if (!$items || !is_array($items)) return [];
    return array_map(function($item) {
        return [
            'numero' => $item['año'] ?? $item['numero'] ?? '',
            'text' => $item['texto'] ?? '',
        ];
    }, $items);
}

/* ─── Assignar variables ─── */
$titol = htmlspecialchars($serveiData['titolservei'] ?? $serveiData['titolcard'] ?? '');
$breadcrumb_titol = htmlspecialchars($serveiData['titolcard'] ?? $titol);
$descRaw = str_replace("\r", '', $serveiData['descipcioservei'] ?? '');
$descCurta = nl2br(htmlspecialchars($descRaw));
$imatges = serveiGallery($serveiData['imatgesservei'] ?? null, $cmsUrl);

$queFemTitol = htmlspecialchars($serveiData['quefemtitol'] ?? 'Què fem?');
$queFemLlista = parseRepeaterText($serveiData['quefemllista'] ?? []);

$procesTitol = htmlspecialchars($serveiData['titolproces'] ?? 'El nostre procés');
$procesPasos = parseRepeaterPasos($serveiData['pasosproces'] ?? []);
$procesImg = serveiImg($serveiData['imatgesservei'] ?? null, $cmsUrl);

$pertuTitol = htmlspecialchars($serveiData['pertutitol'] ?? 'Aquest servei és per a tu si...');
$pertuDesc = $serveiData['pertudescipcio'] ?? '';
$pertuLlista = parseRepeaterText($serveiData['pertullista'] ?? []);

/* ─── Tracking de visita ─── */
if ($serveiId > 0) {
    try {
        $ch = curl_init(rtrim($cmsUrl, '/') . '/api/visit');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(['entry_id' => $serveiId]),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT_MS => 500,
            CURLOPT_CONNECTTIMEOUT_MS => 500,
        ]);
        curl_exec($ch);
        curl_close($ch);
    } catch (Exception $e) {
        // Silenciós
    }
}
?>
<!doctype html>
<html lang="ca">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>VoraStudio | <?= $titol ?></title>

    <!-- Google Fonts: Montserrat, Outfit, Inter, Fira Code -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&family=Outfit:wght@300;400;600;800&family=Inter:wght@300;400;500;600&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet" />
    
    <!-- CSS Principal -->
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/serveis.css" />

    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/TextPlugin.min.js"></script>
    <script src="https://unpkg.com/lenis@1.1.13/dist/lenis.min.js"></script>
  </head>
  <body class="page-projecte">

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
              <li><a href="servei.php?id=59">Estratègia i Branding</a></li>
              <li><a href="servei.php?id=60">Projectes Web</a></li>
              <li><a href="servei.php?id=61">Social Media</a></li>
              <li><a href="serveis.php">Disseny Gràfic</a></li>
              <li><a href="serveis.php">Màrqueting Digital</a></li>
              <li><a href="https://voradata.cat/" target="_blank" rel="noopener noreferrer">voraData</a></li>             
            </ul>
          </li>
          <li class="has-dropdown">
            <a href="projectes.php">Projectes</a>
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
          <li class="has-submenu">
            <a href="javascript:void(0)" class="parent-link">Serveis</a>
            <ul class="submenu">
              <li><a href="servei.php?id=59">Estratègia i Branding</a></li>
              <li><a href="servei.php?id=60">Projectes Web</a></li>
              <li><a href="servei.php?id=61">Social Media</a></li>
              <li><a href="serveis.php">Disseny Gràfic</a></li>
              <li><a href="serveis.php">Màrqueting Digital</a></li>
              <li><a href="https://voradata.cat/" target="_blank">voraData</a></li>            
            </ul>
          </li>
          <li><a href="projectes.php">Projectes</a></li>
          <li><a href="../index.php#pricing">Packs</a></li>
          <li><a href="../index.php#contacte">Contacte</a></li>
        </ul>
      </div>
    </div>

    <!-- ----- MAIN CONTENT ----- -->
    <main class="main-projecte" style="padding-top: 65px; min-height: 100vh;">
      
      <div class="servei-container">
        
        <!-- Breadcrumb -->
        <div class="servei-breadcrumb">
          <a href="serveis.php">Serveis</a> / <span><?= $breadcrumb_titol ?></span>
        </div>

        <!-- Top Section -->
        <div class="servei-hero" id="servei-hero">
          
          <div class="servei-hero__left">
            <div class="title-mask-wrapper">
              <h1 class="servei-title"><?= $titol ?></h1>
            </div>
            
            <?php if ($descCurta): ?>
            <p class="servei-text"><?= $descCurta ?></p>
            <?php endif; ?>
            
            <?php if (count($imatges) >= 2): ?>
            <div class="servei-images">
              <div class="img-reveal-wrapper">
                <img src="<?= $imatges[0] ?>" alt="<?= $breadcrumb_titol ?>" />
              </div>
              <div class="img-reveal-wrapper">
                <img src="<?= $imatges[1] ?>" alt="<?= $breadcrumb_titol ?>" />
              </div>
            </div>
            <?php elseif (count($imatges) === 1): ?>
            <div class="servei-images">
              <div class="img-reveal-wrapper" style="max-width:100%">
                <img src="<?= $imatges[0] ?>" alt="<?= $breadcrumb_titol ?>" />
              </div>
            </div>
            <?php endif; ?>
          </div>

          <div class="servei-hero__right">
            <div class="servei-box-title-wrapper">
              <h2 class="servei-box-title"><?= $queFemTitol ?></h2>
            </div>
            
            <?php if ($queFemLlista): ?>
            <ul class="servei-list">
              <?php foreach ($queFemLlista as $item): ?>
              <li class="lista_Servei"><?= htmlspecialchars($item) ?></li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            
            <a href="#contact" class="servei-btn" id="parlem-btn">Parlem?</a>
          </div>
        </div>

        <!-- El nostre procés -->
        <div class="servei-process" id="servei-process">
          <?php $procesImgIdx = count($imatges) > 1 ? $imatges[1] : ($imatges[0] ?? ''); ?>
          <?php if ($procesImgIdx): ?>
          <div class="servei-process__img">
            <img src="<?= $procesImgIdx ?>" alt="El nostre procés" />
          </div>
          <?php endif; ?>
          <div class="servei-process__content">
            <div class="servei-process-title-wrapper">
              <h2 class="servei-process-title"><?= $procesTitol ?></h2>
            </div>
            
            <?php if ($procesPasos): ?>
            <div class="servei-process-grid">
              <?php foreach ($procesPasos as $pas): ?>
              <div class="servei-process-item">
                <div class="servei-process-number">[<?= htmlspecialchars($pas['numero']) ?>]</div>
                <div class="servei-process-text"><?= htmlspecialchars($pas['text']) ?></div>
              </div>
              <?php endforeach; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Aquest servei és per a tu si... -->
        <div class="servei-target" id="servei-target">
          <div class="servei-target-title-wrapper">
            <h2 class="servei-target-title"><?= $pertuTitol ?></h2>
          </div>
          <?php if ($pertuDesc): ?>
          <p class="servei-text"><?= htmlspecialchars($pertuDesc) ?></p>
          <?php endif; ?>
          <?php if ($pertuLlista): ?>
          <ul class="target-list">
            <?php foreach ($pertuLlista as $item): ?>
            <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
          
          <a href="#contact" class="servei-btn" id="treballem-btn">Treballem junts?</a>
        </div>

      </div>

      <!-- ═══════════ CARRUSEL CMS (idèntic al de serveis.php) ═══════════ -->
      <section class="projectes-scroll" id="projectes-scroll" style="margin-top: 80px;">
        <button class="carousel-arrow carousel-arrow--left" aria-label="Anterior">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <div class="projectes-scroll__track" id="projectes-track">
          <?php if ($allProjects): ?>
            <?php foreach ($allProjects as $p):
              $titlePrj = $p['titol'] ?? 'Projecte';
              $slugPrj  = $p['slug_del_projecte'] ?? $p['project_slug'] ?? '';
              $packRaw = $p['packs'] ?? $p['pack_type'] ?? 'Essencial';
              $imgSrc = '';
              if (!empty($p['imatge_principal'][0]['url'])) $imgSrc = imgUrl($p['imatge_principal'][0]['url'], $cmsUrl);
              if (!$imgSrc && !empty($p['main_image'])) $imgSrc = is_string($p['main_image']) ? imgUrl($p['main_image'], $cmsUrl) : imgUrl($p['main_image']['url'] ?? '', $cmsUrl);
              if (!$imgSrc && !empty($p['galeria'][0]['url'])) $imgSrc = imgUrl($p['galeria'][0]['url'], $cmsUrl);
            ?>
            <div class="projecte-card-wrap">
              <div class="projecte-card__info">
                <h3 class="projecte-card__title"><?= htmlspecialchars($titlePrj) ?></h3>
                <p class="projecte-card__subtitle"><?= htmlspecialchars($packRaw) ?></p>
              </div>
              <a href="../projectes/projecte.php?project=<?= urlencode($slugPrj) ?>" class="projecte-card" data-project="<?= htmlspecialchars($slugPrj) ?>">
                <?php if ($imgSrc): ?>
                  <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($titlePrj) ?>" loading="lazy" />
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

      <!-- Toast Container -->
      <div id="toast-container" class="toast-hidden"></div>

    </main>

    <!-- ----- FOOTER ----- -->
    <footer id="main-footer" class="footer-section">
      <div class="content-wrapper">
        <p>© 2026 VoraStudio | Creativitat sense límits.</p>
      </div>
    </footer>

    <!-- Scripts -->

    <!-- Botó flotant de WhatsApp -->
    <a href="https://wa.me/722812139" class="whatsapp-float" target="_blank" aria-label="Contacta'ns per WhatsApp">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.414 0 .018 5.396.015 12.03a11.782 11.782 0 001.592 5.955L0 24l6.111-1.605a11.765 11.765 0 005.935 1.636h.005c6.634 0 12.032-5.396 12.035-12.03a11.81 11.81 0 00-3.486-8.484z" />
      </svg>
    </a>

    <script src="../js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15/dist/SplitText.min.js"></script>
    <script src="../js/projectes-scroll.js"></script>
    <script src="../js/serveis.js"></script>
  </body>
</html>
