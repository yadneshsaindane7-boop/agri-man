<?php
// index.php — Entry point for AgriMan
// Redirects logged-in users to dashboard, others to login

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Show landing page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AgriMan — Smart Farm Management</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Fraunces:opsz,wght@9..144,600;9..144,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="shortcut icon" href="icon.png" type="image/x-icon">
    <style>
        :root {
            --green:       #10b981;
            --green-dark:  #059669;
            --green-light: #d1fae5;
            --green-xlight:#f0fdf9;
            --dark:        #0f172a;
            --muted:       #64748b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #fff;
            color: var(--dark);
            overflow-x: hidden;
        }

        /* ── Navbar ── */
        .lp-nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 2rem;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
        }

        .lp-brand {
            font-family: 'Fraunces', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--dark);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .4rem;
        }

        .lp-brand i { color: var(--green); }

        .lp-nav-links { display: flex; align-items: center; gap: .75rem; }

        .btn-login {
            padding: .45rem 1.125rem;
            border: 1.5px solid var(--green);
            color: var(--green);
            border-radius: 8px;
            font-weight: 600;
            font-size: .875rem;
            text-decoration: none;
            transition: all .18s;
        }
        .btn-login:hover { background: var(--green); color: #fff; }

        .btn-register {
            padding: .45rem 1.125rem;
            background: var(--green);
            color: #fff;
            border-radius: 8px;
            font-weight: 600;
            font-size: .875rem;
            text-decoration: none;
            transition: background .18s;
        }
        .btn-register:hover { background: var(--green-dark); color: #fff; }

        /* ── Hero ── */
        .lp-hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(160deg, var(--green-xlight) 0%, #e0f2fe 60%, #fff 100%);
            padding: 7rem 2rem 4rem;
            position: relative;
            overflow: hidden;
        }

        .lp-hero::before {
            content: '';
            position: absolute;
            top: -120px; right: -120px;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(16,185,129,.12) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .lp-hero::after {
            content: '';
            position: absolute;
            bottom: -80px; left: -80px;
            width: 350px; height: 350px;
            background: radial-gradient(circle, rgba(56,189,248,.1) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            background: var(--green-light);
            color: var(--green-dark);
            font-size: .78rem;
            font-weight: 600;
            padding: .35rem .875rem;
            border-radius: 999px;
            margin-bottom: 1.25rem;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .hero-title {
            font-family: 'Fraunces', serif;
            font-size: clamp(2.25rem, 5vw, 3.75rem);
            font-weight: 700;
            line-height: 1.1;
            letter-spacing: -.03em;
            margin-bottom: 1.25rem;
        }

        .hero-title span { color: var(--green); }

        .hero-subtitle {
            font-size: 1.1rem;
            color: var(--muted);
            line-height: 1.7;
            max-width: 480px;
            margin-bottom: 2.25rem;
        }

        .hero-cta { display: flex; gap: 1rem; flex-wrap: wrap; }

        .btn-hero-primary {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: var(--green);
            color: #fff;
            padding: .75rem 1.75rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            transition: background .18s, transform .18s;
            box-shadow: 0 4px 14px rgba(16,185,129,.35);
        }
        .btn-hero-primary:hover { background: var(--green-dark); color: #fff; transform: translateY(-2px); }

        .btn-hero-secondary {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: #fff;
            color: var(--dark);
            padding: .75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            border: 1.5px solid #e2e8f0;
            transition: border-color .18s, transform .18s;
        }
        .btn-hero-secondary:hover { border-color: var(--green); color: var(--green); transform: translateY(-2px); }

        /* ── Hero visual card ── */
        .hero-visual {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 20px 60px -12px rgba(0,0,0,.12);
            padding: 1.5rem;
            max-width: 420px;
            width: 100%;
        }

        .hv-header {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .hv-dot { width: 10px; height: 10px; border-radius: 50%; }

        .hv-stat {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .75rem 1rem;
            background: var(--green-xlight);
            border-radius: 10px;
            margin-bottom: .625rem;
        }

        .hv-stat-label { font-size: .8rem; color: var(--muted); font-weight: 500; }
        .hv-stat-value { font-size: 1.25rem; font-weight: 700; color: var(--dark); }

        .hv-task {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .625rem 0;
            border-bottom: 1px solid #f8fafc;
            font-size: .825rem;
        }

        .hv-task-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .weather-mini {
            display: flex;
            gap: .5rem;
            margin-top: 1rem;
        }

        .wm-day {
            flex: 1;
            text-align: center;
            background: #f8fafc;
            border-radius: 8px;
            padding: .5rem .25rem;
            font-size: .7rem;
            color: var(--muted);
        }

        .wm-day i { display: block; font-size: 1.1rem; margin-bottom: .2rem; }

        /* ── Features ── */
        .lp-features {
            padding: 5rem 2rem;
            background: #fff;
        }

        .section-label {
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--green);
            margin-bottom: .75rem;
        }

        .section-title {
            font-family: 'Fraunces', serif;
            font-size: clamp(1.75rem, 3.5vw, 2.5rem);
            font-weight: 700;
            letter-spacing: -.025em;
            margin-bottom: 1rem;
        }

        .feature-card {
            padding: 1.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            height: 100%;
            transition: border-color .18s, box-shadow .18s, transform .18s;
        }

        .feature-card:hover {
            border-color: var(--green-light);
            box-shadow: 0 8px 24px rgba(16,185,129,.1);
            transform: translateY(-3px);
        }

        .feature-icon {
            width: 48px; height: 48px;
            background: var(--green-light);
            color: var(--green-dark);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            margin-bottom: 1rem;
        }

        .feature-title {
            font-weight: 700;
            font-size: 1rem;
            margin-bottom: .5rem;
        }

        .feature-desc {
            font-size: .875rem;
            color: var(--muted);
            line-height: 1.65;
        }

        /* ── Stats strip ── */
        .lp-stats {
            background: var(--dark);
            padding: 3.5rem 2rem;
            color: #fff;
        }

        .stat-item { text-align: center; }

        .stat-num {
            font-family: 'Fraunces', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--green);
            display: block;
            line-height: 1;
            margin-bottom: .35rem;
        }

        .stat-lbl {
            font-size: .875rem;
            color: rgba(255,255,255,.55);
            font-weight: 500;
        }

        /* ── CTA Section ── */
        .lp-cta {
            padding: 5rem 2rem;
            background: linear-gradient(135deg, var(--green-xlight) 0%, #e0f2fe 100%);
            text-align: center;
        }

        /* ── Footer ── */
        .lp-footer {
            background: var(--dark);
            color: rgba(255,255,255,.45);
            text-align: center;
            padding: 1.5rem 2rem;
            font-size: .8rem;
        }

        .lp-footer a { color: var(--green); text-decoration: none; }

        @media (max-width: 768px) {
            .lp-nav { padding: 1rem; }
            .hero-visual { display: none; }
            .lp-hero { padding: 6rem 1.25rem 3rem; }
        }
    </style>
</head>
<body>

<!-- ── Navbar ── -->
<nav class="lp-nav">
    <a href="index.php" class="lp-brand">
        <i class="bi bi-leaf-fill"></i> AgriMan
    </a>
    <div class="lp-nav-links">
        <a href="login.php"    class="btn-login">Sign In</a>
        <a href="register.php" class="btn-register">Get Started</a>
    </div>
</nav>

<!-- ── Hero ── -->
<section class="lp-hero">
    <div class="container-xl">
        <div class="row align-items-center g-5">

            <!-- Text -->
            <div class="col-lg-6">
                <div class="hero-badge">
                    <i class="bi bi-stars"></i> Smart Farm Management
                </div>
                <h1 class="hero-title">
                    Grow smarter,<br>harvest <span>better</span>
                </h1>
                <p class="hero-subtitle">
                    AgriMan helps farmers track fields, plantings, tasks and harvests — with live weather alerts so you're never caught off guard.
                </p>
                <div class="hero-cta">
                    <a href="register.php" class="btn-hero-primary">
                        <i class="bi bi-person-plus"></i> Start for Free
                    </a>
                    <a href="login.php" class="btn-hero-secondary">
                        <i class="bi bi-box-arrow-in-right"></i> Sign In
                    </a>
                </div>
                <p class="mt-3" style="font-size:.8rem;color:var(--muted)">
                    <i class="bi bi-lock me-1"></i> No credit card required &middot; Bilingual EN / मराठी
                </p>
            </div>

            <!-- Visual Card -->
            <div class="col-lg-6 d-flex justify-content-center justify-content-lg-end">
                <div class="hero-visual">
                    <div class="hv-header">
                        <div class="hv-dot" style="background:#ef4444"></div>
                        <div class="hv-dot" style="background:#f59e0b"></div>
                        <div class="hv-dot" style="background:#10b981"></div>
                        <span style="font-size:.78rem;color:#94a3b8;margin-left:.25rem">AgriMan Dashboard</span>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="hv-stat" style="flex-direction:column;align-items:flex-start;gap:.2rem">
                                <div class="hv-stat-label">Active Crops</div>
                                <div class="hv-stat-value">12</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="hv-stat" style="flex-direction:column;align-items:flex-start;gap:.2rem;background:#fffbeb">
                                <div class="hv-stat-label">Tasks Due</div>
                                <div class="hv-stat-value">5</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="hv-stat" style="flex-direction:column;align-items:flex-start;gap:.2rem;background:#eff6ff">
                                <div class="hv-stat-label">Harvest kg</div>
                                <div class="hv-stat-value">3.2k</div>
                            </div>
                        </div>
                    </div>

                    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#94a3b8;margin-bottom:.5rem">
                        Upcoming Tasks
                    </div>

                    <div class="hv-task">
                        <div class="hv-task-dot" style="background:#f59e0b"></div>
                        <span style="flex:1">Apply NPK fertilizer</span>
                        <span style="color:#94a3b8;font-size:.75rem">Today</span>
                    </div>
                    <div class="hv-task">
                        <div class="hv-task-dot" style="background:#10b981"></div>
                        <span style="flex:1">Irrigation — North Block</span>
                        <span style="color:#94a3b8;font-size:.75rem">Tomorrow</span>
                    </div>
                    <div class="hv-task" style="border:none">
                        <div class="hv-task-dot" style="background:#3b82f6"></div>
                        <span style="flex:1">Pesticide spray</span>
                        <div style="display:flex;align-items:center;gap:.25rem;font-size:.72rem;background:#eff6ff;color:#1d4ed8;padding:.15rem .5rem;border-radius:999px">
                            <i class="bi bi-cloud-rain"></i> Rain alert
                        </div>
                    </div>

                    <div class="weather-mini">
                        <div class="wm-day"><i class="bi bi-sun text-warning"></i>Mon<br>34°</div>
                        <div class="wm-day"><i class="bi bi-cloud-sun text-secondary"></i>Tue<br>31°</div>
                        <div class="wm-day" style="background:#eff6ff"><i class="bi bi-cloud-rain text-primary"></i>Wed<br>27°</div>
                        <div class="wm-day" style="background:#eff6ff"><i class="bi bi-cloud-rain text-primary"></i>Thu<br>26°</div>
                        <div class="wm-day"><i class="bi bi-sun text-warning"></i>Fri<br>33°</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ── Features ── -->
<section class="lp-features">
    <div class="container-xl">
        <div class="text-center mb-5">
            <div class="section-label">Everything you need</div>
            <h2 class="section-title">Built for Indian farmers</h2>
            <p style="color:var(--muted);max-width:480px;margin:0 auto;font-size:.9375rem">
                From sowing to selling — manage your entire farm cycle in one place, in your language.
            </p>
        </div>

        <div class="row g-4">
            <?php
            $features = [
                ['bi-geo-alt',        'Field Management',       'Track multiple fields with area measurements and GPS coordinates for weather targeting.'],
                ['bi-tree',           'Planting Records',       'Log every planting with crop variety, dates, and expected harvest. Filter by status instantly.'],
                ['bi-check2-square',  'Task Scheduling',        'Create irrigation, fertilization, and other tasks. Get automatic rain warnings on due dates.'],
                ['bi-cloud-sun',      'Live Weather Alerts',    'Powered by Open-Meteo API. 7-day forecast with rain alerts cross-matched against your task dates.'],
                ['bi-basket3',        'Harvest Tracking',       'Record harvest weights per crop and field. See totals and monthly trends in the reports section.'],
                ['bi-translate',      'Bilingual Support',      'Full English and Marathi (मराठी) interface. Switch language anytime from the sidebar.'],
                ['bi-arrow-repeat',   'Crop Rotation',          'Plan Kharif, Rabi and Zaid rotations year-by-year across all your fields.'],
                ['bi-bar-chart-line', 'Reports & Analytics',    'Visual harvest breakdown by crop, monthly trends, and task completion summaries.'],
            ];
            foreach ($features as [$icon, $title, $desc]):
            ?>
            <div class="col-sm-6 col-lg-3">
                <div class="feature-card">
                    <div class="feature-icon"><i class="bi <?= $icon ?>"></i></div>
                    <div class="feature-title"><?= $title ?></div>
                    <div class="feature-desc"><?= $desc ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ── Stats Strip ── -->
<section class="lp-stats">
    <div class="container-xl">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3 stat-item">
                <span class="stat-num">7</span>
                <span class="stat-lbl">Day Weather Forecast</span>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <span class="stat-num">6</span>
                <span class="stat-lbl">Management Modules</span>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <span class="stat-num">2</span>
                <span class="stat-lbl">Languages Supported</span>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <span class="stat-num">0₹</span>
                <span class="stat-lbl">Cost to Get Started</span>
            </div>
        </div>
    </div>
</section>

<!-- ── CTA ── -->
<section class="lp-cta">
    <div class="container-xl">
        <div class="section-label">Ready to start?</div>
        <h2 class="section-title mb-3">Your farm, fully organized</h2>
        <p style="color:var(--muted);max-width:420px;margin:0 auto 2rem;font-size:.9375rem">
            Create a free account in seconds and start tracking your first field today.
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="register.php" class="btn-hero-primary">
                <i class="bi bi-person-plus"></i> Create Free Account
            </a>
            <a href="login.php" class="btn-hero-secondary">
                <i class="bi bi-box-arrow-in-right"></i> Sign In
            </a>
        </div>
    </div>
</section>

<!-- ── Footer ── -->
<footer class="lp-footer">
    &copy; <?= date('Y') ?> AgriMan — Agricultural Management System &nbsp;&middot;&nbsp;
    <a href="login.php">Login</a> &nbsp;&middot;&nbsp;
    <a href="register.php">Register</a>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>