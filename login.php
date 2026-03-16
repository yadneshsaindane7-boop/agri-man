<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/lang.php';

redirect_if_logged_in();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = esc($conn, $_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email && $password) {
        $user = fetch_one($conn, "SELECT * FROM users WHERE email = '$email' LIMIT 1");
        if ($user && password_verify($password, $user['password'])) {
            login_user($user);
            header('Location: /agriman/dashboard.php');
            exit;
        }
    }
    $error = __('invalid_credentials');
}
?>
<!DOCTYPE html>
<html lang="<?= current_lang() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= __('login') ?> — <?= __('app_name') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="shortcut icon" href="icon.png" type="image/x-icon">

    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="am-auth-wrapper">
    <div class="am-auth-card">
        <div class="am-auth-logo">
            <i class="bi bi-leaf-fill"></i> <?= __('app_name') ?>
        </div>
        <h1 class="h5 fw-bold text-center mb-1"><?= __('login_title') ?></h1>
        <p class="text-muted text-center mb-4" style="font-size:.875rem"><?= __('login_subtitle') ?></p>

        <?php if ($error): ?>
        <div class="alert alert-danger py-2 px-3 mb-3" style="font-size:.875rem;border-radius:8px">
            <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="am-form-label"><?= __('email') ?></label>
                <input type="email" name="email" class="form-control"
                       placeholder="you@example.com" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label class="am-form-label"><?= __('password') ?></label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-em w-100 justify-content-center">
                <i class="bi bi-box-arrow-in-right me-1"></i> <?= __('login_btn') ?>
            </button>
        </form>

        <p class="text-center mt-4 mb-0" style="font-size:.875rem;color:#64748b">
            <?= __('no_account') ?>
            <a href="register.php" class="text-em fw-semibold text-decoration-none ms-1"><?= __('register') ?></a>
        </p>
        <div class="text-center mt-2">
            <!-- <small class="text-muted">Demo: <code>demo@agriman.app</code> / <code>password</code></small> -->
        </div>
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
