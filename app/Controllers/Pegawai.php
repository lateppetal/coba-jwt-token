<?php

namespace App\Controllers;

use App\Models\PegawaiModel;
use CodeIgniter\RESTful\ResourceController;

class Pegawai extends ResourceController
{
    protected $modelName = 'App\Models\PegawaiModel';
    protected $format = 'json';

    public function index() { return $this->respond($this->model->findAll()); }

    public function show($nip = null) {
        $data = $this->model->find($nip);
        return $data ? $this->respond($data) : $this->failNotFound('Data tidak ditemukan');
    }

    public function create() {
        $data = $this->request->getJSON(true);
        $this->model->insert($data);
        return $this->respondCreated($data);
    }

    public function update($nim = null) {
        $data = $this->request->getJSON(true);
        $this->model->update($nim, $data);
        return $this->respond($data);
    }

    public function delete($nim = null) {
        $this->model->delete($nim);
        return $this->respondDeleted(['nim' => $nim]);
    }
}
