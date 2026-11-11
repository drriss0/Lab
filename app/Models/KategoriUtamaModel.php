<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriUtamaModel extends Model
{
    protected $table         = 'kategori_utama';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['nama_kategori', 'keterangan', 'created_at'];
    protected $useTimestamps = false;
    
    /**
     * Dapatkan daftar kategori utama beserta jumlah jenis barang di bawahnya
     */
    public function getKategoriWithCount(): array
    {
        return $this->db->table($this->table)
            ->select('kategori_utama.*, COUNT(barang.id) as total_barang')
            ->join('barang', 'barang.kategori = kategori_utama.nama_kategori', 'left')
            ->groupBy('kategori_utama.id')
            ->orderBy('kategori_utama.nama_kategori', 'ASC')
            ->get()->getResultArray();
    }
}
