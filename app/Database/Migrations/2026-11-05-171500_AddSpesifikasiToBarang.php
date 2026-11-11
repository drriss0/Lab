<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menambahkan kolom `spesifikasi` (TEXT, boleh kosong/NULL) ke tabel `barang`.
 *
 * Aman untuk database yang datanya sudah banyak:
 * - Kolom BARU ditambahkan di samping kolom lama, TIDAK ada data yang diubah/dihapus.
 * - Semua baris lama otomatis terisi NULL (= belum ada spesifikasi, tampil "—").
 * - Spesifikasi barang lama bisa diisi belakangan lewat tombol Edit, tidak wajib.
 *
 * Migration ini idempotent: kalau kolomnya sudah ada (mis. sudah ditambah
 * manual lewat SQL), up() tidak melakukan apa-apa dan tidak error.
 *
 * Cara menjalankan di komputer masing-masing (cukup SEKALI per database):
 *   php spark migrate
 */
class AddSpesifikasiToBarang extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('spesifikasi', 'barang')) {
            $this->forge->addColumn('barang', [
                'spesifikasi' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'merk',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('spesifikasi', 'barang')) {
            $this->forge->dropColumn('barang', 'spesifikasi');
        }
    }
}
