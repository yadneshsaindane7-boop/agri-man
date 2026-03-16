<?php
// rotation/add.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/lang.php';

require_login();
$uid = current_user_id();

$edit_id = isset($_GET['edit']) && ctype_digit($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editing = false; $row = null;
if ($edit_id) {
    $row = fetch_one($conn, "SELECT r.* FROM rotations r JOIN fields f ON f.id=r.field_id WHERE r.id=$edit_id AND f.user_id=$uid LIMIT 1");
    if (!$row) { header('Location: index.php'); exit; }
    $editing = true;
}

$lang    = current_lang();
$crops   = fetch_all($conn, "SELECT * FROM crops ORDER BY name ASC");
$fields  = fetch_all($conn, "SELECT * FROM fields WHERE user_id=$uid ORDER BY name ASC");
$seasons = ['kharif','rabi','zaid'];
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field_id = (int)($_POST['field_id'] ?? 0);
    $crop_id  = (int)($_POST['crop_id']  ?? 0);
    $year     = (int)($_POST['year']     ?? date('Y'));
    $season   = esc($conn, $_POST['season'] ?? 'kharif');
    $notes    = esc($conn, $_POST['notes']  ?? '');

    if (!$field_id) $errors[] = __('field') . ' required.';
    if (!$crop_id)  $errors[] = __('crop')  . ' required.';
    if ($year < 2000 || $year > 2100) $errors[] = 'Invalid year.';

    if ($field_id) {
        $chk = fetch_one($conn, "SELECT id FROM fields WHERE id=$field_id AND user_id=$uid LIMIT 1");
        if (!$chk) $errors[] = 'Invalid field.';
    }

    if (!$errors) {
        $seas = in_array($season, $seasons) ? $season : 'kharif';
        if ($editing) {
            qry($conn, "UPDATE rotations SET field_id=$field_id, crop_id=$crop_id, year=$year, season='$seas', notes='$notes' WHERE id=$edit_id");
        } else {
            qry($conn, "INSERT INTO rotations (field_id,crop_id,year,season,notes) VALUES ($field_id,$crop_id,$year,'$seas','$notes')");
        }
        header('Location: index.php?msg=saved'); exit;
    }
}

$v_field  = $editing ? $row['field_id'] : ($_POST['field_id'] ?? '');
$v_crop   = $editing ? $row['crop_id']  : ($_POST['crop_id']  ?? '');
$v_year   = $editing ? $row['year']     : ($_POST['year']     ?? date('Y'));
$v_season = $editing ? $row['season']   : ($_POST['season']   ?? 'kharif');
$v_notes  = $editing ? $row['notes']    : ($_POST['notes']    ?? '');

$page_title = $editing ? __('add_rotation') : __('add_rotation');
ob_start();
?>
<link rel="stylesheet" href="style.css">
<link rel="shortcut icon" href="icon.png" type="image/x-icon">
<div class="am-page-header">
    <h1 class="am-page-title"><i class="bi bi-arrow-repeat me-2 text-em"></i><?= $page_title ?></h1>
    <a href="index.php" class="btn-em-outline"><i class="bi bi-arrow-left me-1"></i><?= __('back') ?></a>
</div>
<?php if ($errors): ?><div class="alert alert-danger mb-4" style="border-radius:8px;font-size:.875rem"><?= implode('<br>', array_map('htmlspecialchars', $errors)) ?></div><?php endif; ?>
<div class="row justify-content-center">
    <div class="col-12 col-lg-6">
        <div class="am-card">
            <div class="am-card-body">
                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="am-form-label"><?= __('field') ?> *</label>
                            <select name="field_id" class="form-select" required>
                                <option value=""><?= __('select_option') ?></option>
                                <?php foreach ($fields as $f): ?>
                                <option value="<?= $f['id'] ?>" <?= $v_field == $f['id'] ? 'selected' : '' ?>><?= htmlspecialchars($f['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="am-form-label"><?= __('crop') ?> *</label>
                            <select name="crop_id" class="form-select" required>
                                <option value=""><?= __('select_option') ?></option>
                                <?php foreach ($crops as $c): $cn = ($lang === 'mr' && $c['name_mr']) ? $c['name_mr'] : $c['name']; ?>
                                <option value="<?= $c['id'] ?>" <?= $v_crop == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cn) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="am-form-label"><?= __('year') ?></label>
                            <input type="number" name="year" class="form-control" min="2000" max="2100" value="<?= $v_year ?>">
                        </div>
                        <div class="col-md-8">
                            <label class="am-form-label"><?= __('season') ?></label>
                            <select name="season" class="form-select">
                                <?php foreach ($seasons as $s): ?>
                                <option value="<?= $s ?>" <?= $v_season === $s ? 'selected' : '' ?>><?= __($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="am-form-label"><?= __('notes') ?></label>
                            <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($v_notes) ?></textarea>
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
