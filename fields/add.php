<?php
// fields/add.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/lang.php';

require_login();
$uid = current_user_id();

$edit_id = isset($_GET['edit']) && ctype_digit($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editing = false; $row = null;
if ($edit_id) {
    $row = fetch_one($conn, "SELECT * FROM fields WHERE id = $edit_id AND user_id = $uid LIMIT 1");
    if (!$row) { header('Location: index.php'); exit; }
    $editing = true;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = esc($conn, $_POST['name']      ?? '');
    $area  = (float)($_POST['area_sq_m']    ?? 0);
    $lat   = trim($_POST['latitude']        ?? '');
    $lon   = trim($_POST['longitude']       ?? '');

    if (!$name) $errors[] = __('field_name') . ' is required.';
    if ($area <= 0) $errors[] = __('area') . ' must be positive.';

    $lat_val = ($lat !== '' && is_numeric($lat)) ? (float)$lat : null;
    $lon_val = ($lon !== '' && is_numeric($lon)) ? (float)$lon : null;
    $lat_sql = $lat_val !== null ? $lat_val : 'NULL';
    $lon_sql = $lon_val !== null ? $lon_val : 'NULL';

    if (!$errors) {
        if ($editing) {
            qry($conn, "UPDATE fields SET name='$name', area_sq_m=$area, latitude=$lat_sql, longitude=$lon_sql WHERE id=$edit_id AND user_id=$uid");
        } else {
            qry($conn, "INSERT INTO fields (user_id,name,area_sq_m,latitude,longitude) VALUES ($uid,'$name',$area,$lat_sql,$lon_sql)");
        }
        header('Location: index.php?msg=saved'); exit;
    }
}

$v_name = $editing ? $row['name']      : ($_POST['name']      ?? '');
$v_area = $editing ? $row['area_sq_m'] : ($_POST['area_sq_m'] ?? '');
$v_lat  = $editing ? $row['latitude']  : ($_POST['latitude']  ?? '');
$v_lon  = $editing ? $row['longitude'] : ($_POST['longitude'] ?? '');

$page_title = $editing ? __('edit_field') : __('add_field');
ob_start();
?>
<link rel="stylesheet" href="style.css">
<link rel="shortcut icon" href="icon.png" type="image/x-icon">
<div class="am-page-header">
    <h1 class="am-page-title"><i class="bi bi-geo-alt me-2 text-em"></i><?= $page_title ?></h1>
    <a href="index.php" class="btn-em-outline"><i class="bi bi-arrow-left me-1"></i><?= __('back') ?></a>
</div>

<?php if ($errors): ?>
<div class="alert alert-danger mb-4" style="border-radius:8px;font-size:.875rem">
    <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
</div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="am-card">
            <div class="am-card-body">
                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="am-form-label"><?= __('field_name') ?> *</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. North Block" required value="<?= htmlspecialchars($v_name) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="am-form-label"><?= __('area') ?> *</label>
                            <input type="number" name="area_sq_m" class="form-control" step="0.01" min="1" placeholder="e.g. 4000" required value="<?= htmlspecialchars($v_area) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="am-form-label"><?= __('latitude') ?></label>
                            <input type="number" name="latitude"  class="form-control" step="0.000001" placeholder="e.g. 19.9975" value="<?= htmlspecialchars($v_lat) ?>">
                            <div class="form-text">Used for weather forecasts</div>
                        </div>
                        <div class="col-md-6">
                            <label class="am-form-label"><?= __('longitude') ?></label>
                            <input type="number" name="longitude" class="form-control" step="0.000001" placeholder="e.g. 73.7898" value="<?= htmlspecialchars($v_lon) ?>">
                        </div>
                        <div class="col-12 d-flex gap-3 pt-2">
                            <button type="submit" class="btn-em"><i class="bi bi-floppy me-1"></i><?= __('save') ?></button>
                            <a href="index.php" class="btn-em-outline"><?= __('cancel') ?></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="am-card mt-3 p-3" style="font-size:.8rem;color:var(--text-muted)">
            <i class="bi bi-info-circle me-1 text-em"></i>
            <strong>Tip:</strong> Find latitude/longitude on <a href="https://maps.google.com" target="_blank">Google Maps</a> by right-clicking your field location.
        </div>
    </div>
</div>
<?php $content = ob_get_clean(); require __DIR__ . '/../includes/layout.php';
