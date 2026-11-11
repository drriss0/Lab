<?php

namespace App\Models;

use CodeIgniter\Model;

class KategoriModel extends Model
{
    protected $table         = 'jenis_barang';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['kode_kategori', 'nama', 'created_at'];
    protected $useTimestamps = false;
    
    /**
     * Dapatkan daftar kategori dengan jumlah barang (total jenis)
     */
    public function getKategoriWithCount(): array
    {
        return $this->db->table('jenis_barang')
            ->select('jenis_barang.*, COUNT(barang.id) as total_barang')
            ->join('barang', 'barang.id_kategori = jenis_barang.id', 'left')
            ->groupBy('jenis_barang.id')
            ->orderBy('jenis_barang.nama', 'ASC')
            ->get()->getResultArray();
    }
}
