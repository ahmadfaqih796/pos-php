<ol class="breadcrumb pull-right">
    <li class="breadcrumb-item"><a href="<?= base_url('') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('master/produk') ?>">Data Produk</a></li>
    <li class="breadcrumb-item">Tambah Produk</li>
</ol>

<div class='row'>
    <div class="col-md-12">
        <h1 class="page-header">
            Form Tambah Produk
        </h1>
    </div>
</div>

<?php if ($this->session->flashdata('msg')) { ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Gagal!</strong> <?php echo $this->session->flashdata('msg') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php } ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Form</h4>
            </div>
            <div class="panel-body">
                <?= form_open('') ?>

                <input type="hidden" name="id">
                <div class="form-group">
                    <label for="">Nama Produk : <code>*</code></label>
                    <input type="text" name="product_name" id="product_name" class="form-control" value="<?= set_value('product_name') ?>" autocomplete="off">
                    <?= form_error('product_name', '<small class="text-danger">', '</small>') ?>
                </div>

                <div class="form-group">
                    <label for="">Harga Produk : <code>*</code></label>
                    <input type="number" name="price" id="price" class="form-control" value="<?= set_value('price') ?>" autocomplete="off">
                    <?= form_error('price', '<small class="text-danger">', '</small>') ?>
                </div>

                <div class="form-group">
                    <label for="">Stok : <code>*</code></label>
                    <input type="number" name="stock" id="stock" class="form-control" value="<?= set_value('stock') ?>" autocomplete="off">
                    <?= form_error('stock', '<small class="text-danger">', '</small>') ?>
                </div>


                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= site_url('master/produk') ?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>



                <?= form_close() ?>

            </div>
        </div>
    </div>
</div>