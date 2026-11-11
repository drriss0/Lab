<?= $this->extend('templates/layout') ?>

<?= $this->section('head') ?>
<style>
.kelola-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    border: 1px solid #E5E7EB;
    box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 16px;
    transition: 0.3s;
    text-decoration: none;
    color: inherit;
    position: relative;
    overflow: hidden;
}
.kelola-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 20px rgba(0,0,0,0.06);
    border-color: #D1D5DB;
}
.kelola-card .icon-wrapper {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    transition: 0.3s;
}
.kelola-card:hover .icon-wrapper {
    transform: scale(1.1);
}
.kelola-card .title {
    font-size: 20px;
    font-weight: 700;
    color: #1F2937;
}

</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero -->
<div style="display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap;">
    <div style="flex:1;min-width:280px;background:white;border-radius:16px;padding:24px 30px;display:flex;align-items:center;justify-content:space-between;position:relative;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.05);">
        <div style="z-index:1;">
            <h1 style="font-size:22px;font-weight:800;color:#374151;margin-bottom:6px;">
                <span style="color:#F05A1A;">Kelola</span> Sistem
            </h1>
            <p style="font-size:13px;color:#6B7280;line-height:1.5;max-width:420px;margin:0;">
                Pusat kendali administrator. Atur hak akses pengguna dan kelola data pendukung sistem inventaris FTIK.
            </p>
        </div>
        <i class="ri-settings-4-fill" style="position:absolute;right:-10px;bottom:-10px;font-size:120px;color:rgba(240,90,26,.06);pointer-events:none;"></i>
    </div>
</div>

<!-- List Menu -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:24px;">

    <!-- Kelola User -->
    <a href="<?= base_url('users') ?>" style="background:white;border-radius:16px;padding:28px 24px;border:1px solid #E5E7EB;box-shadow:0 1px 3px rgba(0,0,0,.04);display:flex;align-items:center;gap:20px;text-decoration:none;color:inherit;transition:.2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)';this.style.borderColor='#BFDBFE';this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,.04)';this.style.borderColor='#E5E7EB';this.style.transform='none';">
        <div style="width:64px;height:64px;border-radius:14px;background:#EFF6FF;color:#3B82F6;display:flex;align-items:center;justify-content:center;font-size:30px;flex-shrink:0;">
            <i class="ri-team-line"></i>
        </div>
        <div>
            <div style="font-size:18px;font-weight:700;color:#1F2937;margin-bottom:4px;">Kelola User</div>
            <div style="font-size:13px;color:#6B7280;line-height:1.4;">Manajemen akun dan hak akses pengguna sistem.</div>
        </div>
        <i class="ri-arrow-right-s-line" style="margin-left:auto;color:#D1D5DB;font-size:24px;"></i>
    </a>

    <!-- Kelola Kategori Utama -->
    <a href="<?= base_url('kategori-utama') ?>" style="background:white;border-radius:16px;padding:28px 24px;border:1px solid #E5E7EB;box-shadow:0 1px 3px rgba(0,0,0,.04);display:flex;align-items:center;gap:20px;text-decoration:none;color:inherit;transition:.2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)';this.style.borderColor='#E9D5FF';this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,.04)';this.style.borderColor='#E5E7EB';this.style.transform='none';">
        <div style="width:64px;height:64px;border-radius:14px;background:#F3E8FF;color:#9333EA;display:flex;align-items:center;justify-content:center;font-size:30px;flex-shrink:0;">
            <i class="ri-list-check"></i>
        </div>
        <div>
            <div style="font-size:18px;font-weight:700;color:#1F2937;margin-bottom:4px;">Kategori Utama</div>
            <div style="font-size:13px;color:#6B7280;line-height:1.4;">Grup kategori utama (Elektronik, Furnitur, dsb).</div>
        </div>
        <i class="ri-arrow-right-s-line" style="margin-left:auto;color:#D1D5DB;font-size:24px;"></i>
    </a>

    <!-- Kelola Jenis Barang -->
    <a href="<?= base_url('kategori') ?>" style="background:white;border-radius:16px;padding:28px 24px;border:1px solid #E5E7EB;box-shadow:0 1px 3px rgba(0,0,0,.04);display:flex;align-items:center;gap:20px;text-decoration:none;color:inherit;transition:.2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)';this.style.borderColor='#FDE68A';this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,.04)';this.style.borderColor='#E5E7EB';this.style.transform='none';">
        <div style="width:64px;height:64px;border-radius:14px;background:#FEF3C7;color:#D97706;display:flex;align-items:center;justify-content:center;font-size:30px;flex-shrink:0;">
            <i class="ri-price-tag-3-line"></i>
        </div>
        <div>
            <div style="font-size:18px;font-weight:700;color:#1F2937;margin-bottom:4px;">Jenis Barang</div>
            <div style="font-size:13px;color:#6B7280;line-height:1.4;">Pengaturan jenis barang beserta kode prefix.</div>
        </div>
        <i class="ri-arrow-right-s-line" style="margin-left:auto;color:#D1D5DB;font-size:24px;"></i>
    </a>

    <!-- Kelola Jurusan -->
    <a href="<?= base_url('jurusan') ?>" style="background:white;border-radius:16px;padding:28px 24px;border:1px solid #E5E7EB;box-shadow:0 1px 3px rgba(0,0,0,.04);display:flex;align-items:center;gap:20px;text-decoration:none;color:inherit;transition:.2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,.08)';this.style.borderColor='#A7F3D0';this.style.transform='translateY(-2px)';" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,.04)';this.style.borderColor='#E5E7EB';this.style.transform='none';">
        <div style="width:64px;height:64px;border-radius:14px;background:#ECFDF5;color:#10B981;display:flex;align-items:center;justify-content:center;font-size:30px;flex-shrink:0;">
            <i class="ri-graduation-cap-line"></i>
        </div>
        <div>
            <div style="font-size:18px;font-weight:700;color:#1F2937;margin-bottom:4px;">Kelola Jurusan</div>
            <div style="font-size:13px;color:#6B7280;line-height:1.4;">Daftar unit kerja atau jurusan peminjam.</div>
        </div>
        <i class="ri-arrow-right-s-line" style="margin-left:auto;color:#D1D5DB;font-size:24px;"></i>
    </a>

</div>

<?= $this->endSection() ?>
