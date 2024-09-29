<ol class="breadcrumb pull-right">
    <li class="breadcrumb-item"><a href="<?= base_url('') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('master/produk_supplier') ?>">Data Produk</a></li>
    <li class="breadcrumb-item">Tambah Produk Supplier</li>
</ol>

<div class='row'>
    <div class="col-md-12">
        <h1 class="page-header">
            Form Tambah Produk Supplier
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
                    <label for="">Nama Supplier : <code>*</code></label>
                    <select name="suplier_id" id="suplier_id" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <?php
                        foreach ($suplier as $key => $value) { ?>
                            <option value="<?= $value->id ?>" <?= set_select('role', $value->id) ?>><?= $value->name . " - " . $value->name_company ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="">Produk : <code>*</code></label>
                    <input type="text" name="product_name" id="product_name" class="form-control" value="<?= set_value('product_name') ?>" autocomplete="off">
                    <?= form_error('product_name', '<small class="text-danger">', '</small>') ?>
                </div>


                <div class="form-group">
                    <label for="">Harga : <code>*</code></label>
                    <input type="number" name="harga" id="harga" class="form-control" value="<?= set_value('harga') ?>" autocomplete="off">
                    <?= form_error('harga', '<small class="text-danger">', '</small>') ?>
                </div>

                <div class="form-group">
                    <label for="">Satuan : <code>*</code></label>
                    <input type="text" name="satuan" id="satuan" class="form-control" value="<?= set_value('satuan') ?>" autocomplete="off">
                    <?= form_error('satuan', '<small class="text-danger">', '</small>') ?>
                </div>


                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="<?= site_url('master/produk') ?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>



                <?= form_close() ?>

            </div>
        </div>
    </div>
</div>