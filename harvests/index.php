<?php
// harvests/index.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/lang.php';

require_login();
$uid = current_user_id();

if (isset($_GET['delete']) && ctype_digit($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    qry($conn, "DELETE h FROM harvests h JOIN plantings p ON p.id=h.planting_id WHERE h.id=$did AND p.user_id=$uid");
    header('Location: index.php?msg=deleted'); exit;
}

$msg      = $_GET['msg'] ?? '';
$harvests = fetch_all($conn,
    "SELECT h.*, c.name AS crop_name, f.name AS field_name, p.variety_name
     FROM harvests h
     JOIN plantings p ON p.id = h.planting_id
     JOIN crops c     ON c.id = p.crop_id
     JOIN fields f    ON f.id = p.field_id
     WHERE p.user_id = $uid
     ORDER BY h.harvest_date DESC");

$page_title = __('harvests');
ob_start();
?>
<link rel="stylesheet" href="style.css">
<link rel="shortcut icon" href="icon.png" type="image/x-icon">
<div class="am-page-header">
    <h1 class="am-page-title"><i class="bi bi-basket3 me-2 text-em"></i><?= __('harvests_list') ?></h1>
    <a href="add.php" class="btn-em"><i class="bi bi-plus-lg"></i> <?= __('add_harvest') ?></a>
</div>
<?php if ($msg === 'saved'): ?><div class="alert alert-success py-2 px-3 mb-3" data-auto-dismiss style="font-size:.875rem;border-radius:8px"><i class="bi bi-check-circle me-1"></i> <?= __('success_saved') ?></div>
<?php elseif ($msg === 'deleted'): ?><div class="alert alert-warning py-2 px-3 mb-3" data-auto-dismiss style="font-size:.875rem;border-radius:8px"><i class="bi bi-trash me-1"></i> <?= __('success_deleted') ?></div>
<?php endif; ?>

<div class="am-card">
    <?php if (!$harvests): ?>
    <div class="am-card-body text-center py-5 text-muted">
        <i class="bi bi-basket3 fs-2 d-block mb-2"></i> <?= __('no_harvests') ?>
        <div class="mt-3"><a href="add.php" class="btn-em"><?= __('add_harvest') ?></a></div>
    </div>
    <?php else: ?>
    <div class="am-table-wrap">
        <table class="am-table">
            <thead><tr>
                <th><?= __('crop') ?></th><th><?= __('field') ?></th>
                <th><?= __('harvest_date') ?></th><th><?= __('quantity_kg') ?></th>
                <th><?= __('notes') ?></th><th><?= __('actions') ?></th>
            </tr></thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($harvests as $h): $total += $h['quantity_kg']; ?>
                <tr>
                    <td><div class="fw-semibold"><?= htmlspecialchars($h['crop_name']) ?></div>
                        <?php if ($h['variety_name']): ?><small class="text-muted"><?= htmlspecialchars($h['variety_name']) ?></small><?php endif; ?></td>
                    <td><?= htmlspecialchars($h['field_name']) ?></td>
                    <td><?= date('d M Y', strtotime($h['harvest_date'])) ?></td>
                    <td><span class="fw-semibold text-em"><?= number_format($h['quantity_kg'], 1) ?></span> kg</td>
                    <td><?= htmlspecialchars($h['notes'] ?: '—') ?></td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="add.php?edit=<?= $h['id'] ?>" class="btn-em-outline" style="padding:.25rem .6rem;font-size:.78rem"><i class="bi bi-pencil"></i></a>
                            <a href="?delete=<?= $h['id'] ?>" class="btn-em-outline" style="padding:.25rem .6rem;font-size:.78rem;color:#ef4444;border-color:#ef4444" data-confirm="<?= __('confirm_delete') ?>"><i class="bi bi-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-end fw-semibold text-muted" style="font-size:.8rem;padding:.75rem 1rem">TOTAL</td>
                    <td class="fw-bold text-em"><?= number_format($total, 1) ?> kg</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php endif; ?>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../includes/layout.php';
