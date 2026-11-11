<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
    <a href="<?= base_url('kelola') ?>" style="width:36px;height:36px;background:white;border:1px solid #E5E7EB;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#6B7280;text-decoration:none;font-size:18px;transition:.15s;flex-shrink:0;" onmouseover="this.style.background='#F3F4F6';this.style.color='#1F2937';" onmouseout="this.style.background='white';this.style.color='#6B7280';" title="Kembali ke Kelola Sistem">
        <i class="ri-arrow-left-line"></i>
    </a>
    <div style="font-size:20px;font-weight:700;color:#1F2937;">Kelola <span style="color:var(--primary);">Jurusan</span></div>
</div>

<div class="table-section">
    <div class="table-toolbar" style="padding:20px 24px 12px;">
        <div class="table-toolbar-left">
            <span class="table-title">Daftar Jurusan</span>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <div style="background:#FFFFFF;border:1px solid #D1D5DB;border-radius:8px;padding:8px 14px;display:flex;align-items:center;gap:8px;box-shadow:inset 0 1px 2px rgba(0,0,0,0.02);transition:0.2s;">
                <i class="ri-search-line" style="color:#6B7280;font-size:16px;"></i>
                <input type="text" id="cariJurusan" placeholder="Cari jurusan..." 
                       style="border:none;background:transparent;outline:none;font-size:13px;width:200px;color:#374151;">
            </div>
            <button class="btn btn-primary" onclick="openModal('modalTambahJurusan')">
                <i class="ri-add-line"></i> Tambah Jurusan
            </button>
        </div>
    </div>

    <div class="data-table-wrapper">
        <table class="data-table" id="tabelJurusan">
            <thead>
                <tr>
                    <th width="10%" style="text-align:center;">No</th>
                    <th width="75%">Nama Jurusan</th>
                    <th width="15%" style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                    <?php $no = 1; foreach ($list as $j): ?>
                        <tr>
                            <td style="text-align:center;font-weight:500;color:#6B7280;"><?= $no++ ?></td>
                            <td style="font-weight:600;color:#374151;"><?= esc($j['nama_jurusan']) ?></td>
                            <td style="text-align:center;">
                                <div style="display:flex;gap:6px;justify-content:center;">
                                    <button onclick="editJurusan('<?= encode_id($j['id']) ?>', '<?= esc(addslashes($j['nama_jurusan'])) ?>')" 
                                            class="btn-icon" style="background:#EEF3FF;color:#407BFF;border:none;" title="Edit Jurusan">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <form method="post" action="<?= base_url('jurusan/hapus/' . encode_id($j['id'])) ?>" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus jurusan ini?')">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-icon" style="background:#FEF2F2;color:#EF4444;border:none;display:inline-flex;align-items:center;justify-content:center;" title="Hapus Jurusan">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Jurusan -->
<div class="modal-overlay" id="modalTambahJurusan">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Tambah Jurusan</div>
            <button class="modal-close" onclick="closeModal('modalTambahJurusan')"><i class="ri-close-line"></i></button>
        </div>
        <form action="<?= base_url('jurusan/simpan') ?>" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom: 24px;">
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Nama Jurusan <span style="color:#EF4444">*</span></label>
                    <input type="text" class="form-control" name="nama_jurusan" placeholder="Contoh: S1 TEKNIK INFORMATIKA (TI)" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()" required />
                </div>
                
                <div style="margin-top:24px;display:flex;justify-content:flex-end;gap:12px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalTambahJurusan')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Jurusan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Jurusan -->
<div class="modal-overlay" id="modalEditJurusan">
    <div class="modal-box">
        <div class="modal-header">
            <div class="modal-title">Edit Jurusan</div>
            <button class="modal-close" onclick="closeModal('modalEditJurusan')"><i class="ri-close-line"></i></button>
        </div>
        <form id="formEditJurusan" method="post">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom: 24px;">
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="form-label">Nama Jurusan <span style="color:#EF4444">*</span></label>
                    <input type="text" class="form-control" id="edit_nama" name="nama_jurusan" style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase()" required />
                </div>
                
                <div style="margin-top:24px;display:flex;justify-content:flex-end;gap:12px;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('modalEditJurusan')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function editJurusan(id, nama) {
    document.getElementById('edit_nama').value = nama;
    var baseUrl = '<?= base_url('jurusan/edit') ?>';
    document.getElementById('formEditJurusan').action = baseUrl + '/' + id;
    openModal('modalEditJurusan');
}

$(document).ready(function() {
    // Sinkronisasi custom search dengan DataTables API
    $('#cariJurusan').on('keyup', function() {
        if ($.fn.DataTable.isDataTable('#tabelJurusan')) {
            $('#tabelJurusan').DataTable().search(this.value).draw();
        } else {
            // Jika tidak pakai DataTable, fallback sederhana
            let val = $(this).val().toLowerCase();
            $('#tabelJurusan tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1)
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
