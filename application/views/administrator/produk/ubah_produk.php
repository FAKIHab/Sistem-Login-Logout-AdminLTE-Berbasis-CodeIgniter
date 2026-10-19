<?php
// File: application/views/administrator/produk/ubah_produk.php
// $list_gambar dikirim dari controller; jika belum, ambil lewat $gambar_model
if (!isset($list_gambar)) {
    $list_gambar = $gambar_model->get_by_produk_id($produk['id_produk']);
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Ubah Produk</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="<?= base_url('admin/produk') ?>">Produk</a></li>
            <li class="breadcrumb-item active">Ubah Produk</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">

              <?php if ($this->session->flashdata('message')) : ?>
                <?= $this->session->flashdata('message') ?>
              <?php endif ?>

              <form method="post" enctype="multipart/form-data">

                <div class="mb-3">
                  <label for="nama_produk" class="form-label">Nama produk</label>
                  <input type="text" class="form-control" name="nama_produk" id="nama_produk"
                         value="<?= set_value('nama_produk', $produk['nama']) ?>">
                  <small class="text-danger"><em><?= form_error('nama_produk') ?></em></small>
                </div>

                <div class="mb-3">
                  <label for="kategori_produk" class="form-label">Kategori produk</label>
                  <select class="form-select form-control" name="kategori_produk" id="kategori_produk">
                    <option value="">-- pilih kategori --</option>
                    <?php foreach ($list_kategori as $kategori) : ?>
                      <option value="<?= $kategori['id_kategori'] ?>"
                        <?= ((int) $produk['categori_id'] === (int) $kategori['id_kategori']) ? 'selected' : '' ?>>
                        <?= html_escape($kategori['nama']) ?>
                      </option>
                    <?php endforeach ?>
                  </select>
                  <small class="text-danger"><em><?= form_error('kategori_produk') ?></em></small>
                </div>

                <div class="mb-3">
                  <label for="harga_produk" class="form-label">Harga</label>
                  <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="number" name="harga_produk" id="harga_produk" class="form-control" min="0"
                           value="<?= set_value('harga_produk', $produk['harga']) ?>">
                  </div>
                  <small class="text-danger"><em><?= form_error('harga_produk') ?></em></small>
                </div>

                <div class="mb-3">
                  <label for="stok_produk" class="form-label">Stok produk</label>
                  <input type="number" class="form-control" name="stok_produk" id="stok_produk" min="0"
                         value="<?= set_value('stok_produk', $produk['stok']) ?>">
                  <small class="text-danger"><em><?= form_error('stok_produk') ?></em></small>
                </div>

                <!-- ===== GAMBAR PRODUK (BISA DIGANTI) ===== -->
                <div class="mb-3">
                  <label for="gambar_produk" class="form-label">Gambar Produk</label>
                  <input class="form-control" type="file" id="gambar_produk" name="gambar_produk[]"
                         accept=".jpg,.jpeg,.png,.gif" multiple>
                  <small class="text-muted">Format: JPG, PNG, GIF. Maks 5 MB per gambar. Kosongkan jika tidak ingin menambah/mengganti gambar.</small>

                  <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" id="ganti_semua" name="ganti_semua" value="1">
                    <label class="form-check-label" for="ganti_semua">
                      Ganti semua gambar lama dengan gambar yang baru dipilih
                      (jika tidak dicentang, gambar baru hanya ditambahkan)
                    </label>
                  </div>

                  <!-- Preview gambar baru sebelum disimpan -->
                  <div class="row mt-2" id="preview-baru"></div>
                </div>

                <!-- Gambar yang sudah ada -->
                <div class="row mb-3">
                  <?php foreach ($list_gambar as $gambar) : ?>
                    <div class="col-lg-3">
                      <div class="card" style="width: 18rem;">
                        <img src="<?= base_url('uploads/produk/') . html_escape($gambar['nama_gambar']) ?>" class="card-img-top" alt="Gambar produk">
                        <div class="card-body d-grid">
                          <!-- URL: admin/produk/hapus_gambar/{id_gambar}/{id_produk} -->
                          <a href="<?= base_url('admin/produk/hapus_gambar/' . $gambar['id_gambar'] . '/' . $produk['id_produk']) ?>"
                             class="btn btn-danger"
                             onclick="return confirm('Hapus gambar ini? File gambar juga akan dihapus dari server.');">Hapus</a>
                        </div>
                      </div>
                    </div>
                  <?php endforeach ?>
                  <?php if (empty($list_gambar)) : ?>
                    <div class="col-12"><p class="text-muted">Belum ada gambar untuk produk ini.</p></div>
                  <?php endif ?>
                </div>

                <div class="mb-3">
                  <label for="deskripsi_produk" class="form-label">Deskripsi</label>
                  <textarea name="deskripsi_produk" id="deskripsi_produk" cols="30" rows="10"
                            class="form-control"><?= set_value('deskripsi_produk', $produk['deskripsi']) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Ubah</button>
                <a href="<?= base_url('admin/produk') ?>" class="btn btn-danger">Kembali</a>
              </form>

            </div>
          </div>
        </div>
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<script>
// Preview gambar baru sebelum disimpan
document.getElementById('gambar_produk').addEventListener('change', function () {
  var wadah = document.getElementById('preview-baru');
  wadah.innerHTML = '';
  Array.from(this.files).forEach(function (file) {
    if (!file.type.startsWith('image/')) return;
    var col = document.createElement('div');
    col.className = 'col-6 col-md-3 mb-2';
    var img = document.createElement('img');
    img.className = 'img-fluid img-thumbnail';
    img.src = URL.createObjectURL(file);
    col.appendChild(img);
    wadah.appendChild(col);
  });
});
</script>