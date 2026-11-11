<?php

namespace App\Models;

use CodeIgniter\Model;

class PeminjamanModel extends Model
{
    protected $table         = 'peminjaman';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'id_barang', 'nama_peminjam', 'nim_nik', 'no_telp', 'jumlah',
        'tanggal_pinjam', 'tenggat_kembali', 'tanggal_kembali',
        'status', 'keterangan', 'foto_dokumentasi'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil semua peminjaman untuk satu barang.
     */
    public function getByBarang(int $idBarang): array
    {
        // Auto-update status terlambat
        $this->autoUpdateTerlambat($idBarang);

        return $this->db->table('peminjaman')
            ->where('id_barang', $idBarang)
            ->orderBy('tanggal_pinjam', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Otomatis tandai peminjaman yg melewati tenggat sebagai "terlambat".
     */
    private function autoUpdateTerlambat(int $idBarang): void
    {
        $this->db->table('peminjaman')
            ->where('id_barang', $idBarang)
            ->where('status', 'dipinjam')
            ->where('tenggat_kembali <', date('Y-m-d'))
            ->update(['status' => 'terlambat']);
    }

    /**
     * Total barang yang sedang dipinjam (belum dikembalikan).
     */
    public function getTotalDipinjam(int $idBarang): int
    {
        $row = $this->db->table('peminjaman')
            ->selectSum('jumlah')
            ->where('id_barang', $idBarang)
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->get()->getRowArray();
        return (int)($row['jumlah'] ?? 0);
    }

    /**
     * Tandai peminjaman sebagai kembali.
     */
    public function tandaiKembali(int $id): bool
    {
        return $this->db->table('peminjaman')
            ->where('id', $id)
            ->update([
                'status'          => 'kembali',
                'tanggal_kembali' => date('Y-m-d'),
            ]);
    }

    /**
     * Ambil semua peminjaman join barang (untuk halaman Peminjaman Barang).
     */
    public function getAllWithBarang(): array
    {
        // Auto-update semua yg terlambat
        $this->db->table('peminjaman')
            ->where('status', 'dipinjam')
            ->where('tenggat_kembali <', date('Y-m-d'))
            ->update(['status' => 'terlambat']);

        return $this->db->table('peminjaman p')
            ->select('p.*, b.nama_barang, b.kode_barang, b.foto_barang,
                      (SELECT d.penanggung_jawab FROM distribusi d 
                       WHERE d.id_barang = b.id 
                         AND d.tujuan_distribusi = "peminjaman" 
                         AND d.status = "digunakan" 
                       LIMIT 1) AS penanggung_jawab')
            ->join('barang b', 'b.id = p.id_barang', 'left')
            ->orderBy('p.created_at', 'DESC')
            ->get()->getResultArray();
    }
}
