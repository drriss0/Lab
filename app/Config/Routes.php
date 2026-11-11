<?php

use CodeIgniter\Router\RouteCollection;

/**
 * SIn-FTIK USM — Routing
 * Sistem Inventaris FTIK Universitas Semarang
 *
 * Semua route yang mengandung ID database menggunakan (:segment) agar
 * menerima Hashids string (bukan integer mentah) sehingga ID tidak mudah
 * ditebak / dimanipulasi.
 *
 * @var RouteCollection $routes
 */

// ============================================================
// Auth
// ============================================================
$routes->get('/',       'Auth::index');
$routes->get('/login',  'Auth::index');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');

// ============================================================
// Dashboard
// ============================================================
$routes->get('/dashboard', 'Dashboard::index');

// ============================================================
// Kategori Barang (Master Data - Jenis Barang)
// ============================================================
$routes->get('/kategori',                  'Kategori::index');
$routes->post('/kategori/simpan',          'Kategori::simpan');
$routes->post('/kategori/edit/(:segment)', 'Kategori::edit/$1');
$routes->post('/kategori/hapus/(:segment)', 'Kategori::hapus/$1');

// ============================================================
// Kategori Utama (Master Data - Kategori Induk)
// ============================================================
$routes->get('/kategori-utama',                  'KategoriUtama::index');
$routes->post('/kategori-utama/simpan',          'KategoriUtama::simpan');
$routes->post('/kategori-utama/edit/(:segment)', 'KategoriUtama::edit/$1');
$routes->post('/kategori-utama/hapus/(:segment)', 'KategoriUtama::hapus/$1');

// ============================================================
// Jurusan
// ============================================================
$routes->get('/jurusan',                  'Jurusan::index');
$routes->post('/jurusan/simpan',          'Jurusan::simpan');
$routes->post('/jurusan/edit/(:segment)', 'Jurusan::edit/$1');
$routes->post('/jurusan/hapus/(:segment)', 'Jurusan::hapus/$1');

// ============================================================
// Barang
// ============================================================
$routes->get('/barang/generate-kode',                    'Barang::generateKode');
$routes->post('/barang/generate-kode',                   'Barang::generateKode');
$routes->post('/barang/simpan',                          'Barang::simpan');
$routes->get('/barang/(:segment)/detail',                'Barang::detail/$1');
$routes->post('/barang/(:segment)/edit',                 'Barang::edit/$1');
$routes->post('/barang/(:segment)/restock',              'Barang::restock/$1');
$routes->post('/barang/(:segment)/hapus',                'Barang::hapus/$1');

// Distribusi
$routes->get('/barang/(:segment)/distribusi',            'Barang::distribusi/$1');
$routes->post('/barang/(:segment)/distribusi/simpan',    'Barang::simpanDistribusi/$1');
$routes->post('/barang/distribusi/(:segment)/tarik',     'Barang::tarikDistribusi/$1');
$routes->post('/barang/distribusi/(:segment)/hapus',     'Barang::hapusDistribusi/$1');
$routes->post('/barang/distribusi/tarik-bulk',           'Barang::tarikDistribusiBulk');
$routes->post('/barang/distribusi/hapus-bulk',           'Barang::hapusDistribusiBulk');
$routes->get('/barang/cek-kapasitas-ruangan',            'Barang::cekKapasitasRuangan');

// Peminjaman
$routes->get('/barang/(:segment)/peminjaman',            'Barang::peminjaman/$1');
$routes->post('/barang/(:segment)/peminjaman/simpan',    'Barang::simpanPeminjaman/$1');
$routes->post('/barang/peminjaman/(:segment)/kembali',   'Barang::kembalikanPeminjaman/$1');
$routes->post('/barang/peminjaman/(:segment)/hapus',     'Barang::hapusPeminjaman/$1');

// Pelaporan Rusak per barang
$routes->get('/barang/(:segment)/pelaporan',             'Barang::pelaporan/$1');
$routes->post('/barang/(:segment)/pelaporan/simpan',     'Barang::simpanPelaporan/$1');
$routes->post('/barang/pelaporan/(:segment)/status',     'Barang::updateStatusPelaporan/$1');
$routes->post('/barang/pelaporan/(:segment)/edit',       'Barang::editPelaporan/$1');

// ============================================================
// Data Barang Ruangan
// ============================================================
$routes->get('/data-barang-ruangan',                                    'DataBarangRuangan::index');
$routes->post('/data-barang-ruangan/simpan',                            'DataBarangRuangan::simpan');
$routes->get('/data-barang-ruangan/(:segment)',                         'DataBarangRuangan::detail/$1');
$routes->post('/data-barang-ruangan/(:segment)/edit',                   'DataBarangRuangan::edit/$1');
$routes->post('/data-barang-ruangan/(:segment)/hapus',                  'DataBarangRuangan::hapus/$1');
$routes->post('/data-barang-ruangan/alokasi',                           'DataBarangRuangan::alokasi');
$routes->post('/data-barang-ruangan/tarik-bulk',                        'DataBarangRuangan::tarikBulk');
$routes->post('/data-barang-ruangan/alokasi-ulang-bulk',                'DataBarangRuangan::alokasiUlangBulk');
$routes->post('/data-barang-ruangan/lapor-rusak-bulk',                  'DataBarangRuangan::laporRusakBulk');
$routes->post('/data-barang-ruangan/distribusi/(:segment)/alokasi-ulang', 'DataBarangRuangan::alokasiUlang/$1');
$routes->post('/data-barang-ruangan/distribusi/(:segment)/lapor-rusak',   'DataBarangRuangan::laporRusak/$1');

// ============================================================
// Pengajuan Inventaris
// ============================================================
$routes->get('/pengajuan',                       'Pengajuan::index');
$routes->post('/pengajuan/simpan',               'Pengajuan::simpan');
$routes->post('/pengajuan/(:segment)/status',    'Pengajuan::updateStatus/$1');
$routes->post('/pengajuan/(:segment)/edit',      'Pengajuan::edit/$1');
$routes->post('/pengajuan/(:segment)/revisi',    'Pengajuan::revisi/$1');
$routes->post('/pengajuan/(:segment)/tolak',     'Pengajuan::tolak/$1');
$routes->post('/pengajuan/(:segment)/hapus',     'Pengajuan::hapus/$1');

// ============================================================
// Peminjaman Barang (Menu Sidebar)
// ============================================================
$routes->get('/peminjaman-barang',                              'Peminjaman::index');
$routes->get('/peminjaman-barang/(:segment)/detail',            'Peminjaman::detail/$1');
$routes->post('/peminjaman-barang/(:segment)/update-pj',        'Peminjaman::updatePj/$1');
$routes->post('/peminjaman-barang/simpan/(:segment)',            'Peminjaman::simpan/$1');
$routes->post('/peminjaman-barang/(:segment)/edit',             'Peminjaman::edit/$1');
$routes->post('/peminjaman-barang/(:segment)/kembali',          'Peminjaman::kembali/$1');
$routes->post('/peminjaman-barang/(:segment)/hapus',            'Peminjaman::hapus/$1');
$routes->post('/peminjaman-barang/bulk-kembali',                'Peminjaman::bulkKembali');
$routes->post('/peminjaman-barang/bulk-hapus',                  'Peminjaman::bulkHapus');

// ============================================================
// Pelaporan Barang Rusak (Menu Sidebar)
// ============================================================
$routes->get('/pelaporan-rusak',                              'PelaporanRusak::index');
$routes->post('/pelaporan-rusak/(:segment)/update-status',    'PelaporanRusak::updateStatus/$1');
$routes->post('/pelaporan-rusak/(:segment)/kembali',          'PelaporanRusak::kembaliKeRuangan/$1');
$routes->post('/pelaporan-rusak/(:segment)/hapus',            'PelaporanRusak::hapus/$1');
$routes->post('/pelaporan-rusak/bulk-action',                 'PelaporanRusak::bulkAction');

// ============================================================
// Help Desk
// ============================================================
$routes->get('/helpdesk', 'HelpDesk::index');

// ============================================================
// Kelola Modul (Admin — Help Desk Modul)
// ============================================================
$routes->get('/modul',               'Modul::index');
$routes->post('/modul/upload',       'Modul::store');
$routes->post('/modul/hapus/(:segment)', 'Modul::delete/$1');

// ============================================================
// User Management & Kelola (Admin Only)
// ============================================================
$routes->get('/kelola',                       'Kelola::index');
$routes->get('/users',                        'UserManagement::index');
$routes->post('/users/simpan',                'UserManagement::simpan');
$routes->post('/users/(:segment)/edit',         'UserManagement::edit/$1');
$routes->post('/users/(:segment)/toggle-status', 'UserManagement::toggleStatus/$1');
$routes->post('/users/(:segment)/hapus',        'UserManagement::hapus/$1');

// ============================================================
// Profile User
// ============================================================
$routes->get('/profile',                 'Profile::index');
$routes->post('/profile/simpan',         'Profile::simpanProfil');
$routes->get('/profile/ubah-password',   'Profile::ubahPassword');
$routes->post('/profile/ubah-password',  'Profile::simpanPassword');
