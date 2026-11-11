<?= $this->extend('templates/layout') ?>

<?= $this->section('content') ?>

<div class="helpdesk-container" style="max-width: 1000px; margin: 0 auto;">

  <!-- Hero Section -->
  <div class="helpdesk-header" style="background: white; border-radius: 16px; padding: 0 40px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; border: 1px solid #E5E7EB; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); position: relative; overflow: hidden; height: 140px;">
      <img src="<?= base_url('image/header.png') ?>" alt="Avatar" style="height: 100%; width: auto; position: absolute; bottom: 0; left: 20px;" onerror="this.style.display='none'" />
      <div style="z-index: 1; margin-left: 200px; padding: 20px 0;">
          <h1 style="font-size: 24px; font-weight: 800; color: #374151; margin-bottom: 8px;">Pusat Bantuan & FAQ</h1>
          <p style="font-size: 14px; color: #6B7280; max-width: 400px; line-height: 1.6;">Temukan jawaban dari pertanyaan yang sering diajukan, atau unduh panduan lengkap untuk menggunakan sistem dengan optimal.</p>
      </div>
     <div style="flex-shrink: 0; position: absolute; right: 0; bottom: 0;">
         <img src="<?= base_url('assets/img/faq-illustration.png') ?>" alt="FAQ Illustration" style="height: 140px; object-fit: contain;" onerror="this.style.display='none'" />
     </div>
  </div>



  <!-- Main 2-Column Layout -->
  <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 32px;">
      
      <!-- Left Column: FAQ -->
      <div>
          <h2 style="font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 16px;">Pertanyaan Populer (FAQ)</h2>
          
          <div class="faq-list" style="display: flex; flex-direction: column; gap: 12px;">
              <?php if (empty($faq_list)): ?>
                  <!-- Dummy data to match screenshot if DB is empty -->
                  <?php 
                  $dummy_faqs = [
                      [
                          'q' => "Bagaimana cara membatalkan peminjaman yang salah input?",
                          'a' => "Silakan hubungi Admin atau bagian Tata Usaha (TU) untuk menghapus riwayat peminjaman tersebut melalui menu Peminjaman Barang."
                      ],
                      [
                          'q' => "Apa perbedaan antara status \"Digunakan\" dan \"Dipinjam\"?",
                          'a' => "\"Digunakan\" artinya barang ditempatkan secara tetap di sebuah ruangan (misal: komputer di Lab 1). \"Dipinjam\" artinya barang dibawa keluar sementara oleh mahasiswa/dosen."
                      ],
                      [
                          'q' => "Apakah saya bisa mengubah lokasi barang yang sudah didistribusikan?",
                          'a' => "Bisa. Masuk ke menu Data Barang Ruangan, pilih ruangan asal, cari barangnya lalu klik Edit untuk memindahkannya ke ruangan yang baru."
                      ],
                      [
                          'q' => "Bagaimana cara mencatat bahwa barang sudah dikembalikan?",
                          'a' => "Pada menu Peminjaman Barang, cari nama peminjam di tabel, lalu klik tombol \"Tandai Kembali\" (ikon centang hijau) pada kolom Aksi."
                      ],
                      [
                          'q' => "Apa maksud dari status \"Maintenance\"?",
                          'a' => "Status ini menandakan bahwa aset sedang dalam masa perbaikan (servis) dan tidak dapat digunakan atau dipinjam."
                      ],
                      [
                          'q' => "Ke mana barang pergi setelah dilaporkan rusak?",
                          'a' => "Barang akan masuk ke tabel Pelaporan Barang Rusak. Dari situ, Admin bisa menindaklanjutinya dengan mengubah status menjadi Maintenance (diperbaiki) atau Afkir (dihapus/dibuang)."
                      ]
                  ];
                  foreach ($dummy_faqs as $i => $faq): 
                  ?>
                  <div class="faq-card" style="background: white; border-radius: 8px; border: 1px solid #E5E7EB; overflow: hidden;">
                      <button class="faq-q-btn" onclick="toggleFaq('dummy_<?= $i ?>')" style="width: 100%; text-align: left; background: none; border: none; padding: 14px 20px; font-size: 14px; color: #374151; font-weight: 600; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                          <span><?= $faq['q'] ?></span>
                          <i class="ri-arrow-down-s-fill faq-icon" id="faq-icon-dummy_<?= $i ?>" style="color: #9CA3AF; font-size: 20px; transition: transform 0.2s;"></i>
                      </button>
                      <div class="faq-a-content" id="faq-ans-dummy_<?= $i ?>" style="display: none; padding: 0 20px 16px; color: #6B7280; font-size: 13px; line-height: 1.6;">
                          <?= $faq['a'] ?>
                      </div>
                  </div>
                  <?php endforeach; ?>
              <?php else: ?>
                  <?php foreach ($faq_list as $i => $f): ?>
                  <div class="faq-card" style="background: white; border-radius: 8px; border: 1px solid #E5E7EB; overflow: hidden;">
                      <button class="faq-q-btn" onclick="toggleFaq(<?= $f['id'] ?>)" style="width: 100%; text-align: left; background: none; border: none; padding: 14px 20px; font-size: 14px; color: #374151; font-weight: 600; display: flex; justify-content: space-between; align-items: center; cursor: pointer;">
                          <span><?= esc($f['pertanyaan']) ?></span>
                          <i class="ri-arrow-down-s-fill faq-icon" id="faq-icon-<?= $f['id'] ?>" style="color: #9CA3AF; font-size: 20px; transition: transform 0.2s;"></i>
                      </button>
                      <div class="faq-a-content" id="faq-ans-<?= $f['id'] ?>" style="display: none; padding: 0 20px 16px; color: #6B7280; font-size: 13px; line-height: 1.6;">
                          <?= nl2br(esc($f['jawaban'])) ?>
                      </div>
                  </div>
                  <?php endforeach; ?>
              <?php endif; ?>
          </div>
      </div>

      <!-- Right Column: WhatsApp Contact + Download Modul -->
      <div>
          <h2 style="font-size: 18px; font-weight: 700; color: #374151; margin-bottom: 16px;">Bantuan Kendala</h2>
          
          <div style="background: white; border-radius: 16px; padding: 24px; border: 1px solid #E5E7EB; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); position: relative; overflow: hidden; min-height: 250px;">
              <h3 style="color: #F05A1A; font-size: 18px; font-weight: 700; margin-bottom: 12px; line-height: 1.3;">Masih mengalami<br>kendala?</h3>
              <p style="color: #6B7280; font-size: 13px; line-height: 1.5; margin-bottom: 20px; position: relative; z-index: 1;">Jika kendala Anda belum teratasi, hubungi tim IT Help Desk FTIK secara langsung.</p>
              
              <img src="<?= base_url('image/header.png') ?>" alt="CS" style="position: absolute; bottom: -10px; left: -10px; height: 140px; z-index: 0;" onerror="this.style.display='none'" />
              
              <a href="https://wa.me/6281234567890" target="_blank" style="position: absolute; bottom: 24px; right: 24px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: #22C55E; color: white; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; text-decoration: none; z-index: 2; box-shadow: 0 4px 6px rgba(34, 197, 94, 0.2);">
                  <i class="ri-whatsapp-line" style="font-size: 18px;"></i> WhatsApp
              </a>
          </div>

          <!-- Download Modul Card -->
          <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 24px; margin-bottom: 16px;">
              <h2 style="font-size: 18px; font-weight: 700; color: #374151; margin: 0;">Download Modul</h2>
              <?php if (session()->get('user_role') === 'admin'): ?>
              <a href="<?= base_url('modul') ?>" style="background: #FFF3EE; color: #F05A1A; border: 1px solid #FCD9C5; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 4px; transition: 0.2s;" onmouseover="this.style.background='#FFD5C2'" onmouseout="this.style.background='#FFF3EE'">
                  <i class="ri-upload-cloud-2-line"></i> Kelola & Upload
              </a>
              <?php endif; ?>
          </div>
          <div style="background: white; border-radius: 16px; padding: 20px; border: 1px solid #E5E7EB; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 10px;">
              <?php if (empty($modul_list)): ?>
              <div style="text-align: center; padding: 20px 10px; color: #9CA3AF;">
                  <i class="ri-file-download-line" style="font-size: 32px; display: block; margin-bottom: 8px; color: #D1D5DB;"></i>
                  <p style="font-size: 13px; margin: 0;">Belum ada modul tersedia.</p>
              </div>
              <?php else: ?>
              <?php foreach ($modul_list as $modul): ?>
              <?php
                  $ext = strtolower(pathinfo($modul['file_modul'], PATHINFO_EXTENSION));
                  $iconClass = ($ext === 'pdf') ? 'ri-file-pdf-2-fill' : 'ri-file-word-fill';
                  $iconColor = ($ext === 'pdf') ? '#EF4444' : '#2563EB';
                  $bgColor   = ($ext === 'pdf') ? '#FEE2E2' : '#DBEAFE';
              ?>
              <a href="<?= base_url('uploads/modul/' . $modul['file_modul']) ?>" target="_blank"
                 style="display: flex; align-items: center; gap: 12px; padding: 12px 14px; border: 1px solid #E5E7EB; border-radius: 10px; text-decoration: none; transition: all 0.2s; background: #FAFAFA;"
                 onmouseover="this.style.borderColor='#F05A1A'; this.style.background='#FFF7F5';"
                 onmouseout="this.style.borderColor='#E5E7EB'; this.style.background='#FAFAFA';">
                  <div style="width: 36px; height: 36px; border-radius: 8px; background: <?= $bgColor ?>; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                      <i class="<?= $iconClass ?>" style="font-size: 18px; color: <?= $iconColor ?>;"></i>
                  </div>
                  <div style="min-width: 0; flex: 1;">
                      <div style="font-size: 13px; font-weight: 600; color: #1F2937; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                          <?= esc($modul['judul']) ?>
                      </div>
                      <div style="font-size: 11px; color: #9CA3AF; margin-top: 2px; display: flex; align-items: center; gap: 4px;">
                          <i class="ri-download-line"></i> <?= strtoupper($ext) ?>
                      </div>
                  </div>
                  <i class="ri-download-2-line" style="color: #F05A1A; font-size: 18px; flex-shrink: 0;"></i>
              </a>
              <?php endforeach; ?>
              <?php endif; ?>
          </div>

      </div>

  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('head') ?>
<style>
/* Responsive layout adjustments */
@media (max-width: 768px) {
    .helpdesk-header {
        flex-direction: column-reverse;
        text-align: center;
        padding: 24px 20px !important;
    }
    .helpdesk-header img {
        margin-bottom: 20px;
        position: relative !important;
    }
    .helpdesk-container > div:nth-child(2) {
        grid-template-columns: 1fr !important;
    }
}

/* Hover effect for FAQ */
.faq-card:hover {
    border-color: #D1D5DB;
}
.faq-q-btn:hover {
    color: #F05A1A !important;
}

/* Icon rotation when active */
.faq-icon.open {
    transform: rotate(180deg);
    color: #F05A1A !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleFaq(id) {
    var ans  = document.getElementById('faq-ans-' + id);
    var icon = document.getElementById('faq-icon-' + id);
    
    // Check if the clicked one is already open
    var isAlreadyOpen = ans.style.display === 'block';

    // Close all other FAQs
    document.querySelectorAll('.faq-a-content').forEach(function(el) {
        el.style.display = 'none';
    });
    document.querySelectorAll('.faq-icon').forEach(function(el) {
        el.classList.remove('open');
    });

    // If it wasn't open, open it
    if (!isAlreadyOpen) {
        ans.style.display = 'block';
        icon.classList.add('open');
    }
}
</script>
<?= $this->endSection() ?>
