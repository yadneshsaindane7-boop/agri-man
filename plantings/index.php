<?php
// plantings/index.php — List all plantings for current user
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/lang.php';

require_login();
$uid = current_user_id();

// Handle delete
if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    qry($conn, "DELETE FROM plantings WHERE id = $did AND user_id = $uid");
    header('Location: index.php?msg=deleted');
    exit;
}

// Handle mark-complete
if (isset($_GET['complete']) && ctype_digit($_GET['complete'])) {
    $cid = (int)$_GET['complete'];
    qry($conn, "UPDATE plantings SET status='completed' WHERE id = $cid AND user_id = $uid");
    header('Location: index.php?msg=saved');
    exit;
}

$msg = $_GET['msg'] ?? '';

// Filter
$filter_status = esc($conn, $_GET['status'] ?? '');
$where_status  = $filter_status ? "AND p.status = '$filter_status'" : '';

$plantings = fetch_all($conn,
    "SELECT p.*, c.name AS crop_name, c.name_mr AS crop_name_mr,
            f.name AS field_name
     FROM plantings p
     JOIN crops c  ON c.id = p.crop_id
     JOIN fields f ON f.id = p.field_id
     WHERE p.user_id = $uid $where_status
     ORDER BY p.planting_date DESC");

$page_title = __('plantings');
ob_start();
?>

<link rel="stylesheet" href="style.css">
<link rel="shortcut icon" href="icon.png" type="image/x-icon">
<div class="am-page-header">
    <h1 class="am-page-title">
        <i class="bi bi-tree me-2 text-em"></i><?= __('plantings_list') ?>
    </h1>
    <a href="add.php" class="btn-em">
        <i class="bi bi-plus-lg"></i> <?= __('add_planting') ?>
    </a>
</div>

<?php if ($msg === 'saved'): ?>
<div class="alert alert-success py-2 px-3 mb-3" data-auto-dismiss style="font-size:.875rem;border-radius:8px">
    <i class="bi bi-check-circle me-1"></i> <?= __('success_saved') ?>
</div>
<?php elseif ($msg === 'deleted'): ?>
<div class="alert alert-warning py-2 px-3 mb-3" data-auto-dismiss style="font-size:.875rem;border-radius:8px">
    <i class="bi bi-trash me-1"></i> <?= __('success_deleted') ?>
</div>
<?php endif; ?>

<!-- Filter Bar -->
<div class="am-card mb-4">
    <div class="am-card-body py-3">
        <form method="GET" action="" class="d-flex align-items-center gap-3 flex-wrap">
            <label class="am-form-label mb-0"><?= __('status') ?>:</label>
            <div class="d-flex gap-2">
                <?php foreach (['', 'active', 'completed'] as $s): ?>
                <a href="?status=<?= $s ?>"
                   class="am-badge <?= $filter_status === $s ? 'am-badge-green' : 'am-badge-gray' ?>"
                   style="cursor:pointer;text-decoration:none;padding:.35rem .75rem">
                    <?= $s ? __($s) : __('view_all') ?>
                </a>
                <?php endforeach; ?>
            </div>
        </form>
    </div>
</div>

<div class="am-card">
    <?php if (!$plantings): ?>
    <div class="am-card-body text-center py-5 text-muted">
        <i class="bi bi-tree fs-2 d-block mb-2"></i>
        <?= __('no_plantings') ?>
        <div class="mt-3">
            <a href="add.php" class="btn-em"><?= __('add_planting') ?></a>
        </div>
    </div>
    <?php else: ?>
    <div class="am-table-wrap">
        <table class="am-table">
            <thead>
                <tr>
                    <th><?= __('crop') ?></th>
                    <th><?= __('field') ?></th>
                    <th><?= __('variety') ?></th>
                    <th><?= __('planting_date') ?></th>
                    <th><?= __('expected_harvest') ?></th>
                    <th><?= __('status') ?></th>
                    <th><?= __('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($plantings as $p):
                    $lang = current_lang();
                    $crop_display = ($lang === 'mr' && $p['crop_name_mr']) ? $p['crop_name_mr'] : $p['crop_name'];
                ?>
                <tr>
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($crop_display) ?></div>
                    </td>
                    <td><?= htmlspecialchars($p['field_name']) ?></td>
                    <td><?= htmlspecialchars($p['variety_name'] ?: '—') ?></td>
                    <td><?= date('d M Y', strtotime($p['planting_date'])) ?></td>
                    <td><?= $p['expected_harvest_date']
                            ? date('d M Y', strtotime($p['expected_harvest_date']))
                            : '—' ?></td>
                    <td>
                        <span class="am-badge <?= $p['status'] === 'active' ? 'am-badge-green' : 'am-badge-gray' ?>">
                            <?= __($p['status']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-2 align-items-center">
                            <a href="add.php?edit=<?= $p['id'] ?>" class="btn-em-outline" style="padding:.25rem .6rem;font-size:.78rem">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if ($p['status'] === 'active'): ?>
                            <a href="?complete=<?= $p['id'] ?>" class="btn-em-outline" style="padding:.25rem .6rem;font-size:.78rem"
                               title="<?= __('mark_done') ?>">
                                <i class="bi bi-check-lg text-em"></i>
                            </a>
                            <?php endif; ?>
                            <a href="?delete=<?= $p['id'] ?>"
                               class="btn-em-outline"
                               style="padding:.25rem .6rem;font-size:.78rem;color:#ef4444;border-color:#ef4444"
                               data-confirm="<?= __('confirm_delete') ?>">
                                <i class="bi bi-trash"></i>
                            </a>
                            <a href="../tasks/?planting_id=<?= $p['id'] ?>"
                               class="am-badge am-badge-blue text-decoration-none" style="font-size:.72rem">
                                <i class="bi bi-list-task me-1"></i><?= __('tasks') ?>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../includes/layout.php';
