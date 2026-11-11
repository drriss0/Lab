<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<div style="max-width: 900px; margin: 0 auto;">

    <!-- Page Header -->
    <div style="background: white; border-radius: 16px; padding: 28px 32px; margin-bottom: 24px; border: 1px solid #E5E7EB; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 16px;">
        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #F05A1A, #ff8c5a); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <i class="ri-file-download-line" style="font-size: 24px; color: white;"></i>
        </div>
        <div>
            <h1 style="font-size: 22px; font-weight: 700; color: #1F2937; margin-bottom: 4px;">Kelola Modul Panduan</h1>
            <p style="color: #6B7280; font-size: 14px;">Upload dan kelola file modul/panduan yang dapat diunduh pengguna di halaman Help Desk.</p>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div style="background: #D1FAE5; border: 1px solid #6EE7B7; border-radius: 10px; padding: 14px 20px; margin-bottom: 20px; color: #065F46; font-size: 14px; display: flex; align-items: center; gap: 10px;">
            <i class="ri-checkbox-circle-fill" style="font-size: 18px;"></i>
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div style="background: #FEE2E2; border: 1px solid #FCA5A5; border-radius: 10px; padding: 14px 20px; margin-bottom: 20px; color: #991B1B; font-size: 14px; display: flex; align-items: center; gap: 10px;">
            <i class="ri-error-warning-fill" style="font-size: 18px;"></i>
            <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>

    <!-- Upload Form Card -->
    <div style="background: white; border-radius: 16px; padding: 28px 32px; margin-bottom: 24px; border: 1px solid #E5E7EB; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <h2 style="font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <i class="ri-upload-cloud-2-line" style="color: #F05A1A;"></i> Upload Modul Baru
        </h2>

        <form action="<?= base_url('modul/upload') ?>" method="post" enctype="multipart/form-data" id="form-upload-modul">
            <?= csrf_field() ?>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">Judul Modul <span style="color: #EF4444;">*</span></label>
                    <input type="text" name="judul" id="input-judul-modul" required placeholder="Contoh: Panduan Peminjaman Barang"
                           style="width: 100%; padding: 10px 14px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 14px; color: #374151; outline: none; box-sizing: border-box;"
                           onfocus="this.style.borderColor='#F05A1A'" onblur="this.style.borderColor='#D1D5DB'">
                </div>
                <div>
                    <label style="display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px;">File Modul (PDF/Word) <span style="color: #EF4444;">*</span></label>
                    <input type="file" name="file_modul" id="input-file-modul" required accept=".pdf,.doc,.docx"
                           style="width: 100%; padding: 9px 14px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 14px; color: #374151; outline: none; box-sizing: border-box; background: #F9FAFB;">
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" id="btn-upload-modul"
                        style="background: #F05A1A; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; transition: background 0.2s;"
                        onmouseover="this.style.background='#d94d12'" onmouseout="this.style.background='#F05A1A'">
                    <i class="ri-upload-cloud-line"></i> Upload Sekarang
                </button>
            </div>
        </form>
    </div>

    <!-- Modul List Card -->
    <div style="background: white; border-radius: 16px; padding: 28px 32px; border: 1px solid #E5E7EB; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
        <h2 style="font-size: 16px; font-weight: 700; color: #374151; margin-bottom: 20px; display: flex; align-items: center; gap: 8px;">
            <i class="ri-list-check" style="color: #F05A1A;"></i> Daftar Modul
            <span style="margin-left: auto; font-size: 13px; font-weight: 500; color: #6B7280; background: #F3F4F6; padding: 4px 10px; border-radius: 20px;">
                <?= count($modul_list) ?> file
            </span>
        </h2>

        <?php if (empty($modul_list)): ?>
            <div style="text-align: center; padding: 48px 20px; color: #9CA3AF;">
                <i class="ri-file-unknow-line" style="font-size: 48px; display: block; margin-bottom: 12px;"></i>
                <p style="font-size: 14px;">Belum ada modul yang diunggah.</p>
            </div>
        <?php else: ?>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <?php foreach ($modul_list as $modul): ?>
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 14px 18px; border: 1px solid #E5E7EB; border-radius: 10px; transition: border-color 0.2s;"
                     onmouseover="this.style.borderColor='#F05A1A'" onmouseout="this.style.borderColor='#E5E7EB'">
                    <div style="display: flex; align-items: center; gap: 14px; flex: 1; min-width: 0;">
                        <?php
                            $ext = strtolower(pathinfo($modul['file_modul'], PATHINFO_EXTENSION));
                            $iconClass = ($ext === 'pdf') ? 'ri-file-pdf-2-fill' : 'ri-file-word-fill';
                            $iconColor = ($ext === 'pdf') ? '#EF4444' : '#2563EB';
                        ?>
                        <div style="width: 40px; height: 40px; border-radius: 8px; background: <?= ($ext === 'pdf') ? '#FEE2E2' : '#DBEAFE' ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="<?= $iconClass ?>" style="font-size: 20px; color: <?= $iconColor ?>;"></i>
                        </div>
                        <div style="min-width: 0;">
                            <div style="font-size: 14px; font-weight: 600; color: #1F2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= esc($modul['judul']) ?>
                            </div>
                            <div style="font-size: 12px; color: #9CA3AF; margin-top: 2px;">
                                Diunggah: <?= date('d M Y, H:i', strtotime($modul['created_at'])) ?> &nbsp;â€¢&nbsp; <?= strtoupper($ext) ?>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0; margin-left: 16px;">
                        <a href="<?= base_url('uploads/modul/' . $modul['file_modul']) ?>" target="_blank"
                           style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; background: #F0FDF4; border: 1px solid #86EFAC; border-radius: 7px; color: #16A34A; font-size: 13px; font-weight: 600; text-decoration: none;">
                            <i class="ri-download-line"></i> Unduh
                        </a>
                        <form method="post" action="<?= base_url('modul/hapus/' . encode_id($modul['id'])) ?>" style="display:inline;" onsubmit="return confirm('Hapus modul \"<?= esc($modul['judul']) ?>\"? File akan dihapus permanen.')">
                            <?= csrf_field() ?>
                            <button type="submit" style="display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; background: #FEF2F2; border: 1px solid #FCA5A5; border-radius: 7px; color: #DC2626; font-size: 13px; font-weight: 600; cursor: pointer; border: 1px solid #FCA5A5;">
                                <i class="ri-delete-bin-line"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?= $this->endSection() ?>
