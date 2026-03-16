<?php
// reports/index.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/lang.php';

require_login();
$uid = current_user_id();

// Harvest by crop
$harvest_by_crop = fetch_all($conn,
    "SELECT c.name AS crop_name, c.name_mr, SUM(h.quantity_kg) AS total_kg, COUNT(h.id) AS harvest_count
     FROM harvests h
     JOIN plantings p ON p.id = h.planting_id
     JOIN crops c     ON c.id = p.crop_id
     WHERE p.user_id = $uid
     GROUP BY c.id ORDER BY total_kg DESC");

// Tasks summary
$tasks_summary = fetch_all($conn,
    "SELECT t.task_type, t.status, COUNT(*) AS cnt
     FROM tasks t JOIN plantings p ON p.id=t.planting_id
     WHERE p.user_id = $uid
     GROUP BY t.task_type, t.status ORDER BY t.task_type");

// Monthly harvest trend (last 12 months)
$monthly = fetch_all($conn,
    "SELECT DATE_FORMAT(h.harvest_date,'%Y-%m') AS month, SUM(h.quantity_kg) AS total
     FROM harvests h JOIN plantings p ON p.id=h.planting_id
     WHERE p.user_id=$uid AND h.harvest_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
     GROUP BY month ORDER BY month ASC");

// Plantings count by status
$planting_stats = fetch_one($conn,
    "SELECT SUM(status='active') AS active_count, SUM(status='completed') AS completed_count
     FROM plantings WHERE user_id=$uid");

$page_title = __('reports');
ob_start();
?>
<link rel="stylesheet" href="style.css">
<link rel="shortcut icon" href="icon.png" type="image/x-icon">
<div class="am-page-header">
    <h1 class="am-page-title"><i class="bi bi-bar-chart-line me-2 text-em"></i><?= __('reports_title') ?></h1>
</div>

<!-- Quick Summary -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="am-stat">
            <div class="am-stat-icon green"><i class="bi bi-tree"></i></div>
            <div>
                <div class="am-stat-value"><?= $planting_stats['active_count'] ?? 0 ?></div>
                <div class="am-stat-label"><?= __('active') ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="am-stat">
            <div class="am-stat-icon blue"><i class="bi bi-check-all"></i></div>
            <div>
                <div class="am-stat-value"><?= $planting_stats['completed_count'] ?? 0 ?></div>
                <div class="am-stat-label"><?= __('completed') ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="am-stat">
            <div class="am-stat-icon amber"><i class="bi bi-basket3"></i></div>
            <div>
                <div class="am-stat-value"><?= count($harvest_by_crop) ?></div>
                <div class="am-stat-label">Crop types harvested</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="am-stat">
            <div class="am-stat-icon purple"><i class="bi bi-calendar3"></i></div>
            <div>
                <div class="am-stat-value"><?= count($monthly) ?></div>
                <div class="am-stat-label">Active months</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Harvest by Crop -->
    <div class="col-12 col-lg-6">
        <div class="am-card">
            <div class="am-card-header">
                <h2 class="am-card-title"><i class="bi bi-pie-chart me-2 text-em"></i><?= __('harvest_by_crop') ?></h2>
            </div>
            <?php if (!$harvest_by_crop): ?>
            <div class="am-card-body text-muted text-center py-4"><?= __('no_harvests') ?></div>
            <?php else:
                $max_kg = max(array_column($harvest_by_crop, 'total_kg'));
            ?>
            <div class="am-card-body">
                <?php foreach ($harvest_by_crop as $hc):
                    $lang = current_lang();
                    $cn   = ($lang === 'mr' && $hc['name_mr']) ? $hc['name_mr'] : $hc['crop_name'];
                    $pct  = $max_kg > 0 ? round(($hc['total_kg'] / $max_kg) * 100) : 0;
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1" style="font-size:.875rem">
                        <span class="fw-semibold"><?= htmlspecialchars($cn) ?></span>
                        <span class="text-muted"><?= number_format($hc['total_kg'], 0) ?> kg &middot; <?= $hc['harvest_count'] ?> harvest<?= $hc['harvest_count'] > 1 ? 's' : '' ?></span>
                    </div>
                    <div class="progress" style="height:8px;border-radius:999px;background:#e2e8f0">
                        <div class="progress-bar" style="width:<?= $pct ?>%;background:var(--em-green);border-radius:999px"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Monthly Trend -->
    <div class="col-12 col-lg-6">
        <div class="am-card">
            <div class="am-card-header">
                <h2 class="am-card-title"><i class="bi bi-graph-up me-2 text-em"></i>Monthly Harvest Trend</h2>
            </div>
            <?php if (!$monthly): ?>
            <div class="am-card-body text-muted text-center py-4"><?= __('no_harvests') ?></div>
            <?php else:
                $max_m = max(array_column($monthly, 'total'));
            ?>
            <div class="am-card-body">
                <div style="display:flex;align-items:flex-end;gap:6px;height:120px">
                    <?php foreach ($monthly as $m):
                        $h = $max_m > 0 ? round(($m['total'] / $max_m) * 100) : 0;
                        $label = date('M y', strtotime($m['month'] . '-01'));
                    ?>
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px">
                        <div style="font-size:.68rem;color:var(--text-muted)"><?= number_format($m['total'], 0) ?></div>
                        <div style="width:100%;background:var(--em-green);border-radius:4px 4px 0 0;height:<?= $h ?>%;min-height:4px;opacity:.85;transition:opacity .2s" title="<?= $label ?>: <?= $m['total'] ?> kg"></div>
                        <div style="font-size:.65rem;color:var(--text-muted);transform:rotate(-30deg);white-space:nowrap"><?= $label ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tasks Summary -->
    <div class="col-12">
        <div class="am-card">
            <div class="am-card-header">
                <h2 class="am-card-title"><i class="bi bi-list-check me-2 text-em"></i><?= __('tasks_summary') ?></h2>
            </div>
            <?php if (!$tasks_summary): ?>
            <div class="am-card-body text-muted text-center py-4"><?= __('no_tasks_list') ?></div>
            <?php else: ?>
            <div class="am-table-wrap">
                <table class="am-table">
                    <thead><tr>
                        <th><?= __('task_type') ?></th>
                        <th><?= __('pending') ?></th>
                        <th><?= __('completed') ?></th>
                        <th>Total</th>
                    </tr></thead>
                    <tbody>
                        <?php
                        // Group by task_type
                        $by_type = [];
                        foreach ($tasks_summary as $ts) {
                            $by_type[$ts['task_type']][$ts['status']] = $ts['cnt'];
                        }
                        foreach ($by_type as $type => $statuses):
                            $pending   = $statuses['pending']   ?? 0;
                            $completed = $statuses['completed'] ?? 0;
                        ?>
                        <tr>
                            <td><?= __($type) ?></td>
                            <td><span class="am-badge am-badge-amber"><?= $pending ?></span></td>
                            <td><span class="am-badge am-badge-green"><?= $completed ?></span></td>
                            <td class="fw-semibold"><?= $pending + $completed ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $content = ob_get_clean(); require __DIR__ . '/../includes/layout.php';
