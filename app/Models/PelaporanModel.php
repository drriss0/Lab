<?php

namespace App\Models;

use CodeIgniter\Model;

class PelaporanModel extends Model
{
    protected $table         = 'pelaporan_rusak';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'id_barang', 'id_distribusi', 'id_ruangan', 'jumlah', 'tanggal_lapor',
        'tingkat_kerusakan', 'status', 'keterangan'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil semua laporan rusak untuk satu barang beserta nama ruangan.
     */
    public function getByBarang(int $idBarang): array
    {
        return $this->db->table('pelaporan_rusak p')
            ->select('p.*, r.nama_ruangan')
            ->join('ruangan r', 'r.id = p.id_ruangan', 'left')
            ->where('p.id_barang', $idBarang)
            ->orderBy('p.tanggal_lapor', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Total barang rusak (menunggu + sedang diperbaiki) untuk satu barang.
     */
    public function getTotalRusak(int $idBarang): int
    {
        $row = $this->db->table('pelaporan_rusak')
            ->selectSum('jumlah')
            ->where('id_barang', $idBarang)
            ->whereIn('status', ['menunggu', 'sedang_diperbaiki'])
            ->get()->getRowArray();
        return (int)($row['jumlah'] ?? 0);
    }

    /**
     * Update status penanganan laporan.
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->db->table('pelaporan_rusak')
            ->where('id', $id)
            ->update(['status' => $status]);
    }
}
