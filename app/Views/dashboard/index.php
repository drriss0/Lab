<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- ============================================================
     Hero & Tambah Barang
============================================================ -->
<div id="section-hero" style="display: flex; gap: 20px; margin-bottom: 24px; flex-wrap: wrap;">
    <!-- Welcome Banner -->
    <div class="welcome-banner" style="flex: 1; min-width: 300px; background: white; border-radius: 16px; padding: 0 30px; display: flex; align-items: center; position: relative; overflow: hidden; height: 140px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <img src="<?= base_url('image/header.png') ?>" alt="Avatar" class="banner-img-main" style="height: 100%; width: auto; position: absolute; bottom: 0; left: 20px;" onerror="this.style.display='none'; document.getElementById('banner-text').style.marginLeft='0';" />
        <div id="banner-text" style="margin-left: 200px; z-index: 1; padding: 20px 0; transition: margin-left 0.2s;">
            <h1 style="font-size: 24px; font-weight: 700; color: #374151; margin-bottom: 6px;">
                <span style="color: #F05A1A;">Halo,</span> <?= esc(session()->get('user_nama') ?? 'Admin') ?> 👋
            </h1>
            <p style="font-size: 14px; color: #6B7280; line-height: 1.5; max-width: 320px;">
                Selamat datang kembali di Sistem Inventaris FTIK<br/>Universitas Semarang.
            </p>
        </div>
        <img src="<?= base_url('assets/img/ilustrasi-barang.png') ?>" alt="Ilustrasi" class="banner-img-secondary" style="height: 100px; position: absolute; right: 20px; bottom: 10px;" onerror="this.style.display='none'" />
    </div>

<?php if (session()->get('user_role') === 'admin'): ?>
    <!-- Add Item Card -->
    <div style="width: 280px; background: #3B82F6; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
        <i class="ri-folder-add-fill" style="position: absolute; top: -10px; right: -10px; font-size: 100px; color: rgba(255,255,255,0.15); transform: rotate(-10deg);"></i>
        <h3 style="color: white; font-size: 16px; font-weight: 600; line-height: 1.4; margin-bottom: 16px; z-index: 1;">
            Pendataan Aset<br/>Inventaris Baru
        </h3>
        <button onclick="openModal('modalTambahBarang')" style="background: white; color: #3B82F6; border: none; padding: 10px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; z-index: 1; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
            <i class="ri-folder-add-line"></i> Tambah Barang
        </button>
    </div>
<?php endif; ?>
</div>

<!-- ============================================================
     Flash Messages (Diganti dengan SweetAlert Toast)
============================================================ -->
<!-- ============================================================
     Stats Cards
============================================================ -->
<?php
$stats = $stats ?? ['total_inventaris' => 0, 'status_tersedia' => 0, 'total_rusak' => 0, 'status_distribusi' => 0];
?>
<div id="section-stats" class="stats-row" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px;">
    <!-- Card 1 -->
    <div style="background: white; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
            <div style="width: 54px; height: 54px; border-radius: 14px; background: #FFF3EE; color: #F05A1A; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                <i class="ri-box-3-line"></i>
            </div>
            <div style="font-size: 32px; font-weight: 700; color: #F05A1A; line-height: 1;">
                <?= $stats['total_inventaris'] ?>
            </div>
        </div>
        <div style="font-size: 14px; color: #6B7280; font-weight: 500;">Total Inventaris</div>
    </div>
    
    <!-- Card 2 -->
    <div style="background: white; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
            <div style="width: 54px; height: 54px; border-radius: 14px; background: #DCFCE7; color: #16A34A; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                <i class="ri-checkbox-circle-line"></i>
            </div>
            <div style="font-size: 32px; font-weight: 700; color: #16A34A; line-height: 1;">
                <?= $stats['status_tersedia'] ?>
            </div>
        </div>
        <div style="font-size: 14px; color: #6B7280; font-weight: 500;">Barang Tersedia</div>
    </div>

    <!-- Card 3: Barang Rusak -->
    <div style="background: white; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
            <div style="width: 54px; height: 54px; border-radius: 14px; background: #FEF2F2; color: #EF4444; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                <i class="ri-tools-line"></i>
            </div>
            <div style="font-size: 32px; font-weight: 700; color: #EF4444; line-height: 1;">
                <?= $stats['total_rusak'] ?>
            </div>
        </div>
        <div style="font-size: 14px; color: #6B7280; font-weight: 500;">Barang Rusak</div>
    </div>

    <!-- Card 4 -->
    <div style="background: white; border-radius: 16px; padding: 24px; display: flex; flex-direction: column; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
            <div style="width: 54px; height: 54px; border-radius: 14px; background: #FFF3EE; color: #F05A1A; display: flex; align-items: center; justify-content: center; font-size: 26px;">
                <i class="ri-mac-line"></i>
            </div>
            <div style="font-size: 32px; font-weight: 700; color: #F05A1A; line-height: 1;">
                <?= $stats['status_distribusi'] ?>
            </div>
        </div>
        <div style="font-size: 14px; color: #6B7280; font-weight: 500;">Sedang Digunakan</div>
    </div>
</div>

<!-- ============================================================
     Tabel Barang
============================================================ -->
<!-- Print-only header (hanya muncul saat print) -->
<div id="print-header" style="display:none;">
    <div style="display: flex; align-items: center; position: relative; border-bottom: 3px double #F05A1A; padding-bottom: 16px; margin-bottom: 24px;">
        <img src="<?= base_url('assets/img/ftik-logo.png') ?>" alt="Logo" style="width: 85px; position: absolute; left: 10px;" onerror="this.style.display='none'">
        <div style="width: 100%; text-align: center; padding: 0 100px;">
            <h2 style="font-size: 18px; font-weight: 800; margin: 0 0 4px 0; text-transform: uppercase; color: #F05A1A; letter-spacing: 0.5px;">FAKULTAS TEKNOLOGI INFORMASI DAN KOMUNIKASI</h2>
            <h3 style="font-size: 14px; font-weight: 700; margin: 0 0 6px 0; text-transform: uppercase; color: #374151;">Universitas Semarang</h3>
            <p style="font-size: 11px; margin: 0; color: #6B7280; font-weight: 400; line-height: 1.4;">Jl. Soekarno Hatta, Tlogosari, Pedurungan, Kota Semarang, Jawa Tengah 50196<br/>
               Telp: (024) 6702757 | Email: ftik@usm.ac.id | Website: ftik.usm.ac.id</p>
        </div>
    </div>
    <div style="text-align: center; margin-bottom: 20px;">
        <div style="font-size: 14px; font-weight: 700; text-transform: uppercase; color: #374151; letter-spacing: 0.5px; margin-bottom: 4px;">
            Laporan Rekapitulasi Data Barang Inventaris
        </div>
        <div style="font-size: 11px; color: #F05A1A; font-weight: 600; margin-bottom: 6px;">
            Sumber Data: Menu Dashboard (Ringkasan Keseluruhan)
        </div>
        <div style="font-size: 11px; color: #6B7280;">Dicetak pada: <span id="print-tgl"></span></div>
    </div>
</div>

<div class="table-section">

    <!-- Toolbar -->
    <div class="table-toolbar" style="padding: 20px 24px; border-bottom: 1px solid #F3F4F6; flex-wrap: wrap; gap: 12px;">
        <div class="table-toolbar-left">
            <h2 class="table-title" style="margin: 0;">Tabel Barang</h2>
            <form method="GET" action="<?= base_url('dashboard') ?>" id="formCari" style="margin: 0;">
                <?php if ($kategori_aktif ?? null): ?>
                    <input type="hidden" name="kategori" value="<?= esc($kategori_aktif) ?>" />
                <?php endif; ?>
                <?php if ($status_stok ?? null): ?>
                    <input type="hidden" name="status_stok" value="<?= esc($status_stok) ?>" />
                <?php endif; ?>
                <div class="search-box" style="border-radius: 8px; background: white; width: 300px;">
                    <i class="ri-search-line search-icon"></i>
                    <input type="text" name="q" id="inputCari"
                           placeholder="Cari nama, kode, atau jenis barang..."
                           value="<?= esc($keyword ?? '') ?>" autocomplete="off" />
                </div>
            </form>
        </div>
        <div class="table-toolbar-right" style="gap: 10px;">

            <?php if (($keyword ?? '') || ($kategori_aktif ?? '') || ($status_stok ?? '')): ?>
            <a href="<?= base_url('dashboard') ?>"
               style="color:#EF4444;font-size:13px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:5px;padding:9px 16px;border-radius:8px;background:#FEF2F2;border:1px solid #FECACA;transition:.2s;"
               onmouseover="this.style.background='#FEE2E2'" onmouseout="this.style.background='#FEF2F2'">
                <i class="ri-close-circle-line"></i> Reset
            </a>
            <?php endif; ?>

            <?php
                $jumlahFilterAktif = (int)(!empty($kategori_aktif)) + (int)(!empty($status_stok));
                $filterAktif = $jumlahFilterAktif > 0;
                $btnBg       = $filterAktif ? '#3B82F6' : '#F05A1A';
                $btnHover    = $filterAktif ? '#2563EB' : '#C94810';
            ?>
            <button onclick="toggleFilterPanel()" id="btnFilter"
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

            <button onclick="window.print()" id="btnExport"
                    style="background:#FFF3EE;color:#F05A1A;border:1px solid #FCD9C5;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;transition:.2s;"
                    onmouseover="this.style.background='#FFD5C2';" onmouseout="this.style.background='#FFF3EE';">
                <i class="ri-printer-line"></i> Cetak
            </button>
        </div>
    </div>

    <!-- Filter Panel (Toggle) -->
    <div id="filterPanel" style="display:<?= $filterAktif ? 'block' : 'none' ?>; border-bottom: 1px solid #E5E7EB; background: #F9FAFB;">
        <div style="padding: 18px 24px; display: flex; flex-direction: column; gap: 16px;">

            <!-- Filter Kategori -->
            <div>
                <div style="font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                    <i class="ri-price-tag-3-line" style="font-size:13px;"></i> Kategori
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <?php
                    $makeUrl = fn($kat, $stok) => base_url('dashboard') . '?' . http_build_query(array_filter([
                        'q'           => $keyword ?? '',
                        'kategori'    => $kat,
                        'status_stok' => $stok,
                    ]));
                    $kAktif = ($kategori_aktif ?? '') === '';
                    ?>
                    <a href="<?= $makeUrl('', $status_stok ?? '') ?>"
                       style="padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;
                              background:<?= $kAktif ? '#F05A1A' : 'white' ?>;
                              color:<?= $kAktif ? 'white' : '#6B7280' ?>;
                              border:1.5px solid <?= $kAktif ? '#F05A1A' : '#D1D5DB' ?>;transition:.15s;">
                        Semua Kategori
                    </a>
                    <?php foreach ($kategori as $k):
                        $isAktif = ($kategori_aktif ?? '') === $k['nama'];
                    ?>
                    <a href="<?= $makeUrl($k['nama'], $status_stok ?? '') ?>"
                       style="padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;
                              background:<?= $isAktif ? '#F05A1A' : 'white' ?>;
                              color:<?= $isAktif ? 'white' : '#6B7280' ?>;
                              border:1.5px solid <?= $isAktif ? '#F05A1A' : '#D1D5DB' ?>;transition:.15s;">
                        <?= esc($k['nama']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Divider -->
            <div style="height:1px;background:#E5E7EB;"></div>

            <!-- Filter Status Stok -->
            <div>
                <div style="font-size:11px;font-weight:700;color:#9CA3AF;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px;display:flex;align-items:center;gap:6px;">
                    <i class="ri-stack-line" style="font-size:13px;"></i> Status Stok
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;">
                    <?php
                    $stokOpts = [
                        '' => ['label' => 'Semua Stok', 'clr' => '#6B7280', 'border' => '#D1D5DB'],
                        'aman' => ['label' => '✓ Aman', 'clr' => '#16A34A', 'border' => '#86EFAC'],
                        'sedikit' => ['label' => '⚠ Sedikit', 'clr' => '#CA8A04', 'border' => '#FCD34D'],
                        'habis' => ['label' => '✕ Habis', 'clr' => '#DC2626', 'border' => '#FCA5A5'],
                    ];
                    foreach ($stokOpts as $val => $t):
                        $isAktif = ($status_stok ?? '') === $val;
                        $href = $makeUrl($kategori_aktif ?? '', $val);
                    ?>
                    <a href="<?= $href ?>"
                       style="padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;
                              background:<?= $isAktif ? $t['clr'] : 'white' ?>;
                              color:<?= $isAktif ? 'white' : $t['clr'] ?>;
                              border:1.5px solid <?= $isAktif ? $t['clr'] : $t['border'] ?>;transition:.15s;">
                        <?= $t['label'] ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- Table -->
    <div class="data-table-wrapper">
        <table class="data-table" id="tabelBarang">
            <thead>
                <tr>
                    <th style="width:44px;text-align:center;">#</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Jenis Barang</th>
                    <th style="text-align:center;">Gudang</th>
                    <th style="text-align:center;">Digunakan</th>
                    <th style="text-align:center;">Total</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($barang_list)): ?>
                    <?php foreach ($barang_list as $i => $b): ?>
                        <?php
                            $statusStr  = \App\Models\BarangModel::getStatusStok($b);
                            $statusClass = match($statusStr) {
                                'Aman'   => 'badge-aman',
                                'Sedikit' => 'badge-sedikit',
                                'Habis'  => 'badge-habis',
                                default  => 'badge-sedikit',
                            };
                            $total = (int)$b['stok_gudang']
                                   + (int)$b['jumlah_digunakan']
                                   + (int)($b['stok_peminjaman'] ?? 0)
                                   + (int)$b['jumlah_dipinjam']
                                   + (int)$b['jumlah_rusak'];
                        ?>
                        <tr id="row-barang-<?= $b['id'] ?>">
                            <td class="row-num" style="text-align:center;color:#9CA3AF;font-size:12px;"></td>
                            <td>
                                <span style="font-weight:600;color:#374151;"><?= esc($b['kode_barang']) ?></span>
                            </td>
                            <td>
                                <span style="font-weight:500;"><?= esc($b['nama_barang']) ?></span>
                            </td>
                            <td><?= esc($b['kategori'] ?? '—') ?></td>
                            <td><?= esc($b['nama_kategori'] ?? '—') ?></td>
                            <td style="text-align:center;"><?= (int)$b['stok_gudang'] ?></td>
                            <td style="text-align:center;"><?= (int)$b['jumlah_digunakan'] + (int)($b['stok_peminjaman'] ?? 0) + (int)($b['jumlah_dipinjam'] ?? 0) ?></td>
                            <td style="text-align:center;font-weight:600;"><?= $total ?></td>
                            <td>
                                <span class="badge <?= $statusClass ?>">
                                    <?= $statusStr ?>
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <?php if (session()->get('user_role') === 'admin'): ?>
                                <button
                                    style="background: #3B82F6; color: white; border: none; padding: 6px 16px; border-radius: 6px; font-size: 13px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: 0.2s;"
                                    id="btn-kelola-<?= $b['id'] ?>"
                                    onclick="bukaKelola('<?= encode_id($b['id']) ?>', '<?= esc($b['nama_barang'], 'js') ?>')"
                                    onmouseover="this.style.background='#2563EB';"
                                    onmouseout="this.style.background='#3B82F6';"
                                >
                                    <i class="ri-list-settings-line"></i> Kelola
                                </button>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination info -->
    <?php if (!empty($barang_list)): ?>
        <div class="pagination-wrapper">
            <span>Menampilkan <?= count($barang_list) ?> data barang</span>
        </div>
    <?php endif; ?>

</div><!-- .table-section -->

<?= $this->endSection() ?>

<?= $this->section('head') ?>
<style>
@media print {
    /* ---- Sembunyikan elemen yang tidak perlu ---- */
    .sidebar,
    .topbar,
    #section-hero,
    #section-stats,
    .flash-alert,
    #filterPanel,
    .pagination-wrapper,
    .dataTables_length, .dataTables_info, .dataTables_paginate, .dataTables_filter,
    .table-toolbar-left form,
    .table-toolbar-right,
    #tabelBarang th:last-child,
    #tabelBarang td:last-child {
        display: none !important;
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
    
    html, body {
        margin: 0 !important;
        padding: 0 !important;
        height: auto !important;
        background: white !important;
    }

    .main-content {
        margin-left: 0 !important;
        padding: 20px !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    /* ---- Sembunyikan Logo CI4 Debug Toolbar ---- */
    #toolbarContainer {
        display: none !important;
    }

    /* ---- Table section bersih ---- */
    .table-section {
        box-shadow: none !important;
        border-radius: 0 !important;
    }
    .table-toolbar {
        border-bottom: 1px solid #E5E7EB !important;
        padding: 0 0 10px !important;
    }
    .table-title {
        font-size: 13px !important;
    }

    /* ---- Hilangkan pagination & CI toolbar ---- */
    #toolbarContainer,
    .pagination-wrapper, .dataTables_info, .dataTables_paginate, .dataTables_length {
        display: none !important;
    }
    /* ---- Hilangkan default header/footer browser (URL, tanggal) ---- */
    @page {
        margin: 0;
    }

    /* ---- Tabel ---- */
    .data-table {
        width: 100% !important;
        font-size: 11px !important;
        border-collapse: collapse !important;
        table-layout: auto !important;
    }
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
        text-transform: uppercase !important;
        font-size: 11px !important;
    }
    .data-table td {
        background: transparent !important; /* Hapus warna zebra row */
        color: #1F2937 !important; /* Hitam yang lebih soft (gray-800) untuk teks data */
    }

    /* ---- Tampilan Status (Aman, Sedikit, Habis) saat di-print ---- */
    .badge {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        font-weight: 700 !important;
        display: inline !important;
        box-shadow: none !important;
    }
    .badge-aman {
        color: #16A34A !important;
    }
    .badge-sedikit {
        color: #CA8A04 !important;
    }
    .badge-habis {
        color: #DC2626 !important;
    }
}
</style>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    // Live search: submit form saat ketik (debounced)
    var searchTimeout;
    document.getElementById('inputCari').addEventListener('input', function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function () {
            document.getElementById('formCari').submit();
        }, 500);
    });

    // Nomor baris dinamis — sinkron dengan halaman DataTables saat ini
    function updateRowNumbers(dtApi) {
        var start = dtApi.page.info().start;
        dtApi.rows({ page: 'current' }).nodes().each(function(row, i) {
            var cell = row.querySelector('td.row-num');
            if (cell) cell.textContent = start + i + 1;
        });
    }

    // Jalankan updateRowNumbers setiap kali DataTables digambar ulang
    $(document).on('draw.dt', '#tabelBarang', function() {
        var dt = $(this).DataTable();
        updateRowNumbers(dt);
    });

    // Highlight menu aktif
    document.addEventListener('DOMContentLoaded', function () {
    });

    // Toggle filter panel
    function toggleFilterPanel() {
        var panel = document.getElementById('filterPanel');
        var isOpen = panel.style.display !== 'none';
        panel.style.display = isOpen ? 'none' : 'block';
    }

    // Isi tanggal untuk print header
    document.getElementById('print-tgl').textContent =
        new Date().toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' });
</script>
<?= $this->endSection() ?>
