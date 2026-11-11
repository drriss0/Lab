<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
    <a href="<?= base_url('kelola') ?>" style="width:36px;height:36px;background:white;border:1px solid #E5E7EB;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#6B7280;text-decoration:none;font-size:18px;transition:.15s;flex-shrink:0;" onmouseover="this.style.background='#F3F4F6';this.style.color='#1F2937';" onmouseout="this.style.background='white';this.style.color='#6B7280';" title="Kembali ke Kelola Sistem">
        <i class="ri-arrow-left-line"></i>
    </a>
    <div style="font-size:20px;font-weight:700;color:#1F2937;">Kelola <span style="color:var(--primary);">Jenis Barang</span></div>
</div>

<div class="table-section">
    <div class="table-toolbar" style="padding:20px 24px 12px;">
        <div class="table-toolbar-left">
            <span class="table-title">Daftar Jenis Barang</span>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <div style="background:#FFFFFF;border:1px solid #D1D5DB;border-radius:8px;padding:8px 14px;display:flex;align-items:center;gap:8px;box-shadow:inset 0 1px 2px rgba(0,0,0,0.02);transition:0.2s;">
                <i class="ri-search-line" style="color:#6B7280;font-size:16px;"></i>
                <input type="text" id="cariKategori" placeholder="Cari jenis barang..." 
                       style="border:none;background:transparent;outline:none;font-size:13px;width:200px;color:#374151;">
            </div>
            <button class="btn btn-primary" onclick="openModal('modalTambahKategori')">
                <i class="ri-add-line"></i> Tambah Jenis
            </button>
        </div>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table" id="tabelKategori">
            <thead>
                <tr>
                    <th width="5%" style="text-align:center;">No</th>
                    <th width="20%">Kode Awal (Prefix)</th>
                    <th width="35%">Jenis Barang</th>
                    <th width="15%" style="text-align:center;">Total Barang</th>
                    <th width="15%" style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                    <?php $no = 1; foreach ($kategori as $k): ?>
                        <tr>
                            <td style="text-align:center;font-weight:500;color:#6B7280;"><?= $no++ ?></td>
                            <td style="font-weight:700;color:#F05A1A;"><?= esc($k['kode_kategori'] ?? '-') ?></td>
                            <td style="font-weight:500;color:#374151;"><?= esc($k['nama']) ?></td>
                            <td style="text-align:center;">
                                <span style="background:#EEF3FF;color:#1D4ED8;padding:4px 10px;border-radius:6px;font-size:12px;font-weight:600;">
                                    <?= $k['total_barang'] ?> Aset
                                </span>
                            </td>
                            <td style="text-align:center;">
                                <div style="display:flex;gap:6px;justify-content:center;">
                                    <button onclick="editKategori('<?= encode_id($k['id']) ?>', '<?= esc(addslashes($k['nama'])) ?>', '<?= esc(addslashes($k['kode_kategori'] ?? '')) ?>')" 
                                            class="btn-icon" style="background:#EEF3FF;color:#407BFF;border:none;" title="Edit Jenis">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <?php if ($k['total_barang'] == 0): ?>
                                    <form method="post" action="<?= base_url('kategori/hapus/' . encode_id($k['id'])) ?>" style="display:inline;" onsubmit="return confirm('Jenis barang yang dihapus tidak dapat dikembalikan. Hapus permanen?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-icon" style="background:#FEF2F2;color:#EF4444;border:none;display:inline-flex;align-items:center;justify-content:center;" title="Hapus Jenis">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <button class="btn-icon" style="background:#F3F4F6;color:#D1D5DB;border:none;cursor:not-allowed;" title="Tidak dapat dihapus (masih ada barang yang menggunakan jenis ini)">
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
<div class="modal-overlay" id="modalTambahKategori">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Tambah Jenis Barang</div>
            <button class="modal-close" onclick="closeModal('modalTambahKategori')"><i class="ri-close-line"></i></button>
        </div>
        <form action="<?= base_url('kategori/simpan') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom: 24px;">
                <div class="form-row" style="display:flex;gap:12px;margin-bottom:16px;">
                    <div class="form-group" style="flex:1;">
                        <label class="form-label">Kode Awal (Prefix) <span style="color:#EF4444">*</span></label>
                        <input type="text" class="form-control" name="kode_kategori" placeholder="Contoh: CAM" required style="text-transform:uppercase;" maxlength="10" />
                    </div>
                    <div class="form-group" style="flex:2;">
                        <label class="form-label">Jenis Barang <span style="color:#EF4444">*</span></label>
                        <input type="text" class="form-control" name="nama" placeholder="Contoh: Kamera" oninput="this.value = this.value.replace(/\b\w/g, function(l){return l.toUpperCase()})" required />
                    </div>
                </div>
                
                <div style="margin-top:24px;display:flex;justify-content:flex-end;gap:12px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalTambahKategori')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Jenis</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div class="modal-overlay" id="modalEditKategori">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Edit Jenis Barang</div>
            <button class="modal-close" onclick="closeModal('modalEditKategori')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formEditKategori" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom: 24px;">
                <div class="form-row" style="display:flex;gap:12px;margin-bottom:16px;">
                    <div class="form-group" style="flex:1;">
                        <label class="form-label">Kode Awal (Prefix) <span style="color:#EF4444">*</span></label>
                        <input type="text" class="form-control" id="edit_kode" name="kode_kategori" required style="text-transform:uppercase;" maxlength="10" />
                    </div>
                    <div class="form-group" style="flex:2;">
                        <label class="form-label">Jenis Barang <span style="color:#EF4444">*</span></label>
                        <input type="text" class="form-control" id="edit_nama" name="nama" oninput="this.value = this.value.replace(/\b\w/g, function(l){return l.toUpperCase()})" required />
                    </div>
                </div>
                
                <div style="margin-top:24px;display:flex;justify-content:flex-end;gap:12px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditKategori')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function editKategori(id, nama, kode) {
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_kode').value = kode;
    var baseUrl = '<?= base_url('kategori/edit') ?>';
    document.getElementById('formEditKategori').action = baseUrl + '/' + id;
    openModal('modalEditKategori');
}

$(document).ready(function() {
    // Sinkronisasi custom search dengan DataTables API
    $('#cariKategori').on('keyup', function() {
        $('#tabelKategori').DataTable().search(this.value).draw();
    });
});
</script>

<?= $this->endSection() ?>
