<?php

namespace App\Models;
use CodeIgniter\Model;

class PegawaiModel extends Model
{
    protected $table = 'pegawai';
    protected $primaryKey = 'nip';
    protected $allowedFields = ['nip', 'nama', 'jabatan'];
}
