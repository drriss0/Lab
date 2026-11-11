<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="SIn-FTIK USM — Sistem Inventaris Fakultas Teknologi Informasi dan Komunikasi Universitas Semarang" />
    <title><?= esc($title ?? 'SIn-FTIK USM') ?></title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= base_url('assets/img/ftik-logo.png') ?>" />

    <!-- Main CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>?v=<?= @filemtime(FCPATH . 'assets/css/style.css') ?: '2.2' ?>" />

    <!-- Remix Icon (CDN) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" />

    <!-- SweetAlert2 CSS (Opsional untuk custom theming jika perlu) -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        /* â”€â”€â”€ Sembunyikan pagination lama (digantikan DataTables) â”€â”€â”€ */
        .pagination-wrapper { display: none !important; }

        /* â”€â”€â”€ DataTables wrapper: hilangkan style bawaan yang berantakan â”€â”€â”€ */
        .dataTables_wrapper { position: relative; }

        /* â”€â”€â”€ Sembunyikan search bawaan (halaman sudah punya search sendiri) â”€â”€â”€ */
        .dataTables_wrapper .dataTables_filter { display: none !important; }

        /* â”€â”€â”€ Top bar: Tampilkan X data â”€â”€â”€ */
        .dataTables_wrapper .dataTables_length {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px 0;
            color: #6B7280;
            font-size: 13px;
        }
        .dataTables_wrapper .dataTables_length label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1px solid #D1D5DB;
            border-radius: 6px;
            padding: 4px 24px 4px 8px;
            outline: none;
            color: #374151;
            font-size: 13px;
            cursor: pointer;
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
            -webkit-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239CA3AF' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 6px center;
            background-size: 14px;
        }
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: #F05A1A;
            box-shadow: 0 0 0 3px rgba(240,90,26,.12);
        }

        /* â”€â”€â”€ Garis pemisah antara header dan tbody DataTables â”€â”€â”€ */
        table.dataTable thead th,
        table.dataTable thead td {
            border-bottom: 1px solid #E5E7EB !important;
        }
        table.dataTable.no-footer {
            border-bottom: none;
        }

        /* â”€â”€â”€ Bottom bar: info + pagination â”€â”€â”€ */
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            padding: 12px 20px;
        }
        .dataTables_wrapper .dataTables_info {
            color: #9CA3AF;
            font-size: 12px;
            border-top: 1px solid #F3F4F6;
        }
        .dataTables_wrapper .dataTables_paginate {
            text-align: right;
            border-top: 1px solid #F3F4F6;
        }
        /* Agar info dan paginate sejajar di satu baris */
        .dataTables_wrapper > .dataTables_info,
        .dataTables_wrapper > .dataTables_paginate {
            display: inline-block;
            width: auto;
        }

        /* â”€â”€â”€ Tombol-tombol halaman â”€â”€â”€ */
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            display: inline-block;
            padding: 5px 11px;
            margin: 0 2px;
            border-radius: 6px;
            border: 1px solid transparent;
            cursor: pointer;
            font-size: 13px;
            color: #4B5563 !important;
            text-decoration: none;
            transition: background .15s, border-color .15s, color .15s;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #F05A1A !important;
            color: #fff !important;
            border-color: #F05A1A !important;
            font-weight: 600;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current):not(.disabled) {
            background: #FFF3EE !important;
            color: #F05A1A !important;
            border-color: #FCD9C5 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover {
            color: #D1D5DB !important;
            cursor: default;
        }

        /* â”€â”€â”€ Custom DOM layout: hanya paginate + info di bawah â”€â”€â”€ */
        .dt-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0;
            border-top: 1px solid #F3F4F6;
        }
        .dt-bottom .dataTables_info {
            border-top: none !important;
            padding: 10px 20px !important;
        }
        .dt-bottom .dataTables_paginate {
            border-top: none !important;
            padding: 10px 20px !important;
            text-align: right;
        }

        /* â”€â”€â”€ Dropdown tampilkan di dalam toolbar â”€â”€â”€ */
        .dt-length-inline {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #6B7280;
            font-size: 13px;
        }
        .dt-length-inline select {
            border: 1px solid #D1D5DB;
            border-radius: 6px;
            padding: 4px 24px 4px 8px;
            outline: none;
            color: #374151;
            font-size: 13px;
            cursor: pointer;
            background-color: #fff;
            transition: border-color .2s, box-shadow .2s;
            height: 32px;
            -webkit-appearance: none;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%239CA3AF' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 6px center;
            background-size: 14px;
        }
        .dt-length-inline select:focus {
            border-color: #F05A1A;
            box-shadow: 0 0 0 3px rgba(240,90,26,.12);
        }

        /* â”€â”€â”€ Global Print Styles untuk DataTables â”€â”€â”€ */
        @media print {
            .dt-length-inline,
            .dt-bottom,
            .dataTables_wrapper .dataTables_info,
            .dataTables_wrapper .dataTables_paginate,
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .pagination-wrapper,
            .page-footer {
                display: none !important;
            }

            .data-table {
                width: 100% !important;
                table-layout: auto !important;
            }
            .data-table th, .data-table td {
                padding: 6px 8px !important;
                white-space: normal !important;
                word-wrap: break-word !important;
                font-size: 11px !important;
            }
        }

        /* â”€â”€â”€ Sembunyikan static flash-alert lama (Diganti dengan SweetAlert Toast) â”€â”€â”€ */
        .flash-alert {
            display: none !important;
        }
    </style>

    <?= $this->renderSection('head') ?>
</head>
<body>
<div class="app-shell">
    
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" onclick="document.body.classList.remove('sidebar-open')"></div>

    <!-- Sidebar -->
    <?= view('templates/sidebar') ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Mobile Header -->
        <header class="mobile-header">
            <div style="display:flex; align-items:center; gap:10px;">
                <img src="<?= base_url('assets/img/ftik-logo.png') ?>" alt="Logo" class="brand-logo" />
                <span style="font-weight:700; color:var(--primary);">SIn-FTIK</span>
            </div>
            <button class="mobile-toggle" onclick="document.body.classList.toggle('sidebar-open')">
                <i class="ri-menu-line"></i>
            </button>
        </header>

        <div class="page-content">
            <?= $this->renderSection('content') ?>
        </div>

        <footer class="page-footer">
            &copy; <?= date('Y') ?> Inventaris FTIK USM. All Rights Reserved
        </footer>
    </main>

</div><!-- .app-shell -->

<!-- ============================================================
     Modal: Tambah Barang Baru
============================================================ -->
<div class="modal-overlay" id="modalTambahBarang">
    <div class="modal-box modal-lg">
        <div class="modal-header">
            <div>
                <div class="modal-title">Registrasi Aset Inventaris Baru</div>
            </div>
            <button class="modal-close" onclick="closeModal('modalTambahBarang'); resetFotoPreview()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <form action="<?= base_url('barang/simpan') ?>" method="post" id="formTambahBarang" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom: 24px;">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="nama_barang">Nama Barang</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang"
                               placeholder="Contoh: PC Tower Dell Optiplex" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="kode_barang">Kode Barang</label>
                        <input type="text" class="form-control" id="kode_barang" name="kode_barang"
                               placeholder="Otomatis (Pilih Jenis)" readonly style="background-color: #F3F4F6;" />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="kategori">Kategori</label>
                        <select class="form-control" id="kategori" name="kategori" required>
                            <option value="" data-keterangan="">Pilih Kategori</option>
                            <?php
                            // Query hanya untuk admin — tidak dijalankan untuk role lain
                            $kategoriUtamaList = (session()->get('user_role') === 'admin')
                                ? model('App\Models\KategoriUtamaModel')->findAll()
                                : [];
                            ?>
                            <?php foreach ($kategoriUtamaList as $ku): ?>
                                <option value="<?= esc($ku['nama_kategori']) ?>" data-keterangan="<?= esc($ku['keterangan'] ?? '') ?>"><?= esc($ku['nama_kategori']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="id_kategori">Jenis Barang</label>
                        <select class="form-control" id="id_kategori" name="id_kategori" required onchange="generateKodeBarang(this)">
                            <option value="" data-kode="" data-keterangan="">Pilih Jenis Barang</option>
                            <?php
                            // Query hanya untuk admin
                            $katList = (session()->get('user_role') === 'admin')
                                ? model('App\Models\KategoriModel')->findAll()
                                : [];
                            ?>
                            <?php foreach ($katList as $kat): ?>
                                <option value="<?= esc($kat['nama']) ?>" data-kode="<?= esc($kat['kode_kategori'] ?? '') ?>" data-keterangan="<?= esc($kat['keterangan'] ?? '') ?>">
                                    <?= esc($kat['nama']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label class="form-label" for="merk">Merk/Brand</label>
                        <input type="text" class="form-control" id="merk" name="merk"
                               placeholder="Contoh: Dell / Xiaomi" />
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="stok_awal">Jumlah Stok Awal</label>
                        <input type="number" class="form-control" id="stok_awal" name="stok_awal"
                               placeholder="0" min="0" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="tanggal_perolehan">Tanggal Perolehan</label>
                        <input type="date" class="form-control" id="tanggal_perolehan" name="tanggal_perolehan" />
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <label class="form-label" for="spesifikasi">Spesifikasi <span style="font-size:11px;color:#9CA3AF;font-weight:normal;">(opsional — boleh dikosongkan)</span></label>
                        <textarea class="form-control" id="spesifikasi" name="spesifikasi" rows="3" placeholder="Contoh: Intel Core i5, RAM 16GB, SSD 512GB, Layar 24 inch" style="resize: vertical;"></textarea>
                    </div>
                </div>

                <div class="form-row" style="align-items: stretch;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Foto Barang <span style="color:#EF4444">*</span></label>
                        <label for="foto_barang" id="label-foto-barang"
                               style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 110px; border: 2px dashed var(--gray-300); border-radius: 8px; cursor: pointer; color: var(--gray-500); font-size: 13px; transition: var(--transition); overflow: hidden; position: relative;"
                               onmouseover="this.style.borderColor='var(--primary)'; this.style.color='var(--primary)'"
                               onmouseout="this.style.borderColor='var(--gray-300)'; this.style.color='var(--gray-500)'">
                            <!-- Default state (no file) -->
                            <div id="foto-placeholder" style="display: flex; flex-direction: column; align-items: center; pointer-events: none;">
                                <i class="ri-image-add-line" style="font-size: 24px; margin-bottom: 8px;"></i>
                                <span>Foto Barang</span>
                            </div>
                            <!-- Preview state (after file selected) -->
                            <div id="foto-preview-wrap" style="display: none; flex-direction: column; align-items: center; gap: 6px; pointer-events: none; width: 100%; padding: 8px; box-sizing: border-box;">
                                <img id="foto-preview-img" src="" alt="Preview" style="height: 62px; width: auto; max-width: 100%; border-radius: 6px; object-fit: cover; border: 1px solid #E5E7EB;" />
                                <span id="foto-preview-name" style="font-size: 11px; color: #374151; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 90%; text-align: center;"></span>
                            </div>
                        </label>
                        <div id="foto-barang-error" style="display:none; color:#EF4444; font-size:12px; margin-top:4px;"><i class="ri-error-warning-line"></i> Foto barang wajib diupload.</div>
                        <input type="file" id="foto_barang" name="foto_barang" style="display: none;" accept="image/*"
                               onchange="previewFotoBarang(this)" required />
                    </div>
                    <div class="form-group" style="margin-bottom: 0; display: flex; flex-direction: column; justify-content: space-between;">

                        <button type="submit" class="btn btn-primary" id="btnSimpanBarang" style="width: 100%; height: 42px; justify-content: center; font-size: 14px; margin-top: auto; border-radius: 6px; box-shadow: 0 4px 12px rgba(240, 90, 26, 0.25);">
                            <i class="ri-save-line" id="iconSimpanBarang"></i>
                            <span id="labelSimpanBarang">Simpan Barang</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================
     Modal: Kelola Barang (4 pilihan aksi)
============================================================ -->
<div class="modal-overlay" id="modalKelola">
    <div class="modal-box" style="max-width: 400px;">
        <div class="modal-header" style="border-bottom: none; padding-bottom: 10px;">
            <div>
                <div class="modal-title" style="font-weight: 700; color: #374151;">
                    <span style="color: #F05A1A;">Kelola</span> (<span id="kelola-nama-barang">—</span>)
                </div>
            </div>
            <button class="modal-close" onclick="closeModal('modalKelola')" style="background: transparent; color: #1F2937; font-size: 20px;">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="kelola-grid">
                <a href="#" class="kelola-item" id="kelola-link-distribusi">
                    <div class="kelola-icon" style="border-radius: 14px;"><i class="ri-computer-line"></i></div>
                    <span class="kelola-label">Distribusi<br/>Barang</span>
                </a>

                <a href="#" class="kelola-item" id="kelola-link-detail">
                    <div class="kelola-icon" style="border-radius: 14px;"><i class="ri-search-eye-line"></i></div>
                    <span class="kelola-label">Detail<br/>Barang</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================
     JS Global
============================================================ -->
<script>
    // Buka modal
    function openModal(id) {
        document.getElementById(id).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Tutup modal
    function closeModal(id) {
        document.getElementById(id).classList.remove('active');
        document.body.style.overflow = '';
    }

    // (Dihapus: Tutup modal saat klik overlay agar form tidak terhapus saat klik background)

    // Tombol Kelola Barang
    function bukaKelola(id, nama) {
        document.getElementById('kelola-nama-barang').textContent = nama;

        var base = '<?= base_url() ?>';
        document.getElementById('kelola-link-distribusi').href = base + 'barang/' + id + '/distribusi';
        document.getElementById('kelola-link-detail').href     = base + 'barang/' + id + '/detail';

        openModal('modalKelola');
    }

    // Highlight menu aktif
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-dismiss flash messages
        var alerts = document.querySelectorAll('.flash-alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function() { alert.remove(); }, 300);
            }, 2500);
        });

        // Dynamic category dropdown text (Long in dropdown, short when selected)
        const catSelects = document.querySelectorAll('select[name="id_kategori"], select[name="kategori"]');
        catSelects.forEach(select => {
            const longTexts = {};
            Array.from(select.options).forEach(opt => {
                let text = opt.value;
                let keterangan = opt.getAttribute('data-keterangan');
                if (keterangan && keterangan.trim() !== '') {
                    longTexts[opt.value] = text + ' (' + keterangan + ')';
                }
            });
            
            const setLongText = () => {
                Array.from(select.options).forEach(opt => {
                    if (longTexts[opt.value]) opt.text = longTexts[opt.value];
                });
            };
            
            const setShortText = () => {
                Array.from(select.options).forEach(opt => {
                    if (longTexts[opt.value]) {
                        opt.text = (opt.selected) ? opt.value : longTexts[opt.value];
                    }
                });
            };

            setShortText(); // init on load
            select.addEventListener('mousedown', setLongText);
            select.addEventListener('focus', setLongText);
            select.addEventListener('touchstart', setLongText);
            select.addEventListener('change', setShortText);
            select.addEventListener('blur', setShortText);
        });
    });

    // Auto-generate Kode Barang berdasarkan kategori
    function generateKodeBarang(select) {
        var opt = select.options[select.selectedIndex];
        var kodePrefix = opt.getAttribute('data-kode');
        var inputKode = document.getElementById('kode_barang');
        
        if (!kodePrefix) {
            inputKode.value = '';
            inputKode.placeholder = 'Otomatis (Pilih Jenis)';
            return;
        }

        inputKode.value = 'Loading...';
        
        fetch('<?= base_url('barang/generate-kode') ?>?prefix=' + encodeURIComponent(kodePrefix), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.kode) {
                inputKode.value = data.kode;
            } else {
                inputKode.value = kodePrefix + '-001';
            }
        })
        .catch(err => {
            console.error('Error generating kode:', err);
            inputKode.value = kodePrefix + '-001';
        });
    }

    // Preview foto barang after file selected
    function previewFotoBarang(input) {
        var placeholder   = document.getElementById('foto-placeholder');
        var previewWrap   = document.getElementById('foto-preview-wrap');
        var previewImg    = document.getElementById('foto-preview-img');
        var previewName   = document.getElementById('foto-preview-name');
        var label         = document.getElementById('label-foto-barang');
        var errorMsg      = document.getElementById('foto-barang-error');

        if (input.files && input.files[0]) {
            var file   = input.files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src       = e.target.result;
                previewName.textContent = file.name;
                placeholder.style.display  = 'none';
                previewWrap.style.display  = 'flex';
                // Turn border green to confirm success
                label.style.borderColor  = '#22C55E';
                label.style.background   = '#F0FDF4';
                label.style.color        = '#16A34A';
                label.onmouseover = null;
                label.onmouseout  = null;
                // Hide error
                if (errorMsg) errorMsg.style.display = 'none';
            };
            reader.readAsDataURL(file);
        } else {
            // Reset if cancelled
            placeholder.style.display  = 'flex';
            previewWrap.style.display  = 'none';
            label.style.borderColor    = '';
            label.style.background     = '';
        }
    }

    // Validasi form Tambah Barang: foto wajib + loading state
    document.addEventListener('DOMContentLoaded', function() {
        var formTambah = document.getElementById('formTambahBarang');
        if (formTambah) {
            formTambah.addEventListener('submit', function(e) {
                var fotoInput = document.getElementById('foto_barang');
                var label     = document.getElementById('label-foto-barang');
                var errorMsg  = document.getElementById('foto-barang-error');
                if (!fotoInput || !fotoInput.files || fotoInput.files.length === 0) {
                    e.preventDefault();
                    label.style.borderColor = '#EF4444';
                    label.style.background  = '#FEF2F2';
                    label.style.color       = '#EF4444';
                    label.onmouseover = null;
                    label.onmouseout  = null;
                    if (errorMsg) errorMsg.style.display = 'block';
                    label.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }
                // Loading state — cegah double submit
                var btn   = document.getElementById('btnSimpanBarang');
                var icon  = document.getElementById('iconSimpanBarang');
                var label2 = document.getElementById('labelSimpanBarang');
                if (btn) {
                    btn.disabled = true;
                    if (icon)  icon.className  = 'ri-loader-4-line';
                    if (label2) label2.textContent = 'Menyimpan...';
                }
            });
        }
    });

    // Reset foto preview when modal registrasi is closed
    function resetFotoPreview() {
        var input       = document.getElementById('foto_barang');
        var placeholder = document.getElementById('foto-placeholder');
        var previewWrap = document.getElementById('foto-preview-wrap');
        var label       = document.getElementById('label-foto-barang');
        if (input) input.value = '';
        if (placeholder) placeholder.style.display = 'flex';
        if (previewWrap) previewWrap.style.display  = 'none';
        if (label) {
            label.style.borderColor = '';
            label.style.background  = '';
            label.onmouseover = function() { this.style.borderColor='var(--primary)'; this.style.color='var(--primary)'; };
            label.onmouseout  = function() { this.style.borderColor='var(--gray-300)'; this.style.color='var(--gray-500)'; };
        }
    }
</script>

<style>
/* â”€â”€ CUSTOM SWEETALERT2 STYLING â”€â”€ */
.my-swal-popup {
    width: 380px !important;
    border-radius: 16px !important;
    padding: 0 !important;
    font-family: inherit !important;
    overflow: hidden;
}
.my-swal-html {
    padding: 32px 24px 24px !important;
    margin: 0 !important;
}
.my-swal-actions {
    display: flex !important;
    gap: 12px !important;
    width: calc(100% - 48px) !important;
    margin: 0 24px 24px !important;
}
.my-swal-confirm-btn {
    flex: 1;
    border: none;
    padding: 10px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: 0.15s;
}
.my-swal-confirm-btn:hover { filter: brightness(0.9); }
.my-swal-cancel-btn {
    flex: 1;
    background: #F3F4F6;
    color: #6B7280;
    border: none;
    padding: 10px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
    transition: 0.15s;
}
.my-swal-cancel-btn:hover { background: #E5E7EB; }
</style>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // â”€â”€ GLOBAL INTERCEPTOR UNTUK popup confirm() bawaan browser â”€â”€
    function showSweetAlertConfirm(message, onConfirm, onCancel) {
        // Cek apakah ini aksi destruktif (hapus)
        var isHapus = message.toLowerCase().includes('hapus') || message.toLowerCase().includes('permanen');
        
        var confirmColor = isHapus ? '#EF4444' : '#F05A1A';
        var confirmIcon  = isHapus ? '<i class="ri-delete-bin-line"></i>' : '<i class="ri-check-line"></i>';
        var confirmText  = isHapus ? 'Ya, Hapus' : 'Ya, Lanjutkan';
        
        var iconBg       = isHapus ? '#FEE2E2' : '#FEF3C7';
        var iconColor    = isHapus ? '#DC2626' : '#D97706';
        var topIcon      = isHapus ? 'ri-delete-bin-line' : 'ri-error-warning-line';
        var titleText    = isHapus ? 'Hapus Data?' : 'Konfirmasi Aksi';

        var customHtml = `
            <div style="text-align:center;">
                <div style="width:64px;height:64px;border-radius:50%;background:${iconBg};color:${iconColor};display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 16px;">
                    <i class="${topIcon}"></i>
                </div>
                <div style="font-size:17px;font-weight:700;color:#1F2937;margin-bottom:8px;">${titleText}</div>
                <p style="font-size:13px;color:#6B7280;line-height:1.6;margin:0;">${message}</p>
            </div>
        `;
        
        Swal.fire({
            html: customHtml,
            showCancelButton: true,
            showCloseButton: true,
            buttonsStyling: false, 
            confirmButtonText: confirmIcon + ' ' + confirmText,
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'my-swal-popup',
                htmlContainer: 'my-swal-html',
                actions: 'my-swal-actions',
                confirmButton: 'my-swal-confirm-btn',
                cancelButton: 'my-swal-cancel-btn'
            },
            didOpen: () => {
                // Terapkan warna dinamis untuk tombol konfirmasi
                const confirmBtn = Swal.getConfirmButton();
                if (confirmBtn) {
                    confirmBtn.style.backgroundColor = confirmColor;
                    confirmBtn.style.color = 'white';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (onConfirm) onConfirm();
            } else {
                if (onCancel) onCancel();
            }
        });
    }

    // Intercept onclick — fix race condition dengan flag swalOpen
    var _swalOpen = false;
    document.addEventListener('click', function(e) {
        if (_swalOpen) return;
        var target = e.target.closest('[onclick*="return confirm"]');
        if (target) {
            e.preventDefault();
            e.stopPropagation();
            var attr = target.getAttribute('onclick');
            var match = attr.match(/confirm\(\s*['"](.*?)['"]\s*\)/);
            var msg = match ? match[1] : 'Apakah Anda yakin?';

            _swalOpen = true;
            target.removeAttribute('onclick'); // prevent loop
            showSweetAlertConfirm(msg, function() {
                _swalOpen = false;
                if (target.tagName.toLowerCase() === 'a') {
                    window.location.href = target.href;
                } else if (target.tagName.toLowerCase() === 'button' && target.type === 'submit') {
                    var form = target.closest('form');
                    if (form) form.submit();
                } else {
                    target.click();
                }
            }, function() {
                _swalOpen = false;
                target.setAttribute('onclick', attr);
            });
        }
    }, true);

    // Intercept onsubmit
    document.addEventListener('submit', function(e) {
        var form = e.target;
        var attr = form.getAttribute('onsubmit');
        if (attr && attr.includes('return confirm')) {
            e.preventDefault();
            var match = attr.match(/confirm\(\s*['"](.*?)['"]\s*\)/);
            var msg = match ? match[1] : 'Apakah Anda yakin?';
            
            form.removeAttribute('onsubmit');
            showSweetAlertConfirm(msg, function() {
                form.submit();
            }, function() {
                form.setAttribute('onsubmit', attr);
            });
        }
    }, true);
</script>

<!-- jQuery & DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    var globalDataTables = [];
    $(document).ready(function() {
        $('.data-table, .table').each(function() {
            var $table = $(this);
            if ($.fn.DataTable.isDataTable($table)) return;

            var dt = $table.DataTable({
                searching  : true, // Harus true agar filter JS custom dan search API berfungsi
                ordering   : false,  // Matikan semua ikon sort
                pageLength : 10,
                lengthMenu : [[10, 20, 50, 100, -1], [10, 20, 50, 100, 'Semua']],
                dom        : 't<"dt-bottom print-hide no-print"ip>',
                language: {
                    emptyTable: '<div style="padding: 60px 0; text-align: center;"><div style="width: 80px; height: 80px; background: #F3F4F6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #9CA3AF; font-size: 40px;"><i class="ri-inbox-archive-line"></i></div><h4 style="font-size: 18px; font-weight: 700; color: #374151; margin: 0 0 8px;">Belum Ada Data</h4><p style="color: #6B7280; font-size: 14px; margin: 0; max-width: 300px; margin-left: auto; margin-right: auto;">Data tidak ditemukan atau masih kosong. Silakan tambahkan data baru.</p></div>',
                    zeroRecords: '<div style="padding: 60px 0; text-align: center;"><div style="width: 80px; height: 80px; background: #F3F4F6; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #9CA3AF; font-size: 40px;"><i class="ri-search-2-line"></i></div><h4 style="font-size: 18px; font-weight: 700; color: #374151; margin: 0 0 8px;">Tidak Ada Hasil</h4><p style="color: #6B7280; font-size: 14px; margin: 0; max-width: 300px; margin-left: auto; margin-right: auto;">Pencarian Anda tidak membuahkan hasil. Coba gunakan kata kunci lain.</p></div>',
                    info       : 'Menampilkan _START_\u2013_END_ dari _TOTAL_ data',
                    infoEmpty  : 'Tidak ada data tersedia',
                    paginate: {
                        previous: '\u2039 Sebelumnya',
                        next    : 'Selanjutnya \u203a'
                    }
                },
                drawCallback: function(settings) {
                    var api = this.api();
                    if (api.rows({page:'current'}).data().length === 0) {
                        $(api.table().container()).find('.dt-bottom').hide();
                    } else {
                        $(api.table().container()).find('.dt-bottom').show();
                    }
                }
            });
            globalDataTables.push(dt);

            // Buat dropdown "Tampilkan X data" sederhana
            var $sel = $('<select aria-label="Tampilkan"></select>');
            var vals = [10, 20, 50, 100, -1];
            var labels = [10, 20, 50, 100, 'Semua'];
            $.each(vals, function(i, v) {
                $sel.append($('<option>').val(v).text(labels[i]));
            });
            $sel.val(dt.page.len());
            $sel.on('change', function() { dt.page.len(parseInt($(this).val())).draw(); });

            var $lbl = $('<div class="dt-length-inline print-hide no-print"></div>')
                .append($('<span class="print-hide no-print">Tampilkan</span>'))
                .append($sel)
                .append($('<span class="print-hide no-print">data</span>'));

            // Cari toolbar-left dan inject ke sana (sejajar dengan search box)
            var $wrapper     = $table.closest('.data-table-wrapper, .table-section, .table-responsive').parent();
            var $toolbarLeft = $wrapper.find('.table-toolbar-left').first();

            if ($toolbarLeft.length) {
                $toolbarLeft.append($lbl);
            } else {
                var target = $table.closest('.data-table-wrapper');
                if (target.length === 0) {
                    target = $table.closest('.table-responsive');
                }
                if (target.length === 0) {
                    target = $table;
                }
                target.before($('<div style="display:flex; justify-content:space-between; margin-bottom:12px; margin-top:12px;"></div>').append($lbl));
            }
        });
    });

    // Handle cetak laporan agar semua baris DataTables tampil
    var oldDtLengths = [];
    window.addEventListener('beforeprint', function() {
        oldDtLengths = [];
        globalDataTables.forEach(function(dt, index) {
            oldDtLengths[index] = dt.page.len();
            dt.page.len(-1).draw(false);
        });
    });
    window.addEventListener('afterprint', function() {
        globalDataTables.forEach(function(dt, index) {
            if (oldDtLengths[index] !== undefined) {
                dt.page.len(oldDtLengths[index]).draw(false);
            }
        });
    });

    // â”€â”€ GLOBAL SWEETALERT2 TOAST UNTUK FLASH MESSAGES â”€â”€
    document.addEventListener('DOMContentLoaded', function () {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        <?php if (session()->getFlashdata('success')): ?>
        Toast.fire({
            icon: 'success',
            title: <?= json_encode(session()->getFlashdata('success')) ?>
        });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
        Toast.fire({
            icon: 'error',
            title: <?= json_encode(session()->getFlashdata('error')) ?>
        });
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
        <?php $flashErrors = session()->getFlashdata('errors'); ?>
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            html: <?= json_encode('<ul style="text-align:left;margin:0;padding-left:18px">' . implode('', array_map(fn($e) => '<li>' . esc($e) . '</li>', (array)$flashErrors)) . '</ul>') ?>,
            confirmButtonColor: '#F05A1A'
        });
        <?php endif; ?>
    });
</script>

<?= $this->renderSection('scripts') ?>
</body>
</html>
