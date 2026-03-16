<?php

$__nav_items = [
    ['dashboard', 'bi-speedometer2',   'dashboard.php',   'Dashboard'],
    ['fields',    'bi-geo-alt',        'fields/',         'Fields'],
    ['plantings', 'bi-tree',           'plantings/',      'Plantings'],
    ['tasks',     'bi-check2-square',  'tasks/',          'Tasks'],
    ['harvests',  'bi-basket3',        'harvests/',       'Harvests'],
    ['rotation',  'bi-arrow-repeat',   'rotation/',       'Rotation'],
    ['reports',   'bi-bar-chart-line', 'reports/',        'Reports'],
];
$__cur_script = basename($_SERVER['SCRIPT_FILENAME']);
$__cur_dir    = basename(dirname($_SERVER['SCRIPT_FILENAME']));
?>
<nav class="am-nav flex-column mt-2">
    <?php foreach ($__nav_items as [$key, $icon, $path, $label]): ?>
        <?php
        $is_active = ($__cur_script === ltrim($path, '/'))
                  || ($__cur_dir === rtrim($key, '/') && $__cur_dir !== 'agriman');
        ?>
        <a href="<?= base_url($path) ?>"
           class="am-nav-link <?= $is_active ? 'active' : '' ?>">
            <i class="bi <?= $icon ?> me-2"></i>
            <?= __($key) ?>
        </a>
    <?php endforeach; ?>
</nav>

<div class="am-sidebar-footer">
    <!-- Language toggle -->
    <?php $__toggle_lang = (current_lang() === 'en') ? 'mr' : 'en'; ?>
    <a href="<?= base_url('set-lang.php?lang=' . $__toggle_lang . '&ref=' . urlencode($_SERVER['REQUEST_URI'])) ?>"
       class="am-nav-link am-lang-link">
        <i class="bi bi-translate me-2"></i><?= __('toggle_language') ?>
    </a>
    <!-- Logout -->
    <a href="<?= base_url('logout.php') ?>" class="am-nav-link am-logout-link">
        <i class="bi bi-box-arrow-right me-2"></i><?= __('logout') ?>
    </a>
    <div class="am-user-chip">
        <i class="bi bi-person-circle me-1"></i>
        <?= htmlspecialchars(current_user_name()) ?>
    </div>
</div>
