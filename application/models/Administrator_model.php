<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Administrator_model extends CI_Model
{

    private $_table = "administrator";

    public function check_login($username, $password)
    {
        $this->db->where('username', $username);
        $this->db->where('password', $password);
        return $this->db->get($this->_table)->row_array();
    }
}
