<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Produk_gambar_model extends CI_Model
{

    private $_table = "produk_gambar";

    public function tambah($data)
    {
        $this->db->insert($this->_table, $data);
    }

    public function get_by_produk_id($id)
    {
        $this->db->where('produk_id', $id);
        $query = $this->db->get($this->_table);
        return $query->result_array();
    }

    // BARU: ambil satu gambar berdasarkan id_gambar (dipakai tombol Hapus per gambar)
    public function get_by_id($id)
    {
        $this->db->where('id_gambar', $id);
        $query = $this->db->get($this->_table);
        return $query->row_array();
    }

    public function hapus($id)
    {
        $this->db->delete($this->_table, array('id_gambar' => $id));
    }
}