<?php
// fields/index.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/lang.php';

require_login();
$uid = current_user_id();

if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    qry($conn, "DELETE FROM fields WHERE id = $did AND user_id = $uid");
    header('Location: index.php?msg=deleted'); exit;
}

$msg    = $_GET['msg'] ?? '';
$fields = fetch_all($conn, "SELECT * FROM fields WHERE user_id = $uid ORDER BY name ASC");

$page_title = __('fields');
ob_start();
?>

<link rel="stylesheet" href="style.css">
<link rel="shortcut icon" href="icon.png" type="image/x-icon">
<div class="am-page-header">
    <h1 class="am-page-title"><i class="bi bi-geo-alt me-2 text-em"></i><?= __('fields_list') ?></h1>
    <a href="add.php" class="btn-em"><i class="bi bi-plus-lg"></i> <?= __('add_field') ?></a>
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

<div class="am-card">
    <?php if (!$fields): ?>
    <div class="am-card-body text-center py-5 text-muted">
        <i class="bi bi-geo-alt fs-2 d-block mb-2"></i> <?= __('no_fields') ?>
        <div class="mt-3"><a href="add.php" class="btn-em"><?= __('add_field') ?></a></div>
    </div>
    <?php else: ?>
    <div class="am-table-wrap">
        <table class="am-table">
            <thead>
                <tr>
                    <th><?= __('field_name') ?></th>
                    <th><?= __('area') ?></th>
                    <th><?= __('latitude') ?></th>
                    <th><?= __('longitude') ?></th>
                    <th><?= __('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fields as $f): ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($f['name']) ?></td>
                    <td><?= number_format($f['area_sq_m'], 0) ?> m²</td>
                    <td><?= $f['latitude']  ? number_format($f['latitude'],  6) : '—' ?></td>
                    <td><?= $f['longitude'] ? number_format($f['longitude'], 6) : '—' ?></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="add.php?edit=<?= $f['id'] ?>" class="btn-em-outline" style="padding:.25rem .6rem;font-size:.78rem"><i class="bi bi-pencil"></i></a>
                            <a href="?delete=<?= $f['id'] ?>" class="btn-em-outline" style="padding:.25rem .6rem;font-size:.78rem;color:#ef4444;border-color:#ef4444" data-confirm="<?= __('confirm_delete') ?>"><i class="bi bi-trash"></i></a>
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
