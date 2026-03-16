<?php
// rotation/index.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/lang.php';

require_login();
$uid = current_user_id();

if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    qry($conn, "DELETE r FROM rotations r JOIN fields f ON f.id=r.field_id WHERE r.id=$did AND f.user_id=$uid");
    header('Location: index.php?msg=deleted'); exit;
}

$msg       = $_GET['msg'] ?? '';
$rotations = fetch_all($conn,
    "SELECT r.*, c.name AS crop_name, c.name_mr, f.name AS field_name
     FROM rotations r
     JOIN fields f ON f.id = r.field_id
     JOIN crops c  ON c.id = r.crop_id
     WHERE f.user_id = $uid
     ORDER BY r.year DESC, f.name ASC");

$page_title = __('rotation');
ob_start();
?>
<link rel="stylesheet" href="style.css">
<link rel="shortcut icon" href="icon.png" type="image/x-icon">
<div class="am-page-header">
    <h1 class="am-page-title"><i class="bi bi-arrow-repeat me-2 text-em"></i><?= __('rotation_list') ?></h1>
    <a href="add.php" class="btn-em"><i class="bi bi-plus-lg"></i> <?= __('add_rotation') ?></a>
</div>
<?php if ($msg === 'saved'): ?><div class="alert alert-success py-2 px-3 mb-3" data-auto-dismiss style="font-size:.875rem;border-radius:8px"><i class="bi bi-check-circle me-1"></i> <?= __('success_saved') ?></div>
<?php elseif ($msg === 'deleted'): ?><div class="alert alert-warning py-2 px-3 mb-3" data-auto-dismiss style="font-size:.875rem;border-radius:8px"><i class="bi bi-trash me-1"></i> <?= __('success_deleted') ?></div>
<?php endif; ?>
<div class="am-card">
    <?php if (!$rotations): ?>
    <div class="am-card-body text-center py-5 text-muted"><i class="bi bi-arrow-repeat fs-2 d-block mb-2"></i><?= __('no_rotations') ?><div class="mt-3"><a href="add.php" class="btn-em"><?= __('add_rotation') ?></a></div></div>
    <?php else: ?>
    <div class="am-table-wrap">
        <table class="am-table">
            <thead><tr>
                <th><?= __('field') ?></th><th><?= __('year') ?></th><th><?= __('season') ?></th>
                <th><?= __('crop') ?></th><th><?= __('actions') ?></th>
            </tr></thead>
            <tbody>
                <?php foreach ($rotations as $r):
                    $cn = (current_lang() === 'mr' && $r['name_mr']) ? $r['name_mr'] : $r['crop_name'];
                ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($r['field_name']) ?></td>
                    <td><?= htmlspecialchars($r['year']) ?></td>
                    <td><span class="am-badge am-badge-green"><?= __($r['season']) ?></span></td>
                    <td><?= htmlspecialchars($cn) ?></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="add.php?edit=<?= $r['id'] ?>" class="btn-em-outline" style="padding:.25rem .6rem;font-size:.78rem"><i class="bi bi-pencil"></i></a>
                            <a href="?delete=<?= $r['id'] ?>" class="btn-em-outline" style="padding:.25rem .6rem;font-size:.78rem;color:#ef4444;border-color:#ef4444" data-confirm="<?= __('confirm_delete') ?>"><i class="bi bi-trash"></i></a>
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
