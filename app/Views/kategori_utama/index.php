<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
    <a href="<?= base_url('kelola') ?>" style="width:36px;height:36px;background:white;border:1px solid #E5E7EB;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#6B7280;text-decoration:none;font-size:18px;transition:.15s;flex-shrink:0;" onmouseover="this.style.background='#F3F4F6';this.style.color='#1F2937';" onmouseout="this.style.background='white';this.style.color='#6B7280';" title="Kembali ke Kelola Sistem">
        <i class="ri-arrow-left-line"></i>
    </a>
    <div style="font-size:20px;font-weight:700;color:#1F2937;">Kelola <span style="color:var(--primary);">Kategori Utama</span></div>
</div>

<div class="table-section">
    <div class="table-toolbar" style="padding:20px 24px 12px;">
        <div class="table-toolbar-left">
            <span class="table-title">Daftar Kategori Utama</span>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <div style="background:#FFFFFF;border:1px solid #D1D5DB;border-radius:8px;padding:8px 14px;display:flex;align-items:center;gap:8px;box-shadow:inset 0 1px 2px rgba(0,0,0,0.02);transition:0.2s;">
                <i class="ri-search-line" style="color:#6B7280;font-size:16px;"></i>
                <input type="text" id="cariKategoriUtama" placeholder="Cari kategori..." 
                       style="border:none;background:transparent;outline:none;font-size:13px;width:200px;color:#374151;">
            </div>
            <button class="btn btn-primary" onclick="openModal('modalTambahKategoriUtama')">
                <i class="ri-add-line"></i> Tambah Kategori
            </button>
        </div>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table" id="tabelKategoriUtama">
            <thead>
                <tr>
                    <th width="5%" style="text-align:center;">No</th>
                    <th width="25%">Kategori Utama</th>
                    <th width="40%">Keterangan</th>
                    <th width="15%" style="text-align:center;">Total Barang</th>
                    <th width="15%" style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                    <?php $no = 1; foreach ($kategori as $k): ?>
                        <tr>
                            <td style="text-align:center;font-weight:500;color:#6B7280;"><?= $no++ ?></td>
                            <td style="font-weight:700;color:#F05A1A;"><?= esc($k['nama_kategori']) ?></td>
                            <td style="font-weight:500;color:#374151;"><?= esc($k['keterangan'] ?? '-') ?></td>
                            <td style="text-align:center;">
                                <span style="background:#EEF3FF;color:#1D4ED8;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;">
                                    <?= $k['total_barang'] ?> Aset
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;gap:6px;justify-content:center;">
                                    <button onclick="editKategoriUtama('<?= encode_id($k['id']) ?>', '<?= esc(addslashes($k['nama_kategori'])) ?>', '<?= esc(addslashes($k['keterangan'] ?? '')) ?>')" 
                                            class="btn-icon" style="background:#EEF3FF;color:#407BFF;border:none;" title="Edit Kategori">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <?php if ($k['total_barang'] == 0): ?>
                                    <form method="post" action="<?= base_url('kategori-utama/hapus/' . encode_id($k['id'])) ?>" style="display:inline;" onsubmit="return confirm('Kategori yang dihapus tidak dapat dikembalikan. Hapus permanen?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-icon" style="background:#FEF2F2;color:#EF4444;border:none;display:inline-flex;align-items:center;justify-content:center;" title="Hapus Kategori">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <button class="btn-icon" style="background:#F3F4F6;color:#D1D5DB;border:none;cursor:not-allowed;" title="Tidak dapat dihapus (masih ada barang yang menggunakan kategori ini)">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Kategori -->
<div class="modal-overlay" id="modalTambahKategoriUtama">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Tambah Kategori Utama</div>
            <button class="modal-close" onclick="closeModal('modalTambahKategoriUtama')"><i class="ri-close-line"></i></button>
        </div>
        <form action="<?= base_url('kategori-utama/simpan') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom: 24px;">
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Kategori Utama <span style="color:#EF4444">*</span></label>
                    <input type="text" class="form-control" name="nama_kategori" placeholder="Contoh: Elektronik" oninput="this.value = this.value.replace(/\b\w/g, function(l){return l.toUpperCase()})" required />
                </div>
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Keterangan (Opsional)</label>
                    <textarea class="form-control" name="keterangan" placeholder="Contoh: Kategori untuk barang-barang elektronik" rows="3"></textarea>
                </div>
                
                <div style="margin-top:24px;display:flex;justify-content:flex-end;gap:12px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalTambahKategoriUtama')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal-overlay" id="modalEditKategoriUtama">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Edit Kategori Utama</div>
            <button class="modal-close" onclick="closeModal('modalEditKategoriUtama')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formEditKategoriUtama" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom: 24px;">
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Kategori Utama <span style="color:#EF4444">*</span></label>
                    <input type="text" class="form-control" id="edit_nama_kategori" name="nama_kategori" oninput="this.value = this.value.replace(/\b\w/g, function(l){return l.toUpperCase()})" required />
                </div>
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Keterangan (Opsional)</label>
                    <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"></textarea>
                </div>
                
                <div style="margin-top:24px;display:flex;justify-content:flex-end;gap:12px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditKategoriUtama')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function editKategoriUtama(id, nama, keterangan) {
    document.getElementById('edit_nama_kategori').value = nama;
    document.getElementById('edit_keterangan').value = keterangan;
    var baseUrl = '<?= base_url('kategori-utama/edit') ?>';
    document.getElementById('formEditKategoriUtama').action = baseUrl + '/' + id;
    openModal('modalEditKategoriUtama');
}

$(document).ready(function() {
    // Sinkronisasi custom search dengan DataTables API
    $('#cariKategoriUtama').on('keyup', function() {
        $('#tabelKategoriUtama').DataTable().search(this.value).draw();
    });
});
</script>

<?= $this->endSection() ?>
