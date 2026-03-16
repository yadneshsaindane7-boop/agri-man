<?php
// includes/layout.php — HTML wrapper with sidebar, topbar, and footer
// Usage:
//   ob_start();
//   // ... page content ...
//   $content = ob_get_clean();
//   $page_title = 'Dashboard';
//   include __DIR__ . '/layout.php';

$__lang      = current_lang();
$__user_name = current_user_name();
$__page      = $page_title ?? 'AgriMan';

// Navigation items: [key, icon, path]
$__nav = [
    ['dashboard', 'bi-speedometer2',   '../dashboard.php'],
    ['fields',    'bi-geo-alt',        '../fields/'],
    ['plantings', 'bi-tree',           '../plantings/'],
    ['tasks',     'bi-check2-square',  '../tasks/'],
    ['harvests',  'bi-basket3',        '../harvests/'],
    ['rotation',  'bi-arrow-repeat',   '../rotation/'],
    ['reports',   'bi-bar-chart-line', '../reports/'],
];

// Determine active nav
$__current = strtolower($__page);
?>
<!DOCTYPE html>
<html lang="<?= $__lang ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($__page) ?> — <?= __('app_name') ?></title>

    <!-- Google Fonts: DM Sans (body) + Fraunces (logo) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <?php
    // Reliable relative path to assets/ from any page depth (Windows + Linux safe)
    $__script_dir   = str_replace('\\', '/', dirname($_SERVER['SCRIPT_FILENAME']));
    $__project_root = str_replace('\\', '/', dirname(__DIR__)); // agriman/includes -> agriman
    $__depth        = ($__script_dir === $__project_root) ? 0
                    : substr_count(str_replace($__project_root, '', $__script_dir), '/');
    $__prefix       = str_repeat('../', $__depth);
    ?>
    <link href="<?= $__prefix ?>assets/style.css" rel="stylesheet">
    <link rel="shortcut icon" href="icon.png" type="image/x-icon">
</head>
<body>

<!-- ══════════════ MOBILE TOP BAR ══════════════ -->
<nav class="am-topbar d-lg-none">
    <button class="am-menu-btn" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#mobileNav" aria-controls="mobileNav">
        <i class="bi bi-list fs-4"></i>
    </button>
    <span class="am-topbar-brand"><?= __('app_name') ?></span>
    <a href="<?= base_url('set-lang.php?lang=' . ($__lang === 'en' ? 'mr' : 'en') . '&ref=' . urlencode($_SERVER['REQUEST_URI'])) ?>"
       class="am-lang-pill">
        <?= __('toggle_language') ?>
    </a>
</nav>

<!-- ══════════════ MOBILE OFF-CANVAS ══════════════ -->
<div class="offcanvas offcanvas-start am-offcanvas" tabindex="-1" id="mobileNav">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title am-brand"><?= __('app_name') ?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body p-0">
        <?php include __DIR__ . '/sidebar_nav.php'; ?>
    </div>
</div>

<!-- ══════════════ DESKTOP SIDEBAR ══════════════ -->
<aside class="am-sidebar d-none d-lg-flex flex-column">
    <div class="am-sidebar-brand">
        <i class="bi bi-leaf-fill me-2"></i><?= __('app_name') ?>
    </div>
    <?php include __DIR__ . '/sidebar_nav.php'; ?>
</aside>

<!-- ══════════════ MAIN CONTENT ══════════════ -->
<main class="am-main">
    <div class="am-main-inner">
        <?= $content ?? '' ?>
    </div>
    <footer class="am-footer">
        &copy; <?= date('Y') ?> <?= __('app_name') ?> &mdash;
        <small class="text-muted">Agricultural Management System</small>
    </footer>
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-dismiss alerts after 4 s
document.querySelectorAll('.alert[data-auto-dismiss]').forEach(el => {
    setTimeout(() => {
        let bsAlert = bootstrap.Alert.getOrCreateInstance(el);
        bsAlert.close();
    }, 4000);
});
// Confirm delete links
document.querySelectorAll('a[data-confirm]').forEach(a => {
    a.addEventListener('click', e => {
        if (!confirm(a.dataset.confirm)) e.preventDefault();
    });
});
</script>
</body>
</html>
