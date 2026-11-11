<?= $this->extend('templates/layout') ?>

<?= $this->section('head') ?>
<style>
/* ── Status badges ───────────────────────────────────────── */
.pgj-badge {
    display:inline-block;padding:3px 12px;border-radius:20px;
    font-size:11px;font-weight:700;letter-spacing:.3px;
}
.pgj-menunggu { background:#FEF9C3;color:#854D0E; }
.pgj-direvisi { background:#F3F4F6;color:#4B5563; }
.pgj-diproses { background:#DBEAFE;color:#1D4ED8; }
.pgj-selesai  { background:#D1FAE5;color:#065F46; }
.pgj-ditolak  { background:#FEE2E2;color:#991B1B; }
.pgj-urg-tinggi{ background:#FEE2E2;color:#991B1B; }
.pgj-urg-sedang{ background:#FED7AA;color:#9A3412; }
.pgj-urg-rendah{ background:#FEF9C3;color:#854D0E; }

/* ── Kategori Pengajuan badges ───────────────────────────── */
.kat-badge {
    display:inline-block;padding:3px 12px;border-radius:6px;
    font-size:12px;font-weight:600;text-transform:capitalize;
}
.kat-fakultas    { background:#EEF2FF;color:#4F46E5;border:1px solid #C7D2FE; }
.kat-universitas { background:#FDF4FF;color:#C026D3;border:1px solid #F9A8D4; }
.kat-lainnya     { background:#F3F4F6;color:#6B7280;border:1px solid #E5E7EB; }


/* ── Aksi buttons ────────────────────────────────────────── */
.aksi-btn {
    display:inline-flex;align-items:center;justify-content:center;
    width:30px;height:30px;border:none;border-radius:6px;
    font-size:13px;cursor:pointer;transition:.15s;text-decoration:none;
}
.aksi-btn:hover { filter:brightness(.85);transform:translateY(-1px); }

/* ── Print styles ─────────────────────────────────────────── */
@media print {
    .sidebar, .topbar, .table-toolbar, .aksi-btn, .modal-overlay,
    .stats-grid, .hero-wrap, .export-panel, .pagination-wrapper,
    .dataTables_info, .dataTables_paginate, .dataTables_length, .dataTables_filter,
    #toolbarContainer, .flash-alert,
    .no-print { display:none!important; }
    /* Sembunyikan empty-state DataTables (gambar kotak + "Belum Ada Data") */
    .dataTables_empty, td.dataTables_empty, .empty-state { display:none!important; }
    .print-header { display:block!important; }
    body { background:white; margin: 0; padding: 0; font-size: 14px; }
    .main-content { margin-left: 0 !important; padding: 20px !important; width: 100% !important; box-sizing: border-box !important; }
    .data-table-wrapper { overflow:visible; }
    table { page-break-inside:auto; }
    tr { page-break-inside:avoid; }
    
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

    .pgj-badge { 
        background: transparent !important;
        padding: 0 !important;
        border: none !important;
        font-weight: bold !important;
    }
    .kat-badge {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    @page { margin: 0; }
}
.print-header { display:none; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php $stats = $stats ?? ['total'=>0,'menunggu'=>0,'diproses'=>0,'selesai'=>0,'ditolak'=>0]; ?>

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
     Hero
═══════════════════════════════════════════ -->
<div class="hero-wrap" style="display:flex;gap:20px;margin-bottom:24px;flex-wrap:wrap;">
    <div style="flex:1;min-width:280px;background:white;border-radius:16px;padding:0 30px;border:1px solid #E5E7EB;box-shadow:0 2px 8px rgba(0,0,0,.04);display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;height: 140px;">
        <img src="<?= base_url('image/header-pengajuan.png') ?>" alt="Avatar" style="height: 100%; width: auto; position: absolute; bottom: 0; left: 20px;" onerror="this.style.display='none'" />
        <div style="z-index:1; margin-left: 200px; padding: 20px 0;">
            <h1 style="font-size:24px;font-weight:800;color:#1F2937;margin-bottom:8px;">
                <span style="color:#F05A1A;">Pengajuan</span> Inventaris
            </h1>
            <p style="color:#6B7280;font-size:13px;line-height:1.6;margin:0;">
                Ajukan kebutuhan perangkat atau fasilitas untuk menunjang kegiatan operasional dan akademik FTIK.
            </p>
        </div>
        <i class="ri-box-3-fill" style="position:absolute;right:20px;bottom:-10px;font-size:100px;color:rgba(240,90,26,.08);pointer-events:none;"></i>
    </div>
    <?php if (can('admin')): ?>
    <div style="width:260px;background:linear-gradient(135deg,#60A5FA,#3B82F6);border-radius:16px;padding:22px 24px;color:white;display:flex;flex-direction:column;justify-content:space-between;box-shadow:0 4px 14px rgba(59,130,246,.35);flex-shrink:0;">
        <div style="font-size:16px;font-weight:700;line-height:1.4;margin-bottom:16px;">
            Pendataan Pengajuan<br>Inventaris Baru
        </div>
        <button onclick="openModal('modalPengajuanBaru')" id="btn-pengajuan-baru"
                style="background:white;color:#3B82F6;border:none;padding:10px;border-radius:8px;font-weight:700;font-size:13px;display:flex;align-items:center;justify-content:center;gap:6px;cursor:pointer;transition:.2s;"
                onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform=''">
            <i class="ri-file-add-line"></i> Pengajuan Baru
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- ═══════════════════════════════════════════
     Stats Cards
═══════════════════════════════════════════ -->
<div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(155px,1fr));gap:16px;margin-bottom:28px;">
    <?php foreach ([
        ['Menunggu', $stats['menunggu'], 'ri-hourglass-line',  '#F59E0B','#FFFBEB','#FDE68A'],
        ['Diproses', $stats['diproses'], 'ri-refresh-line',    '#3B82F6','#EFF6FF','#BFDBFE'],
        ['Selesai',  $stats['selesai'],  'ri-task-line',       '#10B981','#ECFDF5','#A7F3D0'],
        ['Ditolak',  $stats['ditolak'],  'ri-file-close-line', '#EF4444','#FEF2F2','#FECACA'],
    ] as [$label, $val, $icon, $clr, $bg, $border]): ?>
    <div style="background:white;padding:20px;border-radius:14px;box-shadow:0 1px 3px rgba(0,0,0,.05);border:1px solid #F3F4F6;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
            <div style="width:42px;height:42px;background:<?= $bg ?>;border:1px solid <?= $border ?>;border-radius:10px;display:flex;align-items:center;justify-content:center;color:<?= $clr ?>;font-size:20px;">
                <i class="<?= $icon ?>"></i>
            </div>
            <div style="font-size:28px;font-weight:800;color:<?= $clr ?>;line-height:1;"><?= $val ?></div>
        </div>
        <div style="font-size:12px;font-weight:600;color:#6B7280;"><?= $label ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ═══════════════════════════════════════════
     Export Panel
═══════════════════════════════════════════ -->
<div id="exportPanel" style="display:none;flex-wrap:wrap;gap:12px;align-items:flex-end;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:10px;padding:16px 20px;margin-bottom:16px;" class="no-print">
    <div>
        <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Filter Kategori</label>
        <select id="exportKategori" class="form-control" style="min-width:200px;padding:7px 10px;">
            <option value="">Semua Kategori</option>
            <?php foreach ($kategori_list ?? [] as $k): ?>
                <option value="<?= esc($k) ?>"><?= esc($k) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div>
        <label style="font-size:12px;font-weight:600;color:#374151;display:block;margin-bottom:4px;">Filter Status</label>
        <select id="exportStatus" class="form-control" style="min-width:160px;padding:7px 10px;">
            <option value="">Semua Status</option>
            <option value="menunggu">Menunggu</option>
            <option value="diproses">Diproses</option>
            <option value="selesai">Selesai</option>
            <option value="ditolak">Ditolak</option>
        </select>
    </div>
    <button onclick="doExport()" class="btn btn-primary" style="padding:8px 20px;font-size:13px;">
        <i class="ri-printer-line"></i> Export
    </button>
    <button onclick="document.getElementById('exportPanel').style.display='none'" style="background:#F3F4F6;color:#6B7280;border:none;padding:8px 16px;border-radius:8px;font-size:13px;cursor:pointer;">
        Tutup
    </button>
</div>

<!-- ═══════════════════════════════════════════
     Table Section
═══════════════════════════════════════════ -->
<div class="table-section">
    <div class="table-toolbar" style="padding:20px 24px 12px;">
        <div class="table-toolbar-left">
            <span class="table-title">Tabel Pengajuan</span>
            <form method="GET" action="<?= base_url('pengajuan') ?>" id="formCari" style="margin:0;">
                <div class="search-box" style="border-radius:8px;background:white;width:290px;">
                    <i class="ri-search-line search-icon"></i>
                    <input type="text" name="q" id="cariPengajuan"
                           placeholder="Cari nama barang..."
                           value="<?= esc($keyword ?? '') ?>" autocomplete="off" />
                    <?php if ($filter_status ?? null): ?>
                        <input type="hidden" name="status" value="<?= esc($filter_status) ?>" />
                    <?php endif; ?>
                    <?php if ($filter_kategori ?? null): ?>
                        <input type="hidden" name="kategori" value="<?= esc($filter_kategori) ?>" />
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <div style="display:flex;gap:8px;align-items:center;" class="no-print">
            <?php
            $tabs = [
                ''         => ['label'=>'Semua',   'clr'=>'#6B7280'],
                'menunggu' => ['label'=>'Menunggu', 'clr'=>'#F59E0B'],
                'diproses' => ['label'=>'Diproses', 'clr'=>'#3B82F6'],
                'selesai'  => ['label'=>'Selesai',  'clr'=>'#10B981'],
                'ditolak'  => ['label'=>'Ditolak',  'clr'=>'#EF4444'],
            ];
            foreach ($tabs as $val => $t):
                $active = ($filter_status ?? '') === $val;
                $href   = base_url('pengajuan') . '?' . http_build_query(array_filter(['q'=>$keyword,'status'=>$val,'kategori'=>$filter_kategori??'']));
            ?>
            <a href="<?= $href ?>"
               style="padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;
                      background:<?= $active ? $t['clr'] : 'white' ?>;
                      color:<?= $active ? 'white' : $t['clr'] ?>;
                      border:1px solid <?= $active ? $t['clr'] : '#E5E7EB' ?>;transition:.15s;">
                <?= $t['label'] ?>
            </a>
            <?php endforeach; ?>
            <button onclick="toggleExportPanel()" style="background:#FFF3EE;color:#F05A1A;border:1px solid #FCD9C5;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:4px;">
                <i class="ri-printer-line"></i> Export
            </button>
        </div>
    </div>

    <!-- Print Header -->
    <div class="print-header" style="display:none;">
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
            Laporan Pengajuan Inventaris
            <div style="font-size: 11px; color: #6B7280; font-weight: 500; margin-top: 6px; letter-spacing: 0; text-transform: none;">
                Dicetak pada: <?= date('d/m/Y') ?>
            </div>
        </div>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table" id="tabelPengajuan">
            <thead>
                <tr>
                    <th style="width:36px;text-align:center;">No</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Nama Pemohon</th>
                    <th>Nama Barang</th>
                    <th>Kategori Pengajuan</th>
                    <th style="text-align:center;">Jumlah</th>
                    <th style="text-align:center;">Tingkat Urgensi</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;width:100px;" class="no-print">Aksi</th>
                </tr>
            </thead>
            <tbody>
                    <?php foreach ($list as $i => $p):
                        $status  = strtolower($p['status'] ?? 'menunggu');
                        $urgensi = strtolower($p['urgensi'] ?? 'sedang');
                        $urgMap  = ['tinggi'=>'Tinggi','sedang'=>'Sedang','rendah'=>'Rendah'];
                        $stMap   = ['menunggu'=>'Menunggu','direvisi'=>'Direvisi','diproses'=>'Diproses','selesai'=>'Selesai','ditolak'=>'Ditolak'];
                        $tgl     = $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '—';
                        $bisaEdit  = in_array($status, ['menunggu', 'direvisi']);
                        $bisaHapus = ($status !== 'diproses');
                    ?>
                    <tr data-kategori="<?= esc($p['kategori'] ?? '') ?>">
                        <td style="text-align:center;color:#9CA3AF;font-size:12px;"><?= $i + 1 ?></td>
                        <td style="color:#6B7280;font-size:13px;"><?= $tgl ?></td>
                        <td>
                            <div style="font-weight:600;color:#374151;font-size:13px;"><?= esc($p['nama_pengaju'] ?? '—') ?></div>
                        </td>
                        <td>
                            <div style="font-weight:600;color:#1F2937;font-size:13px;"><?= esc($p['nama_barang']) ?></div>
                        </td>
                        <td style="text-align:center;">
                            <?php
                            $katName = strtolower($p['kategori_pengajuan'] ?? '');
                            $katClass = 'kat-lainnya';
                            if ($katName === 'fakultas') $katClass = 'kat-fakultas';
                            elseif ($katName === 'universitas') $katClass = 'kat-universitas';
                            ?>
                            <span class="kat-badge <?= $katClass ?>">
                                <?= esc($p['kategori_pengajuan'] ?? '—') ?>
                            </span>
                        </td>
                        <td style="text-align:center;font-weight:700;color:#374151;">
                            <?= (int)$p['jumlah'] ?> <span style="font-size:11px;color:#9CA3AF;font-weight:400;">pcs</span>
                        </td>
                        <td style="text-align:center;">
                            <span class="pgj-badge pgj-urg-<?= $urgensi ?>">
                                <?= $urgMap[$urgensi] ?? ucfirst($urgensi) ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <span class="pgj-badge pgj-<?= $status ?>">
                                <?= $stMap[$status] ?? ucfirst($status) ?>
                            </span>
                        </td>
                        <td style="text-align:center;" class="no-print">
                            <div style="display:flex;gap:4px;justify-content:center;">
                                <!-- View Detail -->
                                <?php $p['hash'] = encode_id($p['id']); ?>
                                <button class="aksi-btn" style="background:#8B5CF6;color:white;"
                                    onclick="openModalDetail(<?= htmlspecialchars(json_encode($p)) ?>)"
                                    title="Lihat Detail" id="btn-detail-<?= $p['id'] ?>">
                                    <i class="ri-eye-line"></i>
                                </button>
                                
                                <?php if (can('admin', 'koordinator_lab')): ?>
                                <!-- Update Status Flow -->
                                <?php if (in_array($status, ['menunggu', 'direvisi'])): ?>
                                    <button class="aksi-btn" style="background:#3B82F6;color:white;"
                                        onclick="openModalProsesPgj(<?= htmlspecialchars(json_encode($p)) ?>)"
                                        title="Proses/Revisi Pengajuan" id="btn-proses-<?= $p['id'] ?>">
                                        <i class="ri-play-circle-line"></i>
                                    </button>
                                <?php elseif ($status === 'diproses'): ?>
                                    <button class="aksi-btn" style="background:#10B981;color:white;"
                                        onclick="openModalStatus('<?= encode_id($p['id']) ?>', '<?= $status ?>')"
                                        title="Selesaikan atau Tolak Pengajuan" id="btn-status-<?= $p['id'] ?>">
                                        <i class="ri-check-double-line"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="aksi-btn" style="background:#E5E7EB;color:#9CA3AF;cursor:not-allowed;"
                                        title="Status sudah final" disabled>
                                        <i class="ri-checkbox-circle-line"></i>
                                    </button>
                                <?php endif; ?>
                                <?php endif; ?>

                                <?php if (can('admin')): ?>
                                <!-- Edit (hanya menunggu) -->
                                <?php if ($bisaEdit): ?>
                                <button class="aksi-btn" style="background:#F59E0B;color:white;"
                                    onclick="openModalEdit(<?= htmlspecialchars(json_encode($p)) ?>)"
                                    title="Edit Pengajuan" id="btn-edit-<?= $p['id'] ?>">
                                    <i class="ri-pencil-line"></i>
                                </button>
                                <?php else: ?>
                                <button class="aksi-btn" style="background:#E5E7EB;color:#9CA3AF;cursor:not-allowed;"
                                    title="Tidak bisa diedit (<?= $stMap[$status] ?>)" disabled>
                                    <i class="ri-pencil-line"></i>
                                </button>
                                <?php endif; ?>

                                <!-- Hapus -->
                                <?php if ($bisaHapus): ?>
                                <form method="post" action="<?= base_url('pengajuan/' . encode_id($p['id']) . '/hapus') ?>" style="display:inline;" onsubmit="return confirm('Pengajuan &quot;<?= htmlspecialchars(addslashes($p['nama_barang'])) ?>&quot; akan dihapus permanen. Yakin ingin menghapus?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="aksi-btn" style="background:#EF4444;color:white;display:inline-flex;align-items:center;justify-content:center;" title="Hapus">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <button class="aksi-btn" style="background:#E5E7EB;color:#9CA3AF;cursor:not-allowed;"
                                    title="Tidak bisa dihapus (sedang diproses)" disabled>
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($list)): ?>
    <div class="pagination-wrapper no-print">
        <span>Menampilkan <strong><?= count($list) ?></strong> pengajuan</span>
    </div>
    <?php endif; ?>
</div>

<!-- ═══════════════════════════════════════════
     Modal: Pengajuan Baru
═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modalPengajuanBaru">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div class="modal-title">Form Pengajuan Inventaris Baru</div>
            <button class="modal-close" onclick="closeModal('modalPengajuanBaru')"><i class="ri-close-line"></i></button>
        </div>
        <form action="<?= base_url('pengajuan/simpan') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom:24px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Nama Barang <span style="color:#EF4444;">*</span></label>
                        <input type="text" class="form-control" name="nama_barang" required placeholder="Contoh: Laptop ASUS, Meja Kerja" />
                    </div>
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Nama Pengaju <span style="color:#EF4444;">*</span></label>
                        <input type="text" class="form-control" name="nama_pengaju" placeholder="Nama Lengkap" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIP / NIDN <span style="color:#EF4444;">*</span></label>
                        <input type="text" class="form-control" name="nim_nik"
                               required placeholder="Wajib diisi" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tingkat Urgensi</label>
                        <select class="form-control" name="urgensi">
                            <option value="tinggi">Tinggi</option>
                            <option value="sedang" selected>Sedang</option>
                            <option value="rendah">Rendah</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori Pengajuan <span style="color:#EF4444;">*</span></label>
                        <select class="form-control" name="kategori_pengajuan" required>
                            <option value="">— Pilih Kategori Pengajuan —</option>
                            <option value="fakultas">Fakultas</option>
                            <option value="universitas">Universitas</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori Barang</label>
                        <select class="form-control" name="kategori" required>
                            <option value="">— Pilih Kategori —</option>
                            <?php $kategoriUtamaList = model('App\Models\KategoriUtamaModel')->findAll(); ?>
                            <?php foreach ($kategoriUtamaList as $ku): ?>
                                <option value="<?= esc($ku['nama_kategori']) ?>"><?= esc($ku['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kebutuhan Jurusan <span style="color:#EF4444;">*</span></label>
                        <select class="form-control" name="kebutuhan_jurusan" required>
                            <option value="">— Pilih Jurusan —</option>
                            <?php foreach ($jurusan_list ?? [] as $jur): ?>
                                <option value="<?= esc($jur['nama_jurusan']) ?>"><?= esc($jur['nama_jurusan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah <span style="color:#EF4444;">*</span></label>
                        <input type="number" class="form-control" name="jumlah" min="1" placeholder="0" required />
                    </div>
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Keterangan / Alasan Pengajuan</label>
                        <textarea class="form-control" name="keterangan" rows="3" placeholder="Jelaskan spesifikasi detail / alasan pengajuan..."></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;height:42px;justify-content:center;margin-top:4px;">
                    <i class="ri-send-plane-line"></i> Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     Modal: Edit Pengajuan
═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modalEditPengajuan">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div>
                <div class="modal-title"><span style="color:#F59E0B;" id="editModalTitleIcon">Edit</span> Pengajuan</div>
                <div style="font-size:12px;color:#6B7280;margin-top:2px;" id="editModalSubtitle">Hanya pengajuan <strong>Menunggu</strong> atau <strong>Direvisi</strong> yang dapat diedit</div>
            </div>
            <button class="modal-close" onclick="closeModal('modalEditPengajuan')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formEditPengajuan" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom:24px;">

                <!-- Catatan Revisi (hanya tampil jika status direvisi) -->
                <div id="edit_revisi_info" style="display:none;background:#FFFBEB;border:1.5px solid #FDE68A;border-radius:10px;padding:14px 16px;margin-bottom:18px;">
                    <div style="font-size:12px;font-weight:700;color:#D97706;margin-bottom:6px;display:flex;align-items:center;gap:6px;">
                        <i class="ri-error-warning-line"></i> Catatan Revisi:
                    </div>
                    <div id="edit_catatan_revisi_text" style="font-size:13px;color:#92400E;line-height:1.6;white-space:pre-wrap;"></div>
                    <div style="font-size:11px;color:#B45309;margin-top:8px;border-top:1px dashed #FDE68A;padding-top:8px;">
                        <i class="ri-information-line"></i> Sesuaikan isian di bawah dengan catatan di atas, lalu klik <strong>Simpan & Ajukan Ulang</strong>.
                    </div>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Nama Barang <span style="color:#EF4444;">*</span></label>
                        <input type="text" class="form-control" name="nama_barang" id="edit_nama_barang" required />
                    </div>
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Nama Pengaju <span style="color:#EF4444;">*</span></label>
                        <input type="text" class="form-control" name="nama_pengaju" id="edit_nama_pengaju" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIP / NIDN <span style="color:#EF4444;">*</span></label>
                        <input type="text" class="form-control" name="nim_nik" id="edit_nim_nik" required placeholder="Wajib diisi" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tingkat Urgensi</label>
                        <select class="form-control" name="urgensi" id="edit_urgensi">
                            <option value="tinggi">Tinggi</option>
                            <option value="sedang">Sedang</option>
                            <option value="rendah">Rendah</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori Pengajuan <span style="color:#EF4444;">*</span></label>
                        <select class="form-control" name="kategori_pengajuan" id="edit_kategori_pengajuan" required>
                            <option value="">— Pilih Kategori Pengajuan —</option>
                            <option value="fakultas">Fakultas</option>
                            <option value="universitas">Universitas</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kategori Barang</label>
                        <select class="form-control" name="kategori" id="edit_kategori" required>
                            <option value="">— Pilih Kategori —</option>
                            <?php foreach ($kategoriUtamaList as $ku): ?>
                                <option value="<?= esc($ku['nama_kategori']) ?>"><?= esc($ku['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Kebutuhan Jurusan <span style="color:#EF4444;">*</span></label>
                        <select class="form-control" name="kebutuhan_jurusan" id="edit_kebutuhan_jurusan" required>
                            <option value="">— Pilih Jurusan —</option>
                            <?php foreach ($jurusan_list ?? [] as $jur): ?>
                                <option value="<?= esc($jur['nama_jurusan']) ?>"><?= esc($jur['nama_jurusan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah <span style="color:#EF4444;">*</span></label>
                        <input type="number" class="form-control" name="jumlah" id="edit_jumlah" min="1" required />
                    </div>
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Keterangan / Alasan Pengajuan</label>
                        <textarea class="form-control" name="keterangan" id="edit_keterangan" rows="3"></textarea>
                    </div>
                </div>
                <button type="submit" id="editModalSubmitBtn" class="btn btn-primary" style="width:100%;height:42px;justify-content:center;margin-top:4px;background:#F59E0B;border-color:#F59E0B;">
                    <i class="ri-save-line"></i> <span id="editModalSubmitLabel">Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     Modal: Update Status
═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modalStatusPgj">
    <div class="modal-box" style="max-width:420px;">
        <div class="modal-header">
            <div class="modal-title">Update Status Pengajuan</div>
            <button class="modal-close" onclick="closeModal('modalStatusPgj')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formStatusPgj" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom:24px;">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:6px;">
                    <?php foreach ([
                        ['selesai', 'ri-checkbox-line',        '#D1FAE5','#10B981','Selesai',    'Pengajuan terpenuhi'],
                        ['ditolak', 'ri-close-circle-line','#FEE2E2','#EF4444','Ditolak',   'Tidak dapat dipenuhi'],
                    ] as [$val, $icon, $bg, $clr, $lbl, $sub]): ?>
                    <label id="sopt-<?= $val ?>" style="border:2px solid <?= $bg ?>;border-radius:10px;padding:10px 12px;display:flex;align-items:center;gap:10px;cursor:pointer;transition:.15s;"
                           onclick="selectStatusPgj('<?= $val ?>')">
                        <div style="width:32px;height:32px;background:<?= $bg ?>;border-radius:7px;display:flex;align-items:center;justify-content:center;color:<?= $clr ?>;font-size:16px;flex-shrink:0;">
                            <i class="<?= $icon ?>"></i>
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:12px;color:#374151;"><?= $lbl ?></div>
                            <div style="font-size:10px;color:#9CA3AF;"><?= $sub ?></div>
                        </div>
                        <input type="radio" name="status" value="<?= $val ?>" id="sradio-<?= $val ?>" style="margin-left:auto;accent-color:<?= $clr ?>;transform:scale(1.1);" required>
                    </label>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;height:42px;justify-content:center;margin-top:14px;">
                    <i class="ri-check-line"></i> Konfirmasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     Modal: Tindakan Proses Pengajuan
═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modalProsesPgj">
    <div class="modal-box" style="max-width:420px;text-align:center;">
        <div class="modal-header" style="border:none;padding-bottom:0;">
            <div></div>
            <button class="modal-close" onclick="closeModal('modalProsesPgj')"><i class="ri-close-line"></i></button>
        </div>
        <div class="modal-body" style="padding-top:0;">
            <div style="width:60px;height:60px;background:#EFF6FF;color:#3B82F6;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:30px;margin:0 auto 16px;">
                <i class="ri-question-answer-line"></i>
            </div>
            <h3 style="margin:0 0 8px 0;font-size:18px;color:#1F2937;">Tindak Lanjut Pengajuan</h3>
            <p style="color:#6B7280;font-size:13px;margin:0 0 24px 0;">Pilih langkah selanjutnya untuk pengajuan ini.</p>
            
            <div style="display:flex;flex-direction:column;gap:12px;">
                <!-- Proses -->
                <form action="" id="formProsesLangsungPgj" method="post" style="margin:0;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="diproses">
                    <button type="submit" class="btn" style="width:100%;height:44px;background:#3B82F6;color:white;border:none;border-radius:8px;font-weight:600;display:flex;justify-content:center;align-items:center;gap:8px;">
                        <i class="ri-play-circle-line"></i> Pengajuan Diproses
                    </button>
                </form>
                
                <!-- Revisi -->
                <button type="button" class="btn" onclick="openModalRevisiPgj()" style="width:100%;height:44px;background:white;color:#F59E0B;border:1px solid #F59E0B;border-radius:8px;font-weight:600;display:flex;justify-content:center;align-items:center;gap:8px;">
                    <i class="ri-edit-circle-line"></i> Pengajuan Direvisi
                </button>


            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     Modal: Revisi Pengajuan
═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modalRevisiPengajuan">
    <div class="modal-box" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title"><span style="color:#F59E0B;">Revisi</span> Pengajuan</div>
            <button class="modal-close" onclick="closeModal('modalRevisiPengajuan')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formRevisiPgj" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom:24px;">
                <div style="background:#FFFBEB;border:1px solid #FDE68A;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#92400E;display:flex;align-items:flex-start;gap:8px;">
                    <i class="ri-information-line" style="margin-top:1px;flex-shrink:0;"></i>
                    <span>Setelah revisi dikirim, Admin dapat memperbaiki pengajuan dan mengajukan ulang.</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Catatan Revisi <span style="color:#EF4444;">*</span></label>
                    <textarea class="form-control" name="catatan_revisi" rows="4" required placeholder="Tuliskan apa yang perlu direvisi oleh pengaju..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;height:42px;justify-content:center;margin-top:12px;background:#F59E0B;border-color:#F59E0B;">
                    <i class="ri-send-plane-line"></i> Kirim Catatan Revisi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════
     Modal: Tolak Pengajuan
═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modalTolakPengajuan">
    <div class="modal-box" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title"><span style="color:#EF4444;">Tolak</span> Pengajuan</div>
            <button class="modal-close" onclick="closeModal('modalTolakPengajuan')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formTolakPgj" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="status" value="ditolak">
            <div class="modal-body" style="padding-bottom:24px;">
                <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#991B1B;display:flex;align-items:flex-start;gap:8px;">
                    <i class="ri-error-warning-line" style="margin-top:1px;flex-shrink:0;"></i>
                    <span>Pengajuan yang ditolak bersifat <strong>final</strong> dan tidak dapat diubah kembali. Pastikan alasan penolakan jelas.</span>
                </div>
                <div class="form-group">
                    <label class="form-label">Alasan Penolakan <span style="color:#EF4444;">*</span></label>
                    <textarea class="form-control" name="catatan_revisi" rows="4" required placeholder="Tuliskan alasan pengajuan ini ditolak..."></textarea>
                </div>
                <button type="submit" class="btn" style="width:100%;height:42px;background:#EF4444;color:white;border:none;border-radius:8px;font-weight:600;display:flex;justify-content:center;align-items:center;gap:8px;margin-top:12px;">
                    <i class="ri-close-circle-line"></i> Konfirmasi Penolakan
                </button>
            </div>
        </form>
    </div>
</div>


<!-- ═══════════════════════════════════════════
     Modal: Detail Pengajuan
═══════════════════════════════════════════ -->
<div class="modal-overlay" id="modalDetailPgj">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div class="modal-title">Detail Pengajuan</div>
            <button class="modal-close" onclick="closeModal('modalDetailPgj')"><i class="ri-close-line"></i></button>
        </div>
        <div class="modal-body" style="padding-bottom:24px;">
            <!-- Header Block -->
            <div style="background:#F9FAFB;padding:20px;border-radius:12px;margin-bottom:24px;border:1px solid #F3F4F6;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px;">
                    <div>
                        <div style="font-size:12px;color:#6B7280;margin-bottom:4px;display:flex;align-items:center;gap:6px;"><i class="ri-box-3-line"></i> Nama Barang</div>
                        <div style="font-size:20px;font-weight:800;color:#1F2937;" id="detail_nama_barang"></div>
                    </div>
                    <div style="text-align:right;">
                        <span class="pgj-badge" id="detail_status_badge" style="font-size:13px;padding:6px 14px;box-shadow:0 2px 4px rgba(0,0,0,0.05);"></span>
                    </div>
                </div>
                
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;padding-top:16px;border-top:1px dashed #E5E7EB;">
                    <div>
                        <div style="font-size:11px;color:#6B7280;display:flex;align-items:center;gap:4px;margin-bottom:4px;"><i class="ri-git-merge-line"></i> Kategori Pengajuan</div>
                        <div style="font-size:14px;font-weight:600;color:#374151;text-transform:capitalize;" id="detail_kategori_pengajuan"></div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6B7280;display:flex;align-items:center;gap:4px;margin-bottom:4px;"><i class="ri-price-tag-3-line"></i> Kategori Barang</div>
                        <div style="font-size:14px;font-weight:600;color:#374151;" id="detail_kategori"></div>
                    </div>
                    <div>
                        <div style="font-size:11px;color:#6B7280;display:flex;align-items:center;gap:4px;margin-bottom:4px;"><i class="ri-graduation-cap-line"></i> Kebutuhan Jurusan</div>
                        <div style="font-size:14px;font-weight:600;color:#374151;" id="detail_kebutuhan_jurusan"></div>
                    </div>
                </div>
            </div>

            <!-- Details Block -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;padding:0 8px;">
                <div>
                    <div style="font-size:12px;color:#6B7280;margin-bottom:6px;display:flex;align-items:center;gap:4px;"><i class="ri-user-line"></i> Nama Pemohon</div>
                    <div style="font-size:15px;font-weight:700;color:#1F2937;" id="detail_nama_pengaju"></div>
                    <div style="font-size:12px;color:#6B7280;margin-top:2px;" id="detail_nim_nik"></div>
                </div>
                <div>
                    <div style="font-size:12px;color:#6B7280;margin-bottom:6px;display:flex;align-items:center;gap:4px;"><i class="ri-calendar-event-line"></i> Tanggal Pengajuan</div>
                    <div style="font-size:14px;font-weight:600;color:#374151;" id="detail_tanggal"></div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;padding:0 8px;">
                <div>
                    <div style="font-size:12px;color:#6B7280;margin-bottom:6px;display:flex;align-items:center;gap:4px;"><i class="ri-stack-line"></i> Kuantitas</div>
                    <div style="font-size:15px;font-weight:700;color:#1F2937;">
                        <span id="detail_jumlah"></span> <span style="font-size:13px;font-weight:400;color:#6B7280;">pcs</span>
                    </div>
                </div>
                <div>
                    <div style="font-size:12px;color:#6B7280;margin-bottom:6px;display:flex;align-items:center;gap:4px;"><i class="ri-flashlight-line"></i> Tingkat Urgensi</div>
                    <div style="font-size:14px;font-weight:700;color:#F05A1A;text-transform:capitalize;" id="detail_urgensi"></div>
                </div>
            </div>

            <!-- Keterangan -->
            <div style="padding:0 8px;">
                <div style="font-size:12px;color:#6B7280;margin-bottom:8px;display:flex;align-items:center;gap:4px;"><i class="ri-file-text-line"></i> Keterangan / Alasan</div>
                <div style="font-size:14px;color:#4B5563;line-height:1.6;background:#F9FAFB;padding:16px;border-radius:10px;border:1px solid #F3F4F6;white-space:pre-wrap;" id="detail_keterangan"></div>
            </div>

            <!-- Catatan Revisi -->
            <div id="detail_revisi_container" style="padding:0 8px; margin-top:16px; display:none;">
                <div style="font-size:12px;color:#D97706;margin-bottom:8px;display:flex;align-items:center;gap:4px;font-weight:bold;"><i class="ri-error-warning-line"></i> Catatan Revisi</div>
                <div style="font-size:14px;color:#92400E;line-height:1.6;background:#FEF3C7;padding:16px;border-radius:10px;border:1px solid #FDE68A;white-space:pre-wrap;" id="detail_catatan_revisi"></div>
            </div>


        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ── Live search ───────────────────────────────────────────────────────
var st;
document.getElementById('cariPengajuan').addEventListener('input', function() {
    clearTimeout(st);
    st = setTimeout(() => this.closest('form').submit(), 550);
});

// ── Export panel ──────────────────────────────────────────────────────
function toggleExportPanel() {
    const p = document.getElementById('exportPanel');
    p.style.display = (p.style.display === 'none' || p.style.display === '') ? 'flex' : 'none';
}

function doExport() {
    const kat    = (document.getElementById('exportKategori').value || '').toLowerCase();
    const status = (document.getElementById('exportStatus').value  || '').toLowerCase();
    const rows   = document.querySelectorAll('#tabelPengajuan tbody tr');

    rows.forEach(function(tr) {
        if (tr.querySelector('.empty-state')) return; // skip empty row
        const rowKat    = (tr.dataset.kategori || '').trim().toLowerCase(); // Kolom kategori barang dari dataset
        const badges    = tr.querySelectorAll('.pgj-badge');
        const rowStatus = badges.length >= 2 ? badges[1].textContent.trim().toLowerCase() : '';

        let show = true;
        if (kat    && rowKat    !== kat)    show = false;
        if (status && !rowStatus.startsWith(status.substring(0, 4))) show = false;
        tr.style.display = show ? '' : 'none';
    });

    document.getElementById('exportPanel').style.display = 'none';
    setTimeout(() => {
        window.print();
        rows.forEach(tr => tr.style.display = '');
    }, 200);
}

// ── Modal Status ──────────────────────────────────────────────────────
function openModalStatus(id, statusSaat) {
    document.getElementById('formStatusPgj').action = '<?= base_url('pengajuan') ?>/' + id + '/status';
    ['selesai','ditolak'].forEach(v => {
        const r = document.getElementById('sradio-' + v);
        if (r) r.checked = false; // reset options
    });
    // highlight default empty or if it was already finished (which shouldn't happen here)
    highlightStatus(''); 
    openModal('modalStatusPgj');
}

function selectStatusPgj(val) {
    const r = document.getElementById('sradio-' + val);
    if (r) r.checked = true;
    highlightStatus(val);
}

function highlightStatus(val) {
    const configs = {
        menunggu:{border:'#FDE68A',bg:'#FFFBEB'},
        diproses:{border:'#BFDBFE',bg:'#EFF6FF'},
        selesai: {border:'#A7F3D0',bg:'#ECFDF5'},
        ditolak: {border:'#FECACA',bg:'#FEF2F2'},
    };
    Object.entries(configs).forEach(([k, o]) => {
        const el = document.getElementById('sopt-' + k);
        if (!el) return;
        el.style.background  = k === val ? o.bg : 'white';
        el.style.borderColor = o.border;
        el.style.boxShadow   = k === val ? '0 0 0 3px ' + o.border + '88' : '';
    });
}

// ── Modal Edit ────────────────────────────────────────────────────────
function openModalEdit(data) {
    document.getElementById('formEditPengajuan').action = '<?= base_url('pengajuan') ?>/' + (data.hash || data.id) + '/edit';
    document.getElementById('edit_nama_barang').value  = data.nama_barang  || '';
    document.getElementById('edit_nama_pengaju').value = data.nama_pengaju || '';
    document.getElementById('edit_nim_nik').value      = data.nim_nik      || '';
    document.getElementById('edit_jumlah').value       = data.jumlah       || 1;
    document.getElementById('edit_urgensi').value      = data.urgensi      || 'sedang';
    document.getElementById('edit_keterangan').value   = data.keterangan   || '';
    
    const katPengajuanEl = document.getElementById('edit_kategori_pengajuan');
    if (katPengajuanEl) katPengajuanEl.value = data.kategori_pengajuan || '';
    
    const katEl = document.getElementById('edit_kategori');
    if (katEl) katEl.value = data.kategori || '';

    const jurEl = document.getElementById('edit_kebutuhan_jurusan');
    if (jurEl) jurEl.value = data.kebutuhan_jurusan || '';

    // Tampilkan catatan revisi jika status 'direvisi'
    const revisiPanel = document.getElementById('edit_revisi_info');
    const revisiText  = document.getElementById('edit_catatan_revisi_text');
    const titleIcon   = document.getElementById('editModalTitleIcon');
    const subtitle    = document.getElementById('editModalSubtitle');
    const submitLabel = document.getElementById('editModalSubmitLabel');

    if (data.status === 'direvisi' && data.catatan_revisi) {
        revisiPanel.style.display = 'block';
        revisiText.textContent    = data.catatan_revisi;
        titleIcon.textContent     = 'Revisi &';
        titleIcon.style.color     = '#F59E0B';
        subtitle.innerHTML        = '⚠️ Pengajuan ini perlu direvisi. Perbaiki sesuai catatan di atas, lalu ajukan ulang.';
        submitLabel.textContent   = 'Simpan & Ajukan Ulang';
    } else {
        revisiPanel.style.display = 'none';
        revisiText.textContent    = '';
        titleIcon.textContent     = 'Edit';
        subtitle.innerHTML        = 'Hanya pengajuan <strong>Menunggu</strong> atau <strong>Direvisi</strong> yang dapat diedit';
        submitLabel.textContent   = 'Simpan Perubahan';
    }

    openModal('modalEditPengajuan');
}

// ── Modal Tindakan Proses ─────────────────────────────────────────────
var currentPgjData = null;
function openModalProsesPgj(data) {
    currentPgjData = data;
    document.getElementById('formProsesLangsungPgj').action = '<?= base_url('pengajuan') ?>/' + (data.hash || data.id) + '/status';
    openModal('modalProsesPgj');
}

function openModalRevisiPgj() {
    closeModal('modalProsesPgj');
    if(currentPgjData) {
        document.getElementById('formRevisiPgj').action = '<?= base_url('pengajuan') ?>/' + (currentPgjData.hash || currentPgjData.id) + '/revisi';
        openModal('modalRevisiPengajuan');
    }
}

function openModalTolakPgj() {
    closeModal('modalProsesPgj');
    if(currentPgjData) {
        document.getElementById('formTolakPgj').action = '<?= base_url('pengajuan') ?>/' + (currentPgjData.hash || currentPgjData.id) + '/tolak';
        openModal('modalTolakPengajuan');
    }
}

// ── Modal Detail ──────────────────────────────────────────────────────
function openModalDetail(data) {
    document.getElementById('detail_nama_barang').textContent = data.nama_barang || '—';
    document.getElementById('detail_kategori_pengajuan').textContent = data.kategori_pengajuan || '—';
    document.getElementById('detail_kategori').textContent = data.kategori || '—';
    document.getElementById('detail_kebutuhan_jurusan').textContent = data.kebutuhan_jurusan || '—';
    document.getElementById('detail_nama_pengaju').textContent = data.nama_pengaju || '—';
    document.getElementById('detail_nim_nik').textContent = (data.nim_nik && data.nim_nik !== '-') ? 'NIP/NIDN: ' + data.nim_nik : '';
    document.getElementById('detail_jumlah').textContent = data.jumlah || '0';
    document.getElementById('detail_urgensi').textContent = data.urgensi || 'sedang';
    document.getElementById('detail_keterangan').textContent = (data.keterangan && data.keterangan !== '-') ? data.keterangan : 'Tidak ada keterangan tambahan.';
    
    // Format Tanggal (YYYY-MM-DD to DD/MM/YYYY)
    let tglStr = '—';
    if (data.tanggal) {
        const parts = data.tanggal.split('-');
        if (parts.length === 3) tglStr = parts[2] + '/' + parts[1] + '/' + parts[0];
    }
    document.getElementById('detail_tanggal').textContent = tglStr;
    
    // Setup badge
    const badge = document.getElementById('detail_status_badge');
    const status = (data.status || 'menunggu').toLowerCase();
    badge.className = 'pgj-badge pgj-' + status;
    const stMap = {menunggu:'Menunggu', direvisi:'Direvisi', diproses:'Diproses', selesai:'Selesai', ditolak:'Ditolak'};
    badge.textContent = stMap[status] || data.status;

    // Setup revisi/tolak note
    const revContainer = document.getElementById('detail_revisi_container');
    if (data.catatan_revisi && ['ditolak', 'direvisi', 'menunggu', 'diproses'].includes(status)) {
        revContainer.style.display = 'block';
        const titleEl = revContainer.querySelector('div:first-child');
        const contentEl = document.getElementById('detail_catatan_revisi');
        
        if (status === 'ditolak') {
            titleEl.innerHTML = '<i class="ri-close-circle-line"></i> Alasan Penolakan';
            titleEl.style.color = '#DC2626';
            contentEl.style.color = '#991B1B';
            contentEl.style.background = '#FEF2F2';
            contentEl.style.borderColor = '#FECACA';
        } else {
            titleEl.innerHTML = '<i class="ri-error-warning-line"></i> Catatan Revisi';
            titleEl.style.color = '#D97706';
            contentEl.style.color = '#92400E';
            contentEl.style.background = '#FEF3C7';
            contentEl.style.borderColor = '#FDE68A';
        }
        contentEl.textContent = data.catatan_revisi;
    } else {
        revContainer.style.display = 'none';
    }

    openModal('modalDetailPgj');
}


</script>
<?= $this->endSection() ?>
