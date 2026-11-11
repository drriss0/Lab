<?php

namespace App\Models;

use CodeIgniter\Model;

class JurusanModel extends Model
{
    protected $table            = 'jurusan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['nama_jurusan', 'created_at'];

    // Dates
    protected $useTimestamps = false; 

    public function getAllJurusan()
    {
        return $this->orderBy('nama_jurusan', 'ASC')->findAll();
    }
}
