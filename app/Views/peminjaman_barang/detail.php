<?= $this->extend('templates/layout') ?>

<?= $this->section('head') ?>
<style>
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

<?php 
// ── Jumlah unit aktif (untuk JS) ───────────────────────────────────
$activeCount = 0;
foreach ($barang_list as $b) {
    if ($b['status'] === 'digunakan') $activeCount++;
}
?>

<div id="content-screen">
<!-- Breadcrumb / Back Button -->
<div class="print-hide" style="display: flex; align-items: center; margin-bottom: 24px; padding-top: 8px;">
    <div style="font-size: 20px; font-weight: 700; color: #1F2937; display: flex; align-items: center; gap: 12px;">
        <a href="<?= base_url('peminjaman-barang') ?>" style="color: #4B5563; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; text-decoration: none; border-radius: 10px; background: #F3F4F6; transition: all 0.2s; border: 1px solid #E5E7EB;" onmouseover="this.style.background='#E5E7EB'; this.style.color='#1F2937';" onmouseout="this.style.background='#F3F4F6'; this.style.color='#4B5563';" title="Kembali ke Halaman Peminjaman">
            <i class="ri-arrow-left-line" style="font-size: 20px;"></i>
        </a>
        <span>Detail Stok Peminjaman: <span style="color: var(--primary);"><?= esc($barang['nama_barang']) ?></span></span>
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
    <div style="padding:20px 24px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:16px;">
        <div style="display:flex; gap:16px; align-items:center;">
            <?php if (!empty($barang['foto_barang']) && file_exists(FCPATH . 'uploads/' . $barang['foto_barang'])): ?>
                <img src="<?= base_url('uploads/' . $barang['foto_barang']) ?>" alt="" style="width:80px;height:80px;object-fit:cover;border-radius:12px;border:1px solid #E5E7EB;">
            <?php else: ?>
                <div style="width: 80px; height: 80px; border-radius: 12px; background: #FFF3EE; color: #F05A1A; display: flex; align-items: center; justify-content: center; font-size: 36px; border: 1px solid #FCD9C5; flex-shrink: 0;">
                    <i class="ri-box-3-line"></i>
                </div>
            <?php endif; ?>
            <div>
                <div style="font-size:26px;font-weight:800;color:#F05A1A;margin-bottom:4px;"><?= esc($barang['nama_barang']) ?></div>
                <?php if (!empty($barang['merk'])): ?>
                <div style="font-size:15px;font-weight:600;color:#374151;display:flex;align-items:center;gap:6px;">
                    <i class="ri-price-tag-3-line"></i> Merk: <?= esc($barang['merk']) ?>
                </div>
                <?php endif; ?>
                <div style="font-size:13px;font-weight:600;color:#6B7280;margin-top:6px;display:flex;align-items:center;gap:6px;">
                    <i class="ri-user-star-line"></i> PJ: <?= esc($penanggung_jawab ?: '—') ?>
                    <?php if (can('admin', 'koordinator_lab')): ?>
                        <button type="button" class="aksi-btn" style="background:#F3F4F6;color:#374151;width:24px;height:24px;" onclick="openModalEditPJ()" title="Edit Penanggung Jawab">
                            <i class="ri-edit-line"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div style="background:#22C55E;color:white;padding:10px 20px;border-radius:8px;font-weight:700;font-size:14px;display:flex;align-items:center;gap:8px;box-shadow:0 4px 6px rgba(0,0,0,.1);">
            <i class="ri-check-line" style="font-size: 20px;"></i> Tersedia: <?= $barang['stok_peminjaman'] ?> Unit
        </div>
    </div>
</div>

<!-- Table Section -->
<div class="table-section">
    <div class="table-toolbar" style="padding:20px 24px 16px;">
        <div class="table-toolbar-left" style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
            <span class="table-title">Inventaris Barang Per Unit</span>
            
            <div class="search-box" style="border-radius:8px;background:white;width:240px;">
                <i class="ri-search-line search-icon"></i>
                <input type="text" id="cariBarang" placeholder="Cari unit..." autocomplete="off" />
            </div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">

            <label style="font-size:12px;color:#6B7280;display:flex;align-items:center;gap:6px;cursor:pointer;user-select:none;">
                <input type="checkbox" id="toggleDitarik" onchange="toggleRiwayat(this.checked)" style="accent-color:#F05A1A;">
                Tampilkan riwayat
            </label>

            <button onclick="window.print()" style="background:#FFF3EE;color:#F05A1A;border:1px solid #FCD9C5;padding:10px 16px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:.2s;" onmouseover="this.style.background='#FFD5C2';" onmouseout="this.style.background='#FFF3EE';">
                <i class="ri-printer-line" style="font-size: 16px;"></i> Export
            </button>
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
                    <th>Kategori</th>
                    <th style="text-align:center;width:80px;">No. Unit</th>
                    <th style="text-align:center;">Tgl Distribusi</th>
                    <th style="text-align:center;">Kondisi</th>
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
                        $kondisiClass = match($kondisi) {
                            'Rusak Ringan', 'Perlu ganti' => 'badge-rusak-ringan',
                            'Rusak Berat', 'Rusak', 'Tidak bisa digunakan' => 'badge-rusak-berat',
                            default        => 'badge-baik',
                        };
                        $kondisiIcon = match($kondisi) {
                            'Rusak Ringan', 'Perlu ganti' => '<i class="ri-refresh-line"></i>',
                            'Rusak Berat', 'Rusak', 'Tidak bisa digunakan' => '<i class="ri-close-line"></i>',
                            default        => '<i class="ri-check-line"></i>',
                        };
                        $tglDist      = $b['tanggal'] ? date('d/m/y', strtotime($b['tanggal'])) : '—';
                        $sudahAlokasi = !empty($b['alokasi_ke_ruangan']);
                        $katLower     = strtolower($b['nama_kategori'] ?? 'lainnya');

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
                    >
                        <?php if (can('admin','koordinator_lab')): ?>
                        <td style="text-align:center;">
                            <?php if (!$sudahAlokasi): ?>
                                <input type="checkbox" name="id_distribusi[]" class="check-tarik" data-type="<?= $isDitarik ? 'history' : 'active' ?>" value="<?= encode_id($b['id_distribusi']) ?>" style="accent-color:#F05A1A;" onchange="updateBulkTarikBtn()">
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                        <td style="text-align:center;color:#9CA3AF;font-size:12px;"><?= $isDitarik ? '–' : $noUrut ?></td>
                        <td>
                            <div style="font-size:12px;color:#4B5563;font-weight:600;"><?= esc($kodeBarang) ?></div>
                        </td>
                        <td>
                            <div style="font-weight:600;color:#374151;"><?= esc($b['nama_barang']) ?></div>
                        </td>
                        <td style="color:#6B7280;font-size:13px;"><?= esc($b['kategori'] ?? '—') ?></td>
                        <td style="text-align:center;">
                            <?php if (!$isDitarik): ?>
                                <span class="unit-badge">#<?= $unitNo ?></span>
                            <?php else: ?>
                                <span class="unit-badge" style="background:#F3F4F6;color:#9CA3AF;border:1px solid #E5E7EB;">#<?= $unitNo ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;font-size:13px;color:#6B7280;"><?= $tglDist ?></td>
                        <td style="text-align:center;">
                            <span class="badge <?= $kondisiClass ?>" style="font-size:11px;padding:3px 10px; display:inline-flex; align-items:center; gap:4px;">
                                <?= $kondisiIcon ?> <?= esc($kondisi) ?>
                            </span>
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
                                <!-- Lapor Rusak -->
                                <button type="button" class="aksi-btn" style="background:#EF4444;color:white;"
                                    onclick="openModalLaporRusak(<?= $b['id_distribusi'] ?>, '<?= addslashes(htmlspecialchars($b['nama_barang'], ENT_COMPAT)) ?>', <?= (int)$unitNo ?>)"
                                    title="Laporkan Rusak">
                                    <i class="ri-alarm-warning-line"></i>
                                </button>
                                <!-- Tarik ke Gudang -->
                                <form method="post" action="<?= base_url('barang/distribusi/' . $b['id_distribusi'] . '/tarik') ?>" style="display:inline;"
                                    onsubmit="return confirm('Tarik 1 unit <?= addslashes(htmlspecialchars($b['nama_barang'], ENT_COMPAT)) ?> #<?= $unitNo ?> ke gudang?')">
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
                                        <button type="button" class="aksi-btn" style="background:#FEE2E2;color:#EF4444;border:1px solid #FECACA;width:28px;height:28px;border-radius:6px;transition:all 0.2s;box-shadow:0 1px 2px rgba(239, 68, 68, 0.1);"
                                            onmouseover="this.style.background='#FECACA'; this.style.transform='translateY(-1px)';"
                                            onmouseout="this.style.background='#FEE2E2'; this.style.transform='none';"
                                            onclick="if(confirm('Hapus riwayat barang <?= addslashes(htmlspecialchars($b['nama_barang'], ENT_COMPAT)) ?> #<?= $unitNo ?>?')){ document.getElementById('formHapusRiwayat_<?= $b['id_distribusi'] ?>').submit(); }"
                                            title="Hapus Riwayat">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                        
                                        <form id="formHapusRiwayat_<?= $b['id_distribusi'] ?>" action="<?= base_url('data-barang-ruangan/hapus-distribusi') ?>" method="post" style="display:none;">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id_distribusi" value="<?= encode_id($b['id_distribusi']) ?>">
                                        </form>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- Modal Info Detail per Unit -->
                    <?php if (!$isDitarik): ?>
                    <div class="modal-overlay" id="modalInfo_<?= $b['id_distribusi'] ?>">
                        <div class="modal-box" style="max-width:520px;">
                            <div class="modal-header">
                                <div>
                                    <div class="modal-title" style="color:#F05A1A;">
                                        <?= esc($b['nama_barang']) ?>
                                        <span class="unit-badge" style="margin-left:6px;font-size:13px;">#<?= $unitNo ?></span>
                                    </div>
                                    <div style="font-size:12px;color:#6B7280;">Kode: <?= esc($b['kode_barang']) ?></div>
                                </div>
                                <button type="button" class="modal-close" onclick="closeModal('modalInfo_<?= $b['id_distribusi'] ?>')"><i class="ri-close-line"></i></button>
                            </div>
                            <div class="modal-body" style="padding-bottom:24px;">
                                <?php if (!empty($b['foto_barang'])): ?>
                                <div style="width:100%; display:flex; justify-content:center; margin-bottom: 16px;">
                                    <img src="<?= base_url('uploads/' . $b['foto_barang']) ?>" alt="Foto <?= esc($b['nama_barang']) ?>" style="max-height: 180px; max-width: 100%; object-fit: contain; border-radius: 8px; border: 1px solid #E5E7EB;">
                                </div>
                                <?php else: ?>
                                <div style="width:100%; height:120px; display:flex; flex-direction:column; justify-content:center; align-items:center; background:#F3F4F6; border-radius:8px; margin-bottom: 16px; color:#9CA3AF; border: 1px dashed #D1D5DB;">
                                    <i class="ri-image-line" style="font-size: 32px;"></i>
                                    <span style="font-size: 12px; margin-top: 4px;">Tidak ada foto</span>
                                </div>
                                <?php endif; ?>

                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                                    <?php foreach ([
                                        ['Kategori',        esc($b['kategori'] ?? '—')],
                                        ['Jenis Barang',    esc($b['nama_kategori'] ?? '—')],
                                        ['Merk',            esc($b['merk'] ?? '—')],
                                        ['Kondisi',         esc($kondisi)],
                                        ['Penanggung Jawab',esc($b['penanggung_jawab'] ?: '—')],
                                        ['Tgl Distribusi',  $tglDist],
                                        ['Status',          $isDitarik ? ($b['status'] === 'pelaporan_rusak' ? 'Pelaporan Rusak' : ($b['status'] === 'digudang' ? 'Di Gudang' : 'Dikembalikan ke Gudang')) : 'Aktif (Tersedia)'],
                                    ] as [$k, $v]): ?>
                                    <div style="background:#F9FAFB;border-radius:8px;padding:10px 14px;">
                                        <div style="font-size:11px;color:#9CA3AF;font-weight:600;margin-bottom:3px;"><?= $k ?></div>
                                        <div style="font-size:13px;font-weight:600;color:#374151;"><?= $v ?></div>
                                    </div>
                                    <?php endforeach; ?>
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


<!-- ============================================================
     Modal: Edit Penanggung Jawab
============================================================ -->
<div class="modal-overlay" id="modalEditPJ">
    <div class="modal-box" style="max-width: 400px;">
        <div class="modal-header">
            <div>
                <div class="modal-title">Edit Penanggung Jawab</div>
                <div style="font-size:12px;color:#6B7280;margin-top:2px;">Pool Peminjaman Barang</div>
            </div>
            <button type="button" class="modal-close" onclick="closeModal('modalEditPJ')"><i class="ri-close-line"></i></button>
        </div>
        <form action="<?= base_url('peminjaman-barang/' . encode_id($barang['id']) . '/update-pj') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom:24px;">
                <div class="form-group">
                    <label class="form-label">Nama Penanggung Jawab</label>
                    <input type="text" class="form-control" name="penanggung_jawab" required
                           value="<?= esc($penanggung_jawab) ?>"
                           pattern=".*[a-zA-Z].*[a-zA-Z].*[a-zA-Z].*" title="Harus mengandung minimal 3 huruf alfabet"
                           placeholder="Masukkan nama PJ">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;height:42px;justify-content:center;">
                    Simpan Perubahan
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
        Laporan Rincian Unit Barang Peminjaman
        <div style="font-size: 11px; color: #F05A1A; font-weight: 600; margin-top: 6px; letter-spacing: 0; text-transform: none;">
            Sumber Data: Data Distribusi & Peminjaman
        </div>
    </div>

    <!-- A. INFO BARANG -->
    <div class="pr-section-title">A. Informasi Barang</div>
    <table class="pr-info-table">
        <tbody>
            <tr>
                <td>Kode Barang</td>
                <td><?= esc($barang['kode_barang']) ?></td>
            </tr>
            <tr>
                <td>Nama Barang</td>
                <td><?= esc($barang['nama_barang']) ?></td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td><?= esc($barang['nama_kategori'] ?? 'Lainnya') ?></td>
            </tr>
            <tr>
                <td>Stok Peminjaman Tersedia</td>
                <td><?= $barang['stok_peminjaman'] ?> unit</td>
            </tr>
            <tr>
                <td>Total Unit Aktif (Di Pool)</td>
                <td><?= $activeCount ?> unit</td>
            </tr>
        </tbody>
    </table>

    <!-- B. DAFTAR INVENTARIS -->
    <div class="pr-section-title">B. Daftar Unit Aset</div>
    <table>
        <thead>
            <tr>
                <th style="width:6%">No</th>
                <th style="width:12%">Kode</th>
                <th>Nama Barang</th>
                <th style="width:12%">Kategori</th>
                <th style="width:10%">No. Unit</th>
                <th style="width:15%">Tgl Distribusi</th>
                <th style="width:12%">Kondisi</th>
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
                <td style="text-align:center;font-weight:700;color:#F05A1A;"><?= esc($kodeBarang) ?></td>
                <td><?= esc($b['nama_barang']) ?><?= !empty($b['merk']) ? '<br><small>Merk: '.$b['merk'].'</small>' : '' ?></td>
                <td><?= esc($b['nama_kategori'] ?? '—') ?></td>
                <td style="text-align:center;font-family:monospace;">#<?= $unitNo ?></td>
                <td style="text-align:center;"><?= $tglDist ?></td>
                <td style="text-align:center;"><?= esc($kondisi) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if ($no === 0): ?>
            <tr><td colspan="7" style="text-align:center;">Belum ada barang aktif di ruangan ini</td></tr>
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

<?php if (can('admin','koordinator_lab')): ?>
<!-- Bulk Actions Panel (Floating) -->
<div id="bulkActions" style="display: none; position: fixed; bottom: 24px; left: 50%; transform: translateX(-50%); background: white; padding: 12px 24px; border-radius: 100px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1); border: 1px solid #E5E7EB; align-items: center; gap: 16px; z-index: 50;">
    <div style="display: flex; align-items: center; gap: 8px;">
        <div style="background: #F05A1A; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700;" id="countTarik">0</div>
        <span style="font-size: 14px; font-weight: 600; color: #374151;">terpilih</span>
    </div>
    <div style="width: 1px; height: 24px; background: #E5E7EB;"></div>
    
    <div style="display: flex; gap: 8px;">
        <button type="button" id="btnTarikAction" onclick="submitBulkTarik()" class="btn" style="background: #FFF3EE; color: #F05A1A; border: 1px solid #FCD9C5; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
            <i class="ri-arrow-go-back-line"></i> Tarik Terpilih
        </button>
        
        <button type="button" id="btnHapusAction" onclick="submitBulkHapus()" class="btn" style="background: #FEF2F2; color: #EF4444; border: 1px solid #FECACA; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600;">
            <i class="ri-delete-bin-line"></i> Hapus Riwayat
        </button>
        
        <button type="button" onclick="cancelBulkSelection()" class="btn" style="background: #F3F4F6; color: #4B5563; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; border: 1px solid #E5E7EB;">
            Batal
        </button>
    </div>
</div>
<?php endif; ?>

<!-- Form tersembunyi untuk proses massal (dieksekusi via JS) -->
<form id="formBulkTarik" action="<?= base_url('data-barang-ruangan/tarik-bulk') ?>" method="post" style="display:none;">
    <?= csrf_field() ?>
</form>
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
    var filterKat = document.getElementById('filterKategoriDropdown') ? document.getElementById('filterKategoriDropdown').value : 'semua';
    var trKat = (tr.getAttribute('data-kat') || '').toLowerCase();
    if (filterKat !== 'semua' && !trKat.includes(filterKat)) {
        return false;
    }
    
    // 2. Filter Riwayat (Ditarik)
    var isDitarik = tr.getAttribute('data-ditarik') === '1';
    var showDitarik = document.getElementById('toggleDitarik').checked;
    if (isDitarik && !showDitarik) {
        return false;
    }
    
    return true;
});

// ── Bulk Tarik JS ────────────────────────────────────────────────────────
function updateBulkTarikBtn() {
    const checks = document.querySelectorAll('.check-tarik:checked');
    const bulkActions = document.getElementById('bulkActions');
    const btnTarik = document.getElementById('btnTarikAction');
    const btnHapus = document.getElementById('btnHapusAction');
    
    if (bulkActions) {
        if (checks.length > 0) {
            bulkActions.style.display = 'flex';
            document.getElementById('countTarik').innerText = checks.length;
            
            let hasActive = false;
            let hasHistory = false;
            checks.forEach(c => {
                if (c.getAttribute('data-type') === 'active') hasActive = true;
                if (c.getAttribute('data-type') === 'history') hasHistory = true;
            });
            
            if (btnTarik) btnTarik.style.display = hasActive ? 'inline-flex' : 'none';
            if (btnHapus) btnHapus.style.display = hasHistory ? 'inline-flex' : 'none';
            
        } else {
            bulkActions.style.display = 'none';
            document.getElementById('checkAllTarik').checked = false;
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
    var checks = dt.$('.check-tarik:checked[data-type="active"]');
    if (checks.length === 0) return;

    var msg = `Anda yakin ingin menarik ${checks.length} barang terpilih kembali ke gudang?`;
    if (typeof showSweetAlertConfirm === 'function') {
        showSweetAlertConfirm(msg, function() {
            var form = document.getElementById('formBulkTarik');
            form.action = '<?= base_url('data-barang-ruangan/tarik-bulk') ?>';
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

function submitBulkHapus() {
    var dt = $('#tabelBarangRuangan').DataTable();
    var checks = dt.$('.check-tarik:checked[data-type="history"]');
    if (checks.length === 0) return;

    var msg = `Anda yakin ingin menghapus ${checks.length} riwayat terpilih secara permanen?`;
    if (typeof showSweetAlertConfirm === 'function') {
        showSweetAlertConfirm(msg, function() {
            var form = document.getElementById('formBulkTarik');
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

// ── Filter Kategori ──────────────────────────────────────────────────────
function filterKategoriDropdown(kat) {
    $('#tabelBarangRuangan').DataTable().draw();
}



// ── Modal Lapor Rusak ────────────────────────────────────────────────────
function openModalLaporRusak(idDistribusi, namaBarang, unitNo) {
    document.getElementById('lr-nama').textContent = namaBarang + ' #' + unitNo;
    document.getElementById('formLaporRusak').action =
        '<?= base_url('data-barang-ruangan/distribusi') ?>/' + idDistribusi + '/lapor-rusak';
    openModal('modalLaporRusak');
}

// ── Modal Edit PJ ─────────────────────────────────────────────────────────
function openModalEditPJ() {
    openModal('modalEditPJ');
}
</script>
<?= $this->endSection() ?>
