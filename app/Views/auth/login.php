<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Login — SIn-FTIK USM Sistem Inventaris FTIK Universitas Semarang" />
    <title><?= esc($title ?? 'Login — SIn-FTIK USM') ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>?v=<?= @filemtime(FCPATH . 'assets/css/login.css') ?: '1' ?>" />
    <!-- Remix Icon -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" />
</head>
<body>

<div class="login-shell">

    <!-- ============================================================
         Panel Kiri — Oranye / Branding
    ============================================================ -->
    <div class="login-left">
        <!-- Dekorasi -->
        <div class="left-lines"></div>
        <div class="left-deco-ring"></div>
        <div class="left-deco-ring-2"></div>

        <!-- Konten Utama -->
        <div class="left-content">
            <div class="left-brand">
                <h1 class="left-title">SIn-FTIK USM</h1>
                <p class="left-subtitle">Sistem Inventaris FTIK Universitas Semarang</p>
            </div>
        </div>



        <!-- Ilustrasi -->
        <img
            src="<?= base_url('image/login.png') ?>"
            alt="Ilustrasi Inventaris"
            class="left-illustration"
            onerror="this.style.display='none'"
        />
    </div>

    <!-- ============================================================
         Panel Kanan — Form Login
    ============================================================ -->
    <div class="login-right">

        <!-- Logo -->
        <div class="right-logo">
            <img
                src="<?= base_url('image/sidebar.png') ?>"
                alt="Logo"
                onerror="this.style.display='none'"
            />
        </div>

        <!-- Form Area -->
        <div class="form-area">

            <!-- Greeting -->
            <div class="form-greeting">
                <h2 class="form-hello">
                    <span class="form-wave" style="display:inline-block; margin-bottom:0;">👋</span> Selamat Datang!
                </h2>
                <p class="form-desc">
                    Silakan masuk untuk mengakses Sistem Inventaris FTIK USM.
                </p>
            </div>

            <!-- Flash Error atau Locked State -->
            <?php 
                $cooldown_time = session()->get('login_cooldown_time');
                $isLocked = false;
                $lockMinutes = 0;
                if ($cooldown_time && time() < $cooldown_time) {
                    $isLocked = true;
                    $lockMinutes = ceil(($cooldown_time - time()) / 60);
                }
            ?>
            
            <?php if ($isLocked || session()->getFlashdata('error_title') || session()->getFlashdata('error')): ?>
                <?php 
                    $errTitle = $isLocked ? 'Akses Terkunci Sementara' : (session()->getFlashdata('error_title') ?: 'Login Gagal');
                    $errDesc  = $isLocked ? 'Terlalu banyak percobaan login gagal dari perangkat ini.' : (session()->getFlashdata('error_desc') ?: session()->getFlashdata('error'));
                    $attempts = $isLocked ? null : session()->getFlashdata('attempts_left');
                    $cooldown = $isLocked ? $lockMinutes : session()->getFlashdata('cooldown_minutes');
                ?>
                <div class="login-alert" id="loginAlert" style="background:#FEF2F2;border:1px solid #FCA5A5;border-radius:12px;padding:16px;color:#991B1B;display:flex;gap:12px;align-items:flex-start;margin-bottom:24px;box-shadow:0 2px 4px rgba(239,68,68,0.05);">
                    <i class="ri-error-warning-fill" style="font-size:20px;color:#EF4444;margin-top:2px;"></i>
                    <div style="flex:1;">
                        <h4 style="margin:0 0 4px 0;font-size:14px;font-weight:700;color:#991B1B;"><?= esc($errTitle) ?></h4>
                        <p style="margin:0;font-size:13px;color:#B91C1C;line-height:1.5;">
                            <?= esc($errDesc) ?>
                        </p>
                        <?php if ($attempts !== null && $attempts > 0): ?>
                            <div style="margin-top:8px;font-size:12px;font-weight:600;display:inline-block;background:#FEE2E2;padding:4px 10px;border-radius:20px;color:#DC2626;">
                                Sisa percobaan: <?= esc($attempts) ?> kali
                            </div>
                        <?php endif; ?>
                        <?php if ($cooldown !== null): ?>
                            <div style="margin-top:8px;font-size:12px;font-weight:700;display:inline-block;background:#FECACA;padding:4px 10px;border-radius:20px;color:#991B1B;">
                                <i class="ri-timer-line" style="vertical-align:middle;margin-right:2px;"></i> 
                                Coba lagi dalam <?= esc($cooldown) ?> menit
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form -->
            <form action="<?= base_url('login') ?>" method="post" class="login-form" id="loginForm">
                <?= csrf_field() ?>

                <!-- Email / Username -->
                <div class="form-group">
                    <label class="form-label" for="credential">Email/Username</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="ri-mail-line"></i></span>
                        <input
                            type="text"
                            class="form-control"
                            id="credential"
                            name="credential"
                            placeholder="Email Institusi atau Nama Pengguna"
                            value="<?= old('credential') ?>"
                            autocomplete="username"
                            required
                            <?= $isLocked ? 'disabled style="background:#F3F4F6;cursor:not-allowed;opacity:0.6;"' : '' ?>
                        />
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="input-wrapper">
                        <span class="input-icon"><i class="ri-lock-line"></i></span>
                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            placeholder="Kata Sandi"
                            autocomplete="current-password"
                            required
                            <?= $isLocked ? 'disabled style="background:#F3F4F6;cursor:not-allowed;opacity:0.6;"' : '' ?>
                        />
                        <span class="toggle-password" id="togglePassword" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666; font-size: 1.1rem; z-index: 10;">
                            <i class="ri-eye-off-line"></i>
                        </span>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-login" id="btnLogin" <?= $isLocked ? 'disabled style="background:#D1D5DB;cursor:not-allowed;color:#6B7280;box-shadow:none;"' : '' ?>>
                    <?= $isLocked ? 'Akses Terkunci' : 'Login' ?>
                </button>

            </form>

        </div><!-- .form-area -->

    </div><!-- .login-right -->

</div><!-- .login-shell -->

<script>


    // Loading state on submit
    document.getElementById('loginForm').addEventListener('submit', function() {
        var btn = document.getElementById('btnLogin');
        btn.textContent = 'Memproses...';
        btn.disabled = true;
    });

    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    if (togglePassword && password) {
        togglePassword.addEventListener('click', function () {
            // Toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Toggle the eye icon
            const icon = this.querySelector('i');
            if (type === 'text') {
                icon.classList.remove('ri-eye-off-line');
                icon.classList.add('ri-eye-line');
            } else {
                icon.classList.remove('ri-eye-line');
                icon.classList.add('ri-eye-off-line');
            }
        });
    }
</script>

</body>
</html>
