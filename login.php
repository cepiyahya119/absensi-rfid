<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Absensi RFID SMK</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --navy: #0f1b3d;
            --navy-light: #1a2d5a;
            --navy-mid: #162347;
            --gold: #c8973c;
            --gold-light: #e2b65a;
            --gold-pale: #faf3e6;
            --bg: #edf1f7;
            --card: #ffffff;
            --fg: #1e293b;
            --muted: #64748b;
            --border: #e2e8f0;
            --success: #059669;
            --danger: #dc2626;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--navy);
            color: var(--fg);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            position: relative;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* ===== VERTICAL WRAPPER ===== */
        .page-column {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            max-height: 100vh;
        }

        /* ===== ANIMATED BACKGROUND ===== */
        .bg-orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
            z-index: 0;
        }
        .bg-orb.orb-1 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(200,151,60,0.18), transparent 70%);
            top: -15%; left: -10%;
            animation: orbDrift 16s ease-in-out infinite alternate;
        }
        .bg-orb.orb-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(26,45,90,0.6), transparent 70%);
            bottom: -10%; right: -8%;
            animation: orbDrift 20s ease-in-out infinite alternate-reverse;
        }
        .bg-orb.orb-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(200,151,60,0.08), transparent 70%);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            animation: orbDrift 14s ease-in-out infinite alternate;
        }
        @keyframes orbDrift {
            0%   { transform: translate(0, 0) scale(1); }
            50%  { transform: translate(40px, -30px) scale(1.1); }
            100% { transform: translate(-20px, 30px) scale(0.95); }
        }

        .bg-grid {
            position: fixed; inset: 0; z-index: 0; pointer-events: none;
            background-image:
                linear-gradient(rgba(200,151,60,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(200,151,60,0.03) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        .particle {
            position: fixed;
            width: 3px; height: 3px;
            border-radius: 50%;
            background: rgba(200,151,60,0.3);
            pointer-events: none;
            z-index: 0;
            animation: particleFloat linear infinite;
        }
        @keyframes particleFloat {
            0%   { transform: translateY(100vh) scale(0); opacity: 0; }
            10%  { opacity: 1; }
            90%  { opacity: 1; }
            100% { transform: translateY(-10vh) scale(1); opacity: 0; }
        }

        /* ===== LOGIN CARD ===== */
        .login-card {
            background: var(--card);
            border-radius: 18px;
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.05),
                0 20px 60px rgba(0,0,0,0.3),
                0 4px 16px rgba(0,0,0,0.15);
            width: 100%;
            overflow: hidden;
            position: relative;
            animation: cardAppear 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes cardAppear {
            0%   { opacity: 0; transform: translateY(20px) scale(0.97); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        .login-header {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            padding: 24px 24px 18px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .login-header::before {
            content: '';
            position: absolute;
            top: -40%; right: -20%;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(200,151,60,0.12), transparent 70%);
            pointer-events: none;
        }
        .login-header::after {
            content: '';
            position: absolute;
            bottom: -30%; left: -10%;
            width: 150px; height: 150px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(37,99,235,0.06), transparent 70%);
            pointer-events: none;
        }
        .login-logo {
            width: 48px; height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 10px;
            font-size: 22px;
            color: var(--navy);
            box-shadow: 0 4px 20px rgba(200,151,60,0.3);
            position: relative; z-index: 1;
        }
        .login-header h2 {
            font-size: 17px;
            font-weight: 800;
            color: #fff;
            margin-bottom: 2px;
            position: relative; z-index: 1;
        }
        .login-header p {
            font-size: 10px;
            color: rgba(255,255,255,0.45);
            font-weight: 400;
            letter-spacing: 1px;
            position: relative; z-index: 1;
        }
        .login-header .header-line {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            opacity: 0.6;
        }

        .login-body {
            padding: 18px 22px 14px;
        }

        .form-group {
            margin-bottom: 12px;
        }
        .form-group label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
        }
        .input-icon-wrap {
            position: relative;
        }
        .input-icon-wrap .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            color: var(--muted);
            transition: color 0.25s ease;
            pointer-events: none;
            z-index: 2;
        }
        .input-icon-wrap input {
            width: 100%;
            padding: 9px 40px 9px 38px;
            border: 2px solid var(--border);
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            color: var(--fg);
            background: #fafbfd;
            outline: none;
            transition: all 0.25s ease;
        }
        .input-icon-wrap input::placeholder {
            color: rgba(100,116,139,0.45);
            font-weight: 400;
            font-size: 12px;
        }
        .input-icon-wrap input:focus {
            border-color: var(--gold);
            background: var(--gold-pale);
            box-shadow: 0 0 0 3px rgba(200,151,60,0.08);
        }
        .input-icon-wrap:focus-within .input-icon {
            color: var(--gold);
        }

        .pw-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--muted);
            font-size: 13px;
            cursor: pointer;
            padding: 4px;
            transition: color 0.2s;
            z-index: 2;
        }
        .pw-toggle:hover { color: var(--fg); }

        .btn-submit {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 100%);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
        }
        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0; left: -100%; right: 0; bottom: 0;
            width: 100%;
            background: linear-gradient(90deg, transparent, rgba(200,151,60,0.2), transparent);
            transition: left 0.5s ease;
        }
        .btn-submit:hover::before { left: 100%; }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(15,27,61,0.35);
        }
        .btn-submit:active {
            transform: translateY(0);
        }

        .auth-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 12px 0;
        }
        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        .auth-divider span {
            font-size: 10px;
            color: var(--muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        .btn-register {
            width: 100%;
            padding: 9px;
            border: 2px solid var(--gold);
            border-radius: 10px;
            background: transparent;
            color: var(--gold);
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-register:hover {
            background: var(--gold-pale);
            transform: translateY(-1px);
            box-shadow: 0 4px 18px rgba(200,151,60,0.15);
            color: var(--navy);
            border-color: var(--gold-light);
        }
        .btn-register:active { transform: translateY(0); }

        .back-link {
            text-align: center;
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px solid var(--border);
        }
        .back-link a {
            font-size: 12px;
            color: var(--muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        .back-link a:hover {
            color: var(--navy);
            gap: 8px;
        }
        .back-link a:hover i { transform: translateX(-3px); }

        .alert-auth-error {
            background: #fef2f2;
            border: 1px solid rgba(220,38,38,0.15);
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 12px;
            color: var(--danger);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: alertPop 0.4s ease;
        }
        .alert-auth-error i { font-size: 14px; flex-shrink: 0; }
        @keyframes alertPop {
            0%, 100% { transform: translateX(0); }
            20% { transform: translateX(-4px); }
            40% { transform: translateX(4px); }
            60% { transform: translateX(-2px); }
            80% { transform: translateX(2px); }
        }

        .alert-auth-success {
            background: #ecfdf5;
            border: 1px solid rgba(5,150,105,0.15);
            border-radius: 10px;
            padding: 8px 12px;
            font-size: 12px;
            color: var(--success);
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: alertPop 0.4s ease;
        }
        .alert-auth-success i { font-size: 14px; flex-shrink: 0; }

        .page-footer {
            text-align: center;
            margin-top: 14px;
        }
        .page-footer .fp-brand {
            font-size: 10px;
            font-weight: 700;
            color: rgba(255,255,255,0.3);
            letter-spacing: 1.5px;
        }
        .page-footer .fp-copy {
            font-size: 9px;
            color: rgba(255,255,255,0.15);
            margin-top: 1px;
        }

        /* ===== LAYAR KECIL: SCROLL AJA ===== */
        @media(max-height: 580px) {
            body {
                height: auto;
                min-height: 100vh;
                align-items: flex-start;
                padding-top: 10px;
            }
            .page-column {
                max-height: none;
            }
        }

        @media(max-width: 480px) {
            .login-body { padding: 14px 16px 12px; }
            .login-header { padding: 18px 16px 14px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>

<!-- Background Effects -->
<div class="bg-orb orb-1"></div>
<div class="bg-orb orb-2"></div>
<div class="bg-orb orb-3"></div>
<div class="bg-grid"></div>

<!-- Wrapper kolom vertikal -->
<div class="page-column">

    <!-- Login Card -->
    <div class="login-card">

        <!-- Header -->
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <h2>SMK BHAKTI NUSANTARA</h2>
            <p>PANEL ADMINISTRASI ABSENSI</p>
            <div class="header-line"></div>
        </div>

        <!-- Body -->
        <div class="login-body">

            <?php if (isset($_GET['error'])): ?>
            <div class="alert-auth-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['success'])): ?>
            <div class="alert-auth-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
            <?php endif; ?>

            <form method="POST" action="proses_login.php" id="loginForm">

                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-icon-wrap">
                        <input type="text"
                               name="username"
                               id="username"
                               placeholder="Masukkan username anda"
                               autofocus
                               autocomplete="off"
                               required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-icon-wrap">
                        <input type="password"
                               name="password"
                               id="password"
                               placeholder="Masukkan password anda"
                               autocomplete="current-password"
                               required>
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="pw-toggle" id="togglePw" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i>
                    Masuk ke Sistem
                </button>

            </form>

            <div class="auth-divider">
                <span>atau</span>
            </div>

            <div class="back-link">
                <a href="index.php">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Halaman Absensi
                </a>
            </div>

        </div>

    </div>

    <!-- Footer -->
    <div class="page-footer">
        <div class="fp-brand">BHATARA INFORMATION TECHNOLOGY</div>
        <div class="fp-copy">&copy; <?= date('Y') ?> All Rights Reserved &bull; V2.0</div>
    </div>

</div>

<script>
// Toggle password visibility
const togglePw = document.getElementById('togglePw');
const pwField = document.getElementById('password');

togglePw.addEventListener('click', function() {
    const isPassword = pwField.type === 'password';
    pwField.type = isPassword ? 'text' : 'password';
    togglePw.querySelector('i').className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
});

document.getElementById('username').focus();

// Partikel
(function createParticles() {
    const count = 15;
    for (let i = 0; i < count; i++) {
        const p = document.createElement('div');
        p.classList.add('particle');
        p.style.left = Math.random() * 100 + '%';
        p.style.animationDuration = (8 + Math.random() * 12) + 's';
        p.style.animationDelay = (Math.random() * 10) + 's';
        p.style.width = (2 + Math.random() * 2) + 'px';
        p.style.height = p.style.width;
        p.style.opacity = (0.12 + Math.random() * 0.2);
        document.body.appendChild(p);
    }
})();
</script>

</body>
</html>