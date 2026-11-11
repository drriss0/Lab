<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<?php $b = $barang; ?>

<!-- Breadcrumb -->
<div class="page-breadcrumb" style="margin-bottom:16px;">
    <a href="<?= base_url('dashboard') ?>"><i class="ri-arrow-left-s-line"></i> Dashboard</a>
    <span class="separator">›</span>
    <a href="<?= base_url('barang/' . encode_id($b['id']) . '/detail') ?>">Detail</a>
    <span class="separator">›</span>
    <span>Pelaporan Rusak</span>
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
            <div style="font-size:16px;font-weight:700;color:var(--gray-800);">Pelaporan Barang Rusak</div>
            <div style="font-size: 22px; font-weight: 700; color: var(--primary); margin-bottom: 8px;"><?= esc($b['nama_barang']) ?></div>
            <p style="color: var(--gray-500); font-size: 12px; line-height: 1.5; margin: 0; max-width: 360px;">
                Laporkan kerusakan aset untuk proses pengajuan perbaikan atau pendataan barang tidak layak pakai di lingkungan FTIK.
            </p>
        </div>
        <div style="background: #FCECE4; border: 1px solid #F3C4AD; border-radius: 8px; padding: 16px 24px; display: flex; flex-direction: column; justify-content: center; align-items: center; min-width: 140px;">
            <div style="font-size: 32px; font-weight: 800; color: var(--primary); line-height: 1;"><?= $total_rusak ?></div>
            <div style="font-size: 11px; color: var(--primary); margin-top: 8px; font-weight: 500;">Barang Rusak</div>
        </div>
    </div>

    <!-- Blue Card (Action) -->
    <div style="width: 260px; background: #407BFF; border-radius: 12px; padding: 20px 24px; display: flex; flex-direction: column; justify-content: center; position: relative; overflow: hidden; flex-shrink: 0; box-shadow: 0 4px 12px rgba(64, 123, 255, 0.25);">
        <i class="ri-folder-add-fill" style="position: absolute; top: 10px; right: -10px; font-size: 80px; color: rgba(255,255,255,0.15); transform: rotate(-10deg);"></i>
        <div style="color: white; font-size: 14px; font-weight: 600; line-height: 1.4; margin-bottom: 14px; z-index: 1;">
            Pendataan<br/>Barang Rusak
        </div>
        <button onclick="openModal('modalLaporBaru')" style="background: white; color: #407BFF; border: none; padding: 10px 16px; border-radius: 6px; font-weight: 600; font-size: 13px; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; z-index: 1; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-2px)';" onmouseout="this.style.transform='translateY(0)';">
            <i class="ri-folder-add-line" style="font-size: 16px;"></i> Laporkan Rusak
        </button>
    </div>
</div>

<!-- Table -->
<div class="table-section">
    <div class="table-toolbar">
        <div class="table-toolbar-left" style="flex: 1; max-width: 600px;">
            <span class="table-title" style="font-size: 18px; font-weight: 700; color: var(--gray-800); margin-right: 16px;">Tabel Barang Rusak</span>
            <div class="search-box" style="flex: 1; background: white; border-radius: 8px; border: 1px solid var(--gray-200); padding: 8px 16px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                <span class="search-icon" style="color: var(--primary); font-size: 16px;"><i class="ri-search-line"></i></span>
                <input type="text" id="cariPelaporan" placeholder="Cari di sini..." autocomplete="off" style="border: none; background: transparent; padding-left: 8px; font-size: 13px; width: 100%; outline: none; color: var(--gray-600);" />
            </div>
        </div>
        <div class="table-toolbar-right" style="display: flex; gap: 12px;">
            <button class="btn btn-primary" onclick="openModal('modalFilterPelaporan')" style="padding: 10px 20px; font-size: 13px; border-radius: 6px; gap: 8px;">
                <i class="ri-equalizer-line"></i> Filter
            </button>
            <button onclick="window.print()" id="btnExportPelaporan" style="background:#FFF3EE;color:#F05A1A;border:1px solid #FCD9C5;padding:10px 20px;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:.2s;" onmouseover="this.style.background='#FFD5C2';" onmouseout="this.style.background='#FFF3EE';">
                <i class="ri-printer-line" style="font-size: 16px;"></i> Export
            </button>
        </div>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table" id="tabelPelaporan">
            <thead>
                <tr>
                    <th>Tanggal Lapor</th>
                    <th>Lokasi Asal</th>
                    <th style="text-align:center;">Jumlah</th>
                    <th>Tingkat Kerusakan</th>
                    <th>Status</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>

                    <?php foreach ($pelaporan as $p): ?>
                    <?php
                    $tkClass = match($p['tingkat_kerusakan']) {
                        'rusak_ringan' => 'badge-rusak-ringan',
                        'rusak_berat'  => 'badge-rusak-berat',
                        default        => 'badge-warning',
                    };
                    $tkLabel = match($p['tingkat_kerusakan']) {
                        'rusak_ringan' => 'Rusak Ringan',
                        'rusak_berat'  => 'Rusak Berat',
                        default        => ucfirst($p['tingkat_kerusakan']),
                    };
                    $stClass = match($p['status']) {
                        'menunggu'         => 'badge-menunggu',
                        'sedang_diperbaiki' => 'badge-diperbaiki',
                        'selesai'          => 'badge-selesai',
                        default            => 'badge-info',
                    };
                    $stLabel = match($p['status']) {
                        'menunggu'         => 'Menunggu',
                        'sedang_diperbaiki' => 'Sedang Diperbaiki',
                        'selesai'          => 'Selesai',
                        default            => ucfirst($p['status']),
                    };
                    ?>
                    <tr id="lapor-row-<?= $p['id'] ?>" class="lapor-row" data-tanggal="<?= $p['tanggal_lapor'] ?>" data-jumlah="<?= $p['jumlah'] ?>" data-tingkat="<?= $p['tingkat_kerusakan'] ?>" data-status="<?= $p['status'] ?>">
                        <td><?= date('d/m/y', strtotime($p['tanggal_lapor'])) ?></td>
                        <td style="font-weight:500;"><?= esc($p['nama_ruangan'] ?? 'Tidak diketahui') ?></td>
                        <td style="text-align:center;font-weight:600;"><?= $p['jumlah'] ?> Pcs</td>
                        <td><span class="badge <?= $tkClass ?>"><?= $tkLabel ?></span></td>
                        <td><span class="badge <?= $stClass ?>"><?= $stLabel ?></span></td>
                        <td style="text-align:center;">
                            <button type="button"
                               onclick="isiEditRusak(<?= htmlspecialchars(json_encode($p)) ?>)"
                               style="background: #3B82F6; color: white; width: 32px; height: 32px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; border: none; cursor: pointer;"
                               title="Edit">
                                <i class="ri-pencil-fill" style="font-size: 16px;"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if (!empty($pelaporan)): ?>
    <div class="pagination-wrapper">
        <span>Menampilkan <?= count($pelaporan) ?> laporan kerusakan</span>
    </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<!-- Modal: Laporan Baru -->
<div class="modal-overlay" id="modalLaporBaru">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div class="modal-title">Form Pelaporan Kerusakan Aset</div>
            <button class="modal-close" onclick="closeModal('modalLaporBaru')"><i class="ri-close-line"></i></button>
        </div>
        <form action="<?= base_url('barang/' . encode_id($b['id']) . '/pelaporan/simpan') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom: 24px;">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Lokasi Asal Barang</label>
                        <select class="form-control" name="id_ruangan">
                            <option value="">Pilih Lokasi Ruangan</option>
                            <?php foreach ($ruangan as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= esc($r['nama_ruangan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tingkat Kerusakan</label>
                        <select class="form-control" name="tingkat_kerusakan" required>
                            <option value="">Pilih Kondisi Kerusakan</option>
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tanggal Lapor</label>
                        <input type="date" class="form-control" name="tanggal_lapor"
                               value="<?= date('Y-m-d') ?>" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Rusak</label>
                        <input type="number" class="form-control" name="jumlah" min="1" required placeholder="0" />
                    </div>
                </div>

                <div class="form-row" style="align-items: flex-start; margin-top: 4px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Deskripsi Kerusakan</label>
                        <input type="text" class="form-control" name="keterangan" pattern=".*[a-zA-Z].*[a-zA-Z].*[a-zA-Z].*" title="Harus mengandung minimal 3 huruf alfabet" placeholder="Contoh: LCD Bergaris" />
                    </div>
                    <div class="form-group" style="margin-bottom: 0; display: flex; flex-direction: column;">
                        <button type="submit" class="btn btn-primary" style="width: 100%; height: 42px; justify-content: center; font-size: 14px; margin-top: 24px; border-radius: 6px; box-shadow: 0 4px 12px rgba(240, 90, 26, 0.25);">
                            Simpan Laporan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Filter Pelaporan -->
<div class="modal-overlay" id="modalFilterPelaporan">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div class="modal-title">Filter Barang</div>
            <button type="button" class="modal-close" onclick="closeModal('modalFilterPelaporan')"><i class="ri-close-line"></i></button>
        </div>
        <div class="modal-body" style="padding-bottom: 24px;">
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Tanggal</label>
                    <div style="display: flex; gap: 12px;">
                        <input type="date" id="filter_lapor_tgl_mulai" class="form-control" style="flex: 1;" placeholder="dd/mm/yy">
                        <input type="date" id="filter_lapor_tgl_akhir" class="form-control" style="flex: 1;" placeholder="dd/mm/yy">
                    </div>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label class="form-label">Jumlah Barang</label>
                    <div style="display: flex; gap: 12px;">
                        <input type="number" id="filter_lapor_jumlah_min" class="form-control" style="flex: 1;" placeholder="0" min="0">
                        <input type="number" id="filter_lapor_jumlah_max" class="form-control" style="flex: 1;" placeholder="0" min="0">
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-top: 16px;">
                <label class="form-label">Tingkat Kerusakan</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <label style="border: 1px solid var(--gray-200); border-radius: 8px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <span style="font-size: 14px; font-weight: 500; color: var(--gray-700);">Semua</span>
                        <input type="radio" name="filter_lapor_tingkat" value="semua" checked style="accent-color: var(--primary); transform: scale(1.2);">
                    </label>
                    <label style="border: 1px solid var(--gray-200); border-radius: 8px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <span style="font-size: 14px; font-weight: 500; color: var(--gray-700);">Rusak Ringan</span>
                        <input type="radio" name="filter_lapor_tingkat" value="rusak_ringan" style="accent-color: var(--primary); transform: scale(1.2);">
                    </label>
                    <label style="border: 1px solid var(--gray-200); border-radius: 8px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <span style="font-size: 14px; font-weight: 500; color: var(--gray-700);">Rusak Berat</span>
                        <input type="radio" name="filter_lapor_tingkat" value="rusak_berat" style="accent-color: var(--primary); transform: scale(1.2);">
                    </label>
                </div>
            </div>

            <div class="form-group" style="margin-top: 16px;">
                <label class="form-label">Status</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <label style="border: 1px solid var(--gray-200); border-radius: 8px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <span style="font-size: 14px; font-weight: 500; color: var(--gray-700);">Semua</span>
                        <input type="radio" name="filter_lapor_status" value="semua" checked style="accent-color: var(--primary); transform: scale(1.2);">
                    </label>
                    <label style="border: 1px solid var(--gray-200); border-radius: 8px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <span style="font-size: 14px; font-weight: 500; color: var(--gray-700);">Menunggu</span>
                        <input type="radio" name="filter_lapor_status" value="menunggu" style="accent-color: var(--primary); transform: scale(1.2);">
                    </label>
                    <label style="border: 1px solid var(--gray-200); border-radius: 8px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <span style="font-size: 14px; font-weight: 500; color: var(--gray-700);">Sedang Diperbaiki</span>
                        <input type="radio" name="filter_lapor_status" value="sedang_diperbaiki" style="accent-color: var(--primary); transform: scale(1.2);">
                    </label>
                    <label style="border: 1px solid var(--gray-200); border-radius: 8px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; cursor: pointer;">
                        <span style="font-size: 14px; font-weight: 500; color: var(--gray-700);">Selesai</span>
                        <input type="radio" name="filter_lapor_status" value="selesai" style="accent-color: var(--primary); transform: scale(1.2);">
                    </label>
                </div>
            </div>

            <div class="form-group" style="margin-top: 24px; display: flex; justify-content: flex-end; width: 100%;">
                <button type="button" class="btn btn-primary" onclick="terapkanFilterPelaporan()" style="width: 100%; height: 42px; justify-content: center; font-size: 14px; border-radius: 6px; box-shadow: 0 4px 12px rgba(240, 90, 26, 0.25);">
                    Terapkan Filter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Edit Laporan Rusak -->
<div class="modal-overlay" id="modalEditRusak">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div class="modal-title">Edit Barang</div>
            <button class="modal-close" type="button" onclick="closeModal('modalEditRusak')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formEditRusak" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom: 24px;">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Lokasi Awal</label>
                        <select class="form-control" name="id_ruangan" id="edit_rusak_ruangan" required style="appearance: none; background: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%236B7280\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>') no-repeat right 12px center; background-color: #fff;">
                            <option value="">Pilih Lokasi</option>
                            <?php foreach ($ruangan as $r): ?>
                                <option value="<?= $r['id'] ?>"><?= esc($r['nama_ruangan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tingkat Kerusakan</label>
                        <select class="form-control" name="tingkat_kerusakan" id="edit_rusak_tingkat" required style="appearance: none; background: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%236B7280\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>') no-repeat right 12px center; background-color: #fff;">
                            <option value="rusak_ringan">Rusak Ringan</option>
                            <option value="rusak_berat">Rusak Berat</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status" id="edit_rusak_status" required style="appearance: none; background: url('data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%236B7280\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'><polyline points=\'6 9 12 15 18 9\'></polyline></svg>') no-repeat right 12px center; background-color: #fff;">
                            <option value="menunggu">Menunggu</option>
                            <option value="sedang_diperbaiki">Sedang Diperbaiki</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Barang</label>
                        <input type="number" class="form-control" name="jumlah" id="edit_rusak_jumlah" min="1" required />
                        <div style="font-size: 11px; color: var(--primary); margin-top: 4px; font-weight: 500;">
                            *Stok tersedia: <?= (int)$b['stok_gudang'] ?> Pcs
                        </div>
                    </div>
                </div>

                <div class="form-row" style="align-items: flex-start; margin-top: 4px;">
                    <div class="form-group" style="margin-bottom: 0;"></div>
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

<script>
function applyAllFiltersPelaporan() {
    let q = document.getElementById('cariPelaporan').value.toLowerCase();
    
    let tglMulai = document.getElementById('filter_lapor_tgl_mulai') ? document.getElementById('filter_lapor_tgl_mulai').value : '';
    let tglAkhir = document.getElementById('filter_lapor_tgl_akhir') ? document.getElementById('filter_lapor_tgl_akhir').value : '';
    let minJumlah = document.getElementById('filter_lapor_jumlah_min') ? document.getElementById('filter_lapor_jumlah_min').value : '';
    let maxJumlah = document.getElementById('filter_lapor_jumlah_max') ? document.getElementById('filter_lapor_jumlah_max').value : '';
    
    let tingkatEl = document.querySelector('input[name="filter_lapor_tingkat"]:checked');
    let tingkat = tingkatEl ? tingkatEl.value : 'semua';
    
    let statusEl = document.querySelector('input[name="filter_lapor_status"]:checked');
    let status = statusEl ? statusEl.value : 'semua';

    document.querySelectorAll('.lapor-row').forEach(function(tr) {
        let rowTanggal = tr.getAttribute('data-tanggal');
        let rowJumlah = parseInt(tr.getAttribute('data-jumlah'));
        let rowTingkat = tr.getAttribute('data-tingkat');
        let rowStatus = tr.getAttribute('data-status');

        let show = true;

        if (tglMulai && rowTanggal < tglMulai) show = false;
        if (tglAkhir && rowTanggal > tglAkhir) show = false;
        if (minJumlah !== '' && rowJumlah < parseInt(minJumlah)) show = false;
        if (maxJumlah !== '' && rowJumlah > parseInt(maxJumlah)) show = false;
        if (tingkat !== 'semua' && rowTingkat !== tingkat) show = false;
        if (status !== 'semua' && rowStatus !== status) show = false;
        if (q && !tr.textContent.toLowerCase().includes(q)) show = false;

        tr.style.display = show ? '' : 'none';
    });
}

document.getElementById('cariPelaporan').addEventListener('input', applyAllFiltersPelaporan);

function terapkanFilterPelaporan() {
    applyAllFiltersPelaporan();
    closeModal('modalFilterPelaporan');
}

function isiEditRusak(data) {
    var form = document.getElementById('formEditRusak');
    form.action = '<?= base_url('barang/pelaporan') ?>/' + data.id + '/edit';
    document.getElementById('edit_rusak_ruangan').value = data.id_ruangan || '';
    document.getElementById('edit_rusak_tingkat').value = data.tingkat_kerusakan || 'rusak_ringan';
    document.getElementById('edit_rusak_status').value = data.status || 'menunggu';
    document.getElementById('edit_rusak_jumlah').value = data.jumlah;
    openModal('modalEditRusak');
}
</script>
<?= $this->endSection() ?>
