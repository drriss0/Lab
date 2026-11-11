<?= $this->extend('templates/layout') ?>

<?= $this->section('head') ?>
<style>
.badge-digunakan  { background:#bbf7d0!important;color:#16a34a!important;font-weight:700; }
.badge-ditarik    { background:#d1d5db!important;color:#6b7280!important;font-weight:700; }
.badge-digudang   { background:#FCECE4!important;color:#F05A1A!important;font-weight:700; }
.badge-pelaporan  { background:#FEE2E2!important;color:#EF4444!important;font-weight:700; }
.badge-maintenance{ background:#FEF3C7!important;color:#D97706!important;font-weight:700; }
.badge-dipindah   { background:#EDE9FE!important;color:#6D28D9!important;font-weight:700; }

/* Lokasi pills */
.lokasi-pill {
    display:inline-flex;align-items:center;gap:5px;
    padding:3px 10px;border-radius:20px;
    font-size:12px;font-weight:600;white-space:nowrap;
}
.lokasi-asal   { background:#F3F4F6;color:#6B7280; }
.lokasi-tujuan { background:#EDE9FE;color:#5B21B6; }
.lokasi-aktif  { background:#D1FAE5;color:#065F46; }
.lokasi-arrow  { color:#9CA3AF;font-size:11px;margin:0 2px; }

@media print {
    /* ---- Sembunyikan elemen yang tidak perlu ---- */
    .sidebar,
    .topbar,
    .page-breadcrumb,
    .flash-alert,
    #info-cards,
    .search-box,
    .print-hide,
    #toolbarContainer,
    .table-toolbar-right,
    .pagination-wrapper,
    .dataTables_length, .dataTables_info, .dataTables_paginate, .dataTables_filter,
    #tabelDistribusi th:last-child,
    #tabelDistribusi td:last-child,
    #tabelDistribusi input[type="checkbox"] {
        display: none !important;
    }

    /* ---- Ubah kolom checkbox jadi kolom No. ---- */
    #tabelDistribusi { counter-reset: rowNumber; }
    #tabelDistribusi th:first-child { font-size: 0 !important; }
    #tabelDistribusi th:first-child::before { content: "No."; font-size: 11px; }
    #tabelDistribusi td:first-child { font-size: 0 !important; }
    #tabelDistribusi tbody tr:not(.print-hide) td:first-child::before {
        counter-increment: rowNumber;
        content: counter(rowNumber);
        font-size: 11px;
    }

    /* ---- Tampilkan header laporan ---- */
    #print-header {
        display: block !important;
    }

    /* ---- Reset layout agar sidebar tidak ikut ---- */
    .app-shell {
        display: block !important;
    }
    .sidebar {
        display: none !important;
    }
    .main-content { margin-left: 0 !important; padding: 20px !important; width: 100% !important; box-sizing: border-box !important; }

    /* ---- Table section bersih ---- */
    .table-section {
        box-shadow: none !important;
        border-radius: 0 !important;
        padding: 0 !important;
        background: transparent !important;
    }
    .table-toolbar {
        border-bottom: 1px solid #E5E7EB !important;
        padding: 0 0 10px !important;
    }
    .table-title {
        font-size: 13px !important;
    }

    /* ---- Tabel ---- */
    .data-table {
        width: 100% !important;
        font-size: 11px !important;
        border-collapse: collapse !important;
    }
    .data-table th, .data-table td {
        border: 1px solid #CBD5E1 !important;
        padding: 8px 10px !important;
    }
    .data-table th {
        background: #F05A1A !important;
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        font-weight: 700 !important;
        text-align: center;
    }
    .data-table td {
        background: transparent !important;
        color: #1F2937 !important;
    }
    
    /* Ubah pill lokasi, kode, dan badge Digunakan menjadi teks biasa agar rapi */
    .lokasi-pill, .kode-badge, .unit-badge, .badge-digunakan {
        background: transparent !important;
        color: #1F2937 !important;
        padding: 0 !important;
        border: none !important;
        font-weight: normal !important;
    }
    
    /* Paksa browser mencetak warna asli pada badge status selain Digunakan (sebagai pembeda) */
    .badge:not(.badge-digunakan) {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .lokasi-arrow {
        color: #1F2937 !important;
    }
    
    /* Sembunyikan ikon di dalam badge/pill saat dicetak */
    .lokasi-pill i {
        display: none !important;
    }
    
    /* Hilangkan bold pada kolom teks, kecuali info penting */
    .data-table td, .data-table td * { font-weight: normal !important; }

    .data-table thead { display: table-header-group; }
    
    /* Layout cetak untuk menyembunyikan header/footer browser bawaan tapi tetap ada margin */
    table.layout-print { display: table !important; width: 100%; border: none !important; border-collapse: collapse; margin: 0; }
    table.layout-print > thead { display: table-header-group !important; }
    table.layout-print > tfoot { display: table-footer-group !important; }
    table.layout-print > tbody { display: table-row-group !important; }
    table.layout-print > thead > tr > td { border: none !important; padding: 0 !important; height: 1.5cm; background: transparent !important; }
    table.layout-print > tfoot > tr > td { border: none !important; padding: 0 !important; height: 1.5cm; background: transparent !important; }
    table.layout-print > tbody > tr > td { border: none !important; padding: 0 1.5cm !important; background: transparent !important; }
    
    .print-spacer { display: none !important; }
    
    /* Sembunyikan pagination & CI toolbar */
    .pagination-wrapper, .dataTables_info, .dataTables_paginate, .dataTables_length,
    #toolbarContainer { display: none !important; }
    
    @page { margin: 0; }
}
table.layout-print { display: block; width: 100%; border: none; padding: 0; margin: 0; }
table.layout-print > thead, table.layout-print > tfoot { display: none; }
table.layout-print > tbody, table.layout-print > tbody > tr, table.layout-print > tbody > tr > td { display: block; width: 100%; padding: 0; border: none; margin: 0; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?><?php $b = $barang; ?>

<!-- Page Header / Back Button -->
<div class="print-hide" style="display: flex; align-items: center; margin-bottom: 24px; padding-top: 8px;">
    <div style="font-size: 20px; font-weight: 700; color: #1F2937; display: flex; align-items: center; gap: 12px;">
        <a href="<?= base_url('dashboard') ?>" style="color: #4B5563; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; text-decoration: none; border-radius: 10px; background: #F3F4F6; transition: all 0.2s; border: 1px solid #E5E7EB;" onmouseover="this.style.background='#E5E7EB'; this.style.color='#1F2937';" onmouseout="this.style.background='#F3F4F6'; this.style.color='#4B5563';" title="Kembali ke Dashboard">
            <i class="ri-arrow-left-line" style="font-size: 20px;"></i>
        </a>
        <span>Distribusi Barang: <span style="color: var(--primary);"><?= esc($b['nama_barang']) ?></span></span>
    </div>
</div>

<!-- Flash Messages -->
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

<!-- Info Card & Action Section -->
<div id="info-cards" style="display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap;">
    <!-- White Card (Info) -->
    <div style="flex: 1; min-width: 320px; height: 140px; background: #FFFFFF; border-radius: 12px; padding: 0 24px 0 240px; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: space-between; border: 1px solid var(--gray-200); box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <img src="<?= base_url('image/header.png') ?>" alt="Header" style="height: 100%; width: auto; position: absolute; bottom: 0; left: 20px;" onerror="this.style.display='none'" />
        <div style="flex: 1; padding-right: 20px; z-index: 1;">
            <div style="font-size:16px;font-weight:700;color:var(--gray-800);">Distribusi Barang</div>
            <div style="font-size: 22px; font-weight: 700; color: var(--primary); margin-bottom: 4px;"><?= esc($b['nama_barang']) ?></div>
            <div style="display:inline-flex;align-items:center;gap:6px;background:#FFF3ED;border:1px solid #FDBA99;border-radius:6px;padding:3px 10px;font-size:12px;font-weight:700;color:#C2410C;margin-bottom:8px;">
                <i class="ri-barcode-line" style="font-size:13px;"></i>
                <?= esc($b['kode_barang']) ?>
            </div>
            <p style="color: var(--gray-500); font-size: 12px; line-height: 1.5; margin: 0; max-width: 360px;">
                Alokasikan barang dari gudang untuk penggunaan<br>operasional di lingkungan FTIK.
            </p>
        </div>
        <div style="background: #FCECE4; border: 1px solid #F3C4AD; border-radius: 8px; padding: 16px 24px; display: flex; flex-direction: column; justify-content: center; align-items: center; min-width: 140px;">
            <div style="font-size: 32px; font-weight: 800; color: var(--primary); line-height: 1;"><?= $total_digunakan ?></div>
            <div style="font-size: 11px; color: var(--primary); margin-top: 8px; font-weight: 500;">Barang Digunakan</div>
        </div>
    </div>

    <?php if (can('admin')): ?>
    <!-- Blue Card (Action) -->
    <div style="width: 260px; background: #407BFF; border-radius: 12px; padding: 20px 24px; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 12px rgba(64, 123, 255, 0.25);">
        <i class="ri-folder-add-fill" style="position: absolute; top: 10px; right: -10px; font-size: 80px; color: rgba(255,255,255,0.15); transform: rotate(-10deg);"></i>
        <div style="color: white; font-size: 14px; font-weight: 600; line-height: 1.4; margin-bottom: 14px; z-index: 1;">
            Pendataan<br/>Distribusi Barang
        </div>
        <button onclick="openModal('modalDistribusiBaru')" style="background: white; color: #407BFF; border: none; padding: 10px 16px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; z-index: 1; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
            <i class="ri-folder-add-line" style="font-size: 16px;"></i> Distribusi Baru
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- Layout Print Wrapper -->
<table class="layout-print">
    <thead><tr><td></td></tr></thead>
    <tbody><tr><td>

<!-- Print-only header (hanya muncul saat print) -->
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
        Laporan Riwayat Distribusi Barang
        <div style="font-size: 11px; color: #F05A1A; font-weight: 600; margin-top: 6px; letter-spacing: 0; text-transform: none;">
            Sumber Data: Menu Riwayat Distribusi (<?= esc($b['nama_barang']) ?>)
        </div>
        <div style="font-size: 11px; color: #6B7280; font-weight: 500; margin-top: 6px; text-transform: none;">
            Dicetak pada: <span id="print-tgl"></span>
        </div>
        <?php if (!empty($filters['status']) && $filters['status'] !== 'semua'): ?>
        <div style="font-size: 11px; color: #4B5563; font-weight: 600; margin-top: 6px; text-transform: none;">
            Status: <?= ucwords(str_replace('_', ' ', esc($filters['status']))) ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Table -->
<div class="table-section">
    <form method="GET" action="<?= base_url('barang/' . encode_id($b['id']) . '/distribusi') ?>" id="formFilterDistribusi" style="margin: 0;">
    <form method="GET" action="<?= base_url('barang/' . encode_id($b['id']) . '/distribusi') ?>" id="formFilterDistribusi" style="margin: 0;">
        <div class="table-toolbar" style="padding: 20px 24px; border-bottom: 1px solid #F3F4F6; display: flex; flex-wrap: wrap; gap: 16px; align-items: center; justify-content: space-between;">
            <div class="table-toolbar-left" style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <h2 class="table-title" style="margin: 0; font-size: 18px; font-weight: 700; color: var(--gray-800); white-space: nowrap;">Tabel Distribusi</h2>
                <div class="search-box" style="border-radius: 8px; background: white; width: 280px; border: 1px solid var(--gray-200); box-shadow: 0 1px 2px rgba(0,0,0,0.02); display: flex; align-items: center; padding: 0 12px;">
                    <i class="ri-search-line search-icon" style="color: var(--primary); font-size: 16px;"></i>
                    <input type="text" name="q" id="cariDistribusi" placeholder="Cari di sini..." autocomplete="off" value="<?= esc($filters['q'] ?? '') ?>" style="border: none; background: transparent; padding: 8px; font-size: 13px; width: 100%; outline: none; color: var(--gray-600);" />
                </div>
            </div>

            <div class="table-toolbar-right print-hide" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
                <?php
                    $ada_filter = !empty($filters['q'])
                        || !empty($filters['tanggal_mulai'])
                        || !empty($filters['tanggal_akhir'])
                        || !empty($filters['ruangan'])
                        || (!empty($filters['status']) && $filters['status'] !== 'semua');
                        
                    $jumlahFilterAktif = (int)(!empty($filters['tanggal_mulai']) || !empty($filters['tanggal_akhir']))
                                       + (int)(!empty($filters['ruangan']))
                                       + (int)(!empty($filters['status']) && $filters['status'] !== 'semua');
                    $filterAktif = $jumlahFilterAktif > 0;
                    $btnBg       = $filterAktif ? '#3B82F6' : '#F05A1A';
                    $btnHover    = $filterAktif ? '#2563EB' : '#C94810';
                ?>
                <?php if ($ada_filter): ?>
                <a href="<?= base_url('barang/' . encode_id($b['id']) . '/distribusi') ?>"
                   style="color:#EF4444;font-size:13px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:6px;padding:9px 16px;border-radius:8px;background:#FEF2F2;border:1px solid #FECACA;transition:.2s;"
                   onmouseover="this.style.background='#FEE2E2'" onmouseout="this.style.background='#FEF2F2'">
                    <i class="ri-close-circle-line"></i> Reset
                </a>
                <?php endif; ?>

                <button type="button" onclick="toggleFilterPanel()" id="btnFilter"
                        style="background:<?= $btnBg ?>;color:white;border:none;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;transition:.2s;"
                        onmouseover="this.style.background='<?= $btnHover ?>';"
                        onmouseout="this.style.background='<?= $btnBg ?>';">
                    <i class="ri-equalizer-line"></i> Filter
                    <?php if ($jumlahFilterAktif > 0): ?>
                        <span style="background:white;color:<?= $btnBg ?>;border-radius:50%;width:18px;height:18px;font-size:11px;font-weight:800;display:inline-flex;align-items:center;justify-content:center;line-height:1;">
                            <?= $jumlahFilterAktif ?>
                        </span>
                    <?php endif; ?>
                </button>
                <button onclick="window.print()" type="button" id="btnExportDistribusi"
                        style="background:#FFF3EE;color:#F05A1A;border:1px solid #FCD9C5;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;transition:.2s;"
                        onmouseover="this.style.background='#FFD5C2';" onmouseout="this.style.background='#FFF3EE';">
                    <i class="ri-printer-line"></i> Export
                </button>
            </div>
        </div>

        <!-- Filter Panel -->
        <div id="filterPanel" style="display:<?= $filterAktif ? 'block' : 'none' ?>; border-bottom: 1px solid #E5E7EB; background: #F9FAFB;" class="print-hide">
            <div style="padding: 20px 24px; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                
                <!-- Status Distribusi -->
                <div>
                    <div style="font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                        <i class="ri-checkbox-circle-line" style="font-size:13px;"></i> Status
                    </div>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <?php
                        $statusTabs = [
                            ''            => ['label' => 'Semua',       'color' => '#6B7280'],
                            'digunakan'   => ['label' => 'Digunakan',   'color' => '#10B981'],
                            'maintenance' => ['label' => 'Maintenance', 'color' => '#F59E0B'],
                            'digudang'    => ['label' => 'Di Gudang',   'color' => '#F97316'],
                        ];
                        $currentStatus = $filters['status'] ?? '';
                        if ($currentStatus === 'semua') $currentStatus = '';
                        
                        foreach ($statusTabs as $val => $tab):
                            $isActive = ($currentStatus === $val);
                        ?>
                        <label style="cursor:pointer;margin:0;">
                            <input type="radio" name="status" value="<?= $val ?>" <?= $isActive ? 'checked' : '' ?> style="display:none;" onchange="document.getElementById('formFilterDistribusi').submit()">
                            <div style="padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; border: 1px solid <?= $isActive ? $tab['color'] : '#E5E7EB' ?>; background: <?= $isActive ? $tab['color'] : 'white' ?>; color: <?= $isActive ? 'white' : $tab['color'] ?>; transition: .2s;">
                                <?= $tab['label'] ?>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Rentang Tanggal -->
                <div>
                    <div style="font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                        <i class="ri-calendar-line" style="font-size:13px;"></i> Rentang Tanggal
                    </div>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <div style="flex:1;">
                            <input type="date" name="tanggal_mulai" class="form-control" value="<?= esc($filters['tanggal_mulai'] ?? '') ?>" onchange="document.getElementById('formFilterDistribusi').submit()" style="font-size:13px;padding:8px 12px;">
                        </div>
                        <div style="color:var(--gray-300);">
                            <i class="ri-arrow-right-line"></i>
                        </div>
                        <div style="flex:1;">
                            <input type="date" name="tanggal_akhir" class="form-control" value="<?= esc($filters['tanggal_akhir'] ?? '') ?>" onchange="document.getElementById('formFilterDistribusi').submit()" style="font-size:13px;padding:8px 12px;">
                        </div>
                    </div>
                </div>

                <!-- Ruangan / Lokasi -->
                <div>
                    <div style="font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                        <i class="ri-map-pin-line" style="font-size:13px;"></i> Lokasi Distribusi
                    </div>
                    <select name="ruangan" class="form-control" onchange="document.getElementById('formFilterDistribusi').submit()" style="font-size:13px;padding:8px 12px;">
                        <option value="">-- Semua Ruangan --</option>
                        <?php foreach ($ruangan as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= (isset($filters['ruangan']) && $filters['ruangan'] == $r['id']) ? 'selected' : '' ?>>
                                <?= esc($r['nama_ruangan']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>
        </div>
    </form>

    <div class="data-table-wrapper">
        <table class="data-table" id="tabelDistribusi">
            <thead>
                <tr>
                    <th style="width:30px;text-align:center;">
                        <input type="checkbox" id="checkAll" style="accent-color:#F05A1A;">
                    </th>
                    <th>Kode</th>
                    <th style="text-align:center;">No. Unit</th>
                    <th>Tanggal</th>
                    <th>Lokasi Saat Ini</th>
                    <th style="text-align:center;">Jumlah</th>
                    <th>Penanggung Jawab</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($distribusi)): ?>
                    <?php
                    // Pre-process: temukan record baru hasil alokasi dan pasang informasi asal_ruangan padanya
                    for ($i = 0; $i < count($distribusi); $i++) {
                        if (!empty($distribusi[$i]['alokasi_ke_ruangan']) && $distribusi[$i]['status'] === 'ditarik') {
                            for ($j = 0; $j < count($distribusi); $j++) {
                                // Pencocokan 100% akurat menggunakan id_asal_distribusi
                                if (isset($distribusi[$j]['id_asal_distribusi']) && $distribusi[$j]['id_asal_distribusi'] == $distribusi[$i]['id']) {
                                    $distribusi[$j]['nama_ruangan_asal'] = $distribusi[$i]['nama_ruangan'];
                                    $distribusi[$j]['tgl_pindah'] = $distribusi[$i]['tanggal_alokasi'];
                                    break;
                                }
                            }
                        }
                    }
                    ?>
                    <?php foreach ($distribusi as $d):
                        // Sembunyikan old record karena informasinya sudah dipindahkan ke active record
                        if (!empty($d['alokasi_ke_ruangan']) && $d['status'] === 'ditarik') continue;

                        $sudahDipindah = !empty($d['nama_ruangan_asal']);

                        $statusClass = match($d['status']) {
                            'digunakan'       => 'badge-digunakan',
                            'ditarik'         => 'badge-ditarik',
                            'digudang'        => 'badge-digudang',
                            'pelaporan_rusak' => 'badge-pelaporan',
                            'maintenance'     => 'badge-maintenance',
                            default           => 'badge-info',
                        };
                        $statusLabel = match($d['status']) {
                            'digunakan'       => 'Digunakan',
                            'ditarik'         => 'Ditarik',
                            'digudang'        => 'Di Gudang',
                            'pelaporan_rusak' => 'Pelaporan Rusak',
                            'maintenance'     => 'Maintenance',
                            default           => ucfirst($d['status']),
                        };

                        // Timpa status menjadi status ruangan jika barang sedang 'digunakan' namun ruangan bermasalah
                        if ($d['status'] === 'digunakan' && !empty($d['status_ruangan']) && $d['status_ruangan'] !== 'Bisa Digunakan') {
                            if ($d['status_ruangan'] === 'Maintenance') {
                                $statusClass = 'badge-maintenance';
                                $statusLabel = 'Maintenance';
                            } else {
                                $statusClass = 'badge-ditarik';
                                $statusLabel = $d['status_ruangan'];
                            }
                        }

                        if ($sudahDipindah) {
                            $lokasiAsal   = esc($d['nama_ruangan_asal'] ?? '—');
                            $lokasiTujuan = esc($d['nama_ruangan'] ?? '—');
                            $tglPindah    = date('d/m/y', strtotime($d['tgl_pindah']));
                        } else {
                            if (isset($d['tujuan_distribusi']) && $d['tujuan_distribusi'] === 'peminjaman') {
                                $lokasiAsal = 'Peminjaman';
                            } else {
                                $lokasiAsal = esc($d['nama_ruangan'] ?? '—');
                            }
                            $lokasiTujuan = null;
                            $tglPindah    = null;
                        }
                    ?>
                    <?php $isInactive = in_array($d['status'], ['ditarik', 'digudang', 'pelaporan_rusak']); ?>
                    <tr id="dist-row-<?= $d['id'] ?>" class="dist-row <?= $isInactive ? 'row-ditarik' : '' ?>"
                        style="<?= $isInactive ? 'opacity: 0.6; filter: grayscale(20%); background-color: #F9FAFB;' : '' ?>"
                        data-tanggal="<?= $d['tanggal'] ?>"
                        data-jumlah="<?= $d['jumlah'] ?>"
                        data-status="<?= $d['status'] ?>">
                        <td style="text-align:center;">
                            <?php if (in_array($d['status'], ['digunakan', 'digudang', 'ditarik'])): ?>
                                <input type="checkbox" class="check-item" value="<?= encode_id($d['id']) ?>" data-status="<?= $d['status'] ?>" style="accent-color:#F05A1A;">
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="kode-badge" style="display:inline-block;background:#FFF3ED;border:1px solid #FDBA99;border-radius:5px;padding:2px 8px;font-size:11px;font-weight:700;color:#C2410C;white-space:nowrap;">
                                <?= esc($b['kode_barang']) ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <span class="unit-badge" style="display:inline-flex;align-items:center;justify-content:center;min-width:28px;height:22px;padding:0 7px;background:#EEF2FF;color:#4F46E5;border:1px solid #C7D2FE;border-radius:5px;font-size:11px;font-weight:700;font-family:monospace;white-space:nowrap;">
                                #<?= $d['no_unit'] ?? '–' ?>
                            </span>
                        </td>
                        <td><?= date('d/m/y', strtotime($d['tanggal'])) ?></td>
                        <td>
                            <?php if ($sudahDipindah): ?>
                                <!-- Dipindah: tampilkan Asal → Tujuan -->
                                <div style="display:flex;flex-direction:column;gap:4px;">
                                    <div style="display:flex;align-items:center;gap:4px;flex-wrap:wrap;">
                                        <span class="lokasi-pill lokasi-asal">
                                            <i class="ri-map-pin-line" style="font-size:10px;"></i>
                                            <?= $lokasiAsal ?>
                                        </span>
                                        <span class="lokasi-arrow">→</span>
                                        <span class="lokasi-pill lokasi-tujuan">
                                            <i class="ri-map-pin-2-fill" style="font-size:10px;"></i>
                                            <?= $lokasiTujuan ?>
                                        </span>
                                    </div>
                                    <?php if ($tglPindah): ?>
                                    <div style="font-size:10px;color:#9CA3AF;">Dipindah: <?= $tglPindah ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <!-- Belum dipindah: tampilkan lokasi aktif -->
                                <span class="lokasi-pill lokasi-aktif">
                                    <i class="ri-map-pin-2-fill" style="font-size:10px;"></i>
                                    <?= $lokasiAsal ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;font-weight:600;"><?= $d['jumlah'] ?> pcs</td>
                        <?php
                            $pjTampil = $d['penanggung_jawab'];
                            if (isset($d['tujuan_distribusi']) && $d['tujuan_distribusi'] === 'ruangan') {
                                $pjTampil = !empty($d['ruangan_pj']) ? $d['ruangan_pj'] : $pjTampil;
                            }
                        ?>
                        <td><?= esc($pjTampil ?? '—') ?></td>
                        <td>
                            <div style="display:flex;flex-direction:column;gap:6px;align-items:flex-start;">
                                <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <?php if (!in_array($d['status'], ['ditarik', 'digudang', 'pelaporan_rusak'])): ?>
                                <form method="post" action="<?= base_url('barang/distribusi/' . encode_id($d['id']) . '/tarik') ?>" style="display:inline;" onsubmit="return confirm('Tarik barang ini ke gudang?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-danger btn-icon btn-sm" id="btn-tarik-<?= $d['id'] ?>" title="Tarik ke Gudang">
                                        <i class="ri-arrow-go-back-line"></i>
                                    </button>
                                </form>
                                <?php elseif (in_array($d['status'], ['ditarik', 'digudang'])): ?>
                                <form method="post" action="<?= base_url('barang/distribusi/' . encode_id($d['id']) . '/hapus') ?>" style="display:inline;" onsubmit="return confirm('Hapus riwayat distribusi ini secara permanen?')">
                                    <?= csrf_field() ?>
                                    <button type="submit"
                                       style="display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:6px;background:#FEF2F2;color:#EF4444;border:1px solid #FECACA;cursor:pointer;transition:.2s;"
                                       onmouseover="this.style.background='#FEE2E2';this.style.color='#DC2626';"
                                       onmouseout="this.style.background='#FEF2F2';this.style.color='#EF4444';"
                                       title="Hapus Riwayat">
                                        <i class="ri-delete-bin-line" style="font-size:16px;"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Bulk Actions Panel -->
    <div id="bulkActions" style="display: none; position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); background: white; padding: 12px 24px; border-radius: 100px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); border: 1px solid #E5E7EB; align-items: center; gap: 16px; z-index: 50;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="background: #F05A1A; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;" id="countBulk">0</div>
            <span style="font-size: 14px; font-weight: 600; color: #374151;">terpilih</span>
        </div>
        <div style="width: 1px; height: 24px; background: #E5E7EB;"></div>
        
        <form id="formBulk" action="" method="post" style="display: flex; gap: 8px;">
            <?= csrf_field() ?>
            <div id="bulkInputs"></div>
            
            <button type="button" id="btnBulkTarik" onclick="submitBulk('tarik')" class="btn" style="background: #FEF2F2; color: #EF4444; border: 1px solid #FECACA; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: none;">
                <i class="ri-arrow-go-back-line"></i> Tarik ke Gudang
            </button>
            <button type="button" id="btnBulkHapus" onclick="submitBulk('hapus')" class="btn" style="background: #FEF2F2; color: #EF4444; border: 1px solid #FECACA; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: none;">
                <i class="ri-delete-bin-line"></i> Hapus Riwayat
            </button>
            <button type="button" onclick="cancelBulkSelection()" class="btn" style="background: #F3F4F6; color: #4B5563; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid #E5E7EB;">
                Batal
            </button>
        </form>
    </div>


    <?php if (!empty($distribusi)): ?>
    <div class="pagination-wrapper">
        <span>Menampilkan <?= count($distribusi) ?> data distribusi</span>
    </div>
    <?php endif; ?>
</div>

    </td></tr></tbody>
    <tfoot><tr><td></td></tr></tfoot>
</table>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<!-- Modal: Distribusi Baru -->
<div class="modal-overlay" id="modalDistribusiBaru">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div class="modal-title">Form Distribusi Baru</div>
            <button class="modal-close" onclick="closeModal('modalDistribusiBaru')"><i class="ri-close-line"></i></button>
        </div>
        <form action="<?= base_url('barang/' . encode_id($b['id']) . '/distribusi/simpan') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom: 24px;">

                <!-- Tujuan Distribusi -->
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Tujuan Distribusi <span style="color:#EF4444">*</span></label>
                    <div style="display:flex;gap:12px;">
                        <label style="flex:1;border:2px solid #407BFF;border-radius:8px;padding:12px 16px;display:flex;align-items:center;gap:10px;cursor:pointer;background:#EEF3FF;" id="lbl-ruangan">
                            <input type="radio" name="tujuan_distribusi" value="ruangan" checked
                                   onchange="toggleTujuan(this.value)"
                                   style="accent-color:#407BFF;transform:scale(1.2);">
                            <div>
                                <div style="font-weight:700;font-size:13px;color:#1D4ED8;"><i class="ri-building-2-line"></i> Ke Ruangan / Lab</div>
                                <div style="font-size:11px;color:#6B7280;margin-top:2px;">Distribusi tetap untuk operasional ruangan</div>
                            </div>
                        </label>
                        <label style="flex:1;border:2px solid #E5E7EB;border-radius:8px;padding:12px 16px;display:flex;align-items:center;gap:10px;cursor:pointer;" id="lbl-peminjaman">
                            <input type="radio" name="tujuan_distribusi" value="peminjaman"
                                   onchange="toggleTujuan(this.value)"
                                   style="accent-color:#F05A1A;transform:scale(1.2);">
                            <div>
                                <div style="font-weight:700;font-size:13px;color:#F05A1A;"><i class="ri-hand-coin-line"></i> Untuk Peminjaman</div>
                                <div style="font-size:11px;color:#6B7280;margin-top:2px;">Stok khusus untuk dipinjam pengguna</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Field Ruangan (hanya muncul jika tujuan = ruangan) -->
                <div id="field-ruangan">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Lokasi Tujuan</label>
                            <select class="form-control" name="id_ruangan" id="select-ruangan">
                                <option value="">Pilih Lokasi Tujuan</option>
                                <?php foreach ($ruangan as $r): ?>
                                    <option value="<?= $r['id'] ?>" data-pj="<?= esc($r['penanggung_jawab'] ?? '') ?>"><?= esc($r['nama_ruangan']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Penanggung Jawab Unit <span style="color:#EF4444">*</span></label>
                            <input type="text" class="form-control" name="penanggung_jawab" id="pj-ruangan"
                                   placeholder="Contoh: Budi Santoso, S.Kom" required />
                        </div>
                    </div>
                </div>

                <!-- Field Peminjaman (hanya muncul jika tujuan = peminjaman) -->
                <div id="field-peminjaman" style="display:none;">
                    <div class="form-group">
                        <div style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400E;">
                            <i class="ri-information-line"></i>
                            Barang akan masuk ke <strong>Pool Peminjaman</strong>. Pengguna bisa meminjam barang ini dari halaman Peminjaman Barang.
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Penanggung Jawab Unit <span style="color:#EF4444">*</span></label>
                        <?php if (!empty($existing_pj_peminjaman)): ?>
                            <input type="text" class="form-control" name="penanggung_jawab" id="pj-peminjaman"
                                   value="<?= esc($existing_pj_peminjaman) ?>" readonly style="background:#F3F4F6;" />
                            <div style="font-size:11px; color:#6B7280; margin-top:4px;">
                                <i class="ri-information-line"></i> Terisi otomatis dari data distribusi terakhir barang ini.
                            </div>
                        <?php else: ?>
                            <input type="text" class="form-control" name="penanggung_jawab" id="pj-peminjaman"
                                   placeholder="Contoh: Budi Santoso, S.Kom" disabled autocomplete="off" />
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tanggal Distribusi</label>
                        <input type="date" class="form-control" name="tanggal"
                               value="<?= date('Y-m-d') ?>" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Barang</label>
                        <input type="number" class="form-control" name="jumlah" id="input-jumlah-distribusi" min="1"
                               max="<?= (int)$b['stok_gudang'] ?>" required
                               placeholder="0" />
                        <div style="font-size: 11px; color: var(--primary); margin-top: 4px; font-weight: 500;" id="stok-info">
                            *Stok tersedia: <?= (int)$b['stok_gudang'] ?> Pcs
                        </div>
                    </div>
                </div>

                <!-- Info: status otomatis 'Digunakan' -->
                <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;padding:10px 14px;font-size:12px;color:#15803D;display:flex;align-items:center;gap:8px;margin-top:4px;">
                    <i class="ri-checkbox-circle-fill" style="font-size:16px;"></i>
                    Status distribusi akan otomatis diset ke <strong>Digunakan</strong>.
                </div>

                <div style="margin-top: 14px;">
                    <button type="submit" class="btn btn-primary" style="width: 100%; height: 44px; justify-content: center; font-size: 14px; border-radius: 8px; box-shadow: 0 4px 12px rgba(240, 90, 26, 0.25);">
                        <i class="ri-check-line"></i> Simpan Distribusi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function toggleFilterPanel() {
    var panel = document.getElementById('filterPanel');
    if (panel.style.display === 'none' || panel.style.display === '') {
        panel.style.display = 'block';
    } else {
        panel.style.display = 'none';
    }
}
// Toggle tampilan field ruangan / peminjaman
function toggleTujuan(val) {
    var fieldRuangan   = document.getElementById('field-ruangan');
    var fieldPeminjaman = document.getElementById('field-peminjaman');
    var lblRuangan     = document.getElementById('lbl-ruangan');
    var lblPeminjaman  = document.getElementById('lbl-peminjaman');
    var selectRuangan  = document.getElementById('select-ruangan');
    var pjRuangan      = document.getElementById('pj-ruangan');
    var pjPeminjaman   = document.getElementById('pj-peminjaman');

    if (val === 'peminjaman') {
        fieldRuangan.style.display   = 'none';
        fieldPeminjaman.style.display = '';
        lblRuangan.style.borderColor  = '#E5E7EB';
        lblRuangan.style.background   = 'white';
        lblPeminjaman.style.borderColor = '#F05A1A';
        lblPeminjaman.style.background  = '#FFF7ED';
        selectRuangan.required = false;
        pjRuangan.required = false;
        pjRuangan.disabled = true;
        
        pjPeminjaman.disabled = false;
        pjPeminjaman.required = true;
    } else {
        fieldRuangan.style.display   = '';
        fieldPeminjaman.style.display = 'none';
        lblRuangan.style.borderColor  = '#407BFF';
        lblRuangan.style.background   = '#EEF3FF';
        lblPeminjaman.style.borderColor = '#E5E7EB';
        lblPeminjaman.style.background  = 'white';
        selectRuangan.required = true;
        pjRuangan.required = true;
        pjRuangan.disabled = false;
        
        pjPeminjaman.disabled = true;
        pjPeminjaman.required = false;
    }
}

// Auto-fill penanggung jawab when ruangan is selected
document.getElementById('select-ruangan').addEventListener('change', function() {
    var selectedOption = this.options[this.selectedIndex];
    var pj = selectedOption.getAttribute('data-pj');
    var pjInput = document.getElementById('pj-ruangan');
    if (pj) {
        pjInput.value = pj;
        pjInput.readOnly = true;
        pjInput.style.backgroundColor = '#F3F4F6';
    } else {
        pjInput.value = '';
        pjInput.readOnly = false;
        pjInput.style.backgroundColor = '';
    }
});

var editRuangan = document.getElementById('edit_id_ruangan');
if (editRuangan) {
    editRuangan.addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var pj = selectedOption.getAttribute('data-pj');
        var pjInput = document.getElementById('edit_penanggung_jawab');
        if (pj) {
            pjInput.value = pj;
            pjInput.readOnly = true;
            pjInput.style.backgroundColor = '#F3F4F6';
        } else {
            pjInput.value = '';
            pjInput.readOnly = false;
            pjInput.style.backgroundColor = '';
        }
    });
}

// Live search (debounced)
var _searchTimer;
document.getElementById('cariDistribusi').addEventListener('input', function () {
    clearTimeout(_searchTimer);
    _searchTimer = setTimeout(function () {
        document.getElementById('formFilterDistribusi').submit();
    }, 500);
});

// Isi tanggal untuk print header
if (document.getElementById('print-tgl')) {
    document.getElementById('print-tgl').textContent = new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' });
}
// --- BULK SELECTION LOGIC ---
function updateBulkButtons() {
    var dt = $('#tabelDistribusi').DataTable();
    
    var allRows = dt.rows().nodes();
    $(allRows).removeClass('row-selected');
    
    var checks = dt.$('.check-item:checked');
    checks.each(function() {
        $(this).closest('tr').addClass('row-selected');
    });

    const bulkActions = document.getElementById('bulkActions');
    if (bulkActions) {
        if (checks.length > 0) {
            bulkActions.style.display = 'flex';
            document.getElementById('countBulk').innerText = checks.length;
            
            // Cek status campuran
            let adaDigunakan = false;
            let adaDiGudang = false;
            
            checks.each(function() {
                let st = $(this).attr('data-status');
                if (st === 'digunakan') adaDigunakan = true;
                if (st === 'digudang' || st === 'ditarik') adaDiGudang = true;
            });
            
            if (adaDigunakan && adaDiGudang) {
                $('#btnBulkTarik').hide();
                $('#btnBulkHapus').hide();
            } else if (adaDigunakan) {
                $('#btnBulkTarik').show();
                $('#btnBulkHapus').hide();
            } else if (adaDiGudang) {
                $('#btnBulkTarik').hide();
                $('#btnBulkHapus').show();
            }
            
        } else {
            bulkActions.style.display = 'none';
            var checkAll = document.getElementById('checkAll');
            if (checkAll) checkAll.checked = false;
        }
    }
}

$(document).on('change', '.check-item', function() {
    updateBulkButtons();
});

$(document).on('change', '#checkAll', function() {
    var dt = $('#tabelDistribusi').DataTable();
    var isChecked = $(this).prop('checked');
    
    // Pilih semua check-item pada baris yang difilter meskipun tidak di halaman saat ini
    if ($.fn.DataTable.isDataTable('#tabelDistribusi')) {
        var rows = dt.rows({ search: 'applied' }).nodes();
        $('input.check-item', rows).prop('checked', isChecked);
    } else {
        $('.check-item').prop('checked', isChecked);
    }
    
    updateBulkButtons();
});

window.cancelBulkSelection = function() {
    $('#checkAll').prop('checked', false);
    var dt = $('#tabelDistribusi').DataTable();
    dt.$('.check-item').prop('checked', false);
    updateBulkButtons();
};

window.submitBulk = function(action) {
    var dt = $('#tabelDistribusi').DataTable();
    var checks = dt.$('.check-item:checked');
    if (checks.length === 0) return;

    var msg = '';
    var url = '';
    if (action === 'tarik') {
        msg = `Anda yakin ingin menarik ${checks.length} distribusi terpilih ke gudang?`;
        url = '<?= base_url("barang/distribusi/tarik-bulk") ?>';
    } else if (action === 'hapus') {
        msg = `Anda yakin ingin menghapus ${checks.length} riwayat distribusi terpilih secara permanen?`;
        url = '<?= base_url("barang/distribusi/hapus-bulk") ?>';
    }

    if (typeof showSweetAlertConfirm === 'function') {
        showSweetAlertConfirm(msg, function() {
            prosesSubmitBulk(checks, url);
        });
    } else {
        if (confirm(msg)) {
            prosesSubmitBulk(checks, url);
        }
    }
};

function prosesSubmitBulk(checks, url) {
    var form = document.getElementById('formBulk');
    form.action = url;
    
    var inputsDiv = document.getElementById('bulkInputs');
    inputsDiv.innerHTML = '';
    
    checks.each(function() {
        var inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = 'id_distribusi[]';
        inp.value = this.value;
        inputsDiv.appendChild(inp);
    });
    
    form.submit();
}

// Fitur Pengecekan Kapasitas Ruangan Dinamis
document.addEventListener('DOMContentLoaded', function() {
    const selectRuangan = document.getElementById('select-ruangan');
    const inputJumlah = document.getElementById('input-jumlah-distribusi');
    const stokInfo = document.getElementById('stok-info');
    
    if (selectRuangan && inputJumlah && stokInfo) {
        const defaultMax = parseInt(inputJumlah.getAttribute('max')) || 0;
        const idBarang = '<?= esc($b['id']) ?>';
        const defaultText = stokInfo.innerHTML;
        
        selectRuangan.addEventListener('change', function() {
            const idRuangan = this.value;
            // Kembalikan ke default jika tidak ada ruangan dipilih
            if (!idRuangan) {
                inputJumlah.max = defaultMax;
                stokInfo.innerHTML = defaultText;
                stokInfo.style.color = 'var(--primary)';
                return;
            }
            
            // Tampilkan loading text
            stokInfo.innerHTML = '<i class="ri-loader-4-line" style="animation: spin 1s linear infinite;"></i> Mengecek kapasitas ruangan...';
            stokInfo.style.color = '#4B5563';
            
            // Panggil API
            fetch(`<?= base_url('barang/cek-kapasitas-ruangan') ?>?id_ruangan=${idRuangan}&id_barang=${idBarang}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        stokInfo.innerHTML = defaultText;
                        stokInfo.style.color = 'var(--primary)';
                        return;
                    }
                    
                    if (data.kapasitas !== -1) {
                        // Barang dibatasi kapasitas (Meja/Kursi)
                        const sisaKapasitas = data.sisa;
                        const finalMax = Math.min(defaultMax, sisaKapasitas);
                        
                        inputJumlah.max = finalMax;
                        
                        if (sisaKapasitas <= 0) {
                            stokInfo.innerHTML = `<i class="ri-error-warning-fill"></i> Ruangan Penuh! (Kapasitas ${data.jenis} ${data.kapasitas}, Terisi ${data.terisi})`;
                            stokInfo.style.color = '#EF4444'; // Red
                            inputJumlah.value = 0;
                        } else {
                            stokInfo.innerHTML = `<i class="ri-check-line"></i> Kapasitas sisa: ${sisaKapasitas} ${data.jenis} (Stok Gudang: ${defaultMax})`;
                            stokInfo.style.color = '#10B981'; // Green
                            if (parseInt(inputJumlah.value) > finalMax) {
                                inputJumlah.value = finalMax;
                            }
                        }
                    } else {
                        // Bukan meja/kursi, tidak ada batas kapasitas, kembali ke default
                        inputJumlah.max = defaultMax;
                        stokInfo.innerHTML = defaultText;
                        stokInfo.style.color = 'var(--primary)';
                    }
                })
                .catch(error => {
                    console.error('Error fetching capacity:', error);
                    stokInfo.innerHTML = defaultText;
                    stokInfo.style.color = 'var(--primary)';
                });
        });
        
        // Reset juga jika tujuan distribusi berubah (Ruangan vs Peminjaman)
        const radios = document.querySelectorAll('input[name="tujuan_distribusi"]');
        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'peminjaman') {
                    inputJumlah.max = defaultMax;
                    stokInfo.innerHTML = defaultText;
                    stokInfo.style.color = 'var(--primary)';
                } else if (this.value === 'ruangan' && selectRuangan.value) {
                    selectRuangan.dispatchEvent(new Event('change'));
                }
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
