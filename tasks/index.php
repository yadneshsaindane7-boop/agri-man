<?php
// tasks/index.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/lang.php';
require_once __DIR__ . '/../includes/weather.php';

require_login();
$uid = current_user_id();

if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    qry($conn, "DELETE FROM tasks t USING tasks t JOIN plantings p ON p.id=t.planting_id WHERE t.id=$did AND p.user_id=$uid");
    header('Location: index.php?msg=deleted'); exit;
}
if (isset($_GET['complete']) && ctype_digit($_GET['complete'])) {
    $cid = (int)$_GET['complete'];
    qry($conn, "UPDATE tasks t JOIN plantings p ON p.id=t.planting_id SET t.status='completed' WHERE t.id=$cid AND p.user_id=$uid");
    header('Location: index.php?msg=saved'); exit;
}

$msg = $_GET['msg'] ?? '';
$filter_planting = isset($_GET['planting_id']) && ctype_digit($_GET['planting_id']) ? (int)$_GET['planting_id'] : 0;
$where_extra = $filter_planting ? "AND t.planting_id = $filter_planting" : '';

$tasks = fetch_all($conn,
    "SELECT t.*, p.variety_name, c.name AS crop_name, f.name AS field_name
     FROM tasks t
     JOIN plantings p ON p.id = t.planting_id
     JOIN crops c     ON c.id = p.crop_id
     JOIN fields f    ON f.id = p.field_id
     WHERE p.user_id = $uid $where_extra
     ORDER BY t.due_date ASC");

// Weather lookup for rain warnings
$forecast = false;
$first_field = fetch_one($conn, "SELECT latitude,longitude FROM fields WHERE user_id=$uid AND latitude IS NOT NULL LIMIT 1");
if ($first_field && $first_field['latitude']) {
    $forecast = fetch_weather($first_field['latitude'], $first_field['longitude']);
}
$weather_map = weather_by_date($forecast);

$page_title = __('tasks');
ob_start();
?>

<link rel="stylesheet" href="style.css">
<link rel="shortcut icon" href="icon.png" type="image/x-icon">
<div class="am-page-header">
    <h1 class="am-page-title"><i class="bi bi-check2-square me-2 text-em"></i><?= __('tasks_list') ?></h1>
    <a href="add.php<?= $filter_planting ? "?planting_id=$filter_planting" : '' ?>" class="btn-em">
        <i class="bi bi-plus-lg"></i> <?= __('add_task') ?>
    </a>
</div>

<?php if ($msg === 'saved'): ?>
<div class="alert alert-success py-2 px-3 mb-3" data-auto-dismiss style="font-size:.875rem;border-radius:8px"><i class="bi bi-check-circle me-1"></i> <?= __('success_saved') ?></div>
<?php elseif ($msg === 'deleted'): ?>
<div class="alert alert-warning py-2 px-3 mb-3" data-auto-dismiss style="font-size:.875rem;border-radius:8px"><i class="bi bi-trash me-1"></i> <?= __('success_deleted') ?></div>
<?php endif; ?>

<div class="am-card">
    <?php if (!$tasks): ?>
    <div class="am-card-body text-center py-5 text-muted">
        <i class="bi bi-check2-all fs-2 d-block mb-2"></i> <?= __('no_tasks_list') ?>
        <div class="mt-3"><a href="add.php" class="btn-em"><?= __('add_task') ?></a></div>
    </div>
    <?php else: ?>
    <div class="am-table-wrap">
        <table class="am-table">
            <thead>
                <tr>
                    <th><?= __('task_title') ?></th>
                    <th><?= __('crop') ?> / <?= __('field') ?></th>
                    <th><?= __('task_type') ?></th>
                    <th><?= __('due_date') ?></th>
                    <th><?= __('status') ?></th>
                    <th><?= __('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tasks as $t):
                    $isRain   = isset($weather_map[$t['due_date']]) && is_rain_code($weather_map[$t['due_date']]);
                    $isOverdue = ($t['status'] === 'pending' && $t['due_date'] < date('Y-m-d'));
                ?>
                <tr>
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($t['title']) ?></div>
                        <?php if ($isRain && $t['status'] === 'pending'): ?>
                        <span class="am-badge am-badge-blue mt-1" style="font-size:.68rem">
                            <i class="bi bi-cloud-rain me-1"></i><?= __('rain_warning') ?>
                        </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div><?= htmlspecialchars($t['crop_name']) ?></div>
                        <small class="text-muted"><?= htmlspecialchars($t['field_name']) ?></small>
                    </td>
                    <td><span class="am-badge am-badge-gray"><?= __($t['task_type']) ?></span></td>
                    <td>
                        <span class="<?= $isOverdue ? 'text-danger fw-semibold' : '' ?>">
                            <?= date('d M Y', strtotime($t['due_date'])) ?>
                        </span>
                    </td>
                    <td>
                        <span class="am-badge <?= $t['status'] === 'pending' ? 'am-badge-amber' : 'am-badge-green' ?>">
                            <?= __($t['status']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="add.php?edit=<?= $t['id'] ?>" class="btn-em-outline" style="padding:.25rem .6rem;font-size:.78rem"><i class="bi bi-pencil"></i></a>
                            <?php if ($t['status'] === 'pending'): ?>
                            <a href="?complete=<?= $t['id'] ?>" class="btn-em-outline" style="padding:.25rem .6rem;font-size:.78rem" title="<?= __('mark_done') ?>"><i class="bi bi-check-lg text-em"></i></a>
                            <?php endif; ?>
                            <a href="?delete=<?= $t['id'] ?>" class="btn-em-outline" style="padding:.25rem .6rem;font-size:.78rem;color:#ef4444;border-color:#ef4444" data-confirm="<?= __('confirm_delete') ?>"><i class="bi bi-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../includes/layout.php';
