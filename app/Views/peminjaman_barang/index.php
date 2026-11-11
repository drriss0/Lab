<?= $this->extend('templates/layout') ?>

<?= $this->section('head') ?>
<style>
@media print {
    /* Sembunyikan elemen UI agar yang tercetak murni tabel data (Export Data, bukan Screenshot Halaman) */
    .sidebar, .topbar, .page-breadcrumb, .flash-alert, 
    #section-hero, #section-a-barang, 
    .table-toolbar form, .table-toolbar-right, .pagination-wrapper,
    #toolbarContainer, .print-hide,
    #tabelPeminjaman th:last-child, #tabelPeminjaman td:last-child {
        display: none !important;
    }

    #print-header { display: block !important; }
    
    .app-shell { display: block !important; }
    .sidebar { display: none !important; }
    .main-content { margin-left: 0 !important; padding: 20px !important; width: 100% !important; box-sizing: border-box !important; }
    
    .table-section { box-shadow: none !important; border-radius: 0 !important; padding: 0 !important; background: transparent !important; }
    .table-toolbar { border-bottom: 1px solid #E5E7EB !important; padding: 0 0 10px !important; }
    .table-title { font-size: 13px !important; }
    
    .data-table { width: 100% !important; font-size: 11px !important; border-collapse: collapse !important; }
    .data-table th, .data-table td { border: 1px solid #CBD5E1 !important; padding: 8px 10px !important; }
    .data-table th { background: #F05A1A !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; font-weight: 700 !important; text-align: center; }
    .data-table td { background: white !important; color: #1F2937 !important; }
    
    /* Ubah badge menjadi teks biasa agar lebih rapi seperti dokumen resmi */
    .badge {
        background: transparent !important;
        color: #1F2937 !important;
        padding: 0 !important;
        border: none !important;
        font-weight: normal !important;
    }
    
    /* Hilangkan bold pada kolom teks, kecuali info penting */
    .data-table td, .data-table td * {
        font-weight: normal !important;
    }
    
    /* Sembunyikan pagination & CI toolbar */
    .pagination-wrapper, .dataTables_info, .dataTables_paginate, .dataTables_length,
    #toolbarContainer { display: none !important; }
    
    @page { margin: 0; }
}

/* Card Barang */
.barang-card {
    background: white;
    border-radius: 16px;
    padding: 16px;
    box-shadow: 0 1px 4px rgba(0,0,0,.06);
    border: 1px solid #F3F4F6;
    display: flex;
    flex-direction: column;
    transition: .2s;
    position: relative;
    overflow: hidden;
}
.barang-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,.1);
    transform: translateY(-2px);
}
.barang-card.stok-habis {
    opacity: .6;
    filter: grayscale(.4);
}
.barang-icon {
    width: 52px; height: 52px;
    background: #FFF3EE;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    color: #F05A1A; font-size: 26px;
    border: 1px solid #FFDDC9;
}
.stok-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 13px; font-weight: 600;
    padding: 4px 10px; border-radius: 20px;
}
.stok-ada    { background:#dcfce7;color:#16a34a; }
.stok-sedikit{ background:#fef9c3;color:#ca8a04; }
.stok-habis  { background:#fee2e2;color:#dc2626; }

/* Status badges */
.badge-dipinjam  { background:#dbeafe!important;color:#1d4ed8!important;font-weight:700; }
.badge-kembali   { background:#dcfce7!important;color:#16a34a!important;font-weight:700; }
.badge-terlambat { background:#fee2e2!important;color:#dc2626!important;font-weight:700; }

/* Foto thumbnail */
.foto-thumb {
    width: 40px; height: 40px;
    border-radius: 8px;
    object-fit: cover;
    border: 1px solid #E5E7EB;
    cursor: pointer;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ============================================================
     Hero
=========================================================== -->
<div id="section-hero" style="display:flex;gap:20px;margin-bottom:24px;flex-wrap:wrap;">
    <!-- Banner -->
    <div style="flex:1;min-width:280px;background:white;border-radius:16px;padding:0 30px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 3px rgba(0,0,0,.05);position:relative;overflow:hidden;height: 140px;">
        <img src="<?= base_url('image/header-peminjaman.png') ?>" alt="Avatar" style="height: 100%; width: auto; position: absolute; bottom: 0; left: 20px;" onerror="this.style.display='none'" />
        <div style="z-index:1; margin-left: 200px; padding: 20px 0;">
            <h1 style="font-size:22px;font-weight:700;color:#374151;margin-bottom:6px;">
                <span style="color:#F05A1A;">Peminjaman</span> Barang FTIK
            </h1>
            <p style="font-size:13px;color:#6B7280;line-height:1.5;max-width:340px;">
                Barang tersedia adalah yang telah dialokasikan<br>khusus untuk peminjaman melalui menu Distribusi.
            </p>
        </div>
        <div style="background:#FFF3EE;border:1px solid #FFDDC9;border-radius:10px;padding:14px 22px;text-align:center;min-width:120px;z-index:1;">
            <div style="font-size:30px;font-weight:800;color:#F05A1A;line-height:1;"><?= (int)$total_sedang_dipinjam ?></div>
            <div style="font-size:11px;color:#F05A1A;font-weight:600;margin-top:4px;">Sedang Dipinjam</div>
        </div>
    </div>

    <!-- Stats mini -->
    <div style="display:flex;flex-direction:column;gap:10px;min-width:240px;">
        <div style="background:#3B82F6;border-radius:12px;padding:14px 20px;color:white;display:flex;align-items:center;justify-content:space-between;">
            <div><div style="font-size:20px;font-weight:800;"><?= (int)$total_kembali ?></div><div style="font-size:12px;opacity:.85;">Sudah Kembali Hari Ini</div></div>
            <i class="ri-checkbox-circle-line" style="font-size:28px;opacity:.4;"></i>
        </div>
        <div style="background:#EF4444;border-radius:12px;padding:14px 20px;color:white;display:flex;align-items:center;justify-content:space-between;">
            <div><div style="font-size:20px;font-weight:800;"><?= (int)$total_terlambat ?></div><div style="font-size:12px;opacity:.85;">Terlambat Hari Ini</div></div>
            <i class="ri-alarm-warning-line" style="font-size:28px;opacity:.4;"></i>
        </div>
    </div>
</div>

<!-- Flash -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success flash-alert" style="margin-bottom:16px;"><i class="ri-checkbox-circle-line"></i> <?= session()->getFlashdata('success') ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger flash-alert" style="margin-bottom:16px;"><i class="ri-error-warning-line"></i> <?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<!-- ============================================================
     Section A: Daftar Barang Tersedia (Cards)
=========================================================== -->
<div id="section-a-barang" style="margin-bottom:28px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <div>
            <h2 style="font-size:18px;font-weight:700;color:#1F2937;margin:0;">Barang Tersedia untuk Dipinjam</h2>
            <p style="font-size:12px;color:#9CA3AF;margin:4px 0 0;">
                Hanya barang yang telah didistribusikan khusus untuk peminjaman yang muncul di sini
            </p>
        </div>
        <div style="background:white;border:1px solid #E5E7EB;border-radius:8px;padding:8px 14px;display:flex;align-items:center;gap:8px;box-shadow:0 1px 2px rgba(0,0,0,0.02);">
            <i class="ri-search-line" style="color:#9CA3AF;"></i>
            <input type="text" id="cariBarangCard" placeholder="Cari barang..."
                   style="border:none;background:transparent;outline:none;font-size:13px;color:#374151;width:180px;">
        </div>
    </div>

    <div id="gridBarang" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px;">
        <?php if (empty($barang_list)): ?>
            <div style="grid-column:1/-1;text-align:center;padding:60px 40px;color:#9CA3AF;">
                <i class="ri-hand-coin-line" style="font-size:56px;display:block;margin-bottom:16px;color:#F3D7C8;"></i>
                <div style="font-weight:600;color:#6B7280;margin-bottom:8px;">Belum Ada Barang untuk Dipinjam</div>
                <div style="font-size:13px;">Distribusikan barang terlebih dahulu dengan tujuan <strong>"Untuk Peminjaman"</strong><br>melalui halaman Detail Barang → Distribusi.</div>
            </div>
        <?php else: ?>
            <?php foreach ($barang_list as $b):
                $stokAda   = (int)($b['stok_peminjaman'] ?? 0);
                $habis     = $stokAda <= 0;
                $stokClass = $stokAda <= 0 ? 'stok-habis' : ($stokAda <= 3 ? 'stok-sedikit' : 'stok-ada');
                $stokLabel = $stokAda <= 0 ? 'Stok Habis' : $stokAda . ' pcs tersedia';
            ?>
            <div class="barang-card <?= $habis ? 'stok-habis' : '' ?>"
                 data-nama="<?= strtolower(esc($b['nama_barang'])) ?>"
                 data-kat="<?= strtolower(esc($b['nama_kategori'] ?? '')) ?>">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                    <div class="barang-icon" style="width:44px;height:44px;font-size:22px;">
                        <?php if ($b['foto_barang'] && file_exists(FCPATH . 'uploads/' . $b['foto_barang'])): ?>
                            <img src="<?= base_url('uploads/' . $b['foto_barang']) ?>" alt="" style="width:100%;height:100%;object-fit:cover;border-radius:10px;">
                        <?php else: ?>
                            <i class="ri-box-3-line"></i>
                        <?php endif; ?>
                    </div>
                    <a href="<?= base_url('peminjaman-barang/' . encode_id($b['id']) . '/detail') ?>"
                       title="Lihat Rincian Stok"
                       style="display:flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:8px;background:#F3F4F6;color:#4B5563;text-decoration:none;transition:0.2s;"
                       onmouseover="this.style.background='#E5E7EB';this.style.color='#1F2937'"
                       onmouseout="this.style.background='#F3F4F6';this.style.color='#4B5563'">
                        <i class="ri-list-check" style="font-size:14px;"></i>
                    </a>
                </div>

                <div style="flex-grow:1;">
                    <span class="stok-badge <?= $stokClass ?>" style="display:inline-flex;margin-bottom:6px;">
                        <i class="ri-stack-line"></i> <?= $stokLabel ?>
                    </span>
                    <div style="font-size:16px;font-weight:700;color:#1F2937;margin-bottom:2px;line-height:1.3;">
                        <?= esc($b['nama_barang']) ?>
                    </div>
                    <div style="font-size:12px;color:#9CA3AF;"><?= esc($b['kode_barang']) ?></div>
                    
                    <?php if (!empty($b['penanggung_jawab'])): ?>
                    <div style="font-size:12px;color:#F05A1A;margin-top:6px;font-weight:600;">
                        <i class="ri-user-star-line"></i> PJ: <?= esc($b['penanggung_jawab']) ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div style="margin-top:16px; display:flex; justify-content:center;">
                    <?php if (can('admin', 'koordinator_lab')): ?>
                    <button
                        <?= $habis ? 'disabled' : "onclick=\"openModalPinjam('" . encode_id($b['id']) . "', '" . addslashes(htmlspecialchars($b['nama_barang'], ENT_COMPAT)) . "', {$stokAda})\"" ?>
                        style="display:inline-flex;align-items:center;justify-content:center;gap:6px;width:fit-content;padding:8px 24px;border:none;border-radius:8px;font-weight:600;font-size:13px;cursor:<?= $habis ? 'not-allowed' : 'pointer' ?>;
                               background:<?= $habis ? '#E5E7EB' : '#F05A1A' ?>;
                               color:<?= $habis ? '#9CA3AF' : 'white' ?>;
                               transition:.2s; box-shadow: <?= $habis ? 'none' : '0 2px 4px rgba(240,90,26,0.2)' ?>;"
                        <?= !$habis ? 'onmouseover="this.style.background=\'#C94810\'; this.style.transform=\'translateY(-1px)\'; this.style.boxShadow=\'0 4px 6px rgba(240,90,26,0.25)\'"' : '' ?>
                        <?= !$habis ? 'onmouseout="this.style.background=\'#F05A1A\'; this.style.transform=\'none\'; this.style.boxShadow=\'0 2px 4px rgba(240,90,26,0.2)\'"' : '' ?>>
                        <i class="<?= $habis ? 'ri-close-circle-line' : 'ri-hand-coin-line' ?>" style="font-size:15px;"></i>
                        <?= $habis ? 'Habis' : 'Pinjam' ?>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- ============================================================
     Section B: Tabel Peminjaman
=========================================================== -->
<!-- Print-only header -->
<div id="print-header" style="display:none;">
    <div style="text-align: center; margin-bottom: 24px; position: relative; border-bottom: 3px double #F05A1A; padding-bottom: 16px; margin-top: 15mm;">
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
        Laporan Peminjaman Barang
        <div style="font-size: 11px; color: #F05A1A; font-weight: 600; margin-top: 6px; letter-spacing: 0; text-transform: none;">
            Sumber Data: Menu Peminjaman Barang
        </div>
        <div style="font-size: 11px; color: #6B7280; font-weight: 500; margin-top: 6px; text-transform: none;">
            Dicetak pada: <span id="print-tgl"></span>
        </div>
    </div>
</div>

<div class="table-section">
    <div class="table-toolbar" style="padding:20px 24px 16px;">
        <div class="table-toolbar-left">
            <span class="table-title">Tabel Peminjaman</span>
            <form method="GET" action="<?= base_url('peminjaman-barang') ?>" id="formCariPeminjaman" style="margin:0;">
                <?php if ($filter_status ?? null): ?>
                    <input type="hidden" name="status" value="<?= esc($filter_status) ?>" />
                <?php endif; ?>
                <div class="search-box" style="width:240px;min-width:200px;background:white;border-radius:8px;">
                    <i class="ri-search-line search-icon"></i>
                    <input type="text" name="q" id="cariPeminjaman" placeholder="Cari peminjam/barang..." value="<?= esc($keyword ?? '') ?>" autocomplete="off">
                </div>
            </form>
        </div>
        <div class="table-toolbar-right" style="margin-left: auto;">
            <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;justify-content:flex-end;">
                <?php
                $tabs = [
                    ''          => ['label' => 'Semua',     'clr' => '#6B7280'],
                    'dipinjam'  => ['label' => 'Dipinjam',  'clr' => '#3B82F6'],
                    'terlambat' => ['label' => 'Terlambat', 'clr' => '#EF4444'],
                    'kembali'   => ['label' => 'Kembali',   'clr' => '#22C55E'],
                ];
                foreach ($tabs as $val => $t):
                    $active = ($filter_status ?? '') === $val;
                    $href   = base_url('peminjaman-barang') . '?' . http_build_query(array_filter(['q' => $keyword ?? '', 'status' => $val]));
                ?>
                <a href="<?= $href ?>"
                   style="padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;
                          background:<?= $active ? $t['clr'] : 'white' ?>;
                          color:<?= $active ? 'white' : $t['clr'] ?>;
                          border:1px solid <?= $active ? $t['clr'] : '#E5E7EB' ?>;transition:.15s;">
                    <?= $t['label'] ?>
                </a>
                <?php endforeach; ?>

                <?php if (($keyword ?? '') || ($filter_status ?? '')): ?>
                <a href="<?= base_url('peminjaman-barang') ?>" style="color:#EF4444;font-size:12px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:4px;padding:6px 14px;border-radius:20px;background:#FEF2F2;border:1px solid #FECACA;">
                    <i class="ri-close-circle-line"></i> Reset
                </a>
                <?php endif; ?>

                <button onclick="window.print()" id="btnExportPeminjaman" style="background:#FFF3EE;color:#F05A1A;border:1px solid #FCD9C5;padding:10px 16px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:.2s;" onmouseover="this.style.background='#FFD5C2';" onmouseout="this.style.background='#FFF3EE';">
                    <i class="ri-printer-line" style="font-size: 16px;"></i> Export
                </button>
            </div>
        </div>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table" id="tabelPeminjaman">
            <thead>
                <tr>
                    <th style="width:30px;text-align:center;" class="print-hide">
                        <input type="checkbox" id="checkAllPem" style="accent-color:#F05A1A;">
                    </th>
                    <th style="width:44px;text-align:center;">No</th>
                    <th>Kode</th>
                    <th>Peminjam</th>
                    <th>Barang</th>
                    <th style="text-align:center;">Jumlah</th>
                    <th style="text-align:center;">Tgl Peminjaman</th>
                    <th style="text-align:center;">Batas Waktu</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                    <?php foreach ($peminjaman as $i => $p):
                        $p['hash'] = encode_id($p['id']);
                        $statusClass = match($p['status']) {
                            'dipinjam'  => 'badge-dipinjam',
                            'kembali'   => 'badge-kembali',
                            'terlambat' => 'badge-terlambat',
                            default     => '',
                        };
                        $statusLabel = match($p['status']) {
                            'dipinjam'  => 'Dipinjam',
                            'kembali'   => 'Kembali',
                            'terlambat' => 'Terlambat',
                            default     => ucfirst($p['status']),
                        };
                        $masihAktif = in_array($p['status'], ['dipinjam', 'terlambat']);
                    ?>
                    <tr data-status="<?= $p['status'] ?>" data-search="<?= strtolower(esc($p['nama_peminjam']) . ' ' . esc($p['nama_barang'] ?? '')) ?>">
                        <td style="text-align:center;" class="print-hide">
                            <input type="checkbox" class="check-item-pem" value="<?= encode_id($p['id']) ?>" data-status="<?= $p['status'] ?>" style="accent-color:#F05A1A;">
                        </td>
                        <td style="text-align:center;color:#9CA3AF;font-size:12px;"><?= $i + 1 ?></td>
                        <td>
                            <div style="font-size:12px;color:#4B5563;font-weight:600;"><?= esc($p['kode_barang'] ?? '—') ?></div>
                        </td>
                        <td>
                            <div style="font-weight:600;color:#374151;"><?= esc($p['nama_peminjam']) ?></div>
                            <?php if ($p['nim_nik']): ?>
                                <div style="font-size:11px;color:#9CA3AF;"><?= esc($p['nim_nik']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-weight:500;font-size:13px;"><?= esc($p['nama_barang'] ?? '—') ?></div>
                        </td>
                        <td style="text-align:center;font-weight:600;"><?= $p['jumlah'] ?> pcs</td>
                        <td style="text-align:center;font-size:13px;color:#6B7280;">
                            <?= $p['tanggal_pinjam'] ? date('d/m/y', strtotime($p['tanggal_pinjam'])) : '—' ?>
                        </td>
                        <td style="text-align:center;">
                            <?php
                                $tenggat = $p['tenggat_kembali'] ? date('d/m/y', strtotime($p['tenggat_kembali'])) : '—';
                                $isLate  = $p['status'] === 'terlambat';
                            ?>
                            <span style="font-size:13px;color:<?= $isLate ? '#EF4444' : '#6B7280' ?>;font-weight:<?= $isLate ? '700' : '400' ?>;">
                                <?= $tenggat ?>
                            </span>
                        </td>

                        <td>
                            <span class="badge <?= $statusClass ?>" style="font-size:11px;padding:3px 10px;">
                                <?= $statusLabel ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex;gap:5px;justify-content:center;">
                                <!-- View Detail: semua role -->
                                <button onclick="openModalDetailPeminjaman(<?= htmlspecialchars(json_encode($p)) ?>)"
                                        style="width:30px;height:30px;background:#8B5CF6;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;"
                                        title="Lihat Detail">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <?php if (can('admin')): ?>
                                <!-- Edit -->
                                <?php if ($p['status'] === 'kembali'): ?>
                                <button disabled
                                        style="width:30px;height:30px;background:#9CA3AF;color:white;border:none;border-radius:6px;cursor:not-allowed;font-size:13px;"
                                        title="Tidak bisa diedit (Sudah Kembali)">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <?php else: ?>
                                <button onclick="openModalEditPeminjaman(<?= htmlspecialchars(json_encode($p)) ?>)"
                                        style="width:30px;height:30px;background:#3B82F6;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;"
                                        title="Edit">
                                    <i class="ri-edit-line"></i>
                                </button>
                                <?php endif; ?>
                                <!-- Tandai Kembali -->
                                <?php if ($masihAktif): ?>
                                <form method="post" action="<?= base_url('peminjaman-barang/' . encode_id($p['id']) . '/kembali') ?>" style="display:inline;" onsubmit="return confirm('Tandai barang ini sudah dikembalikan?')">
                                    <?= csrf_field() ?>
                                    <button type="submit"
                                       style="width:30px;height:30px;background:#22C55E;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;display:inline-flex;align-items:center;justify-content:center;"
                                       title="Tandai Kembali">
                                        <i class="ri-checkbox-circle-line"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <!-- Hapus -->
                                <form method="post" action="<?= base_url('peminjaman-barang/' . encode_id($p['id']) . '/hapus') ?>" style="display:inline;" onsubmit="return confirm('Hapus data peminjaman ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit"
                                       style="width:30px;height:30px;background:#EF4444;color:white;border:none;border-radius:6px;cursor:pointer;font-size:13px;display:inline-flex;align-items:center;justify-content:center;"
                                       title="Hapus">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Bulk Actions Panel -->
    <div id="bulkActionsPem" style="display: none; position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); background: white; padding: 12px 24px; border-radius: 100px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); border: 1px solid #E5E7EB; align-items: center; gap: 16px; z-index: 50;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="background: #F05A1A; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;" id="countBulkPem">0</div>
            <span style="font-size: 14px; font-weight: 600; color: #374151;">terpilih</span>
        </div>
        <div style="width: 1px; height: 24px; background: #E5E7EB;"></div>
        
        <form id="formBulkPem" action="" method="post" style="display: flex; gap: 8px;">
            <?= csrf_field() ?>
            <div id="bulkInputsPem"></div>
            
            <button type="button" id="btnBulkKembaliPem" onclick="submitBulkPem('kembali')" class="btn" style="background: #ECFDF5; color: #10B981; border: 1px solid #A7F3D0; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: none;">
                <i class="ri-checkbox-circle-line"></i> Tandai Kembali
            </button>
            <button type="button" id="btnBulkHapusPem" onclick="submitBulkPem('hapus')" class="btn" style="background: #FEF2F2; color: #EF4444; border: 1px solid #FECACA; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: none;">
                <i class="ri-delete-bin-line"></i> Hapus Riwayat
            </button>
            <button type="button" onclick="cancelBulkSelectionPem()" class="btn" style="background: #F3F4F6; color: #4B5563; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid #E5E7EB;">
                Batal
            </button>
        </form>
    </div>

    <?php if (!empty($peminjaman)): ?>
    <div class="pagination-wrapper">
        <span id="peminjamanCountText">Menampilkan <strong><?= count($peminjaman) ?></strong> data peminjaman</span>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<!-- Modal: Form Pinjam Barang -->
<div class="modal-overlay" id="modalPinjamBarang">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div>
                <div class="modal-title">Form Peminjaman</div>
                <div style="font-size:12px;color:#6B7280;margin-top:2px;">Barang: <strong id="pinjam-nama-barang">—</strong></div>
            </div>
            <button class="modal-close" onclick="closeModal('modalPinjamBarang')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formPinjamBarang" method="post" enctype="multipart/form-data" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'ri-loader-4-line ri-spin\'></i> Menyimpan...';">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom:24px;">
                <div class="form-group">
                    <label class="form-label">Nama Peminjam <span style="color:#EF4444">*</span></label>
                    <input type="text" class="form-control" name="nama_peminjam"
                           placeholder="Contoh: Andi Setiawan" 
                           pattern="^[a-zA-Z\s\.,']+$" title="Nama hanya boleh berisi huruf"
                           oninput="this.value = this.value.replace(/[^a-zA-Z\s\.,']/g, '')" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">NIM / NIK</label>
                        <input type="text" class="form-control" name="nim_nik"
                               placeholder="Contoh: G.111.20.0001">
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon/WA <span style="color:#EF4444">*</span></label>
                        <input type="text" class="form-control" name="no_telp"
                               placeholder="Contoh: 08123456789"
                               pattern="^[0-9+]+$" title="No. Telepon hanya boleh berisi angka"
                               oninput="this.value = this.value.replace(/[^0-9+]/g, '')" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tanggal Pinjam <span style="color:#EF4444">*</span></label>
                        <input type="date" class="form-control" name="tanggal_pinjam"
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tenggat Kembali <span style="color:#EF4444">*</span></label>
                        <input type="date" class="form-control" name="tenggat_kembali" required>
                    </div>
                </div>
                <div class="form-row" style="display:flex;gap:12px;margin-bottom:16px;">
                    <div class="form-group" style="flex:0 0 140px;">
                        <label class="form-label">Jumlah <span style="color:#EF4444">*</span></label>
                        <input type="number" class="form-control" name="jumlah" id="pinjam-jumlah"
                               min="1" value="1" required>
                        <div id="pinjam-stok-info" style="font-size:11px;color:#F05A1A;margin-top:4px;font-weight:600;"></div>
                    </div>
                    
                    <div class="form-group" style="flex:1;">
                        <label class="form-label">Foto Dokumentasi (Kamera) <span style="color:#EF4444">*</span></label>
                        <div id="camera-container" style="display:flex;flex-direction:column;gap:12px;align-items:center;background:#FAFAFA;border:2px dashed #D1D5DB;border-radius:12px;padding:16px; width:100%;">
                                <video id="kamera-video" style="width:100%;max-height:280px;height:auto;background:#111;border-radius:8px;display:none;object-fit:contain;" autoplay playsinline></video>
                                <canvas id="kamera-canvas" style="display:none;"></canvas>
                                <img id="kamera-preview" style="display:none;width:100%;max-height:280px;height:auto;object-fit:contain;border-radius:8px;box-shadow:0 2px 8px rgba(0,0,0,0.1);">
                                
                                <div style="display:flex;gap:8px;width:100%;justify-content:center;">
                                    <button type="button" id="btn-buka-kamera" class="btn btn-sm" style="background:#4B5563;color:white;border-radius:8px;border:none;padding:8px 16px;cursor:pointer;font-weight:600;"><i class="ri-camera-line"></i> Buka Kamera</button>
                                    <button type="button" id="btn-ambil-foto" class="btn btn-sm" style="background:#F05A1A;color:white;border-radius:8px;border:none;padding:8px 16px;cursor:pointer;display:none;font-weight:600;flex:1;"><i class="ri-focus-3-line"></i> Ambil Foto</button>
                                    <button type="button" id="btn-ulang-foto" class="btn btn-sm" style="background:#6B7280;color:white;border-radius:8px;border:none;padding:8px 16px;cursor:pointer;display:none;font-weight:600;flex:1;"><i class="ri-refresh-line"></i> Ulang Foto</button>
                                    
                                    <input type="file" id="fallback-file-input" accept="image/*" style="display:none;">
                                    <button type="button" id="btn-upload-foto" class="btn btn-sm" style="background:#3B82F6;color:white;border-radius:8px;border:none;padding:8px 16px;cursor:pointer;display:none;font-weight:600;flex:1;"><i class="ri-upload-2-line"></i> Pilih Foto Manual</button>
                                </div>
                            </div>

                        <input type="hidden" name="foto_base64" id="foto_base64" required>
                        <div id="foto-dok-error" style="display:none; color:#EF4444; font-size:12px; margin-top:8px; text-align:center;"><i class="ri-error-warning-line"></i> Wajib ambil foto dokumentasi.</div>
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom:20px;">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" name="keterangan" rows="3" style="resize:vertical;" placeholder="Opsional (contoh: untuk acara himpunan)"></textarea>
                </div>
                <button type="submit" class="btn btn-primary"
                        style="width:100%;height:44px;justify-content:center;font-size:14px;border-radius:8px;box-shadow:0 4px 12px rgba(240,90,26,.25);">
                    <i class="ri-hand-coin-line"></i> Konfirmasi Peminjaman
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Peminjaman -->
<div class="modal-overlay" id="modalEditPeminjaman">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div class="modal-title">Edit Peminjaman</div>
            <button class="modal-close" onclick="closeModal('modalEditPeminjaman')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formEditPeminjaman" method="post" enctype="multipart/form-data" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'ri-loader-4-line ri-spin\'></i> Menyimpan...';">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom:24px;">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Peminjam</label>
                        <input type="text" class="form-control" name="nama_peminjam" id="ep-nama" 
                               pattern="^[a-zA-Z\s\.,']+$" title="Nama hanya boleh berisi huruf"
                               oninput="this.value = this.value.replace(/[^a-zA-Z\s\.,']/g, '')" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">NIM / NIK</label>
                        <input type="text" class="form-control" name="nim_nik" id="ep-nimnik">
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon/WA</label>
                        <input type="text" class="form-control" name="no_telp" id="ep-notelp" 
                               pattern="^[0-9+]+$" title="No. Telepon hanya boleh berisi angka"
                               oninput="this.value = this.value.replace(/[^0-9+]/g, '')" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Jumlah</label>
                        <input type="number" class="form-control" name="jumlah" id="ep-jumlah" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status" id="ep-status">
                            <option value="dipinjam">Dipinjam</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="kembali">Kembali</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tanggal Pinjam</label>
                        <input type="date" class="form-control" name="tanggal_pinjam" id="ep-tgl-pinjam" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tenggat Kembali</label>
                        <input type="date" class="form-control" name="tenggat_kembali" id="ep-tenggat" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Keterangan</label>
                        <input type="text" class="form-control" name="keterangan" id="ep-ket" pattern=".*[a-zA-Z].*[a-zA-Z].*[a-zA-Z].*" title="Harus mengandung minimal 3 huruf alfabet">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Ganti Foto Dokumentasi</label>
                        <label for="ep-foto-input" style="display:flex;align-items:center;gap:8px;border:1.5px dashed #D1D5DB;border-radius:8px;padding:10px 14px;cursor:pointer;background:#FAFAFA;">
                            <i class="ri-camera-line" style="font-size:18px;color:#9CA3AF;"></i>
                            <span id="ep-foto-label" style="font-size:12px;color:#6B7280;">Biarkan kosong jika tidak ingin ganti</span>
                        </label>
                        <input type="file" id="ep-foto-input" name="foto_dokumentasi" accept="image/*" style="display:none;"
                               onchange="document.getElementById('ep-foto-label').textContent = this.files[0]?.name ?? 'Biarkan kosong jika tidak ingin ganti'">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"
                        style="width:100%;height:42px;justify-content:center;font-size:14px;border-radius:8px;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Lihat Detail Peminjaman -->
<div class="modal-overlay" id="modalDetailPeminjaman">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Detail Peminjaman</div>
            <button class="modal-close" onclick="closeModal('modalDetailPeminjaman')"><i class="ri-close-line"></i></button>
        </div>
        <div class="modal-body" style="padding-bottom:24px;">
            <!-- Header Block -->
            <div style="background:#F9FAFB;padding:20px;border-radius:12px;margin-bottom:24px;border:1px solid #F3F4F6;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                    <div>
                        <div style="font-size:12px;color:#6B7280;margin-bottom:4px;display:flex;align-items:center;gap:6px;"><i class="ri-barcode-line"></i> <span id="dt-kode-barang"></span></div>
                        <div style="font-size:20px;font-weight:800;color:#1F2937;" id="dt-nama-barang"></div>
                    </div>
                    <div style="text-align:right;">
                        <span id="dt-status" style="font-size:13px;padding:6px 14px;box-shadow:0 2px 4px rgba(0,0,0,0.05);border-radius:20px;font-weight:700;"></span>
                    </div>
                </div>
                
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;padding-top:16px;border-top:1px dashed #E5E7EB;">
                    <div>
                        <div style="font-size:11px;color:#6B7280;display:flex;align-items:center;gap:4px;margin-bottom:4px;"><i class="ri-user-star-line"></i> Penanggung Jawab</div>
                        <div style="font-size:14px;font-weight:600;color:#F05A1A;text-transform:capitalize;" id="dt-pj"></div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6B7280;display:flex;align-items:center;gap:4px;margin-bottom:4px;"><i class="ri-stack-line"></i> Jumlah Pinjam</div>
                        <div style="font-size:14px;font-weight:600;color:#374151;"><span id="dt-jumlah"></span> pcs</div>
                    </div>
                </div>
            </div>

            <!-- Details Block -->
            <div style="display:grid;grid-template-columns:1.2fr 1fr;gap:24px;margin-bottom:24px;padding:0 8px;">
                <!-- Kiri: Data Peminjam -->
                <div>
                    <div style="font-size:12px;color:#6B7280;margin-bottom:8px;display:flex;align-items:center;gap:4px;"><i class="ri-user-line"></i> Data Peminjam</div>
                    <div style="background:#F9FAFB;padding:12px 16px;border-radius:10px;border:1px solid #F3F4F6;">
                        <div style="font-size:15px;font-weight:700;color:#1F2937;text-transform:capitalize;line-height:1.4;margin-bottom:8px;" id="dt-peminjam"></div>
                        <div style="display:flex;gap:16px;">
                            <div>
                                <div style="font-size:11px;color:#9CA3AF;margin-bottom:2px;">NIM / NIK</div>
                                <div style="font-size:13px;color:#4B5563;font-weight:500;" id="dt-nimnik"></div>
                            </div>
                            <div>
                                <div style="font-size:11px;color:#9CA3AF;margin-bottom:2px;">Telp / WA</div>
                                <div style="font-size:13px;color:#4B5563;font-weight:500;" id="dt-notelp"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Kanan: Durasi Pinjam -->
                <div>
                    <div style="font-size:12px;color:#6B7280;margin-bottom:8px;display:flex;align-items:center;gap:4px;"><i class="ri-calendar-event-line"></i> Durasi Pinjam</div>
                    <div style="background:#F9FAFB;padding:12px 16px;border-radius:10px;border:1px solid #F3F4F6;">
                        <div style="margin-bottom:12px;">
                            <div style="font-size:11px;color:#9CA3AF;margin-bottom:2px;">Tanggal Pinjam (Mulai)</div>
                            <div style="font-size:14px;font-weight:600;color:#374151;" id="dt-tglpinjam"></div>
                        </div>
                        <div>
                            <div style="font-size:11px;color:#FCA5A5;margin-bottom:2px;">Batas Kembali (Tenggat)</div>
                            <div style="font-size:14px;font-weight:700;color:#EF4444;" id="dt-tenggat"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="padding:0 8px;margin-bottom:24px;">
                <div style="font-size:12px;color:#6B7280;margin-bottom:6px;display:flex;align-items:center;gap:4px;"><i class="ri-file-list-3-line"></i> Keterangan / Alasan</div>
                <div style="background:#F9FAFB;padding:14px;border-radius:8px;font-size:13px;color:#4B5563;border:1px solid #F3F4F6;min-height:50px;white-space:pre-wrap;" id="dt-keterangan"></div>
            </div>
            
            <div style="padding:0 8px;">
                <div style="font-size:12px;color:#6B7280;margin-bottom:8px;display:flex;align-items:center;gap:4px;"><i class="ri-image-line"></i> Foto Dokumentasi</div>
                <div style="text-align:center;">
                    <img id="dt-foto" src="" alt="Tidak ada foto" style="max-width:100%;max-height:250px;border-radius:8px;border:1px solid #E5E7EB;display:none;margin:0 auto;">
                    <div id="dt-nofoto" style="padding:20px;background:#F9FAFB;border-radius:8px;color:#9CA3AF;border:1px dashed #D1D5DB;display:none;font-size:13px;">Tidak ada foto dokumentasi</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Isi tanggal untuk print header
document.addEventListener('DOMContentLoaded', function() {
    // Setup Print Date
    var tglEl = document.getElementById('print-tgl');
    if (tglEl) {
        var d = new Date();
        tglEl.innerHTML = d.toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) + ' ' + d.toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
    }
});

// ——— Logika Bulk Actions Peminjaman ———
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAllPem');
    const checkItems = document.querySelectorAll('.check-item-pem');
    const bulkPanel = document.getElementById('bulkActionsPem');
    const countText = document.getElementById('countBulkPem');
    
    const btnKembali = document.getElementById('btnBulkKembaliPem');
    const btnHapus = document.getElementById('btnBulkHapusPem');
    
    function updateBulkPanel() {
        const checked = document.querySelectorAll('.check-item-pem:checked');
        const count = checked.length;
        
        if (count > 0) {
            bulkPanel.style.display = 'flex';
            countText.textContent = count;
            
            // Cek apakah ada yang masih aktif (dipinjam/terlambat) untuk tombol Tandai Kembali
            let hasActive = false;
            let hasReturned = false;
            
            checked.forEach(cb => {
                const st = cb.getAttribute('data-status');
                if (st === 'dipinjam' || st === 'terlambat') {
                    hasActive = true;
                }
                if (st === 'kembali') {
                    hasReturned = true;
                }
            });
            
            // Tampilkan tombol Kembali hanya jika ADA yang aktif dan TIDAK ADA yang sudah kembali
            btnKembali.style.display = (hasActive && !hasReturned) ? 'inline-flex' : 'none';
            // Tampilkan tombol Hapus (bisa untuk semuanya)
            btnHapus.style.display = 'inline-flex';
            
            checkAll.checked = (count === checkItems.length);
        } else {
            bulkPanel.style.display = 'none';
            checkAll.checked = false;
        }
    }
    
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkItems.forEach(cb => {
                // Hanya check row yang sedang terlihat (berkaitan dengan search JS jika ada)
                const tr = cb.closest('tr');
                if (tr && tr.style.display !== 'none') {
                    cb.checked = this.checked;
                } else if (!this.checked) {
                    cb.checked = false;
                }
            });
            updateBulkPanel();
        });
    }
    
    checkItems.forEach(cb => {
        cb.addEventListener('change', updateBulkPanel);
    });
});

window.cancelBulkSelectionPem = function() {
    const checkAll = document.getElementById('checkAllPem');
    const checkItems = document.querySelectorAll('.check-item-pem');
    const bulkPanel = document.getElementById('bulkActionsPem');
    
    if (checkAll) checkAll.checked = false;
    checkItems.forEach(cb => cb.checked = false);
    if (bulkPanel) bulkPanel.style.display = 'none';
};

window.submitBulkPem = function(action) {
    const checked = document.querySelectorAll('.check-item-pem:checked');
    if (checked.length === 0) return;
    
    let confirmMsg = '';
    let formAction = '';
    
    if (action === 'kembali') {
        confirmMsg = `Tandai ${checked.length} peminjaman terpilih sebagai sudah dikembalikan?`;
        formAction = '<?= base_url('peminjaman-barang/bulk-kembali') ?>';
    } else if (action === 'hapus') {
        confirmMsg = `Hapus ${checked.length} riwayat peminjaman secara permanen?`;
        formAction = '<?= base_url('peminjaman-barang/bulk-hapus') ?>';
    }
    
    if (typeof showSweetAlertConfirm === 'function') {
        showSweetAlertConfirm(confirmMsg, function() {
            executeBulkPemSubmit(action, formAction, checked);
        });
    } else {
        if (confirm(confirmMsg)) {
            executeBulkPemSubmit(action, formAction, checked);
        }
    }
};

function executeBulkPemSubmit(action, formAction, checked) {
    const form = document.getElementById('formBulkPem');
    const inputsContainer = document.getElementById('bulkInputsPem');
    
    inputsContainer.innerHTML = '';
    form.action = formAction;
    
    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id_peminjaman[]';
        input.value = cb.value;
        inputsContainer.appendChild(input);
    });
    
    form.submit();
}

// ——— Search cards (tetap client-side, hanya visual filter)
document.getElementById('cariBarangCard').addEventListener('input', function() {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#gridBarang .barang-card').forEach(function(card) {
        var txt = (card.getAttribute('data-nama') || '') + ' ' + (card.getAttribute('data-kat') || '');
        card.style.display = txt.includes(q) ? '' : 'none';
    });
});

// ——— Search table (server-side auto-submit)
var searchTimeout;
document.getElementById('cariPeminjaman').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => this.closest('form').submit(), 500);
});

// ——— Buka modal pinjam per barang
function openModalPinjam(idBarang, namaBarang, stok) {
    document.getElementById('pinjam-nama-barang').textContent = namaBarang;
    document.getElementById('pinjam-jumlah').max = stok;
    document.getElementById('pinjam-stok-info').textContent = '*Stok tersedia: ' + stok + ' pcs';
    document.getElementById('formPinjamBarang').action = '<?= base_url('peminjaman-barang/simpan') ?>/' + idBarang;
    // Reset form
    document.getElementById('formPinjamBarang').reset();
    
    // Reset camera UI
    const base64Input = document.getElementById('foto_base64');
    const videoEl = document.getElementById('kamera-video');
    const previewEl = document.getElementById('kamera-preview');
    const btnBuka = document.getElementById('btn-buka-kamera');
    const btnAmbil = document.getElementById('btn-ambil-foto');
    const btnUlang = document.getElementById('btn-ulang-foto');
    
    if(base64Input) base64Input.value = '';
    if(videoEl) videoEl.style.display = 'none';
    if(previewEl) previewEl.style.display = 'none';
    if(btnBuka) btnBuka.style.display = 'block';
    if(btnAmbil) btnAmbil.style.display = 'none';
    if(btnUlang) btnUlang.style.display = 'none';
    if(window.stopCamera) window.stopCamera();
    
    var errorDok = document.getElementById('foto-dok-error');
    if (errorDok) errorDok.style.display = 'none';
    document.querySelector('[name="tanggal_pinjam"]').value = '<?= date('Y-m-d') ?>';
    openModal('modalPinjamBarang');
}

// ——— Handler Kamera
let cameraStream = null;
function bukaKamera() {
    const videoEl = document.getElementById('kamera-video');
    const previewEl = document.getElementById('kamera-preview');
    const btnBuka = document.getElementById('btn-buka-kamera');
    const btnAmbil = document.getElementById('btn-ambil-foto');
    const btnUlang = document.getElementById('btn-ulang-foto');
    const base64Input = document.getElementById('foto_base64');

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(stream) {
            cameraStream = stream;
            videoEl.srcObject = stream;
            videoEl.style.display = 'block';
            previewEl.style.display = 'none';
            btnBuka.style.display = 'none';
            btnAmbil.style.display = 'block';
            btnUlang.style.display = 'none';
            base64Input.value = '';
        })
        .catch(function(err) {
            alert("Tidak dapat mengakses kamera: " + err.message);
        });
}

function ambilFoto() {
    const videoEl = document.getElementById('kamera-video');
    const canvasEl = document.getElementById('kamera-canvas');
    const previewEl = document.getElementById('kamera-preview');
    const btnAmbil = document.getElementById('btn-ambil-foto');
    const btnUlang = document.getElementById('btn-ulang-foto');
    const base64Input = document.getElementById('foto_base64');
    
    canvasEl.width = videoEl.videoWidth;
    canvasEl.height = videoEl.videoHeight;
    const ctx = canvasEl.getContext('2d');
    ctx.drawImage(videoEl, 0, 0, canvasEl.width, canvasEl.height);
    const dataUrl = canvasEl.toDataURL('image/jpeg', 0.8);
    
    stopCamera();
    videoEl.style.display = 'none';
    
    previewEl.src = dataUrl;
    previewEl.style.display = 'block';
    
    base64Input.value = dataUrl;
    
    btnAmbil.style.display = 'none';
    btnUlang.style.display = 'block';
    
    const errorEl = document.getElementById('foto-dok-error');
    if(errorEl) errorEl.style.display = 'none';
}

window.stopCamera = function() {
    if(cameraStream) {
        cameraStream.getTracks().forEach(track => track.stop());
        cameraStream = null;
    }
};

const btnBuka = document.getElementById('btn-buka-kamera');
if(btnBuka) btnBuka.addEventListener('click', bukaKamera);
const btnAmbil = document.getElementById('btn-ambil-foto');
if(btnAmbil) btnAmbil.addEventListener('click', ambilFoto);
const btnUlang = document.getElementById('btn-ulang-foto');
if(btnUlang) btnUlang.addEventListener('click', bukaKamera);

const btnUpload = document.getElementById('btn-upload-foto');
const fallbackInput = document.getElementById('fallback-file-input');

if (btnUpload && fallbackInput) {
    btnUpload.addEventListener('click', function() {
        fallbackInput.click();
    });
    
    fallbackInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                const previewEl = document.getElementById('kamera-preview');
                const base64Input = document.getElementById('foto_base64');
                const videoEl = document.getElementById('kamera-video');
                
                if(videoEl) videoEl.style.display = 'none';
                previewEl.src = evt.target.result;
                previewEl.style.display = 'block';
                base64Input.value = evt.target.result;
                
                document.getElementById('btn-upload-foto').style.display = 'none';
                document.getElementById('btn-ulang-foto').style.display = 'block';
                
                // Ubah fungsi tombol ulang agar membuka file manager bukan kamera
                const btnUlangEl = document.getElementById('btn-ulang-foto');
                btnUlangEl.removeEventListener('click', bukaKamera);
                btnUlangEl.onclick = function() { fallbackInput.click(); };
            };
            reader.readAsDataURL(e.target.files[0]);
        }
    });
}

// ——— Validasi form Tambah Peminjaman: foto wajib
document.addEventListener('DOMContentLoaded', function() {
    var formPinjam = document.getElementById('formPinjamBarang');
    if (formPinjam) {
        formPinjam.addEventListener('submit', function(e) {
            var fotoVal = document.getElementById('foto_base64').value;
            var errorEl = document.getElementById('foto-dok-error');
            if (!fotoVal) {
                e.preventDefault();
                if (errorEl) errorEl.style.display = 'block';
                document.getElementById('camera-container').scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
});

// ——— Edit peminjaman
function openModalEditPeminjaman(p) {
    document.getElementById('ep-nama').value      = p.nama_peminjam;
    document.getElementById('ep-nimnik').value    = p.nim_nik;
    document.getElementById('ep-notelp').value    = p.no_telp || '';
    document.getElementById('ep-jumlah').value    = p.jumlah;
    document.getElementById('ep-status').value    = p.status || 'dipinjam';
    document.getElementById('ep-tgl-pinjam').value = p.tanggal_pinjam || '';
    document.getElementById('ep-tenggat').value  = p.tenggat_kembali || '';
    document.getElementById('ep-ket').value      = p.keterangan || '';
    document.getElementById('ep-foto-label').textContent = p.foto_dokumentasi ? 'Foto: ' + p.foto_dokumentasi : 'Biarkan kosong jika tidak ingin ganti';

    document.getElementById('formEditPeminjaman').action = '<?= base_url('peminjaman-barang') ?>/' + p.hash + '/edit';
    openModal('modalEditPeminjaman');
}

// ——— Buka modal detail peminjaman
function openModalDetailPeminjaman(p) {
    document.getElementById('dt-peminjam').textContent = p.nama_peminjam || '-';
    document.getElementById('dt-nimnik').textContent   = p.nim_nik || '—';
    document.getElementById('dt-notelp').textContent   = p.no_telp || '—';
    document.getElementById('dt-kode-barang').textContent = p.kode_barang || '—';
    document.getElementById('dt-nama-barang').textContent = p.nama_barang || '—';
    document.getElementById('dt-pj').textContent       = p.penanggung_jawab || '—';
    document.getElementById('dt-jumlah').textContent   = p.jumlah || '-';
    
    // Format date
    const formatDate = (dateStr) => {
        if (!dateStr) return '-';
        const d = new Date(dateStr);
        return d.toLocaleDateString('id-ID', {day: '2-digit', month: '2-digit', year: 'numeric'});
    };
    
    document.getElementById('dt-tglpinjam').textContent = formatDate(p.tanggal_pinjam);
    document.getElementById('dt-tenggat').textContent = formatDate(p.tenggat_kembali);
    
    let color = '#374151', bg = '#F3F4F6';
    let statusText = p.status ? p.status.toUpperCase() : '-';
    if(p.status === 'dipinjam') { color = '#1D4ED8'; bg = '#DBEAFE'; }
    else if(p.status === 'kembali') { color = '#15803D'; bg = '#DCFCE7'; }
    else if(p.status === 'terlambat') { color = '#B91C1C'; bg = '#FEE2E2'; }
    
    document.getElementById('dt-status').textContent = statusText;
    document.getElementById('dt-status').style.color = color;
    document.getElementById('dt-status').style.backgroundColor = bg;

    document.getElementById('dt-keterangan').textContent = p.keterangan || '-';
    
    if (p.foto_dokumentasi) {
        document.getElementById('dt-foto').src = '<?= base_url('uploads/peminjaman') ?>/' + p.foto_dokumentasi;
        document.getElementById('dt-foto').style.display = 'inline-block';
        document.getElementById('dt-nofoto').style.display = 'none';
    } else {
        document.getElementById('dt-foto').style.display = 'none';
        document.getElementById('dt-nofoto').style.display = 'block';
    }

    openModal('modalDetailPeminjaman');
}
</script>
<?= $this->endSection() ?>
