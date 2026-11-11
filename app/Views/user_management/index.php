<?= $this->extend('templates/layout') ?>

<?= $this->section('head') ?>
<style>
.role-badge {
    display:inline-block;padding:2px 12px;border-radius:20px;font-size:11px;font-weight:700;
}
.role-admin        { background:#FEE2E2;color:#991B1B; }
.role-dekan        { background:#DBEAFE;color:#1D4ED8; }
.role-koordinator_lab { background:#D1FAE5;color:#065F46; }
.role-kepala_lab   { background:#FEF3C7;color:#92400E; }
.role-tu           { background:#EDE9FE;color:#5B21B6; }

.aksi-btn {
    display:inline-flex;align-items:center;justify-content:center;
    width:30px;height:30px;border:none;border-radius:6px;
    font-size:13px;cursor:pointer;transition:.15s;text-decoration:none;
}
.aksi-btn:hover { filter:brightness(.85);transform:scale(1.08); }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

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

<!-- Page Header -->
<div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
    <a href="<?= base_url('kelola') ?>" style="width:36px;height:36px;background:white;border:1px solid #E5E7EB;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#6B7280;text-decoration:none;font-size:18px;transition:.15s;flex-shrink:0;" onmouseover="this.style.background='#F3F4F6';this.style.color='#1F2937';" onmouseout="this.style.background='white';this.style.color='#6B7280';" title="Kembali ke Kelola Sistem">
        <i class="ri-arrow-left-line"></i>
    </a>
    <div style="font-size:20px;font-weight:700;color:#1F2937;">Kelola <span style="color:var(--primary);">User</span></div>
</div>

<!-- Table -->
<div class="table-section">
    <div class="table-toolbar" style="padding:20px 24px 12px;display:flex;justify-content:space-between;align-items:center;">
        <div class="table-toolbar-left">
            <span class="table-title">Daftar Akun User</span>
        </div>
        <div style="display:flex;gap:12px;align-items:center;">
            <button class="btn btn-primary" onclick="openModal('modalTambahUser')">
                <i class="ri-add-line"></i> Tambah User
            </button>
        </div>
    </div>
    <div class="data-table-wrapper">
        <table class="data-table" id="tabelUsers">
            <thead>
                <tr>
                    <th style="width:44px;text-align:center;">No</th>
                    <th>Nama</th>
                    <th>Username / Email</th>
                    <th style="text-align:center;">Role</th>
                    <th>Ruangan (Kepala Lab)</th>
                    <th style="text-align:center;">Status</th>
                    <th style="text-align:center;width:100px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                    <?php foreach ($users as $i => $u): ?>
                    <tr>
                        <td style="text-align:center;color:#9CA3AF;font-size:12px;"><?= $i + 1 ?></td>
                        <td>
                            <div style="font-weight:700;color:#374151;"><?= esc($u['nama']) ?></div>
                            <?php if (! $u['is_active']): ?>
                                <div style="font-size:11px;color:#EF4444;font-weight:600;">⚠ Tidak Aktif</div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-size:13px;color:#374151;"><?= esc($u['username'] ?? '') ?></div>
                            <div style="font-size:11px;color:#9CA3AF;"><?= esc($u['email']) ?></div>
                        </td>
                        <td style="text-align:center;">
                            <span class="role-badge role-<?= esc($u['role']) ?>">
                                <?= match($u['role']) {
                                    'admin'           => 'Admin',
                                    'dekan'           => 'Dekan',
                                    'koordinator_lab' => 'Koordinator Lab',
                                    'kepala_lab'      => 'Kepala Lab',
                                    'tu'              => 'TU',
                                    default           => ucfirst($u['role']),
                                } ?>
                            </span>
                        </td>
                        <td style="font-size:13px;color:#6B7280;">
                            <?= $u['nama_ruangan'] ? esc($u['nama_ruangan']) : '<span style="color:#D1D5DB;">—</span>' ?>
                        </td>
                        <td style="text-align:center;">
                            <?php if ($u['is_active']): ?>
                                <span class="badge" style="background:#D1FAE5;color:#065F46;font-size:11px;">Aktif</span>
                            <?php else: ?>
                                <span class="badge" style="background:#FEE2E2;color:#991B1B;font-size:11px;">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;">
                            <div style="display:flex;gap:4px;justify-content:center;">
                                <button class="aksi-btn" style="background:#F59E0B;color:white;"
                                    onclick="openModalEdit(<?= htmlspecialchars(json_encode(array_merge($u, ['hash' => encode_id($u['id'])]))) ?>)"
                                    title="Edit User">
                                    <i class="ri-pencil-line"></i>
                                </button>
                                <?php if ($u['role'] !== 'admin'): ?>
                                <form method="post" action="<?= base_url('users/' . encode_id($u['id']) . '/toggle-status') ?>" style="display:inline;" onsubmit="return confirm('<?= $u['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?> user ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="aksi-btn"
                                       style="background:<?= $u['is_active'] ? '#EF4444' : '#10B981' ?>;color:white;"
                                       title="<?= $u['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                        <i class="ri-<?= $u['is_active'] ? 'forbid' : 'check' ?>-line"></i>
                                    </button>
                                </form>
                                <form method="post" action="<?= base_url('users/' . encode_id($u['id']) . '/hapus') ?>" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus user ini secara permanen?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="aksi-btn"
                                       style="background:#EF4444;color:white;"
                                       title="Hapus User">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="pagination-wrapper">
        <span>Menampilkan <strong><?= count($users) ?></strong> user</span>
    </div>
</div>

<!-- Modal Tambah User -->
<div class="modal-overlay" id="modalTambahUser">
    <div class="modal-box" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title"><span style="color:#F05A1A;">Tambah</span> User Baru</div>
            <button class="modal-close" onclick="closeModal('modalTambahUser')"><i class="ri-close-line"></i></button>
        </div>
        <form method="post" action="<?= base_url('users/simpan') ?>">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom:24px;">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="nama" class="form-control" required placeholder="Nama lengkap user" />
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" class="form-control" required placeholder="username" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required placeholder="email@ftik.usm.ac.id" />
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Password *</label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="newPasswordInput" class="form-control" required placeholder="Contoh: Admin@123" minlength="8" oninput="checkPasswordStrength(this.value, 'pwChecklist')" style="padding-right:40px;" />
                            <button type="button" onclick="togglePw('newPasswordInput','eyeIconNew')" tabindex="-1"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:16px;line-height:1;">
                                <i class="ri-eye-off-line" id="eyeIconNew"></i>
                            </button>
                        </div>
                        <div id="pwChecklist" style="margin-top:8px;padding:10px 12px;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:8px;font-size:12px;display:none;">
                            <div style="font-weight:600;color:#374151;margin-bottom:6px;">Syarat Password:</div>
                            <div id="pw-len"    style="color:#9CA3AF;">✗ Minimal 8 karakter</div>
                            <div id="pw-upper"  style="color:#9CA3AF;">✗ Minimal 1 huruf kapital (A-Z)</div>
                            <div id="pw-lower"  style="color:#9CA3AF;">✗ Minimal 1 huruf kecil (a-z)</div>
                            <div id="pw-digit"  style="color:#9CA3AF;">✗ Minimal 1 angka (0-9)</div>
                            <div id="pw-special" style="color:#9CA3AF;">✗ Minimal 1 karakter spesial (!@#$%^&*)</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role *</label>
                        <select name="role" class="form-control" id="roleSelect" onchange="toggleRuangan(this.value)" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="dekan">Dekan</option>
                            <option value="koordinator_lab">Koordinator Lab</option>
                            <option value="kepala_lab">Kepala Lab</option>
                            <option value="tu">TU</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="ruanganGroup" style="display:none;">
                    <label class="form-label">Ruangan (untuk Kepala Lab)</label>
                    <select name="id_ruangan" class="form-control">
                        <option value="">-- Pilih Ruangan --</option>
                        <?php foreach ($ruangan as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= esc($r['nama_ruangan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;height:42px;justify-content:center;margin-top:4px;">
                    <i class="ri-user-add-line"></i> Simpan User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div class="modal-overlay" id="modalEditUser">
    <div class="modal-box" style="max-width:500px;">
        <div class="modal-header">
            <div class="modal-title"><span style="color:#F59E0B;">Edit</span> User</div>
            <button class="modal-close" onclick="closeModal('modalEditUser')"><i class="ri-close-line"></i></button>
        </div>
        <form method="post" action="" id="formEditUser">
            <?= csrf_field() ?>
            <div class="modal-body" style="padding-bottom:24px;">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap *</label>
                    <input type="text" name="nama" id="edit-nama" class="form-control" required />
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" id="edit-username" class="form-control" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" id="edit-email" class="form-control" required />
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <div style="position:relative;">
                            <input type="password" name="password" id="editPasswordInput" class="form-control" placeholder="Kosongkan jika tidak diubah" minlength="8" oninput="checkPasswordStrength(this.value, 'editPwChecklist')" style="padding-right:40px;" />
                            <button type="button" onclick="togglePw('editPasswordInput','eyeIconEdit')" tabindex="-1"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#9CA3AF;font-size:16px;line-height:1;">
                                <i class="ri-eye-off-line" id="eyeIconEdit"></i>
                            </button>
                        </div>
                        <div id="editPwChecklist" style="margin-top:8px;padding:10px 12px;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:8px;font-size:12px;display:none;">
                            <div style="font-weight:600;color:#374151;margin-bottom:6px;">Syarat Password:</div>
                            <div id="edit-pw-len"     style="color:#9CA3AF;">✗ Minimal 8 karakter</div>
                            <div id="edit-pw-upper"   style="color:#9CA3AF;">✗ Minimal 1 huruf kapital (A-Z)</div>
                            <div id="edit-pw-lower"   style="color:#9CA3AF;">✗ Minimal 1 huruf kecil (a-z)</div>
                            <div id="edit-pw-digit"   style="color:#9CA3AF;">✗ Minimal 1 angka (0-9)</div>
                            <div id="edit-pw-special" style="color:#9CA3AF;">✗ Minimal 1 karakter spesial (!@#$%^&*)</div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Role *</label>
                        <select name="role" id="edit-role" class="form-control" onchange="toggleRuanganEdit(this.value)" required>
                            <option value="dekan">Dekan</option>
                            <option value="koordinator_lab">Koordinator Lab</option>
                            <option value="kepala_lab">Kepala Lab</option>
                            <option value="tu">TU</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="ruanganGroupEdit" style="display:none;">
                    <label class="form-label">Ruangan (untuk Kepala Lab)</label>
                    <select name="id_ruangan" id="edit-ruangan" class="form-control">
                        <option value="">-- Pilih Ruangan --</option>
                        <?php foreach ($ruangan as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= esc($r['nama_ruangan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;height:42px;justify-content:center;margin-top:4px;background:#F59E0B;border-color:#F59E0B;">
                    <i class="ri-save-line"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleRuangan(role) {
    document.getElementById('ruanganGroup').style.display = role === 'kepala_lab' ? 'block' : 'none';
}
function toggleRuanganEdit(role) {
    document.getElementById('ruanganGroupEdit').style.display = role === 'kepala_lab' ? 'block' : 'none';
}

function openModalEdit(u) {
    document.getElementById('edit-nama').value     = u.nama;
    document.getElementById('edit-username').value = u.username || '';
    document.getElementById('edit-email').value    = u.email;
    document.getElementById('edit-role').value     = u.role;
    toggleRuanganEdit(u.role);
    if (u.id_ruangan) {
        document.getElementById('edit-ruangan').value = u.id_ruangan;
    }
    document.getElementById('formEditUser').action = '<?= base_url('users') ?>/' + u.hash + '/edit';
    openModal('modalEditUser');
}
function checkPasswordStrength(val, checklistId) {
    const prefix = checklistId === 'editPwChecklist' ? 'edit-pw-' : 'pw-';
    const box = document.getElementById(checklistId);
    box.style.display = val.length > 0 ? 'block' : 'none';

    const rules = [
        { id: prefix + 'len',     ok: val.length >= 8,           label: 'Minimal 8 karakter' },
        { id: prefix + 'upper',   ok: /[A-Z]/.test(val),         label: 'Minimal 1 huruf kapital (A-Z)' },
        { id: prefix + 'lower',   ok: /[a-z]/.test(val),         label: 'Minimal 1 huruf kecil (a-z)' },
        { id: prefix + 'digit',   ok: /[0-9]/.test(val),         label: 'Minimal 1 angka (0-9)' },
        { id: prefix + 'special', ok: /[!@#$%^&*()_+\-=\[\]{};'"\\|,.<>\/?`~]/.test(val), label: 'Minimal 1 karakter spesial (!@#$%^&*)' },
    ];
    rules.forEach(r => {
        const el = document.getElementById(r.id);
        el.textContent = (r.ok ? '✓ ' : '✗ ') + r.label;
        el.style.color  = r.ok ? '#059669' : '#9CA3AF';
        el.style.fontWeight = r.ok ? '600' : '400';
    });
}
function togglePw(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'ri-eye-line';
        icon.parentElement.style.color = '#F05A1A';
    } else {
        input.type = 'password';
        icon.className = 'ri-eye-off-line';
        icon.parentElement.style.color = '#9CA3AF';
    }
}
</script>
<?= $this->endSection() ?>
