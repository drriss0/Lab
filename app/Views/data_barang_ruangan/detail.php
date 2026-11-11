<?= $this->extend('templates/layout') ?>

<?= $this->section('head') ?>
<style>
/* ── Selected Row Highlight ────────────────────── */
tr.row-selected, tr.row-selected td {
    background-color: #FFF4ED !important;
    transition: background-color 0.2s ease;
}
tr.row-selected td {
    border-bottom-color: #FCD9C5 !important;
}

/* ── Badge kondisi ─────────────────────────────── */
.badge-baik         { background:#dcfce7!important;color:#16a34a!important;font-weight:700; }
.badge-rusak-ringan { background:#fef9c3!important;color:#ca8a04!important;font-weight:700; }
.badge-perlu-ganti  { background:#fee2e2!important;color:#dc2626!important;font-weight:700; }
.badge-telah        { background:#bbf7d0!important;color:#16a34a!important;font-weight:700;cursor:pointer;position:relative; }
.badge-belum        { background:#f3f4f6!important;color:#6b7280!important;font-weight:600; }
.row-ditarik        { opacity:0.55;background:#F9FAFB; }

/* ── Alokasi popup tooltip ─────────────────────── */
.alokasi-popup {
    position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);
    background:#1f2937;color:white;font-size:11px;padding:6px 10px;border-radius:6px;
    white-space:nowrap;z-index:999;pointer-events:none;display:none;
}
.badge-telah:hover .alokasi-popup { display:block; }

/* ── Action buttons ────────────────────────────── */
.aksi-btn {
    display:inline-flex;align-items:center;justify-content:center;
    width:28px;height:28px;border:none;border-radius:6px;
    cursor:pointer;font-size:13px;transition:.15s;text-decoration:none;
}
.aksi-btn:hover { filter:brightness(.85);transform:scale(1.08); }

/* ── Unit badge (nomor satuan) ─────────────────── */
.unit-badge {
    display:inline-flex;align-items:center;justify-content:center;
    min-width:28px;height:22px;padding:0 7px;
    background:#EEF2FF;color:#4F46E5;
    border:1px solid #C7D2FE;border-radius:5px;
    font-size:11px;font-weight:700;font-family:monospace;
    white-space:nowrap;
}

/* ── Filter Tabs ───────────────────────────────── */
.cat-tabs {
    display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;
    padding:0 4px;
}
.cat-tab {
    display:inline-flex;align-items:center;gap:6px;
    padding:6px 14px;border-radius:20px;border:1px solid #E5E7EB;
    background:white;color:#6B7280;font-size:13px;font-weight:600;
    cursor:pointer;transition:.15s;white-space:nowrap;
}
.cat-tab:hover     { border-color:#F05A1A;color:#F05A1A; }
.cat-tab.active    { background:#F05A1A;color:white;border-color:#F05A1A; }
.cat-tab .count    { background:rgba(0,0,0,.12);border-radius:10px;padding:1px 7px;font-size:11px; }
.cat-tab.active .count { background:rgba(255,255,255,.25); }

/* ── Group separator row ───────────────────────── */
.group-separator td {
    background:#FFF8F5;padding:6px 16px;
    font-size:12px;font-weight:700;color:#F05A1A;
    letter-spacing:.5px;text-transform:uppercase;
    border-top:2px solid #FFDDC9;
}

/* ── Migrasi banner ────────────────────────────── */
.migr-banner {
    background:#FEF3C7;border:1px solid #FDE68A;border-radius:10px;
    padding:12px 18px;margin-bottom:20px;
    display:flex;align-items:center;gap:12px;font-size:13px;color:#92400E;
}
.migr-banner a {
    background:#F59E0B;color:white;border-radius:6px;padding:6px 14px;
    font-weight:600;font-size:12px;text-decoration:none;white-space:nowrap;
}
.migr-banner a:hover { background:#D97706; }

/* ============================================================
   PRINT STYLES — Halaman Detail Ruangan/Kelas
============================================================ */
@media screen {
    #print-report { display: none; }
}
@media print {
    .app-shell { display: block !important; }
    .sidebar   { display: none !important; }
    .main-content { margin-left:0 !important; padding:0 !important; width:100% !important; }
    .page-content { padding:0 !important; }
    .page-footer  { display: none !important; }
    #content-screen { display: none !important; }
    #print-report   { display: block !important; }

    #print-report {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        font-size: 12px; color: #1F2937;
        padding: 0; line-height: 1.5; background: white;
    }
    /* Sembunyikan toolbar CI */
    #toolbarContainer, .print-hide { display: none !important; }
    
    #print-report table {
        width: 100%; border-collapse: collapse; margin-bottom: 24px;
    }
    #print-report table th, #print-report table td {
        border: 1px solid #CBD5E1; padding: 10px 12px; font-size: 11px;
    }
    #print-report table thead tr th {
        background: #F05A1A !important; color: white !important;
        -webkit-print-color-adjust: exact; print-color-adjust: exact;
        font-weight: 700; text-align: center; text-transform: uppercase;
        border-color: #EA580C;
    }
    #print-report .pr-section-title {
        font-weight: 700; font-size: 14px; color: #F05A1A; margin: 24px 0 12px 0;
    }
    #print-report .pr-info-table td:first-child {
        width: 35%; font-weight: 600; color: #F05A1A;
        background: #FFF3EE !important;
        -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }
    #print-report .pr-footer {
        margin-top: 40px; display: flex; justify-content: flex-end; font-size: 12px; color: #374151;
        page-break-inside: avoid; break-inside: avoid;
    }
    #print-report .pr-ttd { text-align: center; min-width: 220px; page-break-inside: avoid; break-inside: avoid; }
    #print-report .pr-ttd .garis {
        border-top: 1px solid #374151; margin-top: 70px; padding-top: 6px; font-weight: 500;
    }
    
    /* Sembunyikan Logo CI4 Debug Toolbar & pagination */
    #toolbarContainer,
    .pagination-wrapper, .dataTables_info, .dataTables_paginate, .dataTables_length { display: none !important; }
    
    /* Trik Layout Table untuk membuat margin-top & bottom semu di setiap halaman */
    table.layout-print { width: 100%; border: none !important; border-collapse: collapse; margin: 0; }
    table.layout-print > thead > tr > td { border: none !important; padding: 0 !important; height: 1.5cm; background: transparent !important; }
    table.layout-print > tfoot > tr > td { border: none !important; padding: 0 !important; height: 1.5cm; background: transparent !important; }
    table.layout-print > tbody > tr > td { border: none !important; padding: 0 1.5cm !important; background: transparent !important; }
    
    @page { margin: 0; }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php $r = $ruangan;

/* ── Kumpulkan kategori unik untuk tab filter ──────────────────────── */
$db = \Config\Database::connect();
// Ambil kategori unik (kategori utama seperti Furnitur, Elektronik) dari tabel barang
$allCategories = $db->query("SELECT DISTINCT kategori AS nama FROM barang WHERE kategori IS NOT NULL AND kategori != ''")->getResultArray();

$kategoriList = [];
$defaults = model('App\Models\KategoriUtamaModel')->findAll();
foreach ($defaults as $def) {
    $kategoriList[$def['nama_kategori']] = 0;
}
foreach ($allCategories as $row) {
    if (!empty(trim($row['nama']))) {
        $kategoriList[trim($row['nama'])] = 0;
    }
}

// Hitung barang di ruangan ini
foreach ($barang_list as $b) {
    if ($b['status'] !== 'digunakan') continue;
    $kat = $b['kategori'] ?? 'Lainnya';
    if (! isset($kategoriList[$kat])) $kategoriList[$kat] = 0;
    $kategoriList[$kat]++;
}
ksort($kategoriList);

/* ── Jumlah unit aktif (untuk JS) ─────────────────────────────────── */
$activeCount = 0;
foreach ($barang_list as $b) {
    if ($b['status'] === 'digunakan') $activeCount++;
}
?>

<div id="content-screen">
<!-- Breadcrumb / Back Button -->
<div class="print-hide" style="display: flex; align-items: center; margin-bottom: 24px; padding-top: 8px;">
    <div style="font-size: 20px; font-weight: 700; color: #1F2937; display: flex; align-items: center; gap: 12px;">
        <a href="<?= base_url('data-barang-ruangan') ?>" style="color: #4B5563; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; text-decoration: none; border-radius: 10px; background: #F3F4F6; transition: all 0.2s; border: 1px solid #E5E7EB;" onmouseover="this.style.background='#E5E7EB'; this.style.color='#1F2937';" onmouseout="this.style.background='#F3F4F6'; this.style.color='#4B5563';" title="Kembali ke Data Barang Ruangan">
            <i class="ri-arrow-left-line" style="font-size: 20px;"></i>
        </a>
        <span>Laporan Aset Ruangan: <span style="color: var(--primary);"><?= esc($r['nama_ruangan']) ?></span></span>
    </div>
</div>

<!-- Flash -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success flash-alert" style="margin-bottom:16px;"><i class="ri-checkbox-circle-line"></i> <?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger flash-alert" style="margin-bottom:16px;"><i class="ri-error-warning-line"></i> <?= session()->getFlashdata('error') ?></div>
<?php endif; ?>
<!-- Header Info -->
<div style="display:flex;flex-direction:column;gap:0;margin-bottom:24px;background:white;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.05);border:1px solid #F3F4F6;overflow:hidden;">
    <?php if (!empty($r['foto_ruangan']) && file_exists(FCPATH . 'uploads/ruangan/' . $r['foto_ruangan'])): ?>
    <div style="width: 100%; height: 240px; background: url('<?= base_url('uploads/ruangan/' . $r['foto_ruangan']) ?>') center/cover no-repeat; border-bottom: 1px solid #E5E7EB;"></div>
    <?php endif; ?>
    
    <div style="padding:20px 24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div style="display:flex; gap:16px; align-items:center;">
            <?php if (empty($r['foto_ruangan']) || !file_exists(FCPATH . 'uploads/ruangan/' . $r['foto_ruangan'])): ?>
            <div style="width: 64px; height: 64px; border-radius: 12px; background: #FFF3EE; color: #F05A1A; display: flex; align-items: center; justify-content: center; font-size: 32px; border: 1px solid #FCD9C5; flex-shrink: 0;">
                <i class="ri-door-line"></i>
            </div>
            <?php endif; ?>
            <div>
                <div style="font-size:26px;font-weight:800;color:#F05A1A;margin-bottom:4px;"><?= esc($r['nama_ruangan']) ?></div>
                <div style="font-size:15px;font-weight:600;color:#374151;display:flex;align-items:center;gap:6px;">
                    <i class="ri-user-star-line"></i> <?= esc($r['penanggung_jawab'] ?? '—') ?>
                </div>
            </div>
        </div>
        <?php
            $status = $r['status_ruangan'] ?? 'Bisa Digunakan';
            $stBg   = $status === 'Bisa Digunakan' ? '#22C55E' : ($status === 'Maintenance' ? '#EAB308' : '#EF4444');
            $stIcon = $status === 'Bisa Digunakan' ? 'ri-check-line' : ($status === 'Maintenance' ? 'ri-refresh-line' : 'ri-close-line');
        ?>
        <div style="background:<?= $stBg ?>;color:white;padding:10px 20px;border-radius:8px;font-weight:700;font-size:14px;display:flex;align-items:center;gap:8px;box-shadow:0 4px 6px rgba(0,0,0,.1);">
            <i class="<?= $stIcon ?>" style="font-size: 20px;"></i> <?= esc($status) ?>
        </div>
    </div>
</div>

<!-- Stats Cards (ringkasan per kategori) -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin-bottom:28px;">
    <?php 
    $cardStyles = [
        ['icon' => 'ri-computer-line',       'clr' => '#3B82F6', 'bg' => '#EFF6FF', 'border' => '#BFDBFE'],
        ['icon' => 'ri-archive-drawer-line', 'clr' => '#8B5CF6', 'bg' => '#F5F3FF', 'border' => '#DDD6FE'],
        ['icon' => 'ri-tools-line',          'clr' => '#10B981', 'bg' => '#ECFDF5', 'border' => '#A7F3D0'],
        ['icon' => 'ri-plug-line',           'clr' => '#F59E0B', 'bg' => '#FEF3C7', 'border' => '#FDE68A'],
        ['icon' => 'ri-file-copy-2-line',    'clr' => '#EC4899', 'bg' => '#FDF2F8', 'border' => '#FBCFE8'],
        ['icon' => 'ri-briefcase-4-line',    'clr' => '#6366F1', 'bg' => '#EEF2FF', 'border' => '#C7D2FE'],
    ];
    $cards = [
        ['Total Barang', $total_barang, 'ri-box-3-line', '#F05A1A', '#FFF3EE', '#FFDDC9']
    ];
    $i = 0;
    foreach ($kategoriStats as $kName => $kVal) {
        $style = $cardStyles[$i % count($cardStyles)];
        $cards[] = [$kName, $kVal, $style['icon'], $style['clr'], $style['bg'], $style['border']];
        $i++;
    }
    ?>
    <?php foreach ($cards as [$label, $val, $icon, $clr, $bg, $border]): ?>
    <div style="background:white;padding:18px;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.05);border:1px solid #F3F4F6;display:flex;flex-direction:column;gap:10px;">
        <div style="display:flex;align-items:center;gap:10px;">
            <div style="width:40px;height:40px;background:<?= $bg ?>;border-radius:10px;display:flex;align-items:center;justify-content:center;color:<?= $clr ?>;font-size:20px;border:1px solid <?= $border ?>;">
                <i class="<?= $icon ?>"></i>
            </div>
            <div style="font-size:28px;font-weight:800;color:<?= $clr ?>;line-height:1;"><?= (int)$val ?></div>
        </div>
        <div style="font-size:12px;font-weight:600;color:#6B7280;"><?= $label ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Table Section -->
<div class="table-section">
    <div class="table-toolbar" style="padding:20px 24px; border-bottom:1px solid #F3F4F6;">
        <div class="table-toolbar-left" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <span class="table-title default-toolbar-item" style="margin:0;">Inventaris Barang Per Unit</span>
            
            <div class="search-box default-toolbar-item" style="width:240px;min-width:200px;background:white;border-radius:8px;">
                <i class="ri-search-line search-icon"></i>
                <input type="text" id="cariBarang" placeholder="Cari barang..." autocomplete="off">
            </div>
        </div>

        <div class="table-toolbar-right" style="margin-left:auto; display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
            <!-- Reset Button -->
            <button onclick="resetSemuaFilter()" id="btnResetFilter" class="btn" style="display:none;background:#FEF2F2;color:#EF4444;border:1px solid #FECACA;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;align-items:center;gap:6px;transition:.2s;">
                <i class="ri-close-circle-line"></i> Reset
            </button>

            <!-- Filter Button -->
            <button onclick="toggleFilterPanel()" id="btnFilter" class="btn" style="background:#F05A1A;color:white;border:none;padding:8px 16px;border-radius:8px;font-size:13px;font-weight:600;display:flex;align-items:center;gap:6px;transition:.2s;">
                <i class="ri-equalizer-line"></i> Filter
                <span id="filterBadge" style="display:none;background:white;color:#F05A1A;border-radius:50%;width:18px;height:18px;font-size:11px;font-weight:800;align-items:center;justify-content:center;line-height:1;">0</span>
            </button>

            <div style="width:1px; height:24px; background:#E5E7EB; margin:0 4px;"></div>
            
            <!-- Export -->
            <button onclick="window.print()" class="btn" style="background:#FFF3EE; color:#F05A1A; border:1px solid #FCD9C5; padding:8px 16px; font-size: 13px;">
                <i class="ri-printer-line" style="font-size:16px;"></i> Export
            </button>
        </div>
    </div>
    
    <!-- Filter Panel (Toggle) -->
    <div id="filterPanel" style="display:none; border-bottom: 1px solid #E5E7EB; background: #F9FAFB;">
        <div style="padding: 18px 24px; display: flex; flex-direction: column; gap: 16px;">
            
            <!-- Filter Kategori -->
            <?php if (!empty($kategoriList)): ?>
            <div>
                <div style="font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                    <i class="ri-price-tag-3-line" style="font-size:13px;"></i> Kategori
                </div>
                <div class="cat-tabs" id="kategoriPills" style="margin-bottom:0;">
                    <button type="button" onclick="setFilterKategori('semua')" class="cat-tab active" data-val="semua">Semua Kategori</button>
                    <?php foreach ($kategoriList as $katNama => $katCount): ?>
                    <button type="button" onclick="setFilterKategori('<?= strtolower(esc($katNama, 'js')) ?>')" class="cat-tab" data-val="<?= strtolower(esc($katNama, 'js')) ?>"><?= esc($katNama) ?></button>
                    <?php endforeach; ?>
                </div>
                <input type="hidden" id="filterKategoriVal" value="semua">
            </div>
            <?php endif; ?>

            <!-- Filter Kondisi -->
            <div>
                <div style="font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                    <i class="ri-heart-pulse-line" style="font-size:13px;"></i> Kondisi Barang
                </div>
                <div class="cat-tabs" id="kondisiPills" style="margin-bottom:0;">
                    <button type="button" onclick="setFilterKondisi('semua')" class="cat-tab active" data-val="semua">Semua Kondisi</button>
                    <button type="button" onclick="setFilterKondisi('badge-baik')" class="cat-tab" data-val="badge-baik"><i class="ri-check-line" style="color:#16a34a;"></i> Baik</button>
                    <button type="button" onclick="setFilterKondisi('badge-bermasalah')" class="cat-tab" data-val="badge-bermasalah"><i class="ri-tools-line" style="color:#ca8a04;"></i> Diperbaiki</button>
                </div>
                <input type="hidden" id="filterKondisiVal" value="semua">
            </div>

            <!-- Filter Status Data -->
            <div>
                <div style="font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                    <i class="ri-database-2-line" style="font-size:13px;"></i> Status Data
                </div>
                <div class="cat-tabs" id="statusDataPills" style="margin-bottom:0;">
                    <button type="button" onclick="setFilterStatusData('aktif')" class="cat-tab active" data-val="aktif"><i class="ri-map-pin-line"></i> Di Ruangan Ini</button>
                    <button type="button" onclick="setFilterStatusData('riwayat')" class="cat-tab" data-val="riwayat"><i class="ri-history-line"></i> Riwayat Keluar</button>
                    <button type="button" onclick="setFilterStatusData('semua')" class="cat-tab" data-val="semua"><i class="ri-list-check"></i> Tampilkan Semua</button>
                </div>
                <input type="hidden" id="filterStatusDataVal" value="aktif">
            </div>
            
        </div>
    </div>


    <div class="data-table-wrapper">
        <table class="data-table" id="tabelBarangRuangan">
            <thead>
                <tr>
                    <?php if (can('admin','koordinator_lab')): ?>
                    <th style="width:30px;text-align:center;"><input type="checkbox" id="checkAllTarik" style="accent-color:#F05A1A;"></th>
                    <?php endif; ?>
                    <th style="width:48px;text-align:center;">No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th style="text-align:center;width:80px;">No. Unit</th>
                    <th>Kategori</th>
                    <th style="text-align:center;">Kondisi</th>
                    <th style="text-align:center;">Tgl Distribusi</th>
                    <th style="text-align:center;">Lokasi Alokasi</th>
                    <th style="text-align:center;width:<?= can('admin','koordinator_lab') ? '130px' : '60px' ?>;">Aksi</th>
                </tr>
            </thead>
            <tbody>

                    <?php
                    $noUrut                = 0;
                    $prevBarang            = null;
                    $unitCounterPerBarang  = []; // track counter per kode_barang di ruangan ini
                    foreach ($barang_list as $b):
                        $isDitarik    = in_array($b['status'], ['ditarik', 'digudang', 'pelaporan_rusak']);
                        $kondisi      = $b['kondisi'] ?? 'Baik';
                        $kondisiLower = strtolower(trim($kondisi));
                        
                        if (in_array($kondisiLower, ['rusak berat', 'rusak', 'tidak bisa digunakan'])) {
                            $kondisiClass = 'badge-rusak-berat';
                            $kondisiIcon = '<i class="ri-close-line"></i>';
                        } elseif (str_contains($kondisiLower, 'rusak') || str_contains($kondisiLower, 'perbai') || str_contains($kondisiLower, 'ganti')) {
                            $kondisiClass = 'badge-rusak-ringan';
                            $kondisiIcon = '<i class="ri-tools-line"></i>';
                        } else {
                            $kondisiClass = 'badge-baik';
                            $kondisiIcon = '<i class="ri-check-line"></i>';
                        }
                        $tglDist      = $b['tanggal'] ? date('d/m/y', strtotime($b['tanggal'])) : '—';
                        $sudahAlokasi = !empty($b['alokasi_ke_ruangan']);
                        $katLower     = strtolower($b['kategori'] ?? 'lainnya');

                        $kodeBarang = $b['kode_barang'] ?? ('_id_' . $b['id_barang']);
                        $unitNo = $b['no_unit'] ?? '–';

                        if (! $isDitarik) $noUrut++;

                        if (! $isDitarik) $prevBarang = $b['id_barang'];
                    ?>

                    <tr
                        class="<?= $isDitarik ? 'row-ditarik riwayat-row' : '' ?>"
                        data-nama="<?= strtolower(esc($b['nama_barang'])) ?>"
                        data-kat="<?= $katLower ?>"
                        data-ditarik="<?= $isDitarik ? '1' : '0' ?>"
                        data-kondisi="<?= $kondisiClass ?>"
                    >
                        <?php if (can('admin','koordinator_lab')): ?>
                        <td style="text-align:center;">
                            <input type="checkbox" name="id_distribusi[]" class="check-tarik" data-type="<?= ($isDitarik || $sudahAlokasi) ? 'history' : 'active' ?>" value="<?= encode_id($b['id_distribusi']) ?>" style="accent-color:#F05A1A;" onchange="updateBulkTarikBtn()">
                        </td>
                        <?php endif; ?>
                        <td style="text-align:center;color:#9CA3AF;font-size:12px;"><?= $isDitarik ? '–' : $noUrut ?></td>
                        <td>
                            <div style="font-size:12px;color:#4B5563;font-weight:600;"><?= esc($b['kode_barang']) ?></div>
                        </td>
                        <td>
                            <div style="font-weight:600;color:#374151;"><?= esc($b['nama_barang']) ?></div>
                        </td>
                        <td style="text-align:center;">
                            <?php if (!$isDitarik): ?>
                                <span class="unit-badge">#<?= $unitNo ?></span>
                            <?php else: ?>
                                <span class="unit-badge" style="background:#F3F4F6;color:#9CA3AF;border:1px solid #E5E7EB;">#<?= $unitNo ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="color:#6B7280;font-size:13px;"><?= esc($b['kategori'] ?? '—') ?></td>
                        <td style="text-align:center;">
                            <span class="badge <?= $kondisiClass ?>" style="font-size:11px;padding:3px 10px; display:inline-flex; align-items:center; gap:4px;">
                                <?= $kondisiIcon ?> <?= esc($kondisi) ?>
                            </span>
                        </td>
                        <td style="text-align:center;font-size:13px;color:#6B7280;"><?= $tglDist ?></td>
                        <td style="text-align:center;">
                            <?php if ($sudahAlokasi): ?>
                                <span class="badge badge-telah" style="font-size:11px;padding:4px 12px;display:inline-flex;align-items:center;gap:4px;" title="Tgl Alokasi: <?= $b['tanggal_alokasi'] ? date('d/m/Y', strtotime($b['tanggal_alokasi'])) : '—' ?>">
                                    Ke: <?= esc($b['nama_ruangan_alokasi'] ?? '—') ?> <i class="ri-arrow-right-up-line"></i>
                                </span>
                            <?php elseif ($isDitarik): ?>
                                <span class="badge" style="background:#F3F4F6;color:#6B7280;font-size:11px;padding:4px 12px;border-radius:20px;">
                                    <?php if ($b['status'] === 'pelaporan_rusak') echo 'Pelaporan Rusak';
                                          elseif ($b['status'] === 'digudang') echo 'Di Gudang';
                                          else echo 'Dikembalikan ke Gudang'; ?>
                                </span>
                            <?php else: ?>
                                <span class="badge badge-belum" style="font-size:11px;padding:4px 12px;">Ruangan Ini</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;">
                            <?php if (!$isDitarik): ?>
                            <div style="display:flex;gap:4px;justify-content:center;">
                                <!-- Info Detail: semua role bisa lihat -->
                                <button type="button" class="aksi-btn" style="background:#3B82F6;color:white;"
                                    onclick="openModal('modalInfo_<?= $b['id_distribusi'] ?>')"
                                    title="Lihat Detail">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <?php if (can('admin', 'koordinator_lab')): ?>
                                <!-- Alokasi Ulang ke Ruangan Lain -->
                                <button type="button" class="aksi-btn" style="background:#8B5CF6;color:white;"
                                    onclick="openModalAlokasiUlang('<?= encode_id($b['id_distribusi']) ?>', '<?= addslashes(htmlspecialchars($b['nama_barang'], ENT_COMPAT)) ?>', <?= (int)$unitNo ?>)"
                                    title="Pindahkan ke Ruangan Lain">
                                    <i class="ri-exchange-box-line"></i>
                                </button>
                                <!-- Lapor Rusak -->
                                <button type="button" class="aksi-btn" style="background:#EF4444;color:white;"
                                    onclick="openModalLaporRusak('<?= encode_id($b['id_distribusi']) ?>', '<?= addslashes(htmlspecialchars($b['nama_barang'], ENT_COMPAT)) ?>', <?= (int)$unitNo ?>)"
                                    title="Laporkan Rusak">
                                    <i class="ri-alarm-warning-line"></i>
                                </button>
                                <!-- Tarik ke Gudang -->
                                <form method="post" action="<?= base_url('barang/distribusi/' . encode_id($b['id_distribusi']) . '/tarik') ?>" style="display:inline;"
                                    onsubmit="return confirm('Tarik unit #<?= $unitNo ?> <?= addslashes(htmlspecialchars($b['nama_barang'], ENT_COMPAT)) ?> ke gudang?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="aksi-btn" style="background:#F05A1A;color:white;" title="Tarik ke Gudang">
                                        <i class="ri-arrow-go-back-line"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                            <?php else: ?>
                                <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                                    <?php if ($sudahAlokasi): ?>
                                        <span style="font-size:11px;color:#8B5CF6;font-weight:600;font-style:italic;">Dipindah</span>
                                    <?php elseif ($b['status'] === 'pelaporan_rusak'): ?>
                                        <span style="font-size:11px;color:#EF4444;font-weight:600;font-style:italic;">Dilaporkan</span>
                                    <?php elseif ($b['status'] === 'digudang'): ?>
                                        <span style="font-size:11px;color:#F05A1A;font-weight:600;font-style:italic;">Di Gudang</span>
                                    <?php else: ?>
                                        <span style="font-size:11px;color:#F05A1A;font-weight:600;font-style:italic;">Ditarik</span>
                                    <?php endif; ?>

                                    <?php if (can('admin', 'koordinator_lab') && in_array($b['status'], ['ditarik', 'digudang'])): ?>
                                        <form method="post" action="<?= base_url('barang/distribusi/' . encode_id($b['id_distribusi']) . '/hapus') ?>" style="display:inline;margin:0;" onsubmit="return confirm('Hapus permanen riwayat barang ini dari ruangan?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="aksi-btn" style="background:#FEF2F2;color:#EF4444;border:1px solid #FECACA;padding:5px 6px;border-radius:6px;cursor:pointer;font-size:15px;display:flex;align-items:center;transition:0.2s;" onmouseover="this.style.background='#FEE2E2'" onmouseout="this.style.background='#FEF2F2'" title="Hapus Riwayat Permanen">
                                                <i class="ri-delete-bin-5-line"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Modal Info Detail per Unit -->
                    <?php if (!$isDitarik): ?>
                    <div class="modal-overlay" id="modalInfo_<?= $b['id_distribusi'] ?>">
                        <div class="modal-box" style="max-width:560px;">
                            <div class="modal-header">
                                <div class="modal-title">Detail Barang Ruangan</div>
                                <button type="button" class="modal-close" onclick="closeModal('modalInfo_<?= $b['id_distribusi'] ?>')"><i class="ri-close-line"></i></button>
                            </div>
                            <div class="modal-body" style="padding-bottom:24px;">
                                
                                <!-- Header Block -->
                                <div style="background:#F9FAFB;padding:20px;border-radius:12px;margin-bottom:24px;border:1px solid #F3F4F6;">
                                    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                                        <div>
                                            <div style="font-size:12px;color:#6B7280;margin-bottom:4px;display:flex;align-items:center;gap:6px;"><i class="ri-barcode-line"></i> Kode: <?= esc($b['kode_barang']) ?></div>
                                            <div style="font-size:20px;font-weight:800;color:#1F2937;">
                                                <?= esc($b['nama_barang']) ?> <span class="unit-badge" style="font-size:13px;vertical-align:middle;margin-left:4px;">#<?= $unitNo ?></span>
                                            </div>
                                        </div>
                                        <div style="text-align:right;">
                                            <span style="font-size:13px;padding:6px 14px;box-shadow:0 2px 4px rgba(0,0,0,0.05);border-radius:20px;font-weight:700;color:#15803D;background:#DCFCE7;">
                                                <?= esc($kondisi) ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;padding-top:16px;border-top:1px dashed #E5E7EB;">
                                        <div>
                                            <div style="font-size:11px;color:#6B7280;display:flex;align-items:center;gap:4px;margin-bottom:4px;"><i class="ri-git-merge-line"></i> Kategori</div>
                                            <div style="font-size:14px;font-weight:600;color:#374151;text-transform:capitalize;"><?= esc($b['kategori'] ?? '—') ?></div>
                                        </div>
                                        <div>
                                            <div style="font-size:11px;color:#6B7280;display:flex;align-items:center;gap:4px;margin-bottom:4px;"><i class="ri-price-tag-3-line"></i> Jenis Barang</div>
                                            <div style="font-size:14px;font-weight:600;color:#374151;"><?= esc($b['jenis_barang'] ?? '—') ?></div>
                                        </div>
                                        <div>
                                            <div style="font-size:11px;color:#6B7280;display:flex;align-items:center;gap:4px;margin-bottom:4px;"><i class="ri-bookmark-line"></i> Merk</div>
                                            <div style="font-size:14px;font-weight:600;color:#374151;"><?= esc($b['merk'] ?? '—') ?></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Details Block -->
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;padding:0 8px;">
                                    <div>
                                        <div style="font-size:12px;color:#6B7280;margin-bottom:6px;display:flex;align-items:center;gap:4px;"><i class="ri-user-star-line"></i> Penanggung Jawab</div>
                                        <div style="font-size:14px;font-weight:700;color:#F05A1A;text-transform:capitalize;"><?= esc($r['penanggung_jawab'] ?: ($b['penanggung_jawab'] ?: '—')) ?></div>
                                        <div style="font-size:12px;color:#6B7280;margin-top:2px;">Tgl Distribusi: <span style="font-weight:600;color:#374151;"><?= $tglDist ?></span></div>
                                    </div>
                                    <div>
                                        <div style="font-size:12px;color:#6B7280;margin-bottom:6px;display:flex;align-items:center;gap:4px;"><i class="ri-map-pin-line"></i> Status Alokasi</div>
                                        <div style="font-size:14px;font-weight:600;color:#374151;">
                                            <?= $sudahAlokasi ? '<span style="color:#8B5CF6;">Telah Dipindah <i class="ri-arrow-right-line"></i> '.$b['nama_ruangan_alokasi'].'</span>' : '<span style="color:#10B981;">Berada di ruangan ini</span>' ?>
                                        </div>
                                    </div>
                                </div>

                                <div style="padding:0 8px;">
                                    <div style="font-size:12px;color:#6B7280;margin-bottom:8px;display:flex;align-items:center;gap:4px;"><i class="ri-image-line"></i> Foto Barang</div>
                                    <div style="text-align:center;">
                                        <?php if (!empty($b['foto_barang'])): ?>
                                            <img src="<?= base_url('uploads/' . $b['foto_barang']) ?>" alt="Foto <?= esc($b['nama_barang']) ?>" style="max-height: 250px; max-width: 100%; object-fit: contain; border-radius: 8px; border: 1px solid #E5E7EB; margin:0 auto;">
                                        <?php else: ?>
                                            <div style="width:100%; height:120px; display:flex; flex-direction:column; justify-content:center; align-items:center; background:#F9FAFB; border-radius:8px; color:#9CA3AF; border: 1px dashed #D1D5DB; font-size:13px;">
                                                <i class="ri-image-line" style="font-size: 32px; margin-bottom:4px;"></i>
                                                Tidak ada foto barang
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (!empty($barang_list)): ?>
    <div class="pagination-wrapper">
        <span id="infoJumlah">Menampilkan <strong><?= $activeCount ?></strong> unit aktif</span>
    </div>
    <?php endif; ?>
</div>

<!-- Bulk Actions Panel (Floating) -->
<div id="bulkActions" style="display: none; position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); background: white; padding: 12px 24px; border-radius: 100px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); border: 1px solid #E5E7EB; align-items: center; gap: 16px; z-index: 50;">
    <div style="display: flex; align-items: center; gap: 8px;">
        <div style="background: #F05A1A; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;" id="countBulk">0</div>
        <span style="font-size: 14px; font-weight: 600; color: #374151;">terpilih</span>
    </div>
    <div style="width: 1px; height: 24px; background: #E5E7EB;"></div>
    
    <div style="display: flex; gap: 8px;">
        <button type="button" id="btnPindahAction" onclick="openModalBulkAlokasi()" class="btn" style="background: #F5F3FF; color: #8B5CF6; border: 1px solid #DDD6FE; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
            <i class="ri-exchange-box-line"></i> Pindah
        </button>
        
        <button type="button" id="btnLaporAction" onclick="openModalBulkLaporRusak()" class="btn" style="background: #FEF2F2; color: #EF4444; border: 1px solid #FECACA; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
            <i class="ri-alarm-warning-line"></i> Lapor Rusak
        </button>
        
        <button type="button" id="btnTarikAction" onclick="submitBulkTarik()" class="btn" style="background: #FFF3EE; color: #F05A1A; border: 1px solid #FCD9C5; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
            <i class="ri-arrow-go-back-line"></i> Tarik ke Gudang
        </button>

        <button type="button" id="btnHapusAction" onclick="submitBulkHapus()" class="btn" style="background: #FEF2F2; color: #EF4444; border: 1px solid #FECACA; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
            <i class="ri-delete-bin-line"></i> Hapus Riwayat
        </button>
        
        <button type="button" onclick="cancelBulkSelection()" class="btn" style="background: #F3F4F6; color: #4B5563; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid #E5E7EB;">
            Batal
        </button>
    </div>
</div>


<!-- Form tersembunyi untuk tarik massal (dieksekusi via JS) -->
<form id="formBulkTarik" action="<?= base_url('data-barang-ruangan/tarik-bulk') ?>" method="post" style="display:none;">
    <?= csrf_field() ?>
</form>


<!-- ============================================================
     Modal: Alokasi Ulang Bulk (Pindah ke Ruangan Lain Secara Massal)
============================================================ -->
<div class="modal-overlay" id="modalBulkAlokasi">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <div class="modal-title"><span style="color:#8B5CF6;">Pindahkan Unit Massal</span></div>
                <div style="font-size:12px;color:#6B7280;">Memindahkan <strong id="bulk-alokasi-count">0</strong> unit sekaligus ke ruangan lain</div>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('modalBulkAlokasi')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formBulkAlokasi" method="post" action="<?= base_url('data-barang-ruangan/alokasi-ulang-bulk') ?>">
            <?= csrf_field() ?>
            <div id="bulk-alokasi-hidden-inputs"></div>
            <div class="modal-body" style="padding-bottom:24px;">
                <div style="background:#F5F3FF;border:1px solid #DDD6FE;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#6D28D9;">
                    <i class="ri-information-line"></i>
                    Semua unit yang Anda centang akan dipindahkan ke ruangan tujuan di bawah ini sekaligus.
                </div>
                <div class="form-group">
                    <label class="form-label">Ruangan Tujuan</label>
                    <select class="form-control" name="id_ruangan_tujuan" required>
                        <option value="">-- Pilih Ruangan Tujuan --</option>
                        <?php foreach ($semua_ruangan as $sr): ?>
                            <option value="<?= $sr['id'] ?>"><?= esc($sr['nama_ruangan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan (opsional)</label>
                    <input type="text" class="form-control" name="keterangan" pattern=".*[a-zA-Z].*[a-zA-Z].*[a-zA-Z].*" title="Harus mengandung minimal 3 huruf alfabet"
                           placeholder="Contoh: Pindah karena renovasi">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;height:42px;justify-content:center;background:#8B5CF6;box-shadow:0 4px 12px rgba(139,92,246,.3);">
                    <i class="ri-exchange-box-line"></i> Konfirmasi Pindah Unit
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     Modal: Alokasi Ulang (Pindah ke Ruangan Lain) — Per Unit
============================================================ -->
<div class="modal-overlay" id="modalAlokasiUlang">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <div class="modal-title"><span style="color:#8B5CF6;">Pindahkan Unit</span> (<span id="au-nama">—</span>)</div>
                <div style="font-size:12px;color:#6B7280;">Memindahkan <strong>1 unit</strong> ke ruangan lain</div>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('modalAlokasiUlang')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formAlokasiUlang" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom:24px;">
                <div style="background:#F5F3FF;border:1px solid #DDD6FE;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#6D28D9;">
                    <i class="ri-information-line"></i>
                    Hanya <strong>1 unit</strong> ini yang akan dipindahkan.
                </div>
                <div class="form-group">
                    <label class="form-label">Ruangan Tujuan</label>
                    <select class="form-control" name="id_ruangan_tujuan" required>
                        <option value="">-- Pilih Ruangan Tujuan --</option>
                        <?php foreach ($semua_ruangan as $sr): ?>
                            <option value="<?= $sr['id'] ?>"><?= esc($sr['nama_ruangan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan (opsional)</label>
                    <input type="text" class="form-control" name="keterangan" pattern=".*[a-zA-Z].*[a-zA-Z].*[a-zA-Z].*" title="Harus mengandung minimal 3 huruf alfabet"
                           placeholder="Contoh: Pindah karena renovasi">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;height:42px;justify-content:center;background:#8B5CF6;box-shadow:0 4px 12px rgba(139,92,246,.3);">
                    <i class="ri-exchange-box-line"></i> Konfirmasi Pindah Unit
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     Modal: Laporkan Rusak — Per Unit
============================================================ -->
<div class="modal-overlay" id="modalLaporRusak">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <div class="modal-title"><span style="color:#EF4444;">Laporan Kerusakan</span></div>
                <div style="font-size:12px;color:#6B7280;margin-top:2px;">Barang: <strong id="lr-nama">—</strong></div>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('modalLaporRusak')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formLaporRusak" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom:24px;">
                <div class="form-group">
                    <label class="form-label">Tingkat Kerusakan</label>
                    <select class="form-control" name="tingkat_kerusakan" required>
                        <option value="rusak_ringan">Rusak Ringan</option>
                        <option value="rusak_berat">Rusak Berat</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan Kerusakan</label>
                    <input type="text" class="form-control" name="keterangan" pattern=".*[a-zA-Z].*[a-zA-Z].*[a-zA-Z].*" title="Harus mengandung minimal 3 huruf alfabet"
                           placeholder="Contoh: Layar bergaris, kabel putus, dsb">
                </div>
                <div style="background:#FFF1F2;border:1px solid #FECDD3;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#BE123C;">
                    <i class="ri-information-line"></i> Unit ini akan <strong>dihapus dari daftar ruangan</strong> dan masuk ke menu <strong>Pelaporan Barang Rusak</strong>.
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;height:42px;justify-content:center;background:#EF4444;box-shadow:0 4px 12px rgba(239,68,68,.3);">
                    <i class="ri-alarm-warning-line"></i> Konfirmasi Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     Modal: Laporkan Rusak Massal (Bulk)
============================================================ -->
<div class="modal-overlay" id="modalBulkLaporRusak">
    <div class="modal-box">
        <div class="modal-header">
            <div>
                <div class="modal-title"><span style="color:#EF4444;">Laporan Kerusakan Massal</span></div>
                <div style="font-size:12px;color:#6B7280;margin-top:2px;">Melaporkan <strong id="bulk-lapor-count">0</strong> unit sekaligus rusak</div>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('modalBulkLaporRusak')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formBulkLaporRusak" method="post" action="<?= base_url('data-barang-ruangan/lapor-rusak-bulk') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id_ruangan_context" value="<?= esc($ruangan['id']) ?>">
            <div id="bulk-lapor-hidden-inputs"></div>
            <div class="modal-body" style="padding-bottom:24px;">
                <div style="background:#FFF1F2;border:1px solid #FECDD3;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:13px;color:#BE123C;">
                    <i class="ri-information-line"></i>
                    Semua unit yang Anda centang akan dilaporkan rusak dengan keterangan yang sama dan dihapus dari ruangan ini.
                </div>
                <div class="form-group">
                    <label class="form-label">Tingkat Kerusakan</label>
                    <select class="form-control" name="tingkat_kerusakan" required>
                        <option value="rusak_ringan">Rusak Ringan</option>
                        <option value="rusak_berat">Rusak Berat</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan Kerusakan</label>
                    <input type="text" class="form-control" name="keterangan" pattern=".*[a-zA-Z].*[a-zA-Z].*[a-zA-Z].*" title="Harus mengandung minimal 3 huruf alfabet"
                           placeholder="Contoh: Layar bergaris, kabel putus, dsb">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;height:42px;justify-content:center;background:#EF4444;box-shadow:0 4px 12px rgba(239,68,68,.3);">
                    <i class="ri-alarm-warning-line"></i> Konfirmasi Laporan Massal
                </button>
            </div>
        </form>
    </div>
</div>

</div><!-- #content-screen -->

<!-- ============================================================
     LAPORAN CETAK — hanya muncul saat print/export
============================================================ -->
<div id="print-report">
    <table class="layout-print">
        <thead>
            <tr><td></td></tr>
        </thead>
        <tbody>
            <tr><td>

    <!-- KOP SURAT -->
    <div class="pr-kop" style="text-align: center; margin-bottom: 24px; position: relative; border-bottom: 3px double #F05A1A; padding-bottom: 16px;">
        <img src="<?= base_url('assets/img/ftik-logo.png') ?>" alt="Logo" onerror="this.style.display='none'" style="position: absolute; left: 0; top: 0; height: 75px; width: auto;">
        <div style="padding-left: 80px;">
            <div style="font-size: 18px; font-weight: 800; color: #F05A1A; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">Fakultas Teknologi Informasi dan Komunikasi</div>
            <div style="font-size: 15px; font-weight: 700; color: #374151; margin-bottom: 6px;">UNIVERSITAS SEMARANG</div>
            <div style="font-size: 11px; color: #4B5563; line-height: 1.4;">
                Jl. Soekarno Hatta, Tlogosari, Pedurungan, Kota Semarang, Jawa Tengah 50196<br>
                Telp: (024) 6702757 | Email: ftik@usm.ac.id | Website: ftik.usm.ac.id
            </div>
        </div>
    </div>

    <div class="pr-title" style="font-size: 14px; font-weight: 700; text-align: center; margin-bottom: 24px; text-transform: uppercase; color: #374151; letter-spacing: 0.5px; text-decoration: none;">
        Laporan Inventaris Barang Per Ruangan / Kelas Lab
        <div style="font-size: 11px; color: #F05A1A; font-weight: 600; margin-top: 6px; letter-spacing: 0; text-transform: none;">
            Sumber Data: Menu Distribusi Barang
        </div>
    </div>

    <!-- A. INFO RUANGAN -->
    <div class="pr-section-title">A. Informasi Ruangan</div>
    <table class="pr-info-table">
        <tbody>
            <tr>
                <td>Nama Ruangan</td>
                <td><?= esc($r['nama_ruangan']) ?></td>
            </tr>
            <tr>
                <td>Penanggung Jawab</td>
                <td><?= esc($r['penanggung_jawab'] ?? '—') ?></td>
            </tr>
            <tr>
                <td>Kapasitas Meja</td>
                <td><?= (int)($r['kapasitas_meja'] ?? 0) ?> unit</td>
            </tr>
            <tr>
                <td>Kapasitas Kursi</td>
                <td><?= (int)($r['kapasitas_kursi'] ?? 0) ?> unit</td>
            </tr>
            <tr>
                <td>Status Ruangan</td>
                <td><?= esc($r['status_ruangan'] ?? 'Bisa Digunakan') ?></td>
            </tr>
            <tr>
                <td>Total Unit Aset Aktif</td>
                <td><?= $activeCount ?> unit</td>
            </tr>
        </tbody>
    </table>

    <!-- B. DAFTAR INVENTARIS -->
    <div class="pr-section-title">B. Daftar Inventaris Barang</div>
    <table>
        <thead>
            <tr>
                <th style="width:6%">No</th>
                <th style="width:10%">Kode</th>
                <th style="width:8%">No. Unit</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th style="width:14%">Kondisi</th>
                <th style="width:16%">Tgl Distribusi</th>
                <th style="width:16%">Lokasi / Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 0;
            foreach ($barang_list as $b):
                $isDitarik = in_array($b['status'], ['ditarik', 'digudang', 'pelaporan_rusak']);

                // Gunakan No. Unit asli dari database, bukan menghitung manual, agar gap/celah akibat barang rusak tetap terlihat
                $unitNo = $b['no_unit'] ?? '–';

                if (!$isDitarik) $no++;

                $kondisi  = $b['kondisi'] ?? 'Baik';
                $tglDist  = $b['tanggal'] ? date('d/m/Y', strtotime($b['tanggal'])) : '—';
                $sudahAlokasi = !empty($b['alokasi_ke_ruangan']);
                $lokasiStatus = $isDitarik 
                    ? ($sudahAlokasi ? 'Dialihkan ke: ' . esc($b['nama_ruangan_alokasi'] ?? '—') : 'Ditarik ke Gudang')
                    : 'Ruangan Ini';
            ?>
            <tr class="<?= $isDitarik ? 'print-riwayat-row' : '' ?>" style="<?= $isDitarik ? 'display:none;' : '' ?>">
                <td style="text-align:center;"><?= $isDitarik ? '–' : str_pad($no, 2, '0', STR_PAD_LEFT) ?></td>
                <td style="text-align:center;font-weight:700;color:#F05A1A;"><?= esc($b['kode_barang']) ?></td>
                <td style="text-align:center;font-family:monospace;">#<?= $unitNo ?></td>
                <td><?= esc($b['nama_barang']) ?><?= $b['merk'] ? '<br><small>Merk: '.$b['merk'].'</small>' : '' ?></td>
                <td><?= esc($b['kategori'] ?? '—') ?></td>
                <td style="text-align:center;"><?= esc($kondisi) ?></td>
                <td style="text-align:center;"><?= $tglDist ?></td>
                <td style="text-align:center;"><?= $lokasiStatus ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if ($no === 0): ?>
            <tr><td colspan="8" style="text-align:center;">Belum ada barang aktif di ruangan ini</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TANDA TANGAN -->
    <div class="pr-footer">
        <div class="pr-ttd">
            <div>Semarang, <?= date('d F Y') ?></div>
            <div>Koordinator Lab FTIK</div>
            <div class="garis">( __________________________ )</div>
        </div>
    </div>
    
            </td></tr>
        </tbody>
        <tfoot>
            <tr><td></td></tr>
        </tfoot>
    </table>
</div><!-- #print-report -->

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ── Konstanta ─────────────────────────────────────────────────────────────
const activeUnitCount = <?= $activeCount ?>;

// ── DataTables Custom Filter ──────────────────────────────────────────────
$.fn.dataTable.ext.search.push(function(settings, data, dataIndex, rowData, counter) {
    if (settings.nTable.id !== 'tabelBarangRuangan') return true;
    
    var tr = settings.aoData[dataIndex].nTr;
    if (!tr) return true;
    
    // 1. Filter Kategori
    var filterKat = document.getElementById('filterKategoriVal') ? document.getElementById('filterKategoriVal').value : 'semua';
    var trKat = (tr.getAttribute('data-kat') || '').toLowerCase();
    if (filterKat !== 'semua' && !trKat.includes(filterKat)) {
        return false;
    }
    
    // 2. Filter Riwayat (Ditarik)
    var isDitarik = tr.getAttribute('data-ditarik') === '1';
    var filterStatusData = document.getElementById('filterStatusDataVal') ? document.getElementById('filterStatusDataVal').value : 'aktif';
    
    if (filterStatusData === 'aktif' && isDitarik) return false;
    if (filterStatusData === 'riwayat' && !isDitarik) return false;
    
    var kondisi = tr.getAttribute('data-kondisi') || '';
    var filterKondisi = document.getElementById('filterKondisiVal') ? document.getElementById('filterKondisiVal').value : 'semua';
    if (filterKondisi !== 'semua') {
        if (filterKondisi === 'badge-bermasalah') {
            if (kondisi === 'badge-baik') return false; // Sembunyikan yang baik
        } else {
            if (kondisi !== filterKondisi) return false;
        }
    }
    
    return true;
});

// ── Bulk Tarik JS (Mendukung DataTables Multi-page) ──────────────────────
function updateBulkTarikBtn() {
    var dt = $('#tabelBarangRuangan').DataTable();
    
    // Reset background semua baris di semua halaman
    var allRows = dt.rows().nodes();
    $(allRows).removeClass('row-selected');

    // Ambil semua checkbox yang dicentang di seluruh halaman
    var checks = dt.$('.check-tarik:checked');
    
    // Tambahkan class background ke baris yang dicentang
    checks.each(function() {
        $(this).closest('tr').addClass('row-selected');
    });

    const bulkActions = document.getElementById('bulkActions');
    
    if (bulkActions) {
        if (checks.length > 0) {
            bulkActions.style.display = 'flex';
            document.getElementById('countBulk').innerText = checks.length;
            
            // Check types to toggle buttons
            var hasActive = dt.$('.check-tarik:checked[data-type="active"]').length > 0;
            var hasHistory = dt.$('.check-tarik:checked[data-type="history"]').length > 0;
            
            var btnTarik = document.getElementById('btnTarikAction');
            var btnLapor = document.getElementById('btnLaporAction');
            var btnPindah = document.getElementById('btnPindahAction');
            var btnHapus = document.getElementById('btnHapusAction');
            
            if (hasHistory && !hasActive) {
                // Only history selected -> show hapus, hide others
                if (btnTarik) btnTarik.style.display = 'none';
                if (btnLapor) btnLapor.style.display = 'none';
                if (btnPindah) btnPindah.style.display = 'none';
                if (btnHapus) btnHapus.style.display = 'block';
            } else if (hasActive && !hasHistory) {
                // Only active selected -> show normal actions, hide hapus
                if (btnTarik) btnTarik.style.display = 'block';
                if (btnLapor) btnLapor.style.display = 'block';
                if (btnPindah) btnPindah.style.display = 'block';
                if (btnHapus) btnHapus.style.display = 'none';
            } else {
                // Mixed -> invalid for actions
                if (btnTarik) btnTarik.style.display = 'none';
                if (btnLapor) btnLapor.style.display = 'none';
                if (btnPindah) btnPindah.style.display = 'none';
                if (btnHapus) btnHapus.style.display = 'none';
            }
        } else {
            bulkActions.style.display = 'none';
            var checkAll = document.getElementById('checkAllTarik');
            if (checkAll) checkAll.checked = false;
        }
    }
}

function cancelBulkSelection() {
    var checkAll = document.getElementById('checkAllTarik');
    if (checkAll) checkAll.checked = false;
    $('.check-tarik').prop('checked', false);
    updateBulkTarikBtn();
}

$(document).on('change', '#checkAllTarik', function() {
    var dt = $('#tabelBarangRuangan').DataTable();
    var isChecked = $(this).prop('checked');
    
    // Pilih semua checkbox pada baris yang sedang difilter (meskipun di halaman lain)
    if ($.fn.DataTable.isDataTable('#tabelBarangRuangan')) {
        var rows = dt.rows({ search: 'applied' }).nodes();
        $('input.check-tarik', rows).prop('checked', isChecked);
    } else {
        $('.check-tarik').prop('checked', isChecked);
    }
    
    updateBulkTarikBtn();
});

$(document).on('change', '.check-tarik', function() {
    updateBulkTarikBtn();
});

function submitBulkTarik() {
    var dt = $('#tabelBarangRuangan').DataTable();
    var checks = dt.$('.check-tarik:checked');
    if (checks.length === 0) return;

    var msg = `Anda yakin ingin menarik ${checks.length} barang terpilih ke gudang?`;
    if (typeof showSweetAlertConfirm === 'function') {
        showSweetAlertConfirm(msg, function() {
            prosesSubmitBulkTarik(checks);
        });
    } else {
        if (confirm(msg)) {
            prosesSubmitBulkTarik(checks);
        }
    }
}

function prosesSubmitBulkTarik(checks) {
    var form = document.getElementById('formBulkTarik');
    // Bersihkan input hidden yang mungkin sudah ada (mencegah duplikat submit ganda)
    $('.hidden-tarik-input').remove();
    
    checks.each(function() {
        var inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'id_distribusi[]';
        inp.value = this.value;
        inp.className = 'hidden-tarik-input';
        form.appendChild(inp);
    });
    
    // Disable checkbox asli agar tidak terkirim ganda untuk yang terlihat di DOM saat ini
    $('.check-tarik').prop('disabled', true);
    
    form.submit();
}

function submitBulkHapus() {
    var dt = $('#tabelBarangRuangan').DataTable();
    var checks = dt.$('.check-tarik:checked[data-type="history"]');
    if (checks.length === 0) return;

    var msg = `Anda yakin ingin menghapus ${checks.length} riwayat terpilih secara permanen dari ruangan ini?`;
    if (typeof showSweetAlertConfirm === 'function') {
        showSweetAlertConfirm(msg, function() {
            var form = document.getElementById('formBulkTarik');
            // Gunakan rute dari BarangController karena hapusDistribusiBulk tersedia di sana
            form.action = '<?= base_url('barang/distribusi/hapus-bulk') ?>'; 
            $('.hidden-tarik-input').remove();
            
            checks.each(function() {
                var inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'id_distribusi[]';
                inp.value = this.value;
                inp.className = 'hidden-tarik-input';
                form.appendChild(inp);
            });
            form.submit();
        });
    }
}

// ── Search Override (Karena menggunakan DataTables, search bawaan harus disesuaikan) ──
document.getElementById('cariBarang').addEventListener('input', function() {
    var dt = $('#tabelBarangRuangan').DataTable();
    dt.search(this.value).draw();
});

// ── Toggle Riwayat (row-ditarik) ─────────────────────────────────────────
function toggleRiwayat(show) {
    $('#tabelBarangRuangan').DataTable().draw();
    
    // Sinkronkan juga untuk tampilan cetak (Export)
    document.querySelectorAll('.print-riwayat-row').forEach(function(tr) {
        tr.style.display = show ? '' : 'none';
    });
}

// ── Filter JS Functions ──────────────────────────────────────────────────
function toggleFilterPanel() {
    $('#filterPanel').slideToggle(200);
}

function updateFilterBadge() {
    var count = 0;
    if (document.getElementById('filterKategoriVal') && document.getElementById('filterKategoriVal').value !== 'semua') count++;
    if (document.getElementById('filterKondisiVal') && document.getElementById('filterKondisiVal').value !== 'semua') count++;
    if (document.getElementById('filterStatusDataVal') && document.getElementById('filterStatusDataVal').value !== 'aktif') count++;
    
    var badge = document.getElementById('filterBadge');
    var btn = document.getElementById('btnFilter');
    var btnReset = document.getElementById('btnResetFilter');
    
    if (count > 0) {
        badge.style.display = 'inline-flex';
        badge.innerText = count;
        btn.style.background = '#3B82F6'; // Blue when active
        if (btnReset) btnReset.style.display = 'flex';
    } else {
        badge.style.display = 'none';
        btn.style.background = '#F05A1A'; // Orange when empty
        if (btnReset) btnReset.style.display = 'none';
    }
}

function resetSemuaFilter() {
    if (document.getElementById('filterKategoriVal')) {
        document.getElementById('filterKategoriVal').value = 'semua';
        $('#kategoriPills .cat-tab').removeClass('active');
        $('#kategoriPills .cat-tab[data-val="semua"]').addClass('active');
    }
    
    document.getElementById('filterKondisiVal').value = 'semua';
    $('#kondisiPills .cat-tab').removeClass('active');
    $('#kondisiPills .cat-tab[data-val="semua"]').addClass('active');
    
    document.getElementById('filterStatusDataVal').value = 'aktif';
    $('#statusDataPills .cat-tab').removeClass('active');
    $('#statusDataPills .cat-tab[data-val="aktif"]').addClass('active');
    
    updateFilterBadge();
    $('#tabelBarangRuangan').DataTable().draw();
}

function setFilterKategori(val) {
    document.getElementById('filterKategoriVal').value = val;
    $('#kategoriPills .cat-tab').removeClass('active');
    $('#kategoriPills .cat-tab[data-val="'+val+'"]').addClass('active');
    updateFilterBadge();
    $('#tabelBarangRuangan').DataTable().draw();
}

function setFilterKondisi(val) {
    document.getElementById('filterKondisiVal').value = val;
    $('#kondisiPills .cat-tab').removeClass('active');
    $('#kondisiPills .cat-tab[data-val="'+val+'"]').addClass('active');
    updateFilterBadge();
    $('#tabelBarangRuangan').DataTable().draw();
}

function setFilterStatusData(val) {
    document.getElementById('filterStatusDataVal').value = val;
    $('#statusDataPills .cat-tab').removeClass('active');
    $('#statusDataPills .cat-tab[data-val="'+val+'"]').addClass('active');
    updateFilterBadge();
    $('#tabelBarangRuangan').DataTable().draw();
}

function toggleRiwayat(checked) {
    // Legacy function support in case called elsewhere
    setFilterStatusData(checked ? 'semua' : 'aktif');
}



// ── Modal Alokasi Ulang (Per Unit) ───────────────────────────────────────
function openModalAlokasiUlang(idDistribusi, namaBarang, unitNo) {
    var dt = $('#tabelBarangRuangan').DataTable();
    var checks = dt.$('.check-tarik:checked');
    
    var isClickedChecked = false;
    checks.each(function() {
        if (this.value == idDistribusi) isClickedChecked = true;
    });
    
    // UX Pintar: Hanya trigger aksi massal jika tombol yang diklik adalah bagian dari baris yang dicentang.
    if (checks.length > 0 && isClickedChecked) {
        openModalBulkAlokasi();
        return;
    }

    document.getElementById('au-nama').textContent = namaBarang + ' #' + unitNo;
    document.getElementById('formAlokasiUlang').action =
        '<?= base_url('data-barang-ruangan/distribusi') ?>/' + idDistribusi + '/alokasi-ulang';
    openModal('modalAlokasiUlang');
}

function handleTarikIndividual(e, href, namaBarang, unitNo, idDistribusi) {
    e.preventDefault();
    var dt = $('#tabelBarangRuangan').DataTable();
    var checks = dt.$('.check-tarik:checked');
    
    var isClickedChecked = false;
    checks.each(function() {
        if (this.value == idDistribusi) isClickedChecked = true;
    });
    
    if (checks.length > 0 && isClickedChecked) {
        submitBulkTarik();
        return false;
    }
    
    var msg = 'Tarik 1 unit ' + namaBarang + ' #' + unitNo + ' ke gudang?';
    if (typeof showSweetAlertConfirm === 'function') {
        showSweetAlertConfirm(msg, function() {
            window.location.href = href;
        });
    } else {
        if (confirm(msg)) {
            window.location.href = href;
        }
    }
    return false;
}

// ── Modal Alokasi Ulang (Massal/Bulk) ────────────────────────────────────
function openModalBulkAlokasi() {
    var dt = $('#tabelBarangRuangan').DataTable();
    var checks = dt.$('.check-tarik:checked');
    if (checks.length === 0) return;

    document.getElementById('bulk-alokasi-count').innerText = checks.length;
    
    // Siapkan hidden inputs
    var hiddenInputsWrap = document.getElementById('bulk-alokasi-hidden-inputs');
    hiddenInputsWrap.innerHTML = '';
    
    checks.each(function() {
        var inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'id_distribusi[]';
        inp.value = this.value;
        hiddenInputsWrap.appendChild(inp);
    });

    openModal('modalBulkAlokasi');
}

// ── Modal Lapor Rusak ────────────────────────────────────────────────────
function openModalLaporRusak(idDistribusi, namaBarang, unitNo) {
    var dt = $('#tabelBarangRuangan').DataTable();
    var checks = dt.$('.check-tarik:checked');
    
    var isClickedChecked = false;
    checks.each(function() {
        if (this.value == idDistribusi) isClickedChecked = true;
    });
    
    if (checks.length > 0 && isClickedChecked) {
        openModalBulkLaporRusak();
        return;
    }

    document.getElementById('lr-nama').textContent = namaBarang + ' #' + unitNo;
    document.getElementById('formLaporRusak').action =
        '<?= base_url('data-barang-ruangan/distribusi') ?>/' + idDistribusi + '/lapor-rusak';
    openModal('modalLaporRusak');
}

// ── Modal Lapor Rusak (Massal/Bulk) ──────────────────────────────────────
function openModalBulkLaporRusak() {
    var dt = $('#tabelBarangRuangan').DataTable();
    var checks = dt.$('.check-tarik:checked');
    if (checks.length === 0) return;

    document.getElementById('bulk-lapor-count').innerText = checks.length;
    
    // Siapkan hidden inputs
    var hiddenInputsWrap = document.getElementById('bulk-lapor-hidden-inputs');
    hiddenInputsWrap.innerHTML = '';
    
    checks.each(function() {
        var inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'id_distribusi[]';
        inp.value = this.value;
        hiddenInputsWrap.appendChild(inp);
    });

    openModal('modalBulkLaporRusak');
}
</script>
<?= $this->endSection() ?>
