<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<?php $b = $barang; ?>

<!-- Breadcrumb -->
<div class="page-breadcrumb" style="margin-bottom:16px;">
    <a href="<?= base_url('dashboard') ?>"><i class="ri-arrow-left-s-line"></i> Dashboard</a>
    <span class="separator">›</span>
    <a href="<?= base_url('barang/' . encode_id($b['id']) . '/detail') ?>">Detail</a>
    <span class="separator">›</span>
    <span>Peminjaman Barang</span>
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
<div style="display: flex; gap: 16px; margin-bottom: 24px; flex-wrap: wrap;">
    <!-- White Card (Info) -->
    <div style="flex: 1; min-width: 320px; background: #FFFFFF; border-radius: 12px; padding: 24px; display: flex; align-items: center; justify-content: space-between; border: 1px solid var(--gray-200); box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <div style="flex: 1; padding-right: 20px;">
            <div style="font-size:16px;font-weight:700;color:var(--gray-800);">Peminjaman Barang</div>
            <div style="font-size: 22px; font-weight: 700; color: var(--primary); margin-bottom: 8px;"><?= esc($b['nama_barang']) ?></div>
            <p style="color: var(--gray-500); font-size: 12px; line-height: 1.5; margin: 0; max-width: 360px;">
                Catat dan kelola aset yang dipinjam oleh individu (Dosen/Mahasiswa/Staf)<br>untuk memastikan ketersediaan dan keamanan barang fakultas.
            </p>
        </div>
        <div style="background: #FCECE4; border: 1px solid #F3C4AD; border-radius: 8px; padding: 16px 24px; display: flex; flex-direction: column; justify-content: center; align-items: center; min-width: 140px;">
            <div style="font-size: 32px; font-weight: 800; color: var(--primary); line-height: 1;"><?= $total_dipinjam ?></div>
            <div style="font-size: 11px; color: var(--primary); margin-top: 8px; font-weight: 500;">Barang Dipinjam</div>
        </div>
    </div>

    <!-- Blue Card (Action) -->
    <div style="width: 260px; background: #407BFF; border-radius: 12px; padding: 20px 24px; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 12px rgba(64, 123, 255, 0.25);">
        <i class="ri-folder-add-fill" style="position: absolute; top: 10px; right: -10px; font-size: 80px; color: rgba(255,255,255,0.15); transform: rotate(-10deg);"></i>
        <div style="color: white; font-size: 14px; font-weight: 600; line-height: 1.4; margin-bottom: 14px; z-index: 1;">
            Pendataan<br/>Peminjaman Barang
        </div>
        <button onclick="openModal('modalPinjamBaru')" style="background: white; color: #407BFF; border: none; padding: 10px 16px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; z-index: 1; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
            <i class="ri-folder-add-line" style="font-size: 16px;"></i> Pinjamkan Barang
        </button>
    </div>
</div>

<!-- Table -->
<div class="table-section">
    <div class="table-toolbar">
        <div class="table-toolbar-left" style="flex: 1; max-width: 600px;">
            <span class="table-title" style="font-size: 18px; font-weight: 700; color: var(--gray-800); margin-right: 16px;">Tabel Peminjaman</span>
            <div class="search-box" style="flex: 1; background: white; border-radius: 8px; border: 1px solid var(--gray-200); padding: 8px 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                <span class="search-icon" style="color: var(--primary); font-size: 16px;"><i class="ri-search-line"></i></span>
                <input type="text" id="cariPeminjaman" placeholder="Cari nama peminjam atau NIM/NIK..." autocomplete="off" style="border: none; background: transparent; padding-left: 8px; font-size: 13px; width: 100%; outline: none; color: var(--gray-600);" />
            </div>
        </div>
        <div class="table-toolbar-right" style="display: flex; gap: 12px;">
            <button class="btn btn-primary" onclick="openModal('modalFilterPeminjaman')" style="padding: 10px 20px; font-size: 13px; border-radius: 6px; gap: 8px;">
                <i class="ri-equalizer-line"></i> Filter
            </button>
            <button onclick="window.print()" id="btnExportPeminjaman" style="background:#FFF3EE;color:#F05A1A;border:1px solid #FCD9C5;padding:10px 20px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:.2s;" onmouseover="this.style.background='#FFD5C2';" onmouseout="this.style.background='#FFF3EE';">
                <i class="ri-printer-line" style="font-size: 16px;"></i> Export
            </button>
        </div>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table" id="tabelPeminjaman">
            <thead>
                <tr>
                    <th>Tanggal Pinjam</th>
                    <th>Nama Peminjam</th>
                    <th>NIM/NIK</th>
                    <th style="text-align:center;">Jumlah</th>
                    <th>Tenggat Kembali</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>

                    <?php foreach ($peminjaman as $p): ?>
                    <?php
                    $sc = match($p['status']) {
                        'dipinjam'  => 'badge-dipinjam',
                        'kembali'   => 'badge-kembali',
                        'terlambat' => 'badge-terlambat',
                        default     => 'badge-info',
                    };
                    $sl = match($p['status']) {
                        'dipinjam'  => 'Dipinjam',
                        'kembali'   => 'Kembali',
                        'terlambat' => 'Terlambat',
                        default     => ucfirst($p['status']),
                    };
                    $isTerlambat = $p['status'] === 'terlambat';
                    $isAktif     = in_array($p['status'], ['dipinjam', 'terlambat']);
                    ?>
                    <tr id="pinjam-row-<?= $p['id'] ?>">
                        <td><?= date('d/m/y', strtotime($p['tanggal_pinjam'])) ?></td>
                        <td style="font-weight:500;"><?= esc($p['nama_peminjam']) ?></td>
                        <td style="color:#6B7280;"><?= esc($p['nim_nik'] ?? '—') ?></td>
                        <td style="text-align:center;font-weight:600;"><?= $p['jumlah'] ?> pcs</td>
                        <td class="<?= $isTerlambat ? 'text-danger-date' : '' ?>">
                            <?= date('d/m/y', strtotime($p['tenggat_kembali'])) ?>
                        </td>
                        <td><span class="badge <?= $sc ?>"><?= $sl ?></span></td>
                        <td style="text-align:center;">
                            <div style="display:flex;gap:6px;justify-content:center;">
                                <a href="#"
                                   onclick="isiEditPeminjaman(<?= htmlspecialchars(json_encode($p)) ?>)"
                                   style="background: #3B82F6; color: white; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border: none; cursor: pointer;"
                                   title="Edit">
                                    <i class="ri-pencil-fill" style="font-size: 16px;"></i>
                                </a>
                                <?php if ($isAktif): ?>
                                <form method="post" action="<?= base_url('barang/peminjaman/' . encode_id($p['id']) . '/kembali') ?>" style="display:inline;" onsubmit="return confirm('Tarik barang ini ke gudang?')">
                                    <?= csrf_field() ?>
                                    <button type="submit"
                                       style="background: #F05A1A; color: white; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; border: none; cursor: pointer;"
                                       title="Tarik ke Gudang">
                                        <i class="ri-inbox-archive-fill" style="font-size: 16px;"></i>
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

    <?php if (!empty($peminjaman)): ?>
    <div class="pagination-wrapper">
        <span>Menampilkan <?= count($peminjaman) ?> data peminjaman</span>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<!-- Modal: Peminjaman Baru -->
<div class="modal-overlay" id="modalPinjamBaru">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div class="modal-title">Form Peminjaman Barang Baru</div>
            <button class="modal-close" onclick="closeModal('modalPinjamBaru')"><i class="ri-close-line"></i></button>
        </div>
        <form action="<?= base_url('barang/' . encode_id($b['id']) . '/peminjaman/simpan') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom: 24px;">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Peminjam</label>
                        <input type="text" class="form-control" name="nama_peminjam" required placeholder="Contoh: Andi Setiawan" 
                               pattern="^[a-zA-Z\s\.,']+$" title="Nama hanya boleh berisi huruf"
                               oninput="this.value = this.value.replace(/[^a-zA-Z\s\.,']/g, '')" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIM/NIK</label>
                        <input type="text" class="form-control" name="nim_nik" placeholder="Contoh: G.111.20.0001" />
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tanggal Pinjam</label>
                        <input type="date" class="form-control" name="tanggal_pinjam"
                               value="<?= date('Y-m-d') ?>" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Barang</label>
                        <input type="number" class="form-control" name="jumlah" min="1"
                               max="<?= (int)$b['stok_gudang'] ?>" required
                               placeholder="0" />
                        <div style="font-size: 11px; color: var(--primary); margin-top: 4px; font-weight: 500;">
                            *Stok tersedia: <?= (int)$b['stok_gudang'] ?> Pcs
                        </div>
                    </div>
                </div>

                <div class="form-row" style="align-items: flex-start; margin-top: 4px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Tanggal Kembali</label>
                        <input type="date" class="form-control" name="tenggat_kembali" required />
                    </div>
                    <div class="form-group" style="margin-bottom: 0; display: flex; flex-direction: column;">
                        <div>
                            <label class="form-label">Tujuan Peminjaman</label>
                            <input type="text" class="form-control" name="keterangan" pattern=".*[a-zA-Z].*[a-zA-Z].*[a-zA-Z].*" title="Harus mengandung minimal 3 huruf alfabet" placeholder="Contoh : Unit Kurang" />
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%; height: 42px; justify-content: center; font-size: 14px; margin-top: 24px; border-radius: 6px; box-shadow: 0 4px 12px rgba(240, 90, 26, 0.25);">
                            Terbitkan Peminjaman
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Peminjaman -->
<div class="modal-overlay" id="modalEditPeminjaman">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div class="modal-title">Edit Barang</div>
            <button class="modal-close" type="button" onclick="closeModal('modalEditPeminjaman')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formEditPeminjaman" action="" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom: 24px;">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nama Peminjam</label>
                        <input type="text" class="form-control" name="nama_peminjam" id="edit_nama_peminjam" required 
                               pattern="^[a-zA-Z\s\.,']+$" title="Nama hanya boleh berisi huruf"
                               oninput="this.value = this.value.replace(/[^a-zA-Z\s\.,']/g, '')" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">NIM/NIK</label>
                        <input type="text" class="form-control" name="nim_nik" id="edit_nim_nik" />
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status" id="edit_status" required style="appearance: none; background: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%236B7280\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>') no-repeat right 12px center; background-color: #fff;">
                            <option value="dipinjam">Dipinjam</option>
                            <option value="terlambat">Terlambat</option>
                            <option value="selesai">Selesai</option>
                            <option value="menunggu">Menunggu</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Barang</label>
                        <input type="number" class="form-control" name="jumlah" id="edit_jumlah" min="1" required />
                        <div style="font-size: 11px; color: var(--primary); margin-top: 4px; font-weight: 500;">
                            *Stok tersedia: <?= (int)$b['stok_gudang'] ?> Pcs
                        </div>
                    </div>
                </div>

                <div class="form-row" style="align-items: flex-start; margin-top: 4px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Tenggat Kembali</label>
                        <input type="date" class="form-control" name="tenggat_kembali" id="edit_tenggat_kembali" required />
                    </div>
                    <div class="form-group" style="margin-bottom: 0; display: flex; flex-direction: column;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; height: 42px; justify-content: center; font-size: 14px; margin-top: 24px; border-radius: 6px; box-shadow: 0 4px 12px rgba(240, 90, 26, 0.25);">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Filter Peminjaman -->
<div class="modal-overlay" id="modalFilterPeminjaman">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div class="modal-title">Filter Barang</div>
            <button class="modal-close" type="button" onclick="closeModal('modalFilterPeminjaman')"><i class="ri-close-line"></i></button>
        </div>
        <form action="" method="get">
            <div class="modal-body" style="padding-bottom: 24px;">
                
                <!-- Row 1: Tanggal Pinjam & Jumlah Barang -->
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tanggal Pinjam</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <input type="date" class="form-control" name="tgl_pinjam_start" style="color: var(--gray-500);" />
                            <input type="date" class="form-control" name="tgl_pinjam_end" style="color: var(--gray-500);" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Barang</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <input type="number" class="form-control" name="jumlah_min" placeholder="0" />
                            <input type="number" class="form-control" name="jumlah_max" placeholder="0" />
                        </div>
                    </div>
                </div>

                <!-- Row 2: Status -->
                <div class="form-group" style="margin-top: 8px;">
                    <label class="form-label">Status</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <label style="display:flex; justify-content:space-between; align-items:center; padding:10px 16px; border:1px solid #E5E7EB; border-radius:8px; cursor:pointer;">
                            <span style="font-size:14px; font-weight:500; color:#374151;">Semua</span>
                            <input type="radio" name="filter_status" value="semua" checked style="accent-color: #F05A1A; width:18px; height:18px;">
                        </label>
                        <label style="display:flex; justify-content:space-between; align-items:center; padding:10px 16px; border:1px solid #E5E7EB; border-radius:8px; cursor:pointer;">
                            <span style="font-size:14px; font-weight:500; color:#374151;">Dipinjam</span>
                            <input type="radio" name="filter_status" value="dipinjam" style="accent-color: #F05A1A; width:18px; height:18px;">
                        </label>
                        <label style="display:flex; justify-content:space-between; align-items:center; padding:10px 16px; border:1px solid #E5E7EB; border-radius:8px; cursor:pointer;">
                            <span style="font-size:14px; font-weight:500; color:#374151;">Kembali</span>
                            <input type="radio" name="filter_status" value="kembali" style="accent-color: #F05A1A; width:18px; height:18px;">
                        </label>
                        <label style="display:flex; justify-content:space-between; align-items:center; padding:10px 16px; border:1px solid #E5E7EB; border-radius:8px; cursor:pointer;">
                            <span style="font-size:14px; font-weight:500; color:#374151;">Terlambat</span>
                            <input type="radio" name="filter_status" value="terlambat" style="accent-color: #F05A1A; width:18px; height:18px;">
                        </label>
                    </div>
                </div>

                <!-- Row 3: Tanggal Kembali & Button -->
                <div class="form-row" style="margin-top: 16px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Tanggal Kembali</label>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                            <input type="number" class="form-control" name="tgl_kembali_start" placeholder="0" />
                            <input type="number" class="form-control" name="tgl_kembali_end" placeholder="0" />
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 0; display: flex; flex-direction: column; justify-content: flex-end;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; height: 42px; justify-content: center; font-size: 14px; border-radius: 6px; box-shadow: 0 4px 12px rgba(240, 90, 26, 0.25);">
                            Terapkan Filter
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('cariPeminjaman').addEventListener('input', function() {
    var q = this.value.toLowerCase();
    document.querySelectorAll('#tabelPeminjaman tbody tr').forEach(function(tr) {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});

function isiEditPeminjaman(data) {
    document.getElementById('formEditPeminjaman').action = '<?= base_url('barang/peminjaman') ?>/' + data.id + '/edit';
    document.getElementById('edit_nama_peminjam').value = data.nama_peminjam;
    document.getElementById('edit_nim_nik').value = data.nim_nik || '';
    document.getElementById('edit_status').value = data.status || 'dipinjam';
    document.getElementById('edit_jumlah').value = data.jumlah;
    document.getElementById('edit_tenggat_kembali').value = data.tenggat_kembali;
    openModal('modalEditPeminjaman');
}

</script>
<?= $this->endSection() ?>
