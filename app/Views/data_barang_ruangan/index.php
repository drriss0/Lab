<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success flash-alert" style="margin-bottom:16px;">
        <i class="ri-checkbox-circle-line"></i> <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger flash-alert" style="margin-bottom:16px; background-color: #FEF2F2; color: #EF4444; border: 1px solid #FECACA; padding: 12px 16px; border-radius: 8px; display: flex; align-items: center; gap: 8px; font-weight: 500; font-size: 14px;">
        <i class="ri-error-warning-line" style="font-size: 18px;"></i> <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- ============================================================
     Hero & Tambah Data
============================================================ -->
<div class="hero-section">
    <!-- Welcome Banner -->
    <div class="hero-banner">
        <img src="<?= base_url('image/header-dataruangan.png') ?>" alt="Avatar" class="hero-banner-img" onerror="this.style.display='none'" />
        <div class="hero-banner-text">
            <h1><span style="color: #F05A1A;">Data</span> Barang Ruangan FTIK</h1>
            <p>Alokasikan barang dari gudang untuk penggunaan operasional di lingkungan FTIK.</p>
        </div>
        
        <!-- Right side Stats in Hero -->
        <div class="hero-stat-badge">
            <div class="hero-stat-number"><?= count($ruangan) ?></div>
            <div class="hero-stat-label">Data Lab/Kelas<br/>Terdaftar</div>
        </div>
    </div>

    <!-- Add Item Card — hanya admin & koordinator_lab -->
    <?php if (can('admin', 'koordinator_lab')): ?>
    <div class="hero-action-card">
        <i class="ri-folder-add-fill" style="position: absolute; top: -10px; right: -10px; font-size: 100px; color: rgba(255,255,255,0.15); transform: rotate(-10deg);"></i>
        <h3 class="hero-action-title">Pendataan<br/>Kelas/Lab FTIK</h3>
        <button onclick="openModal('modalTambahRuangan')" class="hero-action-btn">
            <i class="ri-folder-add-line"></i> Tambah Data
        </button>
    </div>
    <?php endif; ?>
</div>

<!-- ============================================================
     Toolbar
============================================================ -->
<?php
    $statusTabs = [
        '' => ['label' => 'Semua', 'clr' => '#6B7280'],
        'Bisa Digunakan' => ['label' => 'Bisa Digunakan', 'clr' => '#22C55E'],
        'Maintenance' => ['label' => 'Maintenance', 'clr' => '#EAB308'],
        'Tidak Bisa Digunakan' => ['label' => 'Tidak Bisa Digunakan', 'clr' => '#EF4444'],
    ];
?>
<div class="page-toolbar">
    <div class="page-toolbar-left">
        <h2 style="font-size: 18px; font-weight: 700; color: #1F2937; margin: 0; white-space: nowrap;">Tabel Kelas/Lab</h2>
        <form method="GET" action="<?= base_url('data-barang-ruangan') ?>" id="formCari" style="margin: 0; flex: 1; min-width: 0;">
            <?php if ($filter_status ?? null): ?>
                <input type="hidden" name="status" value="<?= esc($filter_status) ?>" />
            <?php endif; ?>
            <div class="page-search-box">
                <i class="ri-search-line" style="color: #F05A1A; flex-shrink: 0;"></i>
                <input type="text" name="q" id="cariRuangan" placeholder="Cari nama ruangan..." value="<?= esc($keyword ?? '') ?>" autocomplete="off" />
            </div>
        </form>
    </div>
    <div class="page-toolbar-right">
        <?php foreach ($statusTabs as $val => $t):
            $active = ($filter_status ?? '') === $val;
            $href   = base_url('data-barang-ruangan') . '?' . http_build_query(array_filter(['q' => $keyword ?? '', 'status' => $val]));
        ?>
        <a href="<?= $href ?>"
           style="padding:6px 14px;border-radius:20px;font-size:12px;font-weight:700;text-decoration:none;
                  background:<?= $active ? $t['clr'] : 'white' ?>;
                  color:<?= $active ? 'white' : $t['clr'] ?>;
                  border:1px solid <?= $active ? $t['clr'] : '#E5E7EB' ?>;transition:.15s;white-space:nowrap;">
            <?= $t['label'] ?>
        </a>
        <?php endforeach; ?>

        <?php if (($keyword ?? '') || ($filter_status ?? '')): ?>
        <a href="<?= base_url('data-barang-ruangan') ?>" style="color:#EF4444;font-size:12px;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:4px;padding:6px 14px;border-radius:20px;background:#FEF2F2;border:1px solid #FECACA;white-space:nowrap;">
            <i class="ri-close-circle-line"></i> Reset
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- ============================================================
     Grid Ruangan
============================================================ -->
<div class="ruangan-grid">
    <?php if (empty($ruangan)): ?>
        <div class="empty-state" style="grid-column:1/-1;">
            <div class="empty-icon">🏢</div>
            <h4>Belum Ada Data Ruangan</h4>
            <p>Data ruangan belum tersedia.</p>
        </div>
    <?php else: ?>
        <?php 
        $db = \Config\Database::connect();
        foreach ($ruangan as $r): 
            // Ambil 3 barang terbanyak di ruangan ini secara dinamis
            $topItems = $db->table('distribusi d')
                ->join('barang b', 'b.id=d.id_barang')
                ->select('b.nama_barang, SUM(d.jumlah) as total')
                ->where('d.id_ruangan', $r['id'])
                ->where('d.status', 'digunakan')
                ->groupBy('d.id_barang')
                ->orderBy('total', 'DESC')
                ->limit(3)
                ->get()->getResultArray();
        ?>
            <div style="background: white; border-radius: 16px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); display: flex; flex-direction: column;">
            <!-- Top Header -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px;">
                <div style="display: flex; gap: 12px; align-items: center;">
                    <?php if (!empty($r['foto_ruangan']) && file_exists(FCPATH . 'uploads/ruangan/' . $r['foto_ruangan'])): ?>
                    <div style="width: 56px; height: 56px; border-radius: 12px; overflow: hidden; border: 1px solid #F3F4F6; flex-shrink: 0;">
                        <img src="<?= base_url('uploads/ruangan/' . $r['foto_ruangan']) ?>" alt="<?= esc($r['nama_ruangan']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <?php else: ?>
                    <div style="width: 56px; height: 56px; background: #FFF3EE; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #F05A1A; font-size: 28px; flex-shrink: 0;">
                        <i class="ri-door-line"></i>
                    </div>
                    <?php endif; ?>
                    <div style="font-size: 18px; font-weight: 800; color: #F05A1A; text-transform: uppercase;">
                        <?= esc($r['nama_ruangan']) ?>
                    </div>
                </div>
                <?php if (can('admin', 'koordinator_lab')): ?>
                <div style="display: flex; gap: 8px;">
                    <button onclick="openModal('modalEditRuangan_<?= $r['id'] ?>')" style="width: 32px; height: 32px; background: #3B82F6; color: white; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Edit Ruangan">
                        <i class="ri-pencil-fill" style="font-size: 15px;"></i>
                    </button>
                    <?php if ($r['total_barang'] == 0): ?>
                    <form method="post" action="<?= base_url('data-barang-ruangan/' . encode_id($r['id']) . '/hapus') ?>" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus ruangan <?= htmlspecialchars(addslashes($r['nama_ruangan'])) ?>? Pastikan ruangan ini kosong dari barang.')">
                        <?= csrf_field() ?>
                        <button type="submit" style="width: 32px; height: 32px; background: #EF4444; color: white; border-radius: 8px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;" title="Hapus Ruangan">
                            <i class="ri-delete-bin-line" style="font-size: 15px;"></i>
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <div style="background: #F3F4F6; border-radius: 8px; padding: 10px 14px; display: flex; flex-direction: column; gap: 8px; margin-bottom: 18px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="ri-account-box-line" style="color: #F05A1A; font-size: 15px;"></i>
                    <span style="font-size: 13px; color: #6B7280; font-weight: 500;">
                        <?= esc($r['penanggung_jawab'] ?? 'Belum ada penanggung jawab') ?>
                    </span>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="ri-group-line" style="color: #F05A1A; font-size: 15px;"></i>
                    <span style="font-size: 13px; color: #6B7280; font-weight: 500;">
                        Kapasitas: <?= (int)($r['kapasitas_meja'] ?? 0) ?> Meja / <?= (int)($r['kapasitas_kursi'] ?? 0) ?> Kursi
                    </span>
                </div>
            </div>

            <!-- List of items -->
            <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 18px; flex: 1; min-height: 100px;">
                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px; border-bottom: 1px dashed #E5E7EB; padding-bottom: 8px; margin-bottom: 4px;">
                    <div style="display: flex; align-items: center; gap: 8px; color: #1F2937; font-weight: 700;">
                        <i class="ri-archive-line" style="color: #F05A1A;"></i> Total Keseluruhan Aset:
                    </div>
                    <span style="color: #F05A1A; font-weight: 800; font-size: 14px;"><?= $r['total_barang'] ?> pcs</span>
                </div>
                
                <?php if (empty($topItems)): ?>
                    <div style="font-size: 12px; color: #9CA3AF; text-align: center; margin-top: 10px; font-style: italic;">
                        Belum ada barang di ruangan ini
                    </div>
                <?php else: ?>
                    <?php foreach($topItems as $item): ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                        <div style="display: flex; align-items: center; gap: 6px; color: #4B5563; font-weight: 500;">
                            <i class="ri-arrow-right-s-line" style="color: #9CA3AF;"></i> 
                            <?= esc(mb_strimwidth($item['nama_barang'], 0, 20, '...')) ?>
                        </div>
                        <span style="color: #374151; font-weight: 600;"><?= $item['total'] ?> pcs</span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <a href="<?= base_url('data-barang-ruangan/' . encode_id($r['id'])) ?>" style="color: #F05A1A; font-size: 13px; font-weight: 600; text-decoration: none; display: flex; align-items: center; gap: 4px; margin-bottom: 16px;">
                Lihat Detail Lengkap <i class="ri-arrow-right-s-line"></i>
            </a>

            <div style="height: 1px; background: #F3F4F6; margin-bottom: 16px;"></div>

            <div style="font-size: 13px; font-weight: 700; color: #374151; margin-bottom: 10px;">Status LAB</div>

            <?php 
                $status = $r['status_ruangan'] ?? 'Bisa Digunakan';
                if ($status === 'Bisa Digunakan') {
                    echo '<button style="width: 100%; padding: 10px; background: #22C55E; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 13px; display: flex; justify-content: center; align-items: center; gap: 6px;"><i class="ri-check-line" style="font-size: 16px;"></i> Bisa Digunakan</button>';
                } elseif ($status === 'Maintenance') {
                    echo '<button style="width: 100%; padding: 10px; background: #EAB308; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 13px; display: flex; justify-content: center; align-items: center; gap: 6px;"><i class="ri-refresh-line" style="font-size: 16px;"></i> Maintenance</button>';
                } else {
                    echo '<button style="width: 100%; padding: 10px; background: #EF4444; color: white; border: none; border-radius: 6px; font-weight: 600; font-size: 13px; display: flex; justify-content: center; align-items: center; gap: 6px;"><i class="ri-close-line" style="font-size: 16px;"></i> Tidak Bisa Digunakan</button>';
                }
            ?>
        </div>

        <?php if (can('admin', 'koordinator_lab')): ?>
        <!-- Modal Edit Ruangan -->
        <div class="modal-overlay" id="modalEditRuangan_<?= $r['id'] ?>">
            <div class="modal-box" style="width: 700px; max-width: 95vw;">
                <div class="modal-header" style="border-bottom: none; padding-bottom: 0;">
                    <div class="modal-title" style="font-size: 18px; font-weight: 700; color: #1F2937;">Edit Kelas/Lab</div>
                    <button class="modal-close" onclick="closeModal('modalEditRuangan_<?= $r['id'] ?>')" style="color: #1F2937;"><i class="ri-close-line"></i></button>
                </div>
                <form action="<?= base_url('data-barang-ruangan/' . encode_id($r['id']) . '/edit') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="modal-body" style="padding: 20px 24px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                            
                            <!-- Kiri -->
                            <div style="display: flex; flex-direction: column; gap: 16px; height: 100%;">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" style="font-weight: 600; color: #374151;">Update Gambar Ruangan</label>
                                    
                                    <?php $hasFoto = !empty($r['foto_ruangan']) && file_exists(FCPATH . 'uploads/ruangan/' . $r['foto_ruangan']); ?>
                                    
                                    <label id="label-foto-ruangan-edit-<?= $r['id'] ?>" for="foto_ruangan_edit_<?= $r['id'] ?>" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 138px; border: 1px solid <?= $hasFoto ? '#22C55E' : '#D1D5DB' ?>; border-radius: 8px; cursor: pointer; background: <?= $hasFoto ? '#F0FDF4' : '#FFFFFF' ?>; transition: border-color 0.2s, background 0.2s;">
                                        
                                        <?php if ($hasFoto): ?>
                                            <div id="ruangan-foto-placeholder-edit-<?= $r['id'] ?>" style="display:none;flex-direction:column;align-items:center;pointer-events:none;">
                                                <i class="ri-image-add-line" style="font-size: 32px; color: #9CA3AF; margin-bottom: 8px;"></i>
                                                <span style="font-size: 13px; color: #6B7280; font-weight: 500;">Ubah Gambar</span>
                                            </div>
                                            <div id="ruangan-foto-preview-wrap-edit-<?= $r['id'] ?>" style="display:flex;flex-direction:column;align-items:center;gap:6px;pointer-events:none;width:100%;padding:8px;box-sizing:border-box;">
                                                <img id="ruangan-foto-preview-img-edit-<?= $r['id'] ?>" src="<?= base_url('uploads/ruangan/' . $r['foto_ruangan']) ?>" alt="Preview" style="height:80px;width:auto;max-width:100%;border-radius:6px;object-fit:cover;border:1px solid #E5E7EB;" />
                                                <span id="ruangan-foto-preview-name-edit-<?= $r['id'] ?>" style="font-size:11px;color:#374151;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:90%;"><?= esc($r['foto_ruangan']) ?></span>
                                            </div>
                                        <?php else: ?>
                                            <div id="ruangan-foto-placeholder-edit-<?= $r['id'] ?>" style="display:flex;flex-direction:column;align-items:center;pointer-events:none;">
                                                <i class="ri-image-add-line" style="font-size: 32px; color: #9CA3AF; margin-bottom: 8px;"></i>
                                                <span style="font-size: 13px; color: #6B7280; font-weight: 500;">Pilih Gambar</span>
                                            </div>
                                            <div id="ruangan-foto-preview-wrap-edit-<?= $r['id'] ?>" style="display:none;flex-direction:column;align-items:center;gap:6px;pointer-events:none;width:100%;padding:8px;box-sizing:border-box;">
                                                <img id="ruangan-foto-preview-img-edit-<?= $r['id'] ?>" src="" alt="Preview" style="height:80px;width:auto;max-width:100%;border-radius:6px;object-fit:cover;border:1px solid #E5E7EB;" />
                                                <span id="ruangan-foto-preview-name-edit-<?= $r['id'] ?>" style="font-size:11px;color:#374151;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:90%;"></span>
                                            </div>
                                        <?php endif; ?>
                                        <input type="file" id="foto_ruangan_edit_<?= $r['id'] ?>" name="foto_ruangan" style="display: none;" accept="image/*" onchange="previewFotoRuanganEdit(this, <?= $r['id'] ?>)">
                                    </label>
                                    <div style="font-size: 11px; color: #9CA3AF; margin-top: 4px; text-align: center;">Kosongkan jika tidak ingin mengubah foto.</div>
                                </div>
                                
                                <div style="flex: 1;"></div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label class="form-label" style="font-weight: 600; color: #374151;">Kapasitas Meja</label>
                                        <input type="number" class="form-control" name="kapasitas_meja" min="0" value="<?= (int)($r['kapasitas_meja'] ?? 0) ?>" />
                                    </div>
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label class="form-label" style="font-weight: 600; color: #374151;">Kapasitas Kursi</label>
                                        <input type="number" class="form-control" name="kapasitas_kursi" min="0" value="<?= (int)($r['kapasitas_kursi'] ?? 0) ?>" />
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Kanan -->
                            <div style="display: flex; flex-direction: column; gap: 16px; height: 100%;">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" style="font-weight: 600; color: #374151;">Nama Kelas/Lab</label>
                                    <input type="text" class="form-control" name="nama_ruangan" value="<?= esc($r['nama_ruangan']) ?>" required />
                                </div>
                                
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" style="font-weight: 600; color: #374151;">Dosen Penanggung Jawab</label>
                                    <input type="text" class="form-control" name="penanggung_jawab" value="<?= esc($r['penanggung_jawab'] ?? '') ?>" />
                                </div>
                                
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label class="form-label" style="font-weight: 600; color: #374151;">Status LAB</label>
                                    <select class="form-control" name="status_ruangan">
                                        <?php $st = $r['status_ruangan'] ?? 'Bisa Digunakan'; ?>
                                        <option value="Bisa Digunakan" <?= $st == 'Bisa Digunakan' ? 'selected' : '' ?>>Bisa Digunakan</option>
                                        <option value="Tidak Bisa Digunakan" <?= $st == 'Tidak Bisa Digunakan' ? 'selected' : '' ?>>Tidak Bisa Digunakan</option>
                                        <option value="Maintenance" <?= $st == 'Maintenance' ? 'selected' : '' ?>>Maintenance</option>
                                    </select>
                                </div>
                                
                                <div style="flex: 1;"></div>
                                
                                <button type="submit" style="width: 100%; background: #F05A1A; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer;">
                                    Simpan Perubahan
                                </button>
                            </div>
                            
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <?php endif; ?>


        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (can('admin', 'koordinator_lab')): ?>
<!-- Modal Tambah Ruangan -->
<div class="modal-overlay" id="modalTambahRuangan">
    <div class="modal-box" style="width: 700px; max-width: 95vw;">
        <div class="modal-header" style="border-bottom: none; padding-bottom: 0;">
            <div class="modal-title" style="font-size: 18px; font-weight: 700; color: #1F2937;">Form Registrasi Kelas/Lab Baru</div>
            <button class="modal-close" onclick="closeModal('modalTambahRuangan')" style="color: #1F2937;"><i class="ri-close-line"></i></button>
        </div>
        <form action="<?= base_url('data-barang-ruangan/simpan') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding: 20px 24px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    
                    <!-- Kiri -->
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Gambar Ruangan <span style="color:#EF4444">*</span></label>
                            <label id="label-foto-ruangan" for="foto_ruangan_input" style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 138px; border: 1px solid #D1D5DB; border-radius: 8px; cursor: pointer; background: #FFFFFF; transition: border-color 0.2s, background 0.2s;">
                                <div id="ruangan-foto-placeholder" style="display:flex;flex-direction:column;align-items:center;pointer-events:none;">
                                    <i class="ri-image-add-line" style="font-size: 32px; color: #9CA3AF; margin-bottom: 8px;"></i>
                                    <span style="font-size: 13px; color: #6B7280; font-weight: 500;">Gambar Ruangan</span>
                                </div>
                                <div id="ruangan-foto-preview-wrap" style="display:none;flex-direction:column;align-items:center;gap:6px;pointer-events:none;width:100%;padding:8px;box-sizing:border-box;">
                                    <img id="ruangan-foto-preview-img" src="" alt="Preview" style="height:80px;width:auto;max-width:100%;border-radius:6px;object-fit:cover;border:1px solid #E5E7EB;" />
                                    <span id="ruangan-foto-preview-name" style="font-size:11px;color:#374151;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:90%;"></span>
                                </div>
                                <input type="file" id="foto_ruangan_input" name="foto_ruangan" style="display: none;" accept="image/*" onchange="previewFotoRuangan(this)">
                            </label>
                            <div id="foto-ruangan-error" style="display:none; color:#EF4444; font-size:12px; margin-top:4px;"><i class="ri-error-warning-line"></i> Gambar ruangan wajib diupload.</div>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-weight: 600; color: #374151;">Kapasitas Meja</label>
                                <input type="number" class="form-control" name="kapasitas_meja" min="0" placeholder="0" />
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label" style="font-weight: 600; color: #374151;">Kapasitas Kursi</label>
                                <input type="number" class="form-control" name="kapasitas_kursi" min="0" placeholder="0" />
                            </div>
                        </div>

                    </div>
                    
                    <!-- Kanan -->
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Nama Kelas/Lab</label>
                            <input type="text" class="form-control" name="nama_ruangan" placeholder="Contoh: LAB M.2.4" required />
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Dosen Penanggung Jawab</label>
                            <input type="text" class="form-control" name="penanggung_jawab" placeholder="Contoh: Melani, S.Kom" />
                        </div>
                        
                        <div class="form-group" style="margin-bottom: 0;">
                            <label class="form-label" style="font-weight: 600; color: #374151;">Status Awal Ruangan</label>
                            <select class="form-control" name="status_ruangan">
                                <option value="" disabled selected hidden>Pilih Status</option>
                                <option value="Bisa Digunakan">Bisa Digunakan</option>
                                <option value="Tidak Bisa Digunakan">Tidak Bisa Digunakan</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>
                        
                        <div style="flex: 1;"></div>
                        
                        <button type="submit" style="width: 100%; background: #F05A1A; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer;">
                            Simpan Ruangan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ── Live search ───────────────────────────────────────────────────────
var searchTimeout;
document.getElementById('cariRuangan').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => this.closest('form').submit(), 500);
});

// Preview foto ruangan
function previewFotoRuangan(input) {
    var placeholder  = document.getElementById('ruangan-foto-placeholder');
    var previewWrap  = document.getElementById('ruangan-foto-preview-wrap');
    var previewImg   = document.getElementById('ruangan-foto-preview-img');
    var previewName  = document.getElementById('ruangan-foto-preview-name');
    var label        = document.getElementById('label-foto-ruangan');
    var errorMsg     = document.getElementById('foto-ruangan-error');

    if (input.files && input.files[0]) {
        var file   = input.files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewName.textContent = file.name;
            placeholder.style.display = 'none';
            previewWrap.style.display = 'flex';
            label.style.borderColor  = '#22C55E';
            label.style.background   = '#F0FDF4';
            if (errorMsg) errorMsg.style.display = 'none';
        };
        reader.readAsDataURL(file);
    } else {
        placeholder.style.display = 'flex';
        previewWrap.style.display = 'none';
        label.style.borderColor   = '';
        label.style.background    = '';
    }
}

// Preview foto ruangan untuk modal Edit
function previewFotoRuanganEdit(input, id) {
    var placeholder  = document.getElementById('ruangan-foto-placeholder-edit-' + id);
    var previewWrap  = document.getElementById('ruangan-foto-preview-wrap-edit-' + id);
    var previewImg   = document.getElementById('ruangan-foto-preview-img-edit-' + id);
    var previewName  = document.getElementById('ruangan-foto-preview-name-edit-' + id);
    var label        = document.getElementById('label-foto-ruangan-edit-' + id);

    if (input.files && input.files[0]) {
        var file   = input.files[0];
        var reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            if (previewName) {
                previewName.textContent = file.name;
            }
            if (placeholder) placeholder.style.display = 'none';
            previewWrap.style.display = 'flex';
            label.style.borderColor  = '#22C55E';
            label.style.background   = '#F0FDF4';
        };
        reader.readAsDataURL(file);
    } else {
        if (placeholder) placeholder.style.display = 'flex';
        previewWrap.style.display = 'none';
        label.style.borderColor   = '';
        label.style.background    = '';
    }
}

// Validasi form Tambah Ruangan: gambar wajib
document.addEventListener('DOMContentLoaded', function() {
    var formRuangan = document.querySelector('#modalTambahRuangan form');
    if (formRuangan) {
        formRuangan.addEventListener('submit', function(e) {
            var fotoInput = document.getElementById('foto_ruangan_input');
            var label     = document.getElementById('label-foto-ruangan');
            var errorMsg  = document.getElementById('foto-ruangan-error');
            if (!fotoInput || !fotoInput.files || fotoInput.files.length === 0) {
                e.preventDefault();
                label.style.borderColor = '#EF4444';
                label.style.background  = '#FEF2F2';
                if (errorMsg) errorMsg.style.display = 'block';
                label.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
