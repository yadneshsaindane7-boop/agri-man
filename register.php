<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/lang.php';

redirect_if_logged_in();

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = esc($conn, $_POST['name']     ?? '');
    $email    = esc($conn, $_POST['email']    ?? '');
    $password = trim($_POST['password']       ?? '');

    if (!$name || !$email || !$password) {
        $error = __('error_generic');
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        $exists = fetch_one($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");
        if ($exists) {
            $error = __('email_taken');
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            qry($conn, "INSERT INTO users (name,email,password) VALUES ('$name','$email','$hash')");
            $user = fetch_one($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");
            login_user($user);
            header('Location: /agriman/dashboard.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= current_lang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= __('register') ?> — <?= __('app_name') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <link rel="shortcut icon" href="icon.png" type="image/x-icon">
</head>
<body>
<div class="am-auth-wrapper">
    <div class="am-auth-card">
        <div class="am-auth-logo">
            <i class="bi bi-leaf-fill"></i> <?= __('app_name') ?>
        </div>
        <h1 class="h5 fw-bold text-center mb-1"><?= __('register_title') ?></h1>
        <p class="text-muted text-center mb-4" style="font-size:.875rem"><?= __('register_subtitle') ?></p>

        <?php if ($error): ?>
        <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.875rem;border-radius:8px">
            <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="am-form-label"><?= __('your_name') ?></label>
                <input type="text" name="name" class="form-control" placeholder="ABC" required
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label class="am-form-label"><?= __('email') ?></label>
                <input type="email" name="email" class="form-control" placeholder="you@example.com" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="am-form-label"><?= __('password') ?></label>
                <input type="password" name="password" class="form-control" placeholder="Min. 6 characters" required>
            </div>
            <button type="submit" class="btn-em w-100 justify-content-center">
                <i class="bi bi-person-plus me-1"></i> <?= __('register_btn') ?>
            </button>
        </form>

        <p class="text-center mt-4 mb-0" style="font-size:.875rem;color:#64748b">
            <?= __('have_account') ?>
            <a href="login.php" class="text-em fw-semibold text-decoration-none ms-1"><?= __('login') ?></a>
        </p>
        <div class="text-center mt-3">
            <a href="?lang=<?= current_lang() === 'en' ? 'mr' : 'en' ?>"
               class="text-muted text-decoration-none" style="font-size:.8rem">
                <i class="bi bi-translate me-1"></i><?= __('toggle_language') ?>
            </a>
        </div>
    </div>
</div>
</body>
</html>
