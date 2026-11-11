<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>
<div style="display:flex;align-items:center;gap:16px;margin-bottom:24px; max-width: 600px; margin-left: auto; margin-right: auto;">
    <a href="<?= base_url('profile') ?>" style="width:40px;height:40px;background:white;border:1px solid #E5E7EB;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#6B7280;text-decoration:none;font-size:20px;transition:.15s;flex-shrink:0;" onmouseover="this.style.background='#F3F4F6';this.style.color='#1F2937';" onmouseout="this.style.background='white';this.style.color='#6B7280';" title="Kembali ke Profil">
        <i class="ri-arrow-left-line"></i>
    </a>
    <div>
        <h1 class="page-title" style="font-size: 20px; font-weight: 700; color: #1F2937; margin: 0;">Ubah Password</h1>
        <p class="page-subtitle" style="color: #6B7280; font-size: 13px; margin: 4px 0 0 0;">Perbarui password akun Anda untuk keamanan yang lebih baik.</p>
    </div>
</div>

<div class="form-section">
    <div style="max-width: 600px; margin: 0 auto; background: #FFFFFF; border-radius: 16px; padding: 32px; box-shadow: 0 1px 2px rgba(0,0,0,0.05), 0 4px 6px rgba(0,0,0,0.03);">
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

        <form action="<?= base_url('profile/ubah-password') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="form-group mb-4" style="margin-bottom: 20px;">
                <label class="form-label" for="password_lama" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 13px;">Password Lama <span style="color:#EF4444">*</span></label>
                <div style="position: relative;">
                    <i class="ri-lock-unlock-line" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9CA3AF; font-size: 18px;"></i>
                    <input type="password" class="form-control" id="password_lama" name="password_lama" required style="width: 100%; padding: 12px 40px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: border-color 0.2s; outline: none;" onfocus="this.style.borderColor='#F05A1A'" onblur="this.style.borderColor='#D1D5DB'" placeholder="Masukkan password lama" />
                    <button type="button" onclick="togglePassword('password_lama', 'eye_lama')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9CA3AF; font-size: 18px; padding: 4px; display: flex; align-items: center; justify-content: center;">
                        <i id="eye_lama" class="ri-eye-off-line"></i>
                    </button>
                </div>
            </div>

            <div class="form-group mb-4" style="margin-bottom: 20px;">
                <label class="form-label" for="password_baru" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 13px;">Password Baru <span style="color:#EF4444">*</span></label>
                <div style="position: relative;">
                    <i class="ri-lock-password-line" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9CA3AF; font-size: 18px;"></i>
                    <input type="password" class="form-control" id="password_baru" name="password_baru" required style="width: 100%; padding: 12px 40px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: border-color 0.2s; outline: none;" onfocus="this.style.borderColor='#F05A1A'" onblur="this.style.borderColor='#D1D5DB'" placeholder="Masukkan password baru" />
                    <button type="button" onclick="togglePassword('password_baru', 'eye_baru')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9CA3AF; font-size: 18px; padding: 4px; display: flex; align-items: center; justify-content: center;">
                        <i id="eye_baru" class="ri-eye-off-line"></i>
                    </button>
                </div>
                <div style="margin-top: 8px; padding: 10px 12px; background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px;">
                    <div style="font-size: 11px; color: #6B7280; font-weight: 600; margin-bottom: 4px;">Syarat Password:</div>
                    <ul style="margin: 0; padding-left: 0; list-style: none; font-size: 11px; color: #6B7280; line-height: 1.6;">
                        <li id="req-len" style="display: flex; align-items: center;"><i class="ri-checkbox-blank-circle-line" style="margin-right: 6px; font-size: 12px; color: #9CA3AF;"></i> Minimal 8 karakter</li>
                        <li id="req-case" style="display: flex; align-items: center;"><i class="ri-checkbox-blank-circle-line" style="margin-right: 6px; font-size: 12px; color: #9CA3AF;"></i> Mengandung huruf kapital & kecil (A-z)</li>
                        <li id="req-num-spc" style="display: flex; align-items: center;"><i class="ri-checkbox-blank-circle-line" style="margin-right: 6px; font-size: 12px; color: #9CA3AF;"></i> Mengandung angka (0-9) & karakter spesial</li>
                    </ul>
                </div>
            </div>

            <div class="form-group mb-4" style="margin-bottom: 28px;">
                <label class="form-label" for="konfirmasi_password" style="display: block; margin-bottom: 8px; font-weight: 600; color: #374151; font-size: 13px;">Konfirmasi Password Baru <span style="color:#EF4444">*</span></label>
                <div style="position: relative;">
                    <i class="ri-shield-keyhole-line" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9CA3AF; font-size: 18px;"></i>
                    <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required style="width: 100%; padding: 12px 40px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 14px; box-sizing: border-box; transition: border-color 0.2s; outline: none;" onfocus="this.style.borderColor='#F05A1A'" onblur="this.style.borderColor='#D1D5DB'" placeholder="Ulangi password baru" />
                    <button type="button" onclick="togglePassword('konfirmasi_password', 'eye_konfirmasi')" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #9CA3AF; font-size: 18px; padding: 4px; display: flex; align-items: center; justify-content: center;">
                        <i id="eye_konfirmasi" class="ri-eye-off-line"></i>
                    </button>
                </div>
            </div>

            <div class="form-actions" style="display: flex; gap: 12px; padding-top: 8px; border-top: 1px solid #F3F4F6;">
                <button type="submit" class="btn btn-primary" style="background-color: #F05A1A; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 12px rgba(240, 90, 26, 0.25); display: flex; align-items: center; justify-content: center; flex: 1;">
                    <i class="ri-save-line" style="margin-right: 8px; font-size: 18px;"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
    
    <script>
    function togglePassword(inputId, iconId) {
        var input = document.getElementById(inputId);
        var icon = document.getElementById(iconId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('ri-eye-off-line');
            icon.classList.add('ri-eye-line');
            icon.parentElement.style.color = '#F05A1A';
        } else {
            input.type = "password";
            icon.classList.remove('ri-eye-line');
            icon.classList.add('ri-eye-off-line');
            icon.parentElement.style.color = '#9CA3AF';
        }
    }

    // Validasi Dinamis Syarat Password
    document.getElementById('password_baru').addEventListener('input', function() {
        const val = this.value;
        const lenMet = val.length >= 8;
        const caseMet = /[a-z]/.test(val) && /[A-Z]/.test(val);
        const numSpcMet = /[0-9]/.test(val) && /[!@#$%^&*()_+\-=\[\]{};:'"\\|,.<>\/?`~]/.test(val);

        function updateReq(id, met) {
            const el = document.getElementById(id);
            const icon = el.querySelector('i');
            if (met) {
                el.style.color = '#16A34A';
                icon.className = 'ri-checkbox-circle-fill';
                icon.style.color = '#16A34A';
            } else {
                el.style.color = '#6B7280';
                icon.className = 'ri-checkbox-blank-circle-line';
                icon.style.color = '#9CA3AF';
            }
        }

        updateReq('req-len', lenMet);
        updateReq('req-case', caseMet);
        updateReq('req-num-spc', numSpcMet);
    });

    // Validasi sebelum submit form
    document.querySelector('form').addEventListener('submit', function(e) {
        const val = document.getElementById('password_baru').value;
        const conf = document.getElementById('konfirmasi_password').value;
        
        const valid = val.length >= 8 && 
                      /[a-z]/.test(val) && /[A-Z]/.test(val) && 
                      /[0-9]/.test(val) && /[!@#$%^&*()_+\-=\[\]{};:'"\\|,.<>\/?`~]/.test(val);
                      
        if (!valid) {
            e.preventDefault();
            alert('Password baru belum memenuhi semua syarat!');
        } else if (val !== conf) {
            e.preventDefault();
            alert('Konfirmasi password tidak cocok dengan password baru!');
        }
    });
    </script>
</div>
<?= $this->endSection() ?>
