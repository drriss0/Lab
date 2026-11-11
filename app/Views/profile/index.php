<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>
<div class="form-section" style="padding-top: 12px;">
    <div style="max-width: 600px; margin: 0 auto; background: #FFFFFF; border-radius: 16px; padding: 24px; box-shadow: 0 1px 2px rgba(0,0,0,0.05), 0 4px 6px rgba(0,0,0,0.03);">
        <h2 style="font-size: 18px; color: #1F2937; margin-top: 0; margin-bottom: 16px; border-bottom: 1px solid #F3F4F6; padding-bottom: 8px;"><i class="ri-user-settings-line" style="margin-right: 8px; color: #F05A1A;"></i> Data Diri</h2>
        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger mb-4" style="background-color: #FEF2F2; color: #EF4444; padding: 12px 16px; border-radius: 8px; border-left: 4px solid #EF4444; font-size: 14px; margin-bottom: 24px;">
                <i class="ri-error-warning-line" style="margin-right: 8px;"></i> <?= esc(session()->getFlashdata('error')) ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success mb-4" style="background-color: #F0FDF4; color: #16A34A; padding: 12px 16px; border-radius: 8px; border-left: 4px solid #22C55E; font-size: 14px; margin-bottom: 24px;">
                <i class="ri-checkbox-circle-line" style="margin-right: 8px;"></i> <?= esc(session()->getFlashdata('success')) ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('profile/simpan') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="form-group mb-4" style="margin-bottom: 12px;">
                <label class="form-label" for="nama" style="display: block; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 13px;">Nama Lengkap <span style="color:#EF4444">*</span></label>
                <div style="position: relative;">
                    <i class="ri-user-line" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9CA3AF; font-size: 18px;"></i>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?= esc($user['nama']) ?>" required style="width: 100%; padding: 12px 40px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: border-color 0.2s; outline: none;" onfocus="this.style.borderColor='#F05A1A'" onblur="this.style.borderColor='#D1D5DB'" placeholder="Masukkan nama lengkap" />
                </div>
            </div>

            <div class="form-group mb-4" style="margin-bottom: 12px;">
                <label class="form-label" for="username" style="display: block; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 13px;">Username <span style="color:#EF4444">*</span></label>
                <div style="position: relative;">
                    <i class="ri-account-circle-line" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9CA3AF; font-size: 18px;"></i>
                    <input type="text" class="form-control" id="username" name="username" value="<?= esc($user['username']) ?>" required style="width: 100%; padding: 12px 40px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: border-color 0.2s; outline: none;" onfocus="this.style.borderColor='#F05A1A'" onblur="this.style.borderColor='#D1D5DB'" placeholder="Masukkan username" />
                </div>
                <div style="margin-top: 8px; font-size: 11px; color: #6B7280;">Username digunakan untuk login.</div>
            </div>

            <div class="form-group mb-4" style="margin-bottom: 16px;">
                <label class="form-label" for="email" style="display: block; margin-bottom: 6px; font-weight: 600; color: #374151; font-size: 13px;">Email <span style="color:#EF4444">*</span></label>
                <div style="position: relative;">
                    <i class="ri-mail-line" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9CA3AF; font-size: 18px;"></i>
                    <input type="email" class="form-control" id="email" name="email" value="<?= esc($user['email']) ?>" required style="width: 100%; padding: 12px 40px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: border-color 0.2s; outline: none;" onfocus="this.style.borderColor='#F05A1A'" onblur="this.style.borderColor='#D1D5DB'" placeholder="Masukkan email aktif" />
                </div>
            </div>

            <div class="form-actions" style="display: flex; gap: 12px; padding-top: 8px; border-top: 1px solid #F3F4F6;">
                <button type="submit" class="btn btn-primary" style="background-color: #F05A1A; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 12px rgba(240, 90, 26, 0.25); display: flex; align-items: center; justify-content: center; flex: 1;">
                    <i class="ri-save-line" style="margin-right: 8px; font-size: 18px;"></i> Simpan Profil
                </button>
            </div>
        </form>

        <!-- Bagian Ubah Password (berupa tombol) -->
        <div style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #F3F4F6;">
            <h2 style="font-size: 16px; color: #1F2937; margin-top: 0; margin-bottom: 4px;"><i class="ri-shield-keyhole-line" style="margin-right: 8px; color: #F05A1A;"></i> Keamanan Akun</h2>
            <p style="font-size: 13px; color: #6B7280; margin-bottom: 12px; margin-top: 0;">Disarankan untuk memperbarui password Anda secara berkala guna menjaga keamanan akun.</p>
            <a href="<?= base_url('profile/ubah-password') ?>" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; background-color: #F3F4F6; color: #4B5563; text-decoration: none; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; transition: 0.2s;" onmouseover="this.style.backgroundColor='#E5E7EB'; this.style.color='#1F2937';" onmouseout="this.style.backgroundColor='#F3F4F6'; this.style.color='#4B5563';">
                <i class="ri-lock-password-line" style="font-size: 16px;"></i> Ganti Password
            </a>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
