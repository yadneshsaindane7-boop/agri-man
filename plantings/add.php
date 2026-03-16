<?php
// plantings/add.php — Add or edit a planting
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/lang.php';

require_login();
$uid = current_user_id();

// ── Edit mode? ────────────────────────────────────────────────
$edit_id = isset($_GET['edit']) && ctype_digit($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editing = false;
$row = null;

if ($edit_id) {
    $row = fetch_one($conn, "SELECT * FROM plantings WHERE id = $edit_id AND user_id = $uid LIMIT 1");
    if (!$row) { header('Location: index.php'); exit; }
    $editing = true;
}

// ── Dropdowns ─────────────────────────────────────────────────
$lang   = current_lang();
$crops  = fetch_all($conn, "SELECT * FROM crops ORDER BY name ASC");
$fields = fetch_all($conn, "SELECT * FROM fields WHERE user_id = $uid ORDER BY name ASC");

// ── Handle POST ───────────────────────────────────────────────
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $field_id    = (int)($_POST['field_id']    ?? 0);
    $crop_id     = (int)($_POST['crop_id']     ?? 0);
    $variety     = esc($conn, $_POST['variety_name']          ?? '');
    $plant_date  = esc($conn, $_POST['planting_date']         ?? '');
    $harvest_exp = esc($conn, $_POST['expected_harvest_date'] ?? '');
    $status      = esc($conn, $_POST['status'] ?? 'active');
    $notes       = esc($conn, $_POST['notes']  ?? '');

    // Validate
    if (!$field_id) $errors[] = __('field') . ' is required.';
    if (!$crop_id)  $errors[] = __('crop')  . ' is required.';
    if (!$plant_date) $errors[] = __('planting_date') . ' is required.';

    // Check field belongs to user
    if ($field_id) {
        $chk = fetch_one($conn, "SELECT id FROM fields WHERE id = $field_id AND user_id = $uid LIMIT 1");
        if (!$chk) $errors[] = 'Invalid field selection.';
    }

    if (!$errors) {
        $harvest_val = $harvest_exp ? "'$harvest_exp'" : 'NULL';
        $status_val  = in_array($status, ['active','completed']) ? $status : 'active';

        if ($editing) {
            qry($conn, "UPDATE plantings SET
                field_id              = $field_id,
                crop_id               = $crop_id,
                variety_name          = '$variety',
                planting_date         = '$plant_date',
                expected_harvest_date = $harvest_val,
                status                = '$status_val',
                notes                 = '$notes'
                WHERE id = $edit_id AND user_id = $uid");
        } else {
            qry($conn, "INSERT INTO plantings
                (user_id, field_id, crop_id, variety_name, planting_date, expected_harvest_date, status, notes)
                VALUES ($uid, $field_id, $crop_id, '$variety', '$plant_date', $harvest_val, '$status_val', '$notes')");
        }
        header('Location: index.php?msg=saved');
        exit;
    }
}

// ── Pre-fill from $row if editing ─────────────────────────────
$v_field_id    = $editing ? $row['field_id']              : ($_POST['field_id']    ?? '');
$v_crop_id     = $editing ? $row['crop_id']               : ($_POST['crop_id']     ?? '');
$v_variety     = $editing ? $row['variety_name']          : ($_POST['variety_name'] ?? '');
$v_plant_date  = $editing ? $row['planting_date']         : ($_POST['planting_date'] ?? '');
$v_harvest_exp = $editing ? $row['expected_harvest_date'] : ($_POST['expected_harvest_date'] ?? '');
$v_status      = $editing ? $row['status']                : ($_POST['status'] ?? 'active');
$v_notes       = $editing ? $row['notes']                 : ($_POST['notes']   ?? '');

$page_title = $editing ? __('edit_planting') : __('add_planting');
ob_start();
?>


<link rel="stylesheet" href="style.css">
<link rel="shortcut icon" href="icon.png" type="image/x-icon">
<div class="am-page-header">
    <h1 class="am-page-title">
        <i class="bi bi-tree me-2 text-em"></i><?= $page_title ?>
    </h1>
    <a href="index.php" class="btn-em-outline">
        <i class="bi bi-arrow-left me-1"></i><?= __('back') ?>
    </a>
</div>

<?php if ($errors): ?>
<div class="alert alert-danger mb-4" style="border-radius:8px;font-size:.875rem">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
</div>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="am-card">
            <div class="am-card-header">
                <h2 class="am-card-title"><?= __('planting_details') ?></h2>
            </div>
            <div class="am-card-body">
                <form method="POST" action="">

                    <div class="row g-3">

                        <!-- Field -->
                        <div class="col-md-6">
                            <label class="am-form-label"><?= __('field') ?> *</label>
                            <?php if (!$fields): ?>
                            <div class="alert alert-warning py-2 px-3" style="font-size:.8rem;border-radius:8px">
                                No fields yet. <a href="../fields/add.php">Add a field first</a>.
                            </div>
                            <?php else: ?>
                            <select name="field_id" class="form-select" required>
                                <option value=""><?= __('select_option') ?></option>
                                <?php foreach ($fields as $f): ?>
                                <option value="<?= $f['id'] ?>" <?= $v_field_id == $f['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($f['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php endif; ?>
                        </div>

                        <!-- Crop -->
                        <div class="col-md-6">
                            <label class="am-form-label"><?= __('crop') ?> *</label>
                            <select name="crop_id" class="form-select" required>
                                <option value=""><?= __('select_option') ?></option>
                                <?php foreach ($crops as $c):
                                    $cname = ($lang === 'mr' && $c['name_mr']) ? $c['name_mr'] : $c['name'];
                                ?>
                                <option value="<?= $c['id'] ?>" <?= $v_crop_id == $c['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cname) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Variety -->
                        <div class="col-md-6">
                            <label class="am-form-label"><?= __('variety') ?></label>
                            <input type="text" name="variety_name" class="form-control"
                                   placeholder="e.g. HD-2967, Sahyadri-4"
                                   value="<?= htmlspecialchars($v_variety) ?>">
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="am-form-label"><?= __('status') ?></label>
                            <select name="status" class="form-select">
                                <option value="active"    <?= $v_status === 'active'    ? 'selected' : '' ?>><?= __('active') ?></option>
                                <option value="completed" <?= $v_status === 'completed' ? 'selected' : '' ?>><?= __('completed') ?></option>
                            </select>
                        </div>

                        <!-- Planting Date -->
                        <div class="col-md-6">
                            <label class="am-form-label"><?= __('planting_date') ?> *</label>
                            <input type="date" name="planting_date" class="form-control" required
                                   value="<?= htmlspecialchars($v_plant_date) ?>">
                        </div>

                        <!-- Expected Harvest -->
                        <div class="col-md-6">
                            <label class="am-form-label"><?= __('expected_harvest') ?></label>
                            <input type="date" name="expected_harvest_date" class="form-control"
                                   value="<?= htmlspecialchars($v_harvest_exp) ?>">
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label class="am-form-label"><?= __('notes') ?></label>
                            <textarea name="notes" class="form-control" rows="3"
                                      placeholder="Optional notes about this planting..."><?= htmlspecialchars($v_notes) ?></textarea>
                        </div>

                        <!-- Actions -->
                        <div class="col-12 d-flex gap-3 pt-2">
                            <button type="submit" class="btn-em">
                                <i class="bi bi-floppy me-1"></i><?= __('save') ?>
                            </button>
                            <a href="index.php" class="btn-em-outline"><?= __('cancel') ?></a>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <?php if ($editing): ?>
        <!-- Quick-add task for this planting -->
        <div class="am-card mt-4">
            <div class="am-card-header">
                <h2 class="am-card-title"><i class="bi bi-check2-square me-2 text-em"></i><?= __('tasks') ?></h2>
                <a href="../tasks/add.php?planting_id=<?= $edit_id ?>" class="btn-em" style="padding:.3rem .75rem;font-size:.8rem">
                    <i class="bi bi-plus-lg me-1"></i><?= __('add_task') ?>
                </a>
            </div>
            <?php
            $ptasks = fetch_all($conn,
                "SELECT * FROM tasks WHERE planting_id = $edit_id ORDER BY due_date ASC LIMIT 8");
            ?>
            <?php if ($ptasks): ?>
            <div class="am-table-wrap">
                <table class="am-table">
                    <thead>
                        <tr><th><?= __('task_title') ?></th><th><?= __('task_type') ?></th>
                            <th><?= __('due_date') ?></th><th><?= __('status') ?></th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ptasks as $t): ?>
                        <tr>
                            <td><?= htmlspecialchars($t['title']) ?></td>
                            <td><?= __($t['task_type']) ?></td>
                            <td><?= date('d M Y', strtotime($t['due_date'])) ?></td>
                            <td>
                                <span class="am-badge <?= $t['status'] === 'pending' ? 'am-badge-amber' : 'am-badge-green' ?>">
                                    <?= __($t['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="am-card-body text-muted text-center py-4"><?= __('no_tasks_list') ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../includes/layout.php';
