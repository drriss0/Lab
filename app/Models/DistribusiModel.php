<?php

namespace App\Models;

use CodeIgniter\Model;

class DistribusiModel extends Model
{
    protected $table         = 'distribusi';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'id_barang', 'no_unit', 'id_ruangan', 'jumlah', 'tanggal',
        'penanggung_jawab', 'kondisi', 'status', 'tujuan_distribusi', 'keterangan',
        'alokasi_ke_ruangan', 'tanggal_alokasi', 'id_asal_distribusi'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil semua distribusi untuk satu barang, lengkap dengan nama ruangan.
     */
    public function getByBarang(int $idBarang, array $filters = []): array
    {
        $builder = $this->db->table('distribusi d')
            ->select('d.*, r.nama_ruangan, r.status_ruangan, r.penanggung_jawab AS ruangan_pj, r2.nama_ruangan AS nama_ruangan_alokasi')
            ->join('ruangan r', 'r.id = d.id_ruangan', 'left')
            ->join('ruangan r2', 'r2.id = d.alokasi_ke_ruangan', 'left')
            ->where('d.id_barang', $idBarang);

        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $builder->groupStart()
                    ->like('r.nama_ruangan', $q)
                    ->orLike('d.penanggung_jawab', $q)
                    ->orLike('d.tujuan_distribusi', $q)
                    ->groupEnd();
        }
        
        if (!empty($filters['tanggal_mulai'])) {
            $builder->where('d.tanggal >=', $filters['tanggal_mulai']);
        }
        
        if (!empty($filters['tanggal_akhir'])) {
            $builder->where('d.tanggal <=', $filters['tanggal_akhir']);
        }
        
        if (!empty($filters['status']) && $filters['status'] !== 'semua') {
            $builder->where('d.status', $filters['status']);
        }
        
        if (!empty($filters['ruangan'])) {
            $builder->where('d.id_ruangan', $filters['ruangan']);
        }

        return $builder->orderBy('d.tanggal', 'DESC')
            ->orderBy('r.nama_ruangan', 'ASC')
            ->orderBy('d.tujuan_distribusi', 'ASC')
            ->orderBy('d.no_unit', 'ASC')
            ->orderBy('d.id', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Ambil barang di suatu ruangan: aktif (digunakan) + riwayat alokasi (ditarik dengan alokasi_ke_ruangan).
     */
    public function getByRuangan(int $idRuangan): array
    {
        return $this->db->table('distribusi d')
            ->select('b.kode_barang, b.nama_barang, b.merk, b.kategori, b.id_kategori AS jenis_barang, b.foto_barang,
                      d.jumlah, d.tanggal, d.penanggung_jawab, d.status, d.kondisi, d.keterangan,
                      d.id AS id_distribusi, d.id_barang, d.no_unit, d.id_ruangan,
                      d.alokasi_ke_ruangan, d.tanggal_alokasi, d.updated_at,
                      r2.nama_ruangan AS nama_ruangan_alokasi')
            ->join('barang b', 'b.id = d.id_barang')
            ->join('ruangan r2', 'r2.id = d.alokasi_ke_ruangan', 'left')
            ->where('d.id_ruangan', $idRuangan)
            ->groupStart()
                ->where('d.status', 'digunakan')
                ->orWhere('d.status', 'maintenance')
                // ditarik & digudang hanya untuk riwayat (tampil jika "Tampilkan riwayat" dicentang di view)
                ->orWhereIn('d.status', ['ditarik', 'digudang'])
                // pelaporan_rusak TIDAK masuk sini — barang rusak muncul di halaman Pelaporan Rusak saja
                // dan akan kembali ke sini (status='digunakan') setelah selesai diperbaiki
            ->groupEnd()
            ->orderBy('b.nama_barang', 'ASC')
            ->orderBy('d.no_unit', 'ASC')
            ->orderBy('d.id', 'ASC')
            ->get()->getResultArray();
    }


    /**
     * Distribusi terbanyak per lokasi (untuk halaman detail barang).
     */
    public function getLokasiTerbanyak(int $idBarang, int $limit = 5): array
    {
        return $this->db->table('distribusi d')
            ->select('r.nama_ruangan, d.tujuan_distribusi, SUM(d.jumlah) AS total_jumlah')
            ->join('ruangan r', 'r.id = d.id_ruangan', 'left')
            ->where('d.id_barang', $idBarang)
            ->where('d.status', 'digunakan')
            ->groupBy('d.id_ruangan, d.tujuan_distribusi')
            ->orderBy('total_jumlah', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }

    /**
     * Total jumlah barang yang sedang digunakan (distribusi aktif).
     */
    public function getTotalDigunakan(int $idBarang): int
    {
        $row = $this->db->table('distribusi')
            ->selectSum('jumlah')
            ->where('id_barang', $idBarang)
            ->where('status', 'digunakan')
            ->get()->getRowArray();
        return (int)($row['jumlah'] ?? 0);
    }

    /**
     * Ambil semua ruangan untuk dropdown form.
     */
    public function getRuangan(): array
    {
        return $this->db->table('ruangan')
            ->orderBy('nama_ruangan', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Ambil daftar barang yang ada pool distribusi untuk peminjaman
     * (status 'digunakan' dengan tujuan_distribusi = 'peminjaman').
     * Mengembalikan stok tersedia pinjam = stok_peminjaman barang tsb.
     */
    public function getPoolPeminjaman(): array
    {
        return $this->db->table('barang b')
            ->select('b.*, k.nama AS nama_kategori, 
                      (SELECT d.penanggung_jawab FROM distribusi d 
                       WHERE d.id_barang = b.id 
                         AND d.tujuan_distribusi = "peminjaman" 
                         AND d.status = "digunakan" 
                       LIMIT 1) AS penanggung_jawab')
            ->join('jenis_barang k', 'k.id = b.id_kategori', 'left')
            ->where('(b.stok_peminjaman > 0 OR b.jumlah_dipinjam > 0)', null, false)
            ->orderBy('b.nama_barang', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Ambil rincian unit barang yang ada di pool peminjaman (mirip detail ruangan).
     */
    public function getPoolPeminjamanDetail(int $idBarang): array
    {
        return $this->db->table('distribusi d')
            ->select('b.kode_barang, b.nama_barang, b.merk, b.kategori, b.id_kategori AS nama_kategori, b.foto_barang,
                      d.jumlah, d.tanggal, d.penanggung_jawab, d.status, d.kondisi, d.keterangan,
                      d.id AS id_distribusi, d.id_barang, d.no_unit, d.id_ruangan,
                      d.alokasi_ke_ruangan, d.tanggal_alokasi, d.updated_at')
            ->join('barang b', 'b.id = d.id_barang')
            ->where('d.id_barang', $idBarang)
            ->where('d.tujuan_distribusi', 'peminjaman')
            ->groupStart()
                ->where('d.status', 'digunakan')
                ->orWhere('d.status', 'maintenance')
                ->orWhereIn('d.status', ['ditarik', 'digudang', 'pelaporan_rusak'])
            ->groupEnd()
            ->orderBy('b.nama_barang', 'ASC')
            ->orderBy('d.no_unit', 'ASC')
            ->orderBy('d.id', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Tarik barang kembali ke gudang (update status distribusi).
     */
    public function tarikBarang(int $id): bool
    {
        return $this->db->table('distribusi')
            ->where('id', $id)
            ->update([
                'status' => 'digudang',
            ]);
    }


}
