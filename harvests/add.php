<?php
// harvests/add.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/lang.php';

require_login();
$uid = current_user_id();

$edit_id = isset($_GET['edit']) && ctype_digit($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editing = false; $row = null;
if ($edit_id) {
    $row = fetch_one($conn, "SELECT h.* FROM harvests h JOIN plantings p ON p.id=h.planting_id WHERE h.id=$edit_id AND p.user_id=$uid LIMIT 1");
    if (!$row) { header('Location: index.php'); exit; }
    $editing = true;
}

$plantings = fetch_all($conn,
    "SELECT p.id, CONCAT(c.name, ' – ', f.name, ' (', p.planting_date, ')') AS label
     FROM plantings p JOIN crops c ON c.id=p.crop_id JOIN fields f ON f.id=p.field_id
     WHERE p.user_id=$uid ORDER BY p.planting_date DESC");

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $planting_id  = (int)($_POST['planting_id']  ?? 0);
    $harvest_date = esc($conn, $_POST['harvest_date'] ?? '');
    $quantity_kg  = (float)($_POST['quantity_kg'] ?? 0);
    $notes        = esc($conn, $_POST['notes']    ?? '');

    if (!$planting_id)  $errors[] = __('plantings') . ' required.';
    if (!$harvest_date) $errors[] = __('harvest_date') . ' required.';
    if ($quantity_kg <= 0) $errors[] = __('quantity_kg') . ' must be > 0.';

    if ($planting_id) {
        $chk = fetch_one($conn, "SELECT id FROM plantings WHERE id=$planting_id AND user_id=$uid LIMIT 1");
        if (!$chk) $errors[] = 'Invalid planting.';
    }

    if (!$errors) {
        if ($editing) {
            qry($conn, "UPDATE harvests SET planting_id=$planting_id, harvest_date='$harvest_date', quantity_kg=$quantity_kg, notes='$notes' WHERE id=$edit_id");
        } else {
            qry($conn, "INSERT INTO harvests (planting_id,harvest_date,quantity_kg,notes) VALUES ($planting_id,'$harvest_date',$quantity_kg,'$notes')");
            // Auto-complete the planting
            qry($conn, "UPDATE plantings SET status='completed' WHERE id=$planting_id AND user_id=$uid");
        }
        header('Location: index.php?msg=saved'); exit;
    }
}

$v_pl   = $editing ? $row['planting_id']  : ($_POST['planting_id']  ?? '');
$v_date = $editing ? $row['harvest_date'] : ($_POST['harvest_date'] ?? date('Y-m-d'));
$v_qty  = $editing ? $row['quantity_kg']  : ($_POST['quantity_kg']  ?? '');
$v_notes= $editing ? $row['notes']        : ($_POST['notes']        ?? '');

$page_title = $editing ? __('edit_harvest') : __('add_harvest');
ob_start();
?>
<link rel="stylesheet" href="style.css">
<link rel="shortcut icon" href="icon.png" type="image/x-icon">
<div class="am-page-header">
    <h1 class="am-page-title"><i class="bi bi-basket3 me-2 text-em"></i><?= $page_title ?></h1>
    <a href="index.php" class="btn-em-outline"><i class="bi bi-arrow-left me-1"></i><?= __('back') ?></a>
</div>
<?php if ($errors): ?>
<div class="alert alert-danger mb-4" style="border-radius:8px;font-size:.875rem"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div>
<?php endif; ?>
<div class="row justify-content-center">
    <div class="col-12 col-lg-6">
        <div class="am-card">
            <div class="am-card-body">
                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="am-form-label"><?= __('plantings') ?> *</label>
                            <select name="planting_id" class="form-select" required>
                                <option value=""><?= __('select_option') ?></option>
                                <?php foreach ($plantings as $pl): ?>
                                <option value="<?= $pl['id'] ?>" <?= $v_pl == $pl['id'] ? 'selected' : '' ?>><?= htmlspecialchars($pl['label']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="am-form-label"><?= __('harvest_date') ?> *</label>
                            <input type="date" name="harvest_date" class="form-control" required value="<?= htmlspecialchars($v_date) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="am-form-label"><?= __('quantity_kg') ?> *</label>
                            <input type="number" name="quantity_kg" class="form-control" step="0.1" min="0.1" placeholder="e.g. 1200" required value="<?= htmlspecialchars($v_qty) ?>">
                        </div>
                        <div class="col-12">
                            <label class="am-form-label"><?= __('notes') ?></label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Quality, storage location, etc..."><?= htmlspecialchars($v_notes) ?></textarea>
                        </div>
                        <div class="col-12 d-flex gap-3 pt-2">
                            <button type="submit" class="btn-em"><i class="bi bi-floppy me-1"></i><?= __('save') ?></button>
                            <a href="index.php" class="btn-em-outline"><?= __('cancel') ?></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../includes/layout.php';
