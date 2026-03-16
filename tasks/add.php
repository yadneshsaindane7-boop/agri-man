<?php
// tasks/add.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/lang.php';

require_login();
$uid = current_user_id();

$edit_id = isset($_GET['edit']) && ctype_digit($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editing = false; $row = null;
if ($edit_id) {
    $row = fetch_one($conn, "SELECT t.* FROM tasks t JOIN plantings p ON p.id=t.planting_id WHERE t.id=$edit_id AND p.user_id=$uid LIMIT 1");
    if (!$row) { header('Location: index.php'); exit; }
    $editing = true;
}

$plantings = fetch_all($conn,
    "SELECT p.id, CONCAT(c.name, ' – ', f.name) AS label
     FROM plantings p JOIN crops c ON c.id=p.crop_id JOIN fields f ON f.id=p.field_id
     WHERE p.user_id=$uid AND p.status='active' ORDER BY p.planting_date DESC");

$task_types = ['irrigation','fertilization','pesticide','weeding','pruning','harvesting','other'];

$errors = [];
$preselect_planting = isset($_GET['planting_id']) && ctype_digit($_GET['planting_id']) ? (int)$_GET['planting_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $planting_id = (int)($_POST['planting_id'] ?? 0);
    $title       = esc($conn, $_POST['title']    ?? '');
    $task_type   = esc($conn, $_POST['task_type'] ?? 'other');
    $due_date    = esc($conn, $_POST['due_date']  ?? '');
    $status      = esc($conn, $_POST['status']    ?? 'pending');
    $notes       = esc($conn, $_POST['notes']     ?? '');

    if (!$planting_id) $errors[] = __('plantings') . ' required.';
    if (!$title)       $errors[] = __('task_title') . ' required.';
    if (!$due_date)    $errors[] = __('due_date')   . ' required.';

    // Verify planting belongs to user
    if ($planting_id) {
        $chk = fetch_one($conn, "SELECT id FROM plantings WHERE id=$planting_id AND user_id=$uid LIMIT 1");
        if (!$chk) $errors[] = 'Invalid planting.';
    }

    if (!$errors) {
        $st  = in_array($status, ['pending','completed']) ? $status : 'pending';
        $tt  = in_array($task_type, $task_types) ? $task_type : 'other';
        if ($editing) {
            qry($conn, "UPDATE tasks SET planting_id=$planting_id, title='$title', task_type='$tt', due_date='$due_date', status='$st', notes='$notes' WHERE id=$edit_id");
        } else {
            qry($conn, "INSERT INTO tasks (planting_id,title,task_type,due_date,status,notes) VALUES ($planting_id,'$title','$tt','$due_date','$st','$notes')");
        }
        header('Location: index.php?msg=saved'); exit;
    }
}

$v_planting = $editing ? $row['planting_id'] : ($_POST['planting_id'] ?? $preselect_planting);
$v_title    = $editing ? $row['title']       : ($_POST['title']    ?? '');
$v_type     = $editing ? $row['task_type']   : ($_POST['task_type'] ?? 'other');
$v_due      = $editing ? $row['due_date']    : ($_POST['due_date']  ?? '');
$v_status   = $editing ? $row['status']      : ($_POST['status']   ?? 'pending');
$v_notes    = $editing ? $row['notes']       : ($_POST['notes']    ?? '');

$page_title = $editing ? __('edit_task') : __('add_task');
ob_start();
?>
<link rel="stylesheet" href="style.css">
<link rel="shortcut icon" href="icon.png" type="image/x-icon">
<div class="am-page-header">
    <h1 class="am-page-title"><i class="bi bi-check2-square me-2 text-em"></i><?= $page_title ?></h1>
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
                        <div class="col-12">
                            <label class="am-form-label"><?= __('plantings') ?> *</label>
                            <select name="planting_id" class="form-select" required>
                                <option value=""><?= __('select_option') ?></option>
                                <?php foreach ($plantings as $pl): ?>
                                <option value="<?= $pl['id'] ?>" <?= $v_planting == $pl['id'] ? 'selected' : '' ?>><?= htmlspecialchars($pl['label']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="am-form-label"><?= __('task_title') ?> *</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Apply NPK fertilizer" required value="<?= htmlspecialchars($v_title) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="am-form-label"><?= __('task_type') ?></label>
                            <select name="task_type" class="form-select">
                                <?php foreach ($task_types as $tt): ?>
                                <option value="<?= $tt ?>" <?= $v_type === $tt ? 'selected' : '' ?>><?= __($tt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="am-form-label"><?= __('due_date') ?> *</label>
                            <input type="date" name="due_date" class="form-control" required value="<?= htmlspecialchars($v_due) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="am-form-label"><?= __('status') ?></label>
                            <select name="status" class="form-select">
                                <option value="pending"   <?= $v_status === 'pending'   ? 'selected' : '' ?>><?= __('pending') ?></option>
                                <option value="completed" <?= $v_status === 'completed' ? 'selected' : '' ?>><?= __('completed') ?></option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="am-form-label"><?= __('notes') ?></label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Optional..."><?= htmlspecialchars($v_notes) ?></textarea>
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
