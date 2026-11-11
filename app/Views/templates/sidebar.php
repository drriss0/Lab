<nav class="sidebar" id="sidebar">

    <!-- Brand / Logo -->
    <div class="sidebar-brand">
        <div class="sidebar-logo" style="justify-content: center;">
            <img src="<?= base_url('image/sidebar.png') ?>" alt="Logo Sidebar" onerror="this.style.display='none'" style="max-height: 75px; width: auto;" />
        </div>
        <div class="sidebar-title">SISTEM INVENTARIS FTIK</div>
    </div>

    <?php $role = session()->get('user_role') ?? 'guest'; $am = $active_menu ?? ''; ?>


    <!-- Navigation Items -->
    <div class="sidebar-nav">

        <?php /* ── Dashboard: HANYA admin & TU ── */ ?>
        <?php if (in_array($role, ['admin', 'tu'])): ?>
        <a href="<?= base_url('dashboard') ?>"
           class="nav-item <?= $am === 'dashboard' ? 'active' : '' ?>"
           id="nav-dashboard">
            <span class="nav-icon"><i class="ri-home-4-line"></i></span>
            <span class="nav-label">Dashboard</span>
        </a>
        <?php endif; ?>

        <?php /* ── Data Barang Ruangan: SEMUA role (dengan batasan berbeda di view) ── */ ?>
        <a href="<?= base_url('data-barang-ruangan') ?>"
           class="nav-item <?= $am === 'data-barang-ruangan' ? 'active' : '' ?>"
           id="nav-data-barang">
            <span class="nav-icon"><i class="ri-folder-2-line"></i></span>
            <span class="nav-label">Data Barang Ruangan</span>
        </a>

        <?php /* ── Peminjaman Barang: HANYA admin & TU ── */ ?>
        <?php if (in_array($role, ['admin', 'tu'])): ?>
        <a href="<?= base_url('peminjaman-barang') ?>"
           class="nav-item <?= $am === 'peminjaman-barang' ? 'active' : '' ?>"
           id="nav-peminjaman-barang">
            <span class="nav-icon"><i class="ri-hand-coin-line"></i></span>
            <span class="nav-label">Peminjaman Barang</span>
        </a>
        <?php endif; ?>

        <?php /* ── Pelaporan Barang Rusak: HANYA admin & TU ── */ ?>
        <?php if (in_array($role, ['admin', 'tu'])): ?>
        <a href="<?= base_url('pelaporan-rusak') ?>"
           class="nav-item <?= $am === 'pelaporan-rusak' ? 'active' : '' ?>"
           id="nav-pelaporan-rusak">
            <span class="nav-icon"><i class="ri-alarm-warning-line"></i></span>
            <span class="nav-label">Pelaporan Barang Rusak</span>
        </a>
        <?php endif; ?>

        <?php /* ── Pengajuan Inventaris: admin, dekan, koordinator, TU — TIDAK kepala_lab ── */ ?>
        <?php if (in_array($role, ['admin', 'dekan', 'koordinator_lab', 'tu'])): ?>
        <a href="<?= base_url('pengajuan') ?>"
           class="nav-item <?= $am === 'pengajuan' ? 'active' : '' ?>"
           id="nav-pengajuan">
            <span class="nav-icon"><i class="ri-file-add-line"></i></span>
            <span class="nav-label">Pengajuan Inventaris</span>
        </a>
        <?php endif; ?>

        <?php /* ── Helpdesk: semua role ── */ ?>
        <a href="<?= base_url('helpdesk') ?>"
           class="nav-item <?= $am === 'helpdesk' ? 'active' : '' ?>"
           id="nav-helpdesk">
            <span class="nav-icon"><i class="ri-information-line"></i></span>
            <span class="nav-label">Help Desk</span>
        </a>

        <?php /* ── Kelola Sistem: HANYA admin ── */ ?>
        <?php if ($role === 'admin'): ?>
        <a href="<?= base_url('kelola') ?>"
           class="nav-item <?= in_array($am, ['kelola', 'users', 'kategori', 'kategori-utama', 'jurusan']) ? 'active' : '' ?>"
           id="nav-kelola">
            <span class="nav-icon"><i class="ri-settings-3-line"></i></span>
            <span class="nav-label">Kelola Sistem</span>
        </a>
        <?php endif; ?>

    </div>

    <!-- Footer / Logout -->
    <div class="sidebar-footer">
        <!-- User info link -->
        <a href="<?= base_url('profile') ?>" style="margin-bottom: 16px; padding: 12px 16px; background: <?= $am === 'profile' || $am === 'ubah-password' ? 'rgba(240,90,26,0.15)' : 'rgba(240,90,26,0.08)' ?>; border-radius: 10px; display: flex; align-items: center; gap: 10px; cursor: pointer; transition: 0.2s; text-decoration: none;" onmouseover="this.style.background='rgba(240,90,26,0.15)'" onmouseout="this.style.background='<?= $am === 'profile' || $am === 'ubah-password' ? 'rgba(240,90,26,0.15)' : 'rgba(240,90,26,0.08)' ?>'">
            <div style="width: 36px; height: 36px; border-radius: 50%; background: #F05A1A; display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; flex-shrink: 0;">
                <i class="ri-user-line"></i>
            </div>
            <div style="min-width: 0; flex: 1;">
                <div style="font-size: 13px; font-weight: 700; color: #1F2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc(session()->get('user_nama') ?? 'Admin') ?></div>
                <div style="font-size: 11px; color: #6B7280; text-transform: capitalize;"><?= esc(session()->get('user_role') ?? 'guest') ?></div>
            </div>
        </a>

        <a href="<?= base_url('logout') ?>" class="nav-item" id="nav-logout"
           onclick="return confirm('Apakah Anda yakin ingin keluar?')"
           style="background: #EF4444; color: white; box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2); margin-bottom: 0;">
            <span class="nav-icon"><i class="ri-logout-box-r-line"></i></span>
            <span class="nav-label">Keluar</span>
        </a>
    </div>

</nav>