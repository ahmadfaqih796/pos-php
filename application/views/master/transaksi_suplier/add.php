<ol class="breadcrumb pull-right">
   <li class="breadcrumb-item"><a href="<?= base_url('') ?>">Dashboard</a></li>
   <li class="breadcrumb-item"><a href="<?= base_url('master/transaksi_suplier') ?>">Data Suplier</a></li>
   <li class="breadcrumb-item">Tambah Transaksi Suplier</li>
</ol>

<div class='row'>
   <div class="col-md-12">
      <h1 class="page-header">
         Form Tambah Transaksi Suplier
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
               <select name="n_suplier" id="n_suplier" class="form-control" required>
                  <option value="">-- Pilih --</option>
                  <?php
                  foreach ($suplier as $key => $value) { ?>
                     <option value="<?= $value->name ?>"><?= $value->name . " - " . $value->name_company ?></option>
                  <?php } ?>
               </select>
            </div>

            <!-- <div class="form-group">
               <label for="">Nama Produk : <code>*</code></label>
               <select name="product_name" id="product_name" class="form-control" required>
                  <option value="">-- Pilih --</option>
                  <?php
                  foreach ($product as $key => $value) { ?>
                     <option value="<?= $value->product_name ?>"><?= $value->product_name ?></option>
                  <?php } ?>
               </select>
            </div> -->

            <div class="form-group">
               <label for="">Jenis Produk : <code>*</code></label>
               <input type="text" name="jenis_produk" id="jenis_produk" class="form-control" value="<?= set_value('jenis_produk') ?>" autocomplete="off" readonly>
            </div>

            <!-- <div class="form-group">
               <label for="">Harga Produk : <code>*</code></label>
               <input type="text" name="harga_produk" id="harga_produk" class="form-control" value="<?= set_value('harga_produk') ?>" autocomplete="off" readonly>
            </div> -->

            <div class="form-group">
               <label for="">Harga Produk : <code>*</code></label>
               <input type="number" name="h_product" id="h_product" class="form-control" value="<?= set_value('h_product') ?>" autocomplete="off" readonly>
               <?= form_error('h_product', '<small class="text-danger">', '</small>') ?>
            </div>

            <div class="form-group">
               <label for="">QTY : <code>*</code></label>
               <input type="number" name="qty" id="qty" class="form-control" value="<?= set_value('qty') ?>" autocomplete="off">
               <?= form_error('qty', '<small class="text-danger">', '</small>') ?>
            </div>

            <div class="form-group">
               <label for="">Total : <code>*</code></label>
               <input type="number" name="t_harga" id="t_harga" class="form-control" value="<?= set_value('t_harga') ?>" autocomplete="off" readonly>
               <?= form_error('t_harga', '<small class="text-danger">', '</small>') ?>
            </div>

            <div class="form-group">
               <label for="">Bayar : <code>*</code></label>
               <input type="number" name="bayar" id="bayar" class="form-control" value="<?= set_value('bayar') ?>" autocomplete="off" required>
               <?= form_error('bayar', '<small class="text-danger">', '</small>') ?>
            </div>

            <div class="form-group">
               <label for="">Kembalian : <code>*</code></label>
               <input type="text" name="kembalian" id="kembalian" class="form-control" value="<?= set_value('kembalian') ?>" autocomplete="off" readonly>
               <?= form_error('kembalian', '<small class="text-danger">', '</small>') ?>
            </div>

            <!-- <input type="hidden" name="id_transaksi" id="id_transaksi"> -->
            <input type="hidden" name="created_at" value="<?= date('Y-m-d H:i:s') ?>">
            <button type="submit" name="simpan" id="simpan" class="btn btn-primary">Bayar</button>
            <a href="<?= site_url('master/transaksi_suplier') ?>" class="btn btn-danger"><i class="fa fa-arrow-left"></i> Kembali</a>

            <?= form_close() ?>

         </div>
      </div>
   </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   $(document).ready(function() {

      $('#n_suplier').change(function() {
         var n_suplier = $(this).val();
         if (n_suplier != '') {
            $.ajax({
               url: "<?= site_url('master/transaksi_suplier/getSupplierDetail') ?>",
               method: "POST",
               data: {
                  n_suplier: n_suplier
               },
               dataType: "json",
               success: function(data) {
                  if (data.status == 'success') {
                     // console.log("sssssssss", data)
                     $('#jenis_produk').val(data.data.jenis_supplier);
                     $('#h_product').val(data.data.harga_supplier);
                     // calculateTotal();
                  } else {
                     // $('#harga_produk').val('');
                  }
               },
               error: function(jqXHR, textStatus, errorThrown) {
                  console.log("error jqXHR : ", jqXHR)
                  console.log("error textStatus : ", textStatus)
                  console.log("error errorThrown : ", errorThrown)
               }
            });
         } else {
            $('#h_product').val('');
         }
      });

      $('#product_name').change(function() {
         var product_name = $(this).val();
         if (product_name != '') {
            $.ajax({
               url: "<?= site_url('master/transaksi_suplier/getProductDetail') ?>",
               method: "POST",
               data: {
                  product_name: product_name
               },
               dataType: "json",
               success: function(data) {
                  if (data.status == 'success') {
                     $('#harga_produk').val(data.data.price);
                     calculateTotal();
                  } else {
                     $('#harga_produk').val('');
                  }
               },
               error: function(jqXHR, textStatus, errorThrown) {
                  console.log("error jqXHR : ", jqXHR)
                  console.log("error textStatus : ", textStatus)
                  console.log("error errorThrown : ", errorThrown)
               }
            });
         } else {
            $('#h_product').val('');
         }
      });



      $('#qty').on('input', function() {
         calculateTotal();
      });
      $('#h_product').on('input', function() {
         calculateTotal();
      });

      $('#bayar').on('input', function() {
         calculateKembalian();
      });

      function calculateTotal() {
         var h_product = parseFloat($('#h_product').val());
         var qty = parseFloat($('#qty').val());
         if (!isNaN(h_product) && !isNaN(qty)) {
            var total = h_product * qty;
            $('#t_harga').val(total);
         } else {
            $('#t_harga').val('');
         }
         disableButton();
      }

      function calculateKembalian() {
         var bayar = parseFloat($('#bayar').val());
         var total = parseFloat($('#t_harga').val());
         if (!isNaN(bayar) && !isNaN(total)) {
            var kembalian = bayar - total;
            $('#kembalian').val(kembalian);
         } else {
            $('#kembalian').val('');
         }
         disableButton();
      }

      function disableButton() {
         var kembalian = parseFloat($('#kembalian').val());
         var bayar = $('#bayar').val();
         if (kembalian < 0 || !bayar) {
            $('#simpan').attr('disabled', true);
         } else {
            $('#simpan').attr('disabled', false);
         }
      }

      disableButton();
   });
</script>