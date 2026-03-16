<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/lang.php';
require_once __DIR__ . '/includes/weather.php';

require_login();

$uid = current_user_id();

//stats
$active_crops = fetch_one($conn,
    "SELECT COUNT(*) AS cnt FROM plantings
     WHERE user_id = $uid AND status = 'active'")['cnt'] ?? 0;

$pending_tasks = fetch_one($conn,
    "SELECT COUNT(*) AS cnt FROM tasks t
     JOIN plantings p ON p.id = t.planting_id
     WHERE p.user_id = $uid AND t.status = 'pending'")['cnt'] ?? 0;

$total_harvest = fetch_one($conn,
    "SELECT COALESCE(SUM(h.quantity_kg),0) AS total FROM harvests h
     JOIN plantings p ON p.id = h.planting_id
     WHERE p.user_id = $uid")['total'] ?? 0;

$total_fields = fetch_one($conn,
    "SELECT COUNT(*) AS cnt FROM fields WHERE user_id = $uid")['cnt'] ?? 0;


$upcoming_tasks = fetch_all($conn,
    "SELECT t.*, p.variety_name, c.name AS crop_name, f.name AS field_name
     FROM tasks t
     JOIN plantings p ON p.id = t.planting_id
     JOIN crops c ON c.id = p.crop_id
     JOIN fields f ON f.id = p.field_id
     WHERE p.user_id = $uid
       AND t.status = 'pending'
       AND t.due_date <= DATE_ADD(CURDATE(), INTERVAL 14 DAY)
     ORDER BY t.due_date ASC
     LIMIT 10");

// Weather
$forecast      = false;
$weather_index = [];
$first_field   = fetch_one($conn,
    "SELECT latitude, longitude FROM fields
     WHERE user_id = $uid AND latitude IS NOT NULL LIMIT 1");

if ($first_field && $first_field['latitude']) {
    $forecast      = fetch_weather($first_field['latitude'], $first_field['longitude']);
    $weather_index = weather_by_date($forecast);
}

// Build rain-day lookup
$rain_dates = [];
if ($weather_index) {
    foreach ($weather_index as $date => $code) {
        if (is_rain_code($code)) $rain_dates[$date] = true;
    }
}

// Page render 
$page_title = __('dashboard');
ob_start();
?>


<link rel="stylesheet" href="style.css">
<link rel="shortcut icon" href="icon.png" type="image/x-icon">
<!-- Page Header -->
<div class="am-page-header">
    <h1 class="am-page-title">
        <?= __('welcome') ?>, <?= htmlspecialchars(current_user_name()) ?> 👋
        <small><?= date('l, d F Y') ?></small>
    </h1>
    <a href="plantings/add.php" class="btn-em">
        <i class="bi bi-plus-lg"></i> <?= __('add_planting') ?>
    </a>
</div>

<!-- Stats Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="am-stat">
            <div class="am-stat-icon green"><i class="bi bi-tree"></i></div>
            <div>
                <div class="am-stat-value"><?= $active_crops ?></div>
                <div class="am-stat-label"><?= __('active_crops') ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="am-stat">
            <div class="am-stat-icon amber"><i class="bi bi-check2-square"></i></div>
            <div>
                <div class="am-stat-value"><?= $pending_tasks ?></div>
                <div class="am-stat-label"><?= __('pending_tasks') ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="am-stat">
            <div class="am-stat-icon blue"><i class="bi bi-basket3"></i></div>
            <div>
                <div class="am-stat-value"><?= number_format($total_harvest, 0) ?></div>
                <div class="am-stat-label"><?= __('total_harvest') ?> (<?= __('kg') ?>)</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="am-stat">
            <div class="am-stat-icon purple"><i class="bi bi-geo-alt"></i></div>
            <div>
                <div class="am-stat-value"><?= $total_fields ?></div>
                <div class="am-stat-label"><?= __('fields') ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Weather Forecast -->
    <div class="col-12 col-xl-7">
        <div class="am-card">
            <div class="am-card-header">
                <h2 class="am-card-title">
                    <i class="bi bi-cloud-sun me-2 text-em"></i><?= __('weather_forecast') ?>
                </h2>
                <?php if ($first_field && $first_field['latitude']): ?>
                <small class="text-muted">
                    <?= number_format($first_field['latitude'], 4) ?>, <?= number_format($first_field['longitude'], 4) ?>
                </small>
                <?php endif; ?>
            </div>
            <div class="am-card-body">
                <?php if ($forecast): ?>
                <div class="am-weather-grid">
                    <?php foreach ($forecast as $day):
                        $wl    = weather_label($day['code']);
                        $isRain = is_rain_code($day['code']);
                        $dt    = new DateTime($day['date']);
                    ?>
                    <div class="am-weather-day <?= $isRain ? 'rain-day' : '' ?>">
                        <div class="weather-date"><?= $dt->format('D') ?><br><?= $dt->format('d M') ?></div>
                        <div class="weather-icon">
                            <i class="bi <?= $wl[1] ?> <?= $wl[2] ?>"></i>
                            <?php if ($isRain): ?>
                            <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size:.8rem"
                               title="<?= __('rain_warning') ?>"></i>
                            <?php endif; ?>
                        </div>
                        <div class="weather-temp">
                            <?= round($day['temp_max']) ?>° / <?= round($day['temp_min']) ?>°
                        </div>
                        <div class="weather-label"><?= $wl[0] ?></div>
                        <?php if ($day['precipitation'] > 0): ?>
                        <div class="weather-label text-primary">
                            <i class="bi bi-droplet-fill"></i> <?= $day['precipitation'] ?> mm
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-cloud-slash fs-2 d-block mb-2"></i>
                    <?= __('weather_unavailable') ?>
                    <div class="mt-2">
                        <a href="fields/add.php" class="btn-em-outline btn-sm">
                            <i class="bi bi-plus me-1"></i><?= __('add_field') ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Upcoming Tasks -->
    <div class="col-12 col-xl-5">
        <div class="am-card h-100">
            <div class="am-card-header">
                <h2 class="am-card-title">
                    <i class="bi bi-calendar3 me-2 text-em"></i><?= __('recent_tasks') ?>
                </h2>
                <a href="tasks/" class="btn-em-outline" style="padding:.3rem .75rem;font-size:.78rem">
                    <?= __('view_all') ?>
                </a>
            </div>
            <div class="am-card-body p-0">
                <?php if (!$upcoming_tasks): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-check-circle fs-2 d-block mb-2 text-em"></i>
                    <?= __('no_tasks') ?>
                </div>
                <?php else: ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($upcoming_tasks as $task):
                        $taskDate     = $task['due_date'];
                        $hasRainAlert = isset($rain_dates[$taskDate]);
                        $isOverdue    = ($taskDate < date('Y-m-d'));
                        $dt           = new DateTime($taskDate);
                    ?>
                    <li class="list-group-item border-0 border-bottom py-3 px-4">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate" style="font-size:.875rem">
                                    <?= htmlspecialchars($task['title']) ?>
                                </div>
                                <div class="text-muted mt-1" style="font-size:.78rem">
                                    <i class="bi bi-tree me-1"></i><?= htmlspecialchars($task['crop_name']) ?>
                                    &middot; <?= htmlspecialchars($task['field_name']) ?>
                                </div>
                                <?php if ($hasRainAlert): ?>
                                <div class="am-alert-rain mt-2" style="padding:.4rem .6rem;font-size:.75rem">
                                    <i class="bi bi-cloud-rain-fill text-primary me-1"></i>
                                    <?= __('rain_warning_msg') ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="text-end flex-shrink-0">
                                <div class="am-badge <?= $isOverdue ? 'am-badge-red' : 'am-badge-amber' ?> mb-1">
                                    <?= $dt->format('d M') ?>
                                </div>
                                <div>
                                    <span class="am-badge am-badge-gray" style="font-size:.68rem">
                                        <?= __($task['task_type']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/includes/layout.php';
