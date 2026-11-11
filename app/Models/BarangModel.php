<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table         = 'barang';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'kode_barang', 'nama_barang', 'kategori', 'id_kategori', 'merk', 'spesifikasi',
        'stok_awal', 'stok_gudang', 'jumlah_digunakan', 'stok_peminjaman', 'jumlah_dipinjam',
        'jumlah_rusak', 'tanggal_perolehan', 'foto_barang'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil semua barang beserta nama kategori, dengan filter dan pencarian.
     */
    public function getAll(?string $keyword = null, ?string $kategori = null, ?string $statusStok = null): array
    {
        $builder = $this->db->table('barang b')
            ->select('b.*, COALESCE(k.nama, b.id_kategori) AS nama_kategori')
            ->join('jenis_barang k', 'k.id = b.id_kategori', 'left');

        if ($keyword) {
            $builder->groupStart()
                ->like('b.nama_barang', $keyword)
                ->orLike('b.kode_barang', $keyword)
                ->orLike('b.id_kategori', $keyword)
                ->groupEnd();
        }

        if ($kategori) {
            $builder->where('b.kategori', $kategori);
        }

        $builder->orderBy('b.kode_barang', 'ASC');

        $rows = $builder->get()->getResultArray();

        // Filter status stok (dilakukan di PHP karena kalkulasi dinamis)
        if ($statusStok) {
            $rows = array_values(array_filter($rows, function($b) use ($statusStok) {
                return strtolower(self::getStatusStok($b)) === strtolower($statusStok);
            }));
        }

        return $rows;
    }

    /**
     * Ambil satu barang lengkap dengan nama kategori.
     */
    public function getDetail(int $id): ?array
    {
        $result = $this->db->table('barang b')
            ->select('b.*, b.id_kategori AS jenis_barang')
            ->where('b.id', $id)
            ->get()
            ->getRowArray();

        return $result ?: null;
    }

    /**
     * Statistik ringkasan inventaris untuk dashboard.
     */
    public function getStats(): array
    {
        $row = $this->db->table('barang')
            ->select('
                COUNT(*) AS total_jenis,
                SUM(stok_gudang) AS total_gudang,
                SUM(jumlah_digunakan) AS total_digunakan,
                SUM(stok_peminjaman) AS total_peminjaman,
                SUM(jumlah_dipinjam) AS total_dipinjam,
                SUM(jumlah_rusak) AS total_rusak,
                SUM(stok_gudang + jumlah_digunakan + stok_peminjaman + jumlah_dipinjam) AS total_inventaris
            ')
            ->get()
            ->getRowArray();

        $totalInventaris = (int)($row['total_inventaris'] ?? 0);
        
        // Status Ketersediaan: Total barang yang masih nganggur (di gudang + pool peminjaman)
        $totalTersedia = (int)($row['total_gudang'] ?? 0) + (int)($row['total_peminjaman'] ?? 0);
        
        // Kondisi Aset: Total barang - Total rusak (asumsi sisanya kondisi baik)
        $totalKondisiBaik = $totalInventaris - (int)($row['total_rusak'] ?? 0);
        
        // Status Distribusi: Total barang yang sedang dipakai (di ruangan + pool peminjaman + sedang dipinjam)
        $totalDistribusi = (int)($row['total_digunakan'] ?? 0) + (int)($row['total_peminjaman'] ?? 0) + (int)($row['total_dipinjam'] ?? 0);

        return [
            'total_inventaris'  => $totalInventaris,
            'status_tersedia'   => $totalTersedia,
            'total_rusak'       => (int)($row['total_rusak'] ?? 0),
            'status_distribusi' => $totalDistribusi,
        ];
    }

    /**
     * Tentukan status stok barang: Aman / Sedikit / Habis.
     */
    public static function getStatusStok(array $barang): string
    {
        $gudang       = (int)$barang['stok_gudang'];
        $digunakan    = (int)$barang['jumlah_digunakan'];
        $peminjaman   = (int)($barang['stok_peminjaman'] ?? 0);
        $dipinjam     = (int)$barang['jumlah_dipinjam'];
        $total        = $gudang + $digunakan + $peminjaman + $dipinjam;

        if ($gudang === 0) return 'Habis';
        if ($total > 0 && ($gudang / $total) < 0.3) return 'Sedikit';
        return 'Aman';
    }

    /**
     * Ambil semua kategori barang (Elektronik, Furnitur, dll) untuk filter dashboard.
     */
    public function getKategori(): array
    {
        $kategoriUtama = $this->db->table('kategori_utama')
            ->select('nama_kategori AS nama')
            ->orderBy('nama_kategori', 'ASC')
            ->get()
            ->getResultArray();

        return $kategoriUtama;
    }

}
