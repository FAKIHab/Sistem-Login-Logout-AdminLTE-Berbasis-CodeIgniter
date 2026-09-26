<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
    public function index() {
        // Halaman default diarahkan ke area administrator
        // (jika belum login, otomatis diarahkan ke admin/login)
        redirect('admin');
    }
}
