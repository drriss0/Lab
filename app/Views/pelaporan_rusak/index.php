<?= $this->extend('templates/layout') ?>

<?= $this->section('head') ?>
<style>
/* ── Status badges ─────────────────────────────────────── */
@media print {
    /* Sembunyikan elemen UI agar yang tercetak murni tabel data (Export Data) */
    .sidebar, .topbar, .page-breadcrumb, .flash-alert, 
    #section-hero, #section-stats, 
    .table-toolbar, .status-tabs, .pagination-wrapper,
    .dataTables_info, .dataTables_paginate, .dataTables_length, .dataTables_filter,
    a[href*="tingkat="], a.status-tab,
    #tabelPelaporanRusak th:last-child, #tabelPelaporanRusak td:last-child,
    .col-checkbox,
    .print-hide {
        display: none !important;
    }
    
    #print-header, #print-footer { display: block !important; }
    
    .app-shell { display: block !important; }
    .sidebar { display: none !important; }
    .main-content { margin-left: 0 !important; padding: 20px !important; width: 100% !important; box-sizing: border-box !important; }
    
    .table-section { box-shadow: none !important; border-radius: 0 !important; padding: 0 !important; background: transparent !important; border: none !important; }
    .data-table-wrapper { overflow: visible !important; }
    
    /* Font lebih kecil agar muat di kertas */
    .data-table { width: 100% !important; font-size: 11px !important; border-collapse: collapse; table-layout: auto !important; }
    .data-table th, .data-table td { 
        border: 1px solid #CBD5E1 !important; 
        padding: 6px 8px !important;
        vertical-align: middle !important;
        white-space: normal !important;
        word-wrap: break-word !important;
    }
    .data-table th { 
        background: #F05A1A !important;
        color: white !important;
        border: 1px solid #DF4D0F !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        font-weight: bold !important; 
        font-size: 11px !important; 
        text-transform: uppercase; 
    }
    .data-table td {
        background: transparent !important;
        color: #1F2937 !important;
    }

    /* Ubah badge menjadi teks biasa agar lebih rapi */
    .badge {
        background: transparent !important;
        padding: 0 !important;
        border: none !important;
        font-weight: bold !important;
    }

    /* Sembunyikan ikon-ikon dekoratif pada tabel */
    .data-table td i, .data-table th i { display: none !important; }
    
    #toolbarContainer { display: none !important; }
    .pr-ttd { page-break-inside: avoid; break-inside: avoid; text-align: center; width: 220px; float: right; margin-top: 40px; font-size: 12px; color: #1F2937; }
    .pr-ttd .garis { border-top: 1px solid #1F2937; margin-top: 70px; padding-top: 6px; font-weight: bold; }
    
    @page { margin: 0; }
}

.badge-dikembalikan{ background:#EDE9FE!important;color:#6D28D9!important;font-weight:700; }
.badge-proses      { background:#DBEAFE!important;color:#1D4ED8!important;font-weight:700; }
.badge-selesai     { background:#D1FAE5!important;color:#065F46!important;font-weight:700; }
.badge-tidak_bisa_diperbaiki { background:#FEE2E2!important;color:#DC2626!important;font-weight:700; }

/* ── Tingkat kerusakan ─────────────────────────────────── */
.badge-ringan { background:#FEF9C3!important;color:#854D0E!important;font-weight:700; }
.badge-berat  { background:#FEE2E2!important;color:#991B1B!important;font-weight:700; }

/* ── Filter tabs ───────────────────────────────────────── */
.status-tabs { display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;padding:0 4px; }
.status-tab  {
    display:inline-flex;align-items:center;gap:6px;padding:6px 16px;
    border-radius:20px;border:1px solid #E5E7EB;background:white;
    color:#6B7280;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;
    transition:.15s;
}
.status-tab:hover       { border-color:#F05A1A;color:#F05A1A; }
.status-tab.tab-all.active   { background:#F05A1A;color:white;border-color:#F05A1A; }
.status-tab.tab-proses.active{ background:#3B82F6;color:white;border-color:#3B82F6; }
.status-tab.tab-selesai.active{ background:#10B981;color:white;border-color:#10B981; }
.status-tab .tab-count  { background:rgba(0,0,0,.12);border-radius:10px;padding:1px 7px;font-size:11px; }
.status-tab.active .tab-count{ background:rgba(255,255,255,.25); }

/* ── Action buttons ────────────────────────────────────── */
.aksi-btn {
    display:inline-flex;align-items:center;justify-content:center;
    width:30px;height:30px;border:none;border-radius:6px;
    cursor:pointer;font-size:13px;transition:.15s;text-decoration:none;
}
.aksi-btn:hover { filter:brightness(.85);transform:scale(1.08); }

/* ── Row highlight by severity ─────────────────────────── */
.row-berat { border-left:3px solid #EF4444; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
/* ── Hitung jumlah per status untuk tab ──────────────── */
$cntAll    = count($laporan_list);
$cntProses = count(array_filter($laporan_list, fn($l) => $l['status'] === 'sedang_diperbaiki'));
$cntSelesai= count(array_filter($laporan_list, fn($l) => $l['status'] === 'selesai'));
$cntDikembalikan = count(array_filter($laporan_list, fn($l) => $l['status'] === 'dikembalikan'));
?>

<!-- Flash -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success flash-alert" style="margin-bottom:16px;">
        <i class="ri-checkbox-circle-line"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger flash-alert" style="margin-bottom:16px;">
        <i class="ri-error-warning-line"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- ═══════════════════════════════════════════
     Hero Banner
═══════════════════════════════════════════ -->
<div id="section-hero" style="display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap;">
    <!-- Welcome banner -->
    <div style="flex:1;min-width:280px;background:white;border-radius:16px;padding:0 30px;display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;height: 140px;box-shadow:0 1px 3px rgba(0,0,0,.05);">
        <img src="<?= base_url('image/header.png') ?>" alt="Avatar" style="height: 100%; width: auto; position: absolute; bottom: 0; left: 20px;" onerror="this.style.display='none'" />
        <div style="z-index:1; margin-left: 200px; padding: 20px 0;">
            <h1 style="font-size:22px;font-weight:800;color:#374151;margin-bottom:6px;">
                <span style="color:#EF4444;">Pelaporan</span> Barang Rusak
            </h1>
            <p style="font-size:13px;color:#6B7280;line-height:1.5;max-width:360px;margin:0;">
                Kelola dan pantau laporan kerusakan aset inventaris FTIK.<br>
                Perbarui status penanganan setiap laporan dengan mudah.
            </p>
        </div>
        <!-- Decorative icon -->
        <i class="ri-alarm-warning-fill" style="position:absolute;right:-10px;bottom:-10px;font-size:120px;color:rgba(239,68,68,.07);pointer-events:none;"></i>
        <!-- Stats mini -->
        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:12px;padding:14px 22px;text-align:center;min-width:130px;z-index:1;">
            <div style="font-size:32px;font-weight:800;color:#EF4444;line-height:1;margin-bottom:4px;"><?= $stats['total_unit'] ?></div>
            <div style="font-size:12px;font-weight:600;color:#EF4444;">Unit Rusak<br>Tercatat</div>
        </div>
    </div>

    <!-- Red action card -->
    <div style="width:260px;background:linear-gradient(135deg,#EF4444,#DC2626);border-radius:16px;padding:22px 24px;display:flex;flex-direction:column;justify-content:center;position:relative;overflow:hidden;flex-shrink:0;box-shadow:0 4px 14px rgba(239,68,68,.35);">
        <i class="ri-tools-fill" style="position:absolute;top:-8px;right:-8px;font-size:90px;color:rgba(255,255,255,.15);transform:rotate(-10deg);pointer-events:none;"></i>
        <div style="color:white;font-size:14px;font-weight:600;line-height:1.5;margin-bottom:14px;z-index:1;">
            Total Laporan<br>Masuk
        </div>
        <div style="color:white;font-size:36px;font-weight:800;z-index:1;line-height:1;"><?= $stats['total_laporan'] ?></div>
        <div style="color:rgba(255,255,255,.7);font-size:12px;margin-top:4px;z-index:1;">laporan terdaftar</div>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     Stats Cards
═══════════════════════════════════════════ -->
<div id="section-stats" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:14px;margin-bottom:28px;">
    <?php foreach ([
        ['Sedang Diproses', $stats['total_proses'],   'ri-tools-line',         '#3B82F6','#EFF6FF','#BFDBFE'],
        ['Selesai',         $stats['total_selesai'],  'ri-checkbox-circle-line','#10B981','#ECFDF5','#A7F3D0'],
        ['Rusak Ringan',    $stats['total_ringan'],   'ri-spam-2-line',        '#F59E0B','#FFFBEB','#FDE68A'],
        ['Rusak Berat',     $stats['total_berat'],    'ri-error-warning-line', '#EF4444','#FEF2F2','#FECACA'],
    ] as [$label, $val, $icon, $clr, $bg, $border]): ?>
    <div style="background:white;padding:18px;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.05);border:1px solid #F3F4F6;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
            <div style="width:40px;height:40px;background:<?= $bg ?>;border-radius:10px;display:flex;align-items:center;justify-content:center;color:<?= $clr ?>;font-size:20px;border:1px solid <?= $border ?>;">
                <i class="<?= $icon ?>"></i>
            </div>
            <div style="font-size:28px;font-weight:800;color:<?= $clr ?>;line-height:1;"><?= $val ?></div>
        </div>
        <div style="font-size:12px;font-weight:600;color:#6B7280;"><?= $label ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ═══════════════════════════════════════════
     Table Section
═══════════════════════════════════════════ -->
<!-- Print-only header -->
<div id="print-header" style="display:none;">
    <div style="text-align: center; margin-bottom: 24px; position: relative; border-bottom: 3px double #F05A1A; padding-bottom: 16px;">
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
    
    <div style="font-size: 14px; font-weight: 700; text-align: center; margin-bottom: 24px; text-transform: uppercase; color: #374151; letter-spacing: 0.5px;">
        Laporan Barang Rusak FTIK
        <div style="font-size: 11px; color: #6B7280; font-weight: 500; margin-top: 6px; letter-spacing: 0; text-transform: none;">
            Dicetak pada: <span id="print-tgl"></span>
        </div>
    </div>
</div>

<div class="table-section">
    <div class="table-toolbar" style="padding:20px 24px 12px;">
        <div class="table-toolbar-left">
            <span class="table-title">Daftar Laporan Kerusakan</span>
            <form method="GET" action="<?= base_url('pelaporan-rusak') ?>" style="margin:0;">
                <?php if ($filter_status ?? null): ?>
                    <input type="hidden" name="status" value="<?= esc($filter_status) ?>" />
                <?php endif; ?>
                <?php if ($filter_tingkat ?? null): ?>
                    <input type="hidden" name="tingkat" value="<?= esc($filter_tingkat) ?>" />
                <?php endif; ?>
                <div class="search-box" style="border-radius:8px;background:white;width:280px;">
                    <i class="ri-search-line search-icon"></i>
                    <input type="text" name="q" id="cariLaporan"
                           placeholder="Cari barang / ruangan..."
                           value="<?= esc($keyword ?? '') ?>"
                           autocomplete="off" />
                </div>
            </form>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <!-- Filter tingkat kerusakan -->
            <a href="<?= base_url('pelaporan-rusak') . ($keyword ? '?q='.urlencode($keyword) : '') ?>"
               style="background:<?= !$filter_tingkat ? '#F05A1A' : 'white' ?>;color:<?= !$filter_tingkat ? 'white' : '#6B7280' ?>;border:1px solid <?= !$filter_tingkat ? '#F05A1A' : '#E5E7EB' ?>;padding:7px 14px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;">
                Semua
            </a>
            <a href="<?= base_url('pelaporan-rusak?tingkat=rusak_ringan') . ($keyword ? '&q='.urlencode($keyword) : '') ?>"
               style="background:<?= $filter_tingkat === 'rusak_ringan' ? '#F59E0B' : 'white' ?>;color:<?= $filter_tingkat === 'rusak_ringan' ? 'white' : '#6B7280' ?>;border:1px solid <?= $filter_tingkat === 'rusak_ringan' ? '#F59E0B' : '#E5E7EB' ?>;padding:7px 14px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;">
                Rusak Ringan
            </a>
            <a href="<?= base_url('pelaporan-rusak?tingkat=rusak_berat') . ($keyword ? '&q='.urlencode($keyword) : '') ?>"
               style="background:<?= $filter_tingkat === 'rusak_berat' ? '#EF4444' : 'white' ?>;color:<?= $filter_tingkat === 'rusak_berat' ? 'white' : '#6B7280' ?>;border:1px solid <?= $filter_tingkat === 'rusak_berat' ? '#EF4444' : '#E5E7EB' ?>;padding:7px 14px;border-radius:6px;font-size:12px;font-weight:600;text-decoration:none;">
                Rusak Berat
            </a>
            <button onclick="window.print()" style="background:#FFF3EE;color:#F05A1A;border:1px solid #FCD9C5;padding:7px 14px;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:4px;transition:.2s;" onmouseover="this.style.background='#FFD5C2';" onmouseout="this.style.background='#FFF3EE';">
                <i class="ri-printer-line"></i> Export
            </button>
        </div>
    </div>

    <!-- Status Filter Tabs -->
    <div class="status-tabs" style="padding:0 24px 8px;">
        <a href="<?= base_url('pelaporan-rusak') . ($keyword ? '?q='.urlencode($keyword) : '') . ($filter_tingkat ? ($keyword ? '&' : '?').'tingkat='.urlencode($filter_tingkat) : '') ?>"
           class="status-tab tab-all <?= !$filter_status ? 'active' : '' ?>">
            <i class="ri-apps-line"></i> Semua
            <span class="tab-count"><?= $cntAll ?></span>
        </a>
        <a href="<?= base_url('pelaporan-rusak?status=sedang_diperbaiki') . ($keyword ? '&q='.urlencode($keyword) : '') . ($filter_tingkat ? '&tingkat='.urlencode($filter_tingkat) : '') ?>"
           class="status-tab tab-proses <?= $filter_status === 'sedang_diperbaiki' ? 'active' : '' ?>">
            <i class="ri-tools-line"></i> Sedang Diproses
            <span class="tab-count"><?= $cntProses ?></span>
        </a>
        <a href="<?= base_url('pelaporan-rusak?status=selesai') . ($keyword ? '&q='.urlencode($keyword) : '') . ($filter_tingkat ? '&tingkat='.urlencode($filter_tingkat) : '') ?>"
           class="status-tab tab-selesai <?= $filter_status === 'selesai' ? 'active' : '' ?>">
            <i class="ri-checkbox-circle-line"></i> Selesai
            <span class="tab-count"><?= $cntSelesai ?></span>
        </a>
        <a href="<?= base_url('pelaporan-rusak?status=dikembalikan') . ($keyword ? '&q='.urlencode($keyword) : '') . ($filter_tingkat ? '&tingkat='.urlencode($filter_tingkat) : '') ?>"
           class="status-tab <?= $filter_status === 'dikembalikan' ? 'active' : '' ?>" style="<?= $filter_status === 'dikembalikan' ? 'background:#E5E7EB;color:#4B5563;border-color:#E5E7EB;' : '' ?>">
            <i class="ri-arrow-go-back-line"></i> Dikembalikan
            <span class="tab-count"><?= $cntDikembalikan ?></span>
        </a>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table" id="tabelPelaporanRusak">
            <thead>
                <tr>
                    <?php if (can('admin','koordinator_lab')): ?>
                    <th class="col-checkbox" style="width:30px;text-align:center;"><input type="checkbox" id="checkAllBulk" style="accent-color:#F05A1A;"></th>
                    <?php endif; ?>
                    <th style="width:44px;text-align:center;">No</th>
                    <th>Kode</th>
                    <th>Barang</th>
                    <th style="text-align:center;">No. Unit</th>
                    <th>Ruangan / Lokasi</th>
                    <th>Keterangan</th>
                    <th style="text-align:center;">Tingkat</th>
                    <th style="text-align:center;">Tgl Lapor</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;width:120px;">Aksi</th>
                </tr>
            </thead>
            <tbody>

                    <?php foreach ($laporan_list as $i => $l):
                        $isBerat = $l['tingkat_kerusakan'] === 'rusak_berat';

                        $statusClass = match($l['status']) {
                            'sedang_diperbaiki'    => 'badge-proses',
                            'selesai'              => 'badge-selesai',
                            'dikembalikan'         => 'badge-dikembalikan',
                            'tidak_bisa_diperbaiki'=> 'badge-tidak_bisa_diperbaiki',
                            default                => 'badge-proses',
                        };
                        $statusLabel = match($l['status']) {
                            'sedang_diperbaiki'    => 'Sedang Diproses',
                            'selesai'              => 'Selesai',
                            'dikembalikan'         => 'Dikembalikan',
                            'tidak_bisa_diperbaiki'=> 'Tidak Bisa Diperbaiki',
                            default                => ucfirst($l['status']),
                        };
                        $tglLapor = $l['tanggal_lapor']
                            ? date('d/m/Y', strtotime($l['tanggal_lapor']))
                            : '—';
                    ?>
                    <tr class="<?= $isBerat ? 'row-berat' : '' ?>">
                        <?php if (can('admin','koordinator_lab')): ?>
                        <td class="col-checkbox" style="text-align:center;">
                            <input type="checkbox" name="laporan_ids[]" value="<?= encode_id($l['id']) ?>" class="check-item-bulk" data-status="<?= $l['status'] ?>" style="accent-color:#F05A1A;">
                        </td>
                        <?php endif; ?>
                        <td style="text-align:center;color:#9CA3AF;font-size:12px;"><?= $i + 1 ?></td>
                        <td>
                            <div style="font-size:12px;color:#4B5563;font-weight:600;"><?= esc($l['kode_barang']) ?></div>
                        </td>
                        <td>
                            <div style="font-weight:700;color:#374151;"><?= esc($l['nama_barang']) ?></div>
                            <div style="font-size:11px;color:#9CA3AF;"><?= $l['merk'] ? esc($l['merk']) : '' ?></div>
                            <?php if ($l['nama_kategori']): ?>
                            <div style="font-size:11px;color:#6B7280;margin-top:1px;"><?= esc($l['nama_kategori']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;font-size:13px;font-weight:700;color:#374151;">
                            <?= $l['no_unit'] ? '#' . esc($l['no_unit']) : '—' ?>
                        </td>
                        <td>
                            <?php if ($l['nama_ruangan']): ?>
                                <div style="display:flex;align-items:center;gap:5px;">
                                    <i class="ri-map-pin-2-fill" style="color:#EF4444;font-size:12px;"></i>
                                    <span style="font-weight:500;color:#374151;font-size:13px;"><?= esc($l['nama_ruangan']) ?></span>
                                </div>
                            <?php elseif (isset($l['tujuan_distribusi']) && $l['tujuan_distribusi'] === 'peminjaman'): ?>
                                <span style="font-weight:600;color:#374151;font-size:13px;">Peminjaman</span>
                            <?php else: ?>
                                <span style="color:#9CA3AF;font-size:13px;">— (Gudang)</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($l['keterangan']): ?>
                                <div style="font-size:12px;color:#4B5563;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                                     title="<?= esc($l['keterangan']) ?>">
                                    <?= esc($l['keterangan']) ?>
                                </div>
                            <?php else: ?>
                                <span style="color:#9CA3AF;font-size:12px;">—</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;">
                            <span class="badge <?= $isBerat ? 'badge-berat' : 'badge-ringan' ?>" style="font-size:11px;padding:3px 10px;">
                                <?= $isBerat ? 'Rusak Berat' : 'Rusak Ringan' ?>
                            </span>
                        </td>
                        <td style="text-align:center;font-size:13px;color:#6B7280;"><?= $tglLapor ?></td>
                        <td style="text-align:center;">
                            <span class="badge <?= $statusClass ?>" style="font-size:11px;padding:3px 10px;">
                                <?= $statusLabel ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex;gap:4px;justify-content:center;">
                                <?php if (can('admin')): ?>
                                <?php if ($l['status'] === 'sedang_diperbaiki'): ?>
                                <!-- Pilih Aksi Perbaikan -->
                                <button type="button" class="aksi-btn" style="background:#3B82F6;color:white;"
                                    onclick="bukaAksiPelaporan('<?= encode_id($l['id']) ?>')"
                                    title="Tandai Selesai / Tidak Bisa Diperbaiki">
                                    <i class="ri-check-line"></i>
                                </button>
                                <?php elseif ($l['status'] === 'selesai'): ?>
                                <!-- Kembali ke Ruangan -->
                                <form method="post" action="<?= base_url('pelaporan-rusak/' . encode_id($l['id']) . '/kembali') ?>" style="display:inline;" onsubmit="return confirm('Kembalikan barang ini ke ruangan?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="aksi-btn" style="background:#10B981;color:white;" title="Kembali ke Ruangan">
                                        <i class="ri-arrow-go-back-line"></i>
                                    </button>
                                </form>

                                <!-- Hapus -->
                                <form method="post" action="<?= base_url('pelaporan-rusak/' . encode_id($l['id']) . '/hapus') ?>" style="display:inline;" onsubmit="return confirm('Hapus laporan ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="aksi-btn" style="background:#EF4444;color:white;" title="Hapus Laporan">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                                <?php elseif ($l['status'] === 'dikembalikan' || $l['status'] === 'tidak_bisa_diperbaiki'): ?>
                                <!-- Hapus (Bekas laporan) -->
                                <form method="post" action="<?= base_url('pelaporan-rusak/' . encode_id($l['id']) . '/hapus') ?>" style="display:inline;" onsubmit="return confirm('Hapus histori laporan ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="aksi-btn" style="background:#EF4444;color:white;" title="Hapus Laporan">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Bulk Actions Panel (Floating) -->
    <?php if (can('admin','koordinator_lab')): ?>
    <div id="bulkActions" style="display: none; position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); background: white; padding: 12px 24px; border-radius: 100px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); border: 1px solid #E5E7EB; align-items: center; gap: 16px; z-index: 50;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="background: #F05A1A; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;" id="countBulk">0</div>
            <span style="font-size: 14px; font-weight: 600; color: #374151;">terpilih</span>
        </div>
        <div style="width: 1px; height: 24px; background: #E5E7EB;"></div>
        
        <form id="formBulkAction" action="<?= base_url('pelaporan-rusak/bulk-action') ?>" method="post" style="display: flex; gap: 8px; margin: 0;">
            <?= csrf_field() ?>
            <div id="bulkInputs"></div>
            <input type="hidden" name="bulk_action" id="bulkActionInput" value="">
            
            <button type="button" id="btnBulkSelesai" onclick="submitBulk('selesai')" class="btn" style="display: none; background: #ECFDF5; color: #10B981; border: 1px solid #A7F3D0; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                <i class="ri-check-double-line"></i> Selesai Diperbaiki
            </button>
            <button type="button" id="btnBulkTidakBisa" onclick="submitBulk('tidak_bisa_diperbaiki')" class="btn" style="display: none; background: #FEF2F2; color: #EF4444; border: 1px solid #FECACA; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                <i class="ri-close-circle-line"></i> Tidak Bisa Diperbaiki
            </button>
            <button type="button" id="btnBulkKembali" onclick="submitBulk('kembali')" class="btn" style="display: none; background: #EFF6FF; color: #3B82F6; border: 1px solid #BFDBFE; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                <i class="ri-arrow-go-back-line"></i> Kembalikan ke Ruangan
            </button>
            <button type="button" id="btnBulkHapus" onclick="submitBulk('hapus')" class="btn" style="display: none; background: #FEF2F2; color: #EF4444; border: 1px solid #FECACA; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                <i class="ri-delete-bin-line"></i> Hapus Laporan
            </button>

            <button type="button" onclick="cancelBulkSelection()" class="btn" style="background: #F3F4F6; color: #4B5563; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid #E5E7EB;">
                Batal
            </button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Print Footer TTD -->
    <div id="print-footer" style="display:none;">
        <div class="pr-ttd">
            <div>Semarang, <span id="print-tgl-ttd"></span></div>
            <div>Koordinator Lab FTIK</div>
            <div class="garis">( _________________________ )</div>
        </div>
    </div>

    <?php if (!empty($laporan_list)): ?>
    <div class="pagination-wrapper">
        <span>Menampilkan <strong><?= count($laporan_list) ?></strong> laporan kerusakan</span>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Aksi Pelaporan -->
<div class="modal-overlay" id="modalAksiPelaporan">
    <div class="modal-box" style="width: 420px; text-align: center; padding: 32px 24px; position: relative;">
        <button class="modal-close" onclick="closeModal('modalAksiPelaporan')" style="background: transparent; color: #1F2937; font-size: 20px; position: absolute; top: 16px; right: 16px; border: none; cursor: pointer;"><i class="ri-close-line"></i></button>
        <div style="font-size: 18px; font-weight: 700; color: #1F2937; margin-bottom: 8px;">Aksi Perbaikan</div>
        <p style="font-size: 13px; color: #6B7280; margin-bottom: 24px;">Pilih status akhir untuk laporan perbaikan ini.</p>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <!-- Opsi 1: Selesai -->
            <form action="" method="post" id="form-aksi-selesai" style="margin: 0;">
                <?= csrf_field() ?>
                <input type="hidden" name="status" value="selesai">
                <button type="submit" style="width: 100%; border: 1px solid #E5E7EB; background: white; border-radius: 12px; padding: 20px 12px; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.borderColor='#10B981'; this.style.boxShadow='0 4px 12px rgba(16,185,129,0.1)';"
                        onmouseout="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none';"
                        onclick="return confirm('Tandai laporan ini sebagai selesai?')">
                    <div style="width: 56px; height: 56px; border-radius: 14px; background: #ECFDF5; color: #10B981; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 12px;">
                        <i class="ri-check-double-line"></i>
                    </div>
                    <div style="font-weight: 600; color: #374151; font-size: 14px;">Selesai<br/>Diperbaiki</div>
                </button>
            </form>

            <!-- Opsi 2: Tidak bisa diperbaiki -->
            <form action="" method="post" id="form-aksi-batal" style="margin: 0;">
                <?= csrf_field() ?>
                <input type="hidden" name="status" value="tidak_bisa_diperbaiki">
                <button type="submit" style="width: 100%; border: 1px solid #E5E7EB; background: white; border-radius: 12px; padding: 20px 12px; cursor: pointer; transition: all 0.2s;"
                        onmouseover="this.style.borderColor='#EF4444'; this.style.boxShadow='0 4px 12px rgba(239,68,68,0.1)';"
                        onmouseout="this.style.borderColor='#E5E7EB'; this.style.boxShadow='none';"
                        onclick="return confirm('Tandai laporan ini sebagai tidak bisa diperbaiki?')">
                    <div style="width: 56px; height: 56px; border-radius: 14px; background: #FEF2F2; color: #EF4444; display: flex; align-items: center; justify-content: center; font-size: 24px; margin: 0 auto 12px;">
                        <i class="ri-close-circle-line"></i>
                    </div>
                    <div style="font-weight: 600; color: #374151; font-size: 14px;">Tidak Bisa<br/>Diperbaiki</div>
                </button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Isi tanggal untuk print header dan TTD
const tglSekarang = new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' });
if (document.getElementById('print-tgl')) {
    document.getElementById('print-tgl').textContent = tglSekarang;
}
if (document.getElementById('print-tgl-ttd')) {
    document.getElementById('print-tgl-ttd').textContent = tglSekarang;
}

// ── Search live ───────────────────────────────────────────────────────
var searchTimeout;
document.getElementById('cariLaporan').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        this.closest('form').submit();
    }, 500);
});

// Aksi Pelaporan
function bukaAksiPelaporan(id) {
    const base = '<?= base_url() ?>';
    document.getElementById('form-aksi-selesai').action = base + 'pelaporan-rusak/' + id + '/update-status';
    document.getElementById('form-aksi-batal').action = base + 'pelaporan-rusak/' + id + '/update-status';
    openModal('modalAksiPelaporan');
}

// Bulk Actions Logic
const bulkPanel = document.getElementById('bulkActions');
const bulkCount = document.getElementById('countBulk');
const bulkInputs = document.getElementById('bulkInputs');
const bulkActionInput = document.getElementById('bulkActionInput');
const btnBulkSelesai = document.getElementById('btnBulkSelesai');
const btnBulkTidakBisa = document.getElementById('btnBulkTidakBisa');
const btnBulkKembali = document.getElementById('btnBulkKembali');
const btnBulkHapus = document.getElementById('btnBulkHapus');

function updateBulkPanel() {
    if (!bulkPanel) return;
    const checked = $('.check-item-bulk:checked');
    if (checked.length > 0) {
        bulkPanel.style.display = 'flex';
        bulkCount.innerText = checked.length;
        
        // Dynamically show/hide buttons based on selected statuses
        const statuses = new Set();
        checked.each(function() {
            statuses.add($(this).attr('data-status'));
        });
        
        const onlyProses = statuses.size === 1 && statuses.has('sedang_diperbaiki');
        const onlySelesai = statuses.size === 1 && statuses.has('selesai');
        const canHapus = !statuses.has('sedang_diperbaiki') && !statuses.has('menunggu');

        btnBulkSelesai.style.display = onlyProses ? 'inline-flex' : 'none';
        btnBulkTidakBisa.style.display = onlyProses ? 'inline-flex' : 'none';
        btnBulkKembali.style.display = onlySelesai ? 'inline-flex' : 'none';
        btnBulkHapus.style.display = canHapus ? 'inline-flex' : 'none';
        
    } else {
        bulkPanel.style.display = 'none';
        $('#checkAllBulk').prop('checked', false);
    }
}

$(document).on('change', '#checkAllBulk', function() {
    var isChecked = $(this).prop('checked');
    
    // Select all check-item-bulk across all pages using DataTables API if available
    var dt = $('#tabelLaporan').DataTable();
    if ($.fn.DataTable.isDataTable('#tabelLaporan')) {
        var rows = dt.rows({ search: 'applied' }).nodes();
        $('input.check-item-bulk', rows).prop('checked', isChecked);
    } else {
        $('.check-item-bulk').prop('checked', isChecked);
    }
    updateBulkPanel();
});

$(document).on('change', '.check-item-bulk', function() {
    updateBulkPanel();
});

function cancelBulkSelection() {
    $('#checkAllBulk').prop('checked', false);
    
    var dt = $('#tabelLaporan').DataTable();
    if ($.fn.DataTable.isDataTable('#tabelLaporan')) {
        var rows = dt.rows().nodes();
        $('input.check-item-bulk', rows).prop('checked', false);
    } else {
        $('.check-item-bulk').prop('checked', false);
    }
    
    updateBulkPanel();
}

function submitBulk(action) {
    var dt = $('#tabelLaporan').DataTable();
    let checked = [];
    if ($.fn.DataTable.isDataTable('#tabelLaporan')) {
        checked = dt.$('.check-item-bulk:checked').toArray();
    } else {
        checked = $('.check-item-bulk:checked').toArray();
    }
    
    if (checked.length === 0) return;

    let actionLabel = action;
    if(action === 'selesai') actionLabel = 'menandai selesai';
    if(action === 'tidak_bisa_diperbaiki') actionLabel = 'menandai tidak bisa diperbaiki';
    if(action === 'kembali') actionLabel = 'mengembalikan ke ruangan pada';
    if(action === 'hapus') actionLabel = 'menghapus';

    const msg = `Anda yakin ingin ${actionLabel} ${checked.length} data terpilih?`;

    if (typeof showSweetAlertConfirm === 'function') {
        showSweetAlertConfirm(msg, function() {
            executeBulkAction(action, checked);
        });
    } else {
        if (confirm(msg)) {
            executeBulkAction(action, checked);
        }
    }
}

function executeBulkAction(action, checked) {
    bulkActionInput.value = action;
    bulkInputs.innerHTML = '';
    checked.forEach(c => {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'laporan_ids[]';
        inp.value = c.value;
        bulkInputs.appendChild(inp);
    });
    document.getElementById('formBulkAction').submit();
}
</script>
<?= $this->endSection() ?>
