<?= $this->extend('templates/layout') ?>

<?= $this->section('head') ?>
<style>
/* ============================================================
   PRINT STYLES — Halaman Detail Barang
   Format: Laporan Resmi (Tabel Bersih, tanpa UI dekoratif)
============================================================ */
@media screen {
    /* Sembunyikan elemen khusus cetak di layar */
    #print-report, .print-header-only { display: none; }
}

@media print {
    /* Sembunyikan seluruh tampilan UI dan toolbar CI */
    .print-hide,
    .app-shell > .sidebar,
    .flash-alert,
    button,
    .btn,
    #toolbarContainer,
    [id="print-hide-all"] { display: none !important; }

    /* Reset layout sidebar */
    .app-shell { display: block !important; }
    .sidebar   { display: none !important; }
    .main-content {
        margin-left: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .page-content { padding: 0 !important; }
    .page-footer  { display: none !important; }

    /* Sembunyikan seluruh konten web, hanya tampilkan laporan */
    #content-screen { display: none !important; }
    #print-report   { display: block !important; }

    /* Gaya tabel laporan */
    #print-report {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        font-size: 12px;
        color: #1F2937;
        padding: 0; /* Menggunakan @page margin untuk mencegah mentok di halaman 2 */
        line-height: 1.5;
        background: white;
    }
    #print-report .pr-kop {
        display: flex;
        align-items: center;
        position: relative;
        border-bottom: 3px double #F05A1A; /* Garis ganda khas surat resmi */
        padding-bottom: 16px;
        margin-bottom: 24px;
    }
    #print-report .pr-kop img {
        width: 85px;
        position: absolute;
        left: 10px;
    }
    #print-report .pr-kop-text {
        width: 100%;
        text-align: center;
        padding: 0 100px; /* Sisakan ruang untuk logo */
    }
    #print-report .pr-kop h2 {
        font-size: 18px; font-weight: 800; margin: 0 0 4px 0; text-transform: uppercase; color: #F05A1A; letter-spacing: 0.5px;
    }
    #print-report .pr-kop h3 {
        font-size: 14px; font-weight: 700; margin: 0 0 6px 0; text-transform: uppercase; color: #374151;
    }
    #print-report .pr-kop p {
        font-size: 11px; margin: 0; color: #6B7280; font-weight: 400; line-height: 1.4;
    }
    #print-report .pr-title {
        font-size: 14px; font-weight: 700; text-align: center;
        margin-bottom: 20px; text-transform: uppercase; color: #374151; letter-spacing: 0.5px;
    }
    #print-report table {
        width: 100%; border-collapse: collapse; margin-bottom: 24px;
    }
    #print-report table th, #print-report table td {
        border: 1px solid #CBD5E1; padding: 10px 12px; font-size: 11px; vertical-align: middle;
    }
    #print-report table thead tr th {
        background: #F05A1A !important;
        color: white !important;
        border: 1px solid #DF4D0F !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        font-weight: 700; text-align: center;
        text-transform: uppercase; letter-spacing: 0.5px; font-size: 10px;
    }
    #print-report .pr-section-title {
        font-weight: 700; font-size: 13px; margin: 0 0 10px 0;
        color: #F05A1A; display: flex; align-items: center; gap: 8px;
    }
    #print-report .pr-info-table td {
        border: 1px solid #CBD5E1 !important;
    }
    #print-report .pr-info-table td:first-child {
        width: 30%; font-weight: 600; background: #FFF7F5 !important; color: #F05A1A;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    #print-report .pr-footer {
        margin-top: 40px; display: flex; justify-content: flex-end;
        font-size: 12px; color: #374151;
    }
    #print-report .pr-ttd {
        text-align: center; min-width: 200px;
    }
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
    
    @page { 
        margin: 0; /* Reset margin 0 agar teks URL/Tanggal bawaan browser hilang total */
    }
    body { margin: 0 !important; }
}
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>

<?php
$b = $barang;
$total = (int)$b['stok_gudang'] + (int)$b['jumlah_digunakan'] + (int)($b['stok_peminjaman'] ?? 0) + (int)$b['jumlah_dipinjam'] + (int)$b['jumlah_rusak'];
?>

<div id="content-screen">
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

<!-- Page Header for Print -->
<div class="print-header-only">
    Laporan Detail Aset: <?= esc($b['nama_barang']) ?>
</div>

<!-- Breadcrumb & Page Header -->
<div class="print-hide" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-top: 8px;">
    <div style="font-size: 20px; font-weight: 700; color: #1F2937; display: flex; align-items: center; gap: 12px;">
        <a href="<?= base_url('dashboard') ?>" style="color: #4B5563; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; text-decoration: none; border-radius: 10px; background: #F3F4F6; transition: all 0.2s; border: 1px solid #E5E7EB;" onmouseover="this.style.background='#E5E7EB'; this.style.color='#1F2937';" onmouseout="this.style.background='#F3F4F6'; this.style.color='#4B5563';" title="Kembali ke Dashboard">
            <i class="ri-arrow-left-line" style="font-size: 20px;"></i>
        </a>
        <span>Detail Aset: <span style="color: var(--primary);"><?= esc($b['nama_barang']) ?></span></span>
    </div>
    <div style="display:flex;gap:12px;">
        <button class="btn btn-outline" onclick="openModal('modalRestockBarang')" style="border: 1px solid #10B981; color: #10B981; background: transparent; padding: 10px 20px; font-size: 13px; border-radius: 6px; display: flex; align-items: center; gap: 6px; font-weight: 500;">
            <i class="ri-add-box-line" style="font-size: 16px;"></i> Restock
        </button>
        <button class="btn btn-outline" onclick="openModal('modalEditBarang')" id="btnEditBarang" style="border: 1px solid var(--primary); color: var(--primary); background: transparent; padding: 10px 20px; font-size: 13px; border-radius: 6px; display: flex; align-items: center; gap: 6px; font-weight: 500;">
            <i class="ri-edit-line" style="font-size: 16px;"></i> Edit
        </button>
        <button onclick="window.print()" id="btnExportDetail" style="background:#FFF3EE;color:#F05A1A;border:1px solid #FCD9C5;padding:10px 20px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px;transition:.2s;" onmouseover="this.style.background='#FFD5C2';" onmouseout="this.style.background='#FFF3EE';">
            <i class="ri-printer-line" style="font-size: 16px;"></i> Export
        </button>
    </div>
</div>

<!-- ============================================================
     Stats Cards
============================================================ -->
<div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 16px; margin-bottom: 24px;">
    <!-- Total Aset -->
    <div style="background: #FFFFFF; border: 1px solid var(--gray-200); border-radius: 12px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 44px; height: 44px; border-radius: 8px; background: #FCECE4; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 22px;">
                <i class="ri-box-3-line"></i>
            </div>
            <div style="font-size: 24px; font-weight: 700; color: var(--primary);"><?= $total ?></div>
        </div>
        <div style="font-size: 13px; color: var(--gray-500); font-weight: 500;">Total Aset</div>
    </div>
    
    <!-- Stok Gudang -->
    <div style="background: #FFFFFF; border: 1px solid var(--gray-200); border-radius: 12px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 44px; height: 44px; border-radius: 8px; background: #FCECE4; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 22px;">
                <i class="ri-archive-line"></i>
            </div>
            <div style="font-size: 24px; font-weight: 700; color: var(--primary);"><?= (int)$b['stok_gudang'] ?></div>
        </div>
        <div style="font-size: 13px; color: var(--gray-500); font-weight: 500;">Stok Gudang</div>
    </div>
    
    <!-- Digunakan (Total Distribusi: Ruangan + Peminjaman) -->
    <div style="background: #FFFFFF; border: 1px solid var(--gray-200); border-radius: 12px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 44px; height: 44px; border-radius: 8px; background: #FCECE4; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 22px;">
                <i class="ri-computer-line"></i>
            </div>
            <div style="font-size: 24px; font-weight: 700; color: var(--primary);"><?= (int)$b['jumlah_digunakan'] + (int)($b['stok_peminjaman'] ?? 0) + (int)($b['jumlah_dipinjam'] ?? 0) ?></div>
        </div>
        <div style="font-size: 13px; color: var(--gray-500); font-weight: 500;">Digunakan</div>
    </div>

    <!-- Pool Peminjaman -->
    <div style="background: #FFFFFF; border: 1px solid var(--gray-200); border-radius: 12px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 44px; height: 44px; border-radius: 8px; background: #FCECE4; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 22px;">
                <i class="ri-user-shared-line"></i>
            </div>
            <div style="font-size: 24px; font-weight: 700; color: var(--primary);"><?= (int)($b['stok_peminjaman'] ?? 0) ?></div>
        </div>
        <div style="font-size: 13px; color: var(--gray-500); font-weight: 500;">Pool Peminjaman</div>
    </div>
    
    <!-- Dipinjam -->
    <div style="background: #FFFFFF; border: 1px solid var(--gray-200); border-radius: 12px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 44px; height: 44px; border-radius: 8px; background: #FCECE4; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 22px;">
                <i class="ri-inbox-unarchive-line"></i>
            </div>
            <div style="font-size: 24px; font-weight: 700; color: var(--primary);"><?= (int)$b['jumlah_dipinjam'] ?></div>
        </div>
        <div style="font-size: 13px; color: var(--gray-500); font-weight: 500;">Dipinjam</div>
    </div>
    
    <!-- Rusak -->
    <div style="background: #FFFFFF; border: 1px solid var(--gray-200); border-radius: 12px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="width: 44px; height: 44px; border-radius: 8px; background: #FCECE4; display: flex; align-items: center; justify-content: center; color: var(--primary); font-size: 22px;">
                <i class="ri-box-1-line"></i>
            </div>
            <div style="font-size: 24px; font-weight: 700; color: var(--primary);"><?= (int)$b['jumlah_rusak'] ?></div>
        </div>
        <div style="font-size: 13px; color: var(--gray-500); font-weight: 500;">Rusak</div>
    </div>
</div>

<!-- ============================================================
     Info Barang
============================================================ -->
<div style="background: #FFFFFF; border: 1px solid var(--gray-200); border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-bottom: 24px; display: flex; gap: 24px; align-items: stretch;">
    
    <!-- Foto / Image -->
    <div style="width: 200px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
        <?php 
        $foto = !empty($b['foto_barang']) ? base_url('uploads/' . $b['foto_barang']) : '';
        if ($foto): ?>
            <img src="<?= $foto ?>" alt="<?= esc($b['nama_barang']) ?>" style="max-width: 100%; max-height: 160px; object-fit: contain; border-radius: 8px;">
        <?php else: ?>
            <div style="width: 100%; height: 160px; background: #F3F4F6; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #D1D5DB; font-size: 64px;">
                <i class="ri-image-line"></i>
            </div>
        <?php endif; ?>
    </div>

    <!-- Info Details -->
    <div style="flex: 1; display: flex; flex-direction: column; justify-content: center;">
        <h2 style="font-size: 22px; font-weight: 700; color: var(--primary); margin: 0 0 4px 0;">
            <?= esc($b['nama_barang']) ?>
        </h2>
        <p style="font-size: 16px; color: var(--gray-800); font-weight: 700; margin: 0 0 20px 0;">
            (Kode: <?= esc($b['kode_barang']) ?>)
        </p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px;">
            <div style="background: #F9FAFB; border: 1px solid #F3F4F6; border-radius: 8px; padding: 12px 16px;">
                <div style="font-size: 12px; font-weight: 600; color: var(--gray-500); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Merk</div>
                <div style="font-size: 15px; font-weight: 700; color: var(--gray-800);"><?= esc($b['merk'] ?: '—') ?></div>
            </div>
            
            <div style="background: #F9FAFB; border: 1px solid #F3F4F6; border-radius: 8px; padding: 12px 16px;">
                <div style="font-size: 12px; font-weight: 600; color: var(--gray-500); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Kategori</div>
                <div style="font-size: 15px; font-weight: 700; color: var(--gray-800);"><?= esc($b['kategori'] ?: '—') ?></div>
            </div>
            
            <div style="background: #F9FAFB; border: 1px solid #F3F4F6; border-radius: 8px; padding: 12px 16px;">
                <div style="font-size: 12px; font-weight: 600; color: var(--gray-500); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Jenis Barang</div>
                <div style="font-size: 15px; font-weight: 700; color: var(--gray-800);"><?= esc($b['jenis_barang'] ?: '—') ?></div>
            </div>
            
            <div style="background: #F9FAFB; border: 1px solid #F3F4F6; border-radius: 8px; padding: 12px 16px;">
                <div style="font-size: 12px; font-weight: 600; color: var(--gray-500); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Tgl Didaftarkan</div>
                <div style="font-size: 15px; font-weight: 700; color: var(--gray-800);">
                    <?= $b['tanggal_perolehan'] ? date('d/m/Y', strtotime($b['tanggal_perolehan'])) : '—' ?>
                </div>
            </div>
            
            <div style="background: #F9FAFB; border: 1px solid #F3F4F6; border-radius: 8px; padding: 12px 16px;">
                <div style="font-size: 12px; font-weight: 600; color: var(--gray-500); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px;">Status</div>
                <div>
                    <?php
                    $s = \App\Models\BarangModel::getStatusStok($b);
                    $sc = match($s) { 'Aman'=>'badge-aman', 'Sedikit'=>'badge-sedikit', default=>'badge-habis' };
                    ?>
                    <span class="badge <?= $sc ?>"><?= $s ?></span>
                </div>
            </div>

        </div>
        <div style="background: #F9FAFB; border: 1px solid #F3F4F6; border-radius: 8px; padding: 12px 16px; margin-top: 12px;">
            <div style="font-size: 12px; font-weight: 600; color: var(--gray-500); margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Spesifikasi</div>
            <div style="font-size: 14px; font-weight: 500; color: var(--gray-800); line-height: 1.6; white-space: pre-line;"><?= !empty($b['spesifikasi']) ? esc($b['spesifikasi']) : '—' ?></div>
        </div>
    </div>
</div>

<!-- ============================================================
     2 Kolom bawah: Distribusi Terbanyak + Aktivitas Terakhir
============================================================ -->
<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    <!-- Distribusi Terbanyak -->
    <div style="background: #FFFFFF; border: 1px solid var(--gray-200); border-radius: 12px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div style="font-size: 14px; font-weight: 700; color: var(--gray-800); margin-bottom: 16px;">Lokasi Distribusi Terbanyak</div>
        <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
            <thead>
                <tr style="border-bottom: 1px solid var(--gray-200);">
                    <th style="text-align: left; padding: 8px 0; color: var(--gray-800); font-weight: 600;">No</th>
                    <th style="text-align: left; padding: 8px 0; color: var(--gray-800); font-weight: 600;">Lokasi Distribusi</th>
                    <th style="text-align: right; padding: 8px 0; color: var(--gray-800); font-weight: 600;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($distribusi)): ?>
                    <tr><td colspan="3" style="text-align:center;color:#9CA3AF;padding:24px;">Belum ada distribusi</td></tr>
                <?php else: ?>
                    <?php foreach ($distribusi as $i => $d): ?>
                    <tr class="<?= $i >= 5 ? 'dist-hidden-item' : '' ?>" style="border-bottom: 1px solid #F9FAFB; <?= $i >= 5 ? 'display:none;' : '' ?>">
                        <td style="color:#9CA3AF; padding: 10px 0;"><?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?></td>
                        <td style="color: var(--gray-600); padding: 10px 0;">
                            <?php 
                                if (isset($d['tujuan_distribusi']) && $d['tujuan_distribusi'] === 'peminjaman') {
                                    echo 'Peminjaman';
                                } else {
                                    echo esc($d['nama_ruangan'] ?? '—');
                                }
                            ?>
                        </td>
                        <td style="text-align:right; color: var(--gray-600); padding: 10px 0;"><?= $d['total_jumlah'] ?>pcs</td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (count($distribusi) > 5): ?>
                    <?php $sisa_dist = count($distribusi) - 5; ?>
                    <tr id="dist-btn-row">
                        <td colspan="3" style="text-align:center; padding-top:12px;">
                            <button type="button" onclick="document.querySelectorAll('.dist-hidden-item').forEach(e => e.style.display='table-row'); document.getElementById('dist-btn-row').style.display='none';" style="background:none; border:1px solid var(--gray-200); color:var(--gray-600); padding:6px 12px; border-radius:6px; font-size:12px; cursor:pointer; font-weight:600; width:100%; transition: 0.2s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='none'">
                                Lihat <?= $sisa_dist ?> Lokasi Lainnya
                            </button>
                        </td>
                    </tr>
                    <?php endif; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Aktivitas Terakhir -->
    <div style="background: #FFFFFF; border: 1px solid var(--gray-200); border-radius: 12px; padding: 20px 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div style="font-size: 14px; font-weight: 700; color: var(--gray-800); margin-bottom: 2px;">Aktivitas Terakhir</div>
        <p style="font-size: 11px; color: var(--gray-500); margin: 0 0 16px 0;">Jejak digital pergerakan barang di lingkungan FTIK.</p>
        
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <?php if (empty($aktivitas)): ?>
                <div style="text-align:center; color:#9CA3AF; padding:24px 0;">Belum ada aktivitas</div>
            <?php else: ?>
                <?php 
                $ak_count = 0;
                foreach ($aktivitas as $ak): 
                    $isHidden = $ak_count >= 5;
                    $ak_count++;
                    
                    $icon = 'ri-history-line';
                    $color = '#6B7280';
                    $bg = '#F3F4F6';
                    
                    if ($ak['tipe'] === 'distribusi') {
                        $icon = 'ri-arrow-right-circle-line';
                        $color = '#3B82F6';
                        $bg = '#EFF6FF';
                    } elseif ($ak['tipe'] === 'peminjaman') {
                        $icon = 'ri-user-shared-line';
                        $color = '#8B5CF6';
                        $bg = '#F5F3FF';
                    } elseif ($ak['tipe'] === 'pelaporan') {
                        $icon = 'ri-error-warning-line';
                        $color = '#EF4444';
                        $bg = '#FEF2F2';
                    } elseif ($ak['tipe'] === 'restock') {
                        $icon = 'ri-add-box-line';
                        $color = '#10B981';
                        $bg = '#ECFDF5';
                    } elseif ($ak['tipe'] === 'ditarik') {
                        $icon = 'ri-arrow-go-back-line';
                        $color = '#EA580C';
                        $bg = '#FFF7ED';
                    }
                ?>
                <div class="<?= $isHidden ? 'ak-hidden-item' : '' ?>" style="background: #F9FAFB; border-radius: 8px; border: 1px solid #F3F4F6; padding: 12px; display: <?= $isHidden ? 'none' : 'flex' ?>; align-items: flex-start; gap: 12px;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: <?= $bg ?>; color: <?= $color ?>; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;">
                        <i class="<?= $icon ?>"></i>
                    </div>
                    <div style="flex: 1; display: flex; flex-direction: column; gap: 4px;">
                        <div style="font-size: 12.5px; color: #374151; line-height: 1.4; font-weight: 500;">
                            <?= esc($ak['keterangan']) ?>
                        </div>
                        <div style="font-size: 11px; color: #9CA3AF; display: flex; align-items: center; gap: 4px;">
                            <i class="ri-calendar-line"></i> <?= $ak['tgl'] ? date('d M Y', strtotime($ak['tgl'])) : '—' ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (count($aktivitas) > 5): ?>
                <?php $sisa_ak = count($aktivitas) - 5; ?>
                <button type="button" id="ak-btn" onclick="document.querySelectorAll('.ak-hidden-item').forEach(e => e.style.display='flex'); this.style.display='none';" style="background:none; border:1px solid var(--gray-200); color:var(--gray-600); padding:8px 12px; border-radius:6px; font-size:12px; cursor:pointer; font-weight:600; width:100%; transition: 0.2s;" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='none'">
                    Lihat <?= $sisa_ak ?> Aktivitas Lainnya
                </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

</div><!-- .grid 2col -->

</div><!-- #content-screen -->

<!-- ============================================================
     LAPORAN CETAK (hanya muncul saat print/export)
============================================================ -->
<div id="print-report">
    <table class="layout-print">
        <thead>
            <tr><td></td></tr> <!-- Margin Atas Semu -->
        </thead>
        <tbody>
            <tr><td>
    <!-- KOP SURAT -->
    <div class="pr-kop">
        <img src="<?= base_url('assets/img/ftik-logo.png') ?>" alt="Logo" onerror="this.style.display='none'">
        <div class="pr-kop-text">
            <h2>FAKULTAS TEKNOLOGI INFORMASI DAN KOMUNIKASI</h2>
            <h3>Universitas Semarang</h3>
            <p>Jl. Soekarno Hatta, Tlogosari, Pedurungan, Kota Semarang, Jawa Tengah 50196<br/>
               Telp: (024) 6702757 | Email: ftik@usm.ac.id | Website: ftik.usm.ac.id</p>
        </div>
    </div>

    <div class="pr-title">
        Laporan Detail Aset Inventaris
        <div style="font-size: 11px; color: #F05A1A; font-weight: 600; margin-top: 6px; letter-spacing: 0; text-transform: none;">
            Sumber Data: Menu Pendataan Barang (Rincian Item)
        </div>
    </div>

    <!-- A. INFO BARANG -->
    <div class="pr-section-title">A. Informasi Barang</div>
    <table class="pr-info-table">
        <tbody>
            <tr>
                <td>Nama Barang</td>
                <td><?= esc($b['nama_barang']) ?></td>
            </tr>
            <tr>
                <td>Kode Barang</td>
                <td><?= esc($b['kode_barang']) ?></td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td><?= esc($b['kategori'] ?? '—') ?></td>
            </tr>
            <tr>
                <td>Jenis Barang</td>
                <td><?= esc($b['jenis_barang'] ?? '—') ?></td>
            </tr>
            <tr>
                <td>Merk / Tipe</td>
                <td><?= esc($b['merk'] ?? '—') ?></td>
            </tr>
            <tr>
                <td>Spesifikasi</td>
                <td><?= !empty($b['spesifikasi']) ? nl2br(esc($b['spesifikasi'])) : '—' ?></td>
            </tr>
            <tr>
                <td>Tanggal Didaftarkan</td>
                <td><?= $b['tanggal_perolehan'] ? date('d F Y', strtotime($b['tanggal_perolehan'])) : '—' ?></td>
            </tr>
            <tr>
                <td>Status Stok</td>
                <td><?= \App\Models\BarangModel::getStatusStok($b) ?></td>
            </tr>
        </tbody>
    </table>

    <!-- B. REKAP STOK -->
    <div class="pr-section-title">B. Rekap Stok</div>
    <table>
        <thead>
            <tr>
                <th>Total Aset</th>
                <th>Stok Gudang</th>
                <th>Digunakan</th>
                <th>Pool Peminjaman</th>
                <th>Sedang Dipinjam</th>
                <th>Rusak</th>
            </tr>
        </thead>
        <tbody>
            <tr style="text-align:center;">
                <td><?= $total ?> pcs</td>
                <td><?= (int)$b['stok_gudang'] ?> pcs</td>
                <td><?= (int)$b['jumlah_digunakan'] ?> pcs</td>
                <td><?= (int)($b['stok_peminjaman'] ?? 0) ?> pcs</td>
                <td><?= (int)$b['jumlah_dipinjam'] ?> pcs</td>
                <td><?= (int)$b['jumlah_rusak'] ?> pcs</td>
            </tr>
        </tbody>
    </table>

    <!-- C. DISTRIBUSI PER LOKASI -->
    <div class="pr-section-title">C. Rekap Distribusi Per Lokasi</div>
    <table>
        <thead>
            <tr>
                <th style="width:8%">No</th>
                <th>Lokasi Distribusi</th>
                <th style="width:20%">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($distribusi)): ?>
                <tr><td colspan="3" style="text-align:center;">Belum ada distribusi</td></tr>
            <?php else: ?>
                <?php foreach ($distribusi as $i => $d): ?>
                <tr>
                    <td style="text-align:center;"><?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?></td>
                    <td><?php
                        if (isset($d['tujuan_distribusi']) && $d['tujuan_distribusi'] === 'peminjaman') {
                            echo 'Peminjaman';
                        } else {
                            echo esc($d['nama_ruangan'] ?? '—');
                        }
                    ?></td>
                    <td style="text-align:center;"><?= $d['total_jumlah'] ?> pcs</td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- D. RIWAYAT AKTIVITAS -->
    <div class="pr-section-title">D. Riwayat Aktivitas</div>
    <table>
        <thead>
            <tr>
                <th style="width:8%">No</th>
                <th>Keterangan Aktivitas</th>
                <th style="width:22%">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($aktivitas)): ?>
                <tr><td colspan="3" style="text-align:center;">Belum ada aktivitas tercatat</td></tr>
            <?php else: ?>
                <?php foreach ($aktivitas as $i => $ak): ?>
                <tr>
                    <td style="text-align:center;"><?= str_pad($i+1, 2, '0', STR_PAD_LEFT) ?></td>
                    <td><?= esc($ak['keterangan']) ?></td>
                    <td style="text-align:center;"><?= $ak['tgl'] ? date('d/m/Y', strtotime($ak['tgl'])) : '—' ?></td>
                </tr>
                <?php endforeach; ?>
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
            <tr><td></td></tr> <!-- Margin Bawah Semu -->
        </tfoot>
    </table>
</div><!-- #print-report -->

<!-- ============================================================
     Modal Edit Barang
============================================================ -->
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<!-- Modal Edit (override di luar layout modal) -->
<div class="modal-overlay" id="modalEditBarang">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div class="modal-title">Edit Informasi Barang</div>
            <button class="modal-close" onclick="closeModal('modalEditBarang')"><i class="ri-close-line"></i></button>
        </div>
        <form action="<?= base_url('barang/' . encode_id($b['id']) . '/edit') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <!-- Kode Barang -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Kode Barang</label>
                        <input type="text" class="form-control" name="kode_barang" id="edit_kode_barang"
                               value="<?= esc($b['kode_barang']) ?>" readonly style="background-color: #F3F4F6;" />
                    </div>
                    
                    <!-- Nama Barang -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Nama Barang</label>
                        <input type="text" class="form-control" name="nama_barang"
                               value="<?= esc($b['nama_barang']) ?>" required />
                    </div>
                    
                    <!-- Kategori -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Kategori</label>
                        <select class="form-control" name="kategori" required>
                            <option value="">— Pilih Kategori —</option>
                            <?php $kategoriUtamaList = model('App\Models\KategoriUtamaModel')->findAll(); ?>
                            <?php foreach ($kategoriUtamaList as $ku): ?>
                                <option value="<?= esc($ku['nama_kategori']) ?>" <?= ($b['kategori'] === $ku['nama_kategori']) ? 'selected' : '' ?>><?= esc($ku['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Jenis Barang -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Jenis Barang</label>
                        <select class="form-control" name="id_kategori" required onchange="generateKodeBarangEdit(this)">
                            <option value="" data-kode="">— Pilih Jenis Barang —</option>
                            <?php $katList = model('App\Models\KategoriModel')->findAll(); ?>
                            <?php foreach ($katList as $kat): ?>
                                <option value="<?= esc($kat['nama']) ?>" data-kode="<?= esc($kat['kode_kategori'] ?? '') ?>" <?= ($b['id_kategori'] === $kat['nama']) ? 'selected' : '' ?>>
                                    <?= esc($kat['nama']) ?> <?= !empty($kat['keterangan']) ? '('.esc($kat['keterangan']).')' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Merk/Brand -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Merk/Brand</label>
                        <input type="text" class="form-control" name="merk"
                               value="<?= esc($b['merk'] ?? '') ?>"
                               placeholder="Contoh: Dell / Xiaomi" />
                    </div>

                    <!-- Stok Awal -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Stok Awal</label>
                        <?php 
                            $stok_terpakai = (int)$b['jumlah_digunakan'] 
                                           + (int)($b['stok_peminjaman'] ?? 0) 
                                           + (int)$b['jumlah_dipinjam'] 
                                           + (int)$b['jumlah_rusak'];
                            $currentTotal = $stok_terpakai + (int)$b['stok_gudang'];
                            
                            $max_stok = (int)$b['stok_awal'];
                            if ($max_stok != $currentTotal) {
                                $max_stok = $currentTotal;
                            }
                            $min_stok = max($stok_terpakai, 1);
                        ?>
                        <input type="number" class="form-control" name="stok_awal"
                               value="<?= $max_stok ?>" min="<?= $min_stok ?>" max="<?= $max_stok ?>" required />
                        <div style="font-size: 11px; color: #6B7280; margin-top: 4px;">
                            <i class="ri-information-line"></i> Maksimal <strong><?= $max_stok ?></strong>, Minimal <strong><?= $min_stok ?></strong> 
                            <?php if ($stok_terpakai > 0): ?>
                                (karena ada <?= $stok_terpakai ?> unit yang sedang digunakan/berstatus lain di luar gudang).
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Tanggal Perolehan -->
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Tanggal Perolehan</label>
                        <input type="date" class="form-control" name="tanggal_perolehan"
                               value="<?= esc($b['tanggal_perolehan'] ?? '') ?>" />
                    </div>

                    <!-- Spesifikasi -->
                    <div class="form-group" style="margin-bottom: 0; grid-column: 1 / -1;">
                        <label class="form-label">Spesifikasi <span style="font-size:11px;color:#9CA3AF;font-weight:normal;">(opsional — boleh dikosongkan)</span></label>
                        <textarea class="form-control" name="spesifikasi" rows="3"
                                  placeholder="Contoh: Intel Core i5, RAM 16GB, SSD 512GB, Layar 24 inch"
                                  style="resize: vertical;"><?= esc($b['spesifikasi'] ?? '') ?></textarea>
                    </div>

                    <!-- Foto Barang -->
                    <div class="form-group" style="margin-bottom: 0; grid-column: 1 / -1;">
                        <label class="form-label">Foto Barang<?php if (empty($b['foto_barang'])): ?> <span style="color:#EF4444">*</span><?php else: ?> <span style="font-size:11px;color:#9CA3AF;font-weight:normal;">(opsional — kosongkan jika tidak ingin ganti)</span><?php endif; ?></label>
                        <label for="edit_foto_barang" id="label-edit-foto"
                               style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:120px;border:2px dashed var(--gray-300);border-radius:8px;cursor:pointer;color:var(--gray-500);font-size:13px;transition:var(--transition);overflow:hidden;position:relative;"
                               onmouseover="this.style.borderColor='var(--primary)';this.style.color='var(--primary)'"
                               onmouseout="this.style.borderColor='var(--gray-300)';this.style.color='var(--gray-500)'">
                            <?php if (!empty($b['foto_barang'])): ?>
                                <!-- Foto sudah ada -->
                                <div id="edit-foto-existing" style="display:flex;flex-direction:column;align-items:center;gap:4px;pointer-events:none;">
                                    <img src="<?= base_url('uploads/' . $b['foto_barang']) ?>" alt="Foto"
                                         style="height:70px;width:auto;max-width:100%;border-radius:6px;object-fit:cover;border:1px solid #E5E7EB;" />
                                    <span style="font-size:11px;color:#9CA3AF;">Klik untuk ganti foto</span>
                                </div>
                            <?php else: ?>
                                <!-- Belum ada foto -->
                                <div id="edit-foto-placeholder" style="display:flex;flex-direction:column;align-items:center;pointer-events:none;">
                                    <i class="ri-image-add-line" style="font-size:28px;margin-bottom:8px;"></i>
                                    <span>Upload Foto Barang</span>
                                </div>
                            <?php endif; ?>
                            <!-- Preview setelah file dipilih -->
                            <div id="edit-foto-preview-wrap" style="display:none;flex-direction:column;align-items:center;gap:6px;pointer-events:none;width:100%;padding:8px;box-sizing:border-box;">
                                <img id="edit-foto-preview-img" src="" alt="Preview"
                                     style="height:70px;width:auto;max-width:100%;border-radius:6px;object-fit:cover;border:1px solid #E5E7EB;" />
                                <span id="edit-foto-preview-name"
                                      style="font-size:11px;color:#374151;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:90%;"></span>
                            </div>
                        </label>
                        <?php if (empty($b['foto_barang'])): ?>
                        <div id="edit-foto-barang-error" style="display:none; color:#EF4444; font-size:12px; margin-top:4px;"><i class="ri-error-warning-line"></i> Foto barang wajib diupload.</div>
                        <?php endif; ?>
                        <input type="file" id="edit_foto_barang" name="foto_barang"
                               style="display:none;" accept="image/*"
                               onchange="previewEditFoto(this)" />
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEditBarang')">Batal</button>
                <button type="submit" class="btn btn-primary"><i class="ri-save-line"></i> Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     Modal Restock Barang
============================================================ -->
<div class="modal-overlay" id="modalRestockBarang">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Restock Barang (Tambah Stok)</div>
            <button class="modal-close" onclick="closeModal('modalRestockBarang')"><i class="ri-close-line"></i></button>
        </div>
        <form action="<?= base_url('barang/' . encode_id($b['id']) . '/restock') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-body">
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Jumlah Restock (Pcs) <span style="color:var(--danger)">*</span></label>
                    <input type="number" class="form-control" name="jumlah" min="1" required placeholder="Contoh: 10" />
                </div>
                <div class="form-group" style="margin-bottom: 16px;">
                    <label class="form-label">Tanggal Masuk</label>
                    <input type="date" class="form-control" name="tanggal_masuk" value="<?= date('Y-m-d') ?>" />
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan / Sumber (Opsional)</label>
                    <textarea class="form-control" name="keterangan" rows="3" placeholder="Contoh: Pengadaan tahun ajaran 2026/2027"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalRestockBarang')">Batal</button>
                <button type="submit" class="btn btn-primary" style="background-color: #10B981; border: none;"><i class="ri-add-box-line"></i> Tambah Stok</button>
            </div>
        </form>
    </div>
</div>

<script>
const originalJenis = "<?= esc($b['id_kategori'], 'js') ?>";
const originalKode = "<?= esc($b['kode_barang'], 'js') ?>";

function generateKodeBarangEdit(select) {
    var opt = select.options[select.selectedIndex];
    var kodePrefix = opt.getAttribute('data-kode');
    var inputKode = document.getElementById('edit_kode_barang');
    
    // Jika kembali ke jenis aslinya, kembalikan kode aslinya
    if (select.value === originalJenis) {
        inputKode.value = originalKode;
        return;
    }

    if (!kodePrefix) {
        inputKode.value = '';
        inputKode.placeholder = 'Ketik kode manual...';
        return;
    }

    inputKode.value = 'Loading...';
    
    fetch('<?= base_url('barang/generate-kode') ?>?prefix=' + encodeURIComponent(kodePrefix), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.kode) {
            inputKode.value = data.kode;
        } else {
            inputKode.value = kodePrefix + '-001';
        }
    })
    .catch(err => {
        console.error('Error fetching kode:', err);
        inputKode.value = kodePrefix + '-001';
    });
}

// Preview foto saat file dipilih di modal edit
function previewEditFoto(input) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    var reader = new FileReader();
    reader.onload = function(e) {
        // Sembunyikan placeholder / foto lama
        var existing = document.getElementById('edit-foto-existing');
        var placeholder = document.getElementById('edit-foto-placeholder');
        if (existing) existing.style.display = 'none';
        if (placeholder) placeholder.style.display = 'none';
        // Tampilkan preview baru
        var wrap = document.getElementById('edit-foto-preview-wrap');
        wrap.style.display = 'flex';
        document.getElementById('edit-foto-preview-img').src = e.target.result;
        document.getElementById('edit-foto-preview-name').textContent = file.name;
        // Style label hijau
        var label = document.getElementById('label-edit-foto');
        if (label) { label.style.borderColor = '#22C55E'; label.style.background = '#F0FDF4'; label.onmouseover = null; label.onmouseout = null; }
        // Hide error
        var errorEl = document.getElementById('edit-foto-barang-error');
        if (errorEl) errorEl.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

<?php if (empty($b['foto_barang'])): ?>
// Validasi form Edit Barang: foto wajib jika belum ada foto sebelumnya
document.addEventListener('DOMContentLoaded', function() {
    var formEdit = document.querySelector('#modalEditBarang form');
    if (formEdit) {
        formEdit.addEventListener('submit', function(e) {
            var fotoInput = document.getElementById('edit_foto_barang');
            var label     = document.getElementById('label-edit-foto');
            var errorEl   = document.getElementById('edit-foto-barang-error');
            if (!fotoInput || !fotoInput.files || fotoInput.files.length === 0) {
                e.preventDefault();
                if (label) { label.style.borderColor = '#EF4444'; label.style.background = '#FEF2F2'; label.style.color = '#EF4444'; label.onmouseover = null; label.onmouseout = null; }
                if (errorEl) errorEl.style.display = 'block';
                label.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
});
<?php endif; ?>
</script>
<?= $this->endSection() ?>
