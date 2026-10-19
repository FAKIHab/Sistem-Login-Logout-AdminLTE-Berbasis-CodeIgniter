<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Produk_controller extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        is_admin_logged_in();
        // Load model
        $this->load->model('produk_model');
        $this->load->model('produk_kategori_model');
        $this->load->model('produk_gambar_model');
    }

    public function index()
    {
        $data['title'] = 'Daftar Produk';
        $data['list_produk'] = $this->produk_model->get_all();
        $this->load->view('administrator/templates/header', $data);
        $this->load->view('administrator/templates/sidebar');
        $this->load->view('administrator/produk/index');
        $this->load->view('administrator/templates/footer');
    }

    public function tambah_produk()
    {
        $data['title'] = 'Tambah Produk';
        $this->form_validation->set_rules('nama_produk', 'Nama produk', 'required');
        $this->form_validation->set_rules('kategori_produk', 'Kategori', 'required');
        if ($this->form_validation->run() !== FALSE) {
            $this->__simpan_produk();
        } else {
            $data['list_kategori'] = $this->produk_kategori_model->get_all();
            $this->load->view('administrator/templates/header', $data);
            $this->load->view('administrator/templates/sidebar');
            $this->load->view('administrator/produk/tambah_produk', $data);
            $this->load->view('administrator/templates/footer');
        }
    }

    private function __simpan_produk()
    {
        $data = [
            'nama' => ucwords($this->input->post('nama_produk')),
            'categori_id' => $this->input->post('kategori_produk'),
            'harga' => $this->input->post('harga_produk'),
            'stok' => $this->input->post('stok_produk'),
            'deskripsi' => ucfirst($this->input->post('deskripsi_produk'))
        ];

        #simpan dan return id_produk yang disimpan
        $id_produk = $this->produk_model->tambah($data);
        # simpan data, trus lakukan upload gambar kebalikan id produk

        $count = count($_FILES['gambar_produk']['name']);
        if ($count > 0) {
            // jika ada gambar upload
            $this->__produk_gambar_upload($count, $id_produk);
        }
        $this->session->set_flashdata('message', '
            <div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:">
                    <use xlink:href="#check-circle-fill" />
                </svg>
                <div>
                    Berhasil menambahkan produk!!
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            ');

        redirect('admin/produk');
    }

    private function __produk_gambar_upload($count, $id_produk)
    {
        for ($i = 0; $i < $count; $i++) {
            if (!empty($_FILES['gambar_produk']['name'][$i])) {
                $_FILES['file']['name'] = $_FILES['gambar_produk']['name'][$i];
                $_FILES['file']['type'] = $_FILES['gambar_produk']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['gambar_produk']['tmp_name'][$i];
                $_FILES['file']['error'] = $_FILES['gambar_produk']['error'][$i];
                $_FILES['file']['size'] = $_FILES['gambar_produk']['size'][$i];

                $config['upload_path'] = 'uploads/produk/';
                $config['allowed_types'] = 'jpg|jpeg|png|gif';
                $config['max_size'] = '5000';
                // $config['file_name'] = $_FILES['gambar_produk']['name'][$i];
                $config['file_name'] = "produk-" . $id_produk . '-' . $i;

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('file')) {
                    $uploadData = $this->upload->data();
                    $filename = $uploadData['file_name'];
                    $data = [
                        'nama_gambar' => $filename,
                        'produk_id' => $id_produk
                    ];
                    $this->produk_gambar_model->tambah($data);
                }
            }
        }
    }

    /* =====================================================================
     * UBAH PRODUK  (+ TUGAS: gambar dapat diganti ketika klik tombol Ubah)
     * ===================================================================== */
    public function ubah_produk($id)
    {
        // check dulu apakah id dengan produk ada?
        $produk = $this->produk_model->get_by_id($id);
        if ($produk) {

            // [TUGAS] tombol "Hapus" gambar: admin/produk/ubah/{id_produk}?hapus_gambar={id_gambar}
            // (memakai route ubah yang sudah ada, jadi tidak perlu route baru)
            if ($this->input->get('hapus_gambar')) {
                $dihapus = $this->__hapus_satu_gambar($this->input->get('hapus_gambar'), $id);
                $this->session->set_flashdata(
                    'message',
                    $dihapus
                        ? $this->__pesan('success', 'Gambar berhasil dihapus!!')
                        : $this->__pesan('danger', 'Gambar tidak ditemukan!!')
                );
                redirect('admin/produk/ubah/' . $id);
            }

            $this->form_validation->set_rules('nama_produk', 'Nama produk', 'required');
            $this->form_validation->set_rules('kategori_produk', 'Kategori', 'required');
            if ($this->form_validation->run() !== FALSE) {
                $this->__ubah_produk($id);
            } else {
                $data['title'] = 'Ubah produk';
                $data['produk'] = $produk;
                $data['list_kategori'] = $this->produk_kategori_model->get_all();
                $data['gambar_model'] = $this->produk_gambar_model;
                $data['list_gambar'] = $this->produk_gambar_model->get_by_produk_id($id);
                $this->load->view('administrator/templates/header', $data);
                $this->load->view('administrator/templates/sidebar');
                $this->load->view('administrator/produk/ubah_produk', $data);
                $this->load->view('administrator/templates/footer');
            }
        } else {
            $this->session->set_flashdata('message', '
            <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:">
                    <use xlink:href="#exclamation-triangle-fill" />
                </svg>
                <div>
                    produk tidak ditemukan!!
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            ');
            redirect('admin/produk');
        }
    }

    private function __ubah_produk($id)
    {
        $data = [
            'nama' => ucwords($this->input->post('nama_produk')),
            'categori_id' => $this->input->post('kategori_produk'),
            'harga' => $this->input->post('harga_produk'),
            'stok' => $this->input->post('stok_produk'),
            'deskripsi' => ucfirst($this->input->post('deskripsi_produk'))
        ];

        $ubah = $this->produk_model->ubah($data, $id);

        // [TUGAS] ganti gambar jika user memilih file baru
        $gambar_berhasil = 0;
        $gambar_gagal = [];
        if (!empty($_FILES['gambar_produk']['name'][0])) {
            list($gambar_berhasil, $gambar_gagal) = $this->__ganti_gambar($id, (bool) $this->input->post('ganti_semua'));
        }

        // $ubah bernilai false jika data produk tidak berubah (hanya gambar yang diganti),
        // maka keberhasilan gambar juga dihitung
        if ($ubah || $gambar_berhasil > 0) {
            $this->session->set_flashdata('message', '
            <div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:">
                    <use xlink:href="#check-circle-fill" />
                </svg>
                <div>
                    Berhasil mengubah produk!!
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            ');
        } else {
            $this->session->set_flashdata('message', '
            <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:">
                    <use xlink:href="#exclamation-triangle-fill" />
                </svg>
                <div>
                    Gagal mengubah produk!!
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            ');
        }

        // ada gambar yang gagal diunggah -> kembali ke form ubah dan tampilkan alasannya
        if (!empty($gambar_gagal)) {
            $this->session->set_flashdata('message', $this->__pesan(
                'danger',
                'Data produk tersimpan, tetapi ada gambar yang gagal diunggah:<br>' . implode('<br>', $gambar_gagal)
            ));
            redirect('admin/produk/ubah/' . $id);
        }

        redirect('admin/produk');
    }

    /* [TUGAS] unggah gambar baru; jika $ganti_semua true, gambar lama dihapus
     * (hanya jika minimal satu gambar baru berhasil diunggah)
     * return: [jumlah_berhasil, daftar_pesan_gagal] */
    private function __ganti_gambar($id_produk, $ganti_semua)
    {
        $gambar_lama = $this->produk_gambar_model->get_by_produk_id($id_produk);
        $berhasil = 0;
        $gagal = [];

        $this->load->library('upload');
        $files = $_FILES['gambar_produk'];
        $count = count($files['name']);

        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_NO_FILE) {
                continue;
            }

            $_FILES['file']['name'] = $files['name'][$i];
            $_FILES['file']['type'] = $files['type'][$i];
            $_FILES['file']['tmp_name'] = $files['tmp_name'][$i];
            $_FILES['file']['error'] = $files['error'][$i];
            $_FILES['file']['size'] = $files['size'][$i];

            // initialize() dipanggil tiap perulangan supaya konfigurasi selalu terbaca
            $this->upload->initialize([
                'upload_path'   => 'uploads/produk/',
                'allowed_types' => 'jpg|jpeg|png|gif',
                'max_size'      => '5000',
                'file_name'     => 'produk-' . $id_produk . '-' . time() . '-' . $i,
            ]);

            if ($this->upload->do_upload('file')) {
                $this->produk_gambar_model->tambah([
                    'nama_gambar' => $this->upload->data('file_name'),
                    'produk_id'   => $id_produk,
                ]);
                $berhasil++;
            } else {
                $gagal[] = html_escape($files['name'][$i]) . ' — ' . strip_tags($this->upload->display_errors('', ''));
            }
        }

        if ($berhasil > 0 && $ganti_semua) {
            foreach ($gambar_lama as $gambar) {
                $this->produk_gambar_model->hapus($gambar['id_gambar']);
                $path = './uploads/produk/' . $gambar['nama_gambar'];
                if (is_file($path)) {
                    unlink($path);
                }
            }
        }

        return [$berhasil, $gagal];
    }

    /* [TUGAS] dipanggil dari link Hapus pada gambar
     * URL: admin/produk/hapus_gambar/{id_gambar}/{id_produk}
     * (butuh route di routes.php, lihat catatan) */
    public function hapus_gambar($id_gambar, $id_produk)
    {
        if (!$this->produk_model->get_by_id($id_produk)) {
            $this->session->set_flashdata('message', $this->__pesan('danger', 'Produk tidak ditemukan!!'));
            redirect('admin/produk');
        }

        $dihapus = $this->__hapus_satu_gambar($id_gambar, $id_produk);
        $this->session->set_flashdata(
            'message',
            $dihapus
                ? $this->__pesan('success', 'Gambar berhasil dihapus!!')
                : $this->__pesan('danger', 'Gambar tidak ditemukan!!')
        );
        redirect('admin/produk/ubah/' . $id_produk);
    }

    /* [TUGAS] hapus satu gambar (database + file), hanya milik produk yang dibuka */
    private function __hapus_satu_gambar($id_gambar, $id_produk)
    {
        foreach ($this->produk_gambar_model->get_by_produk_id($id_produk) as $gambar) {
            if ((int) $gambar['id_gambar'] === (int) $id_gambar) {
                $this->produk_gambar_model->hapus($gambar['id_gambar']);
                $path = './uploads/produk/' . $gambar['nama_gambar'];
                if (is_file($path)) {
                    unlink($path);
                }
                return true;
            }
        }
        return false;
    }

    /* alert dengan gaya yang sama seperti pesan lainnya */
    private function __pesan($tipe, $teks)
    {
        $icon  = ($tipe === 'success') ? 'check-circle-fill' : 'exclamation-triangle-fill';
        $label = ($tipe === 'success') ? 'Success:' : 'Danger:';
        return '
            <div class="alert alert-' . $tipe . ' d-flex align-items-center alert-dismissible fade show" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="' . $label . '">
                    <use xlink:href="#' . $icon . '" />
                </svg>
                <div>
                    ' . $teks . '
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            ';
    }

    public function hapus_produk($id)
    {
        // check apakah ada produk
        $produk = $this->produk_model->get_by_id($id);
        if ($produk) {
            // ambil data gambar
            $list_gambar = $this->produk_gambar_model->get_by_produk_id($id);
            foreach ($list_gambar as $gambar) {
                $id_gambar = $gambar['id_gambar'];
                $nama_gambar = $gambar['nama_gambar'];
                // hapus gambar di database
                $this->produk_gambar_model->hapus($id_gambar);
                // hapus gambar di file
                $path = './uploads/produk/' . $nama_gambar;
                unlink($path);
            }
            // hapus produk di database
            $this->produk_model->hapus($id);
            $this->session->set_flashdata('message', '
            <div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:">
                    <use xlink:href="#check-circle-fill" />
                </svg>
                <div>
                    Berhasil menghapus produk!!
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            ');
            redirect('admin/produk');
        } else {
            $this->session->set_flashdata('message', '
            <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:">
                    <use xlink:href="#exclamation-triangle-fill" />
                </svg>
                <div>
                    produk tidak ditemukan!!
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            ');
            redirect('admin/produk');
        }
    }
}