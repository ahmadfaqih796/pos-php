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
               <select name="suplier_id" id="suplier_id" class="form-control" required>
                  <option value="">-- Pilih --</option>
                  <?php foreach ($suplier as $key => $value) { ?>
                     <option value="<?= $value->id ?>"><?= $value->name . " - " . $value->name_company ?></option>
                  <?php } ?>
               </select>
            </div>

            <div class="form-group">
               <!-- <label for="">Nama Produk : <code>*</code></label> -->
               <table class="table table-bordered" id="product_table">
                  <thead>
                     <tr>
                        <th>Nama Produk</th>
                        <th>Satuan</th>
                        <th>QTY</th>
                        <th>Harga</th>
                        <th>Total</th>
                     </tr>
                  </thead>
                  <tbody>
                     <!-- Product rows will be inserted here -->
                  </tbody>
               </table>
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
      // Function to update total when QTY changes
      $(document).on('input', '.qty-input', function() {
         var qty = $(this).val(); // Get the quantity input
         var harga = $(this).data('harga'); // Get the price from data-harga

         // Calculate total for this product
         var total = qty * harga;

         // Update the total price for this row
         $(this).closest('tr').find('.total-price').text(total);

         updateTotal(); // Update overall total price
      });

      // Before form submission, collect all product data and append it to the form
      $('form').on('submit', function(e) {
         updateProductData(); // Ensure product data is updated before submission
      });

      // Function to collect product data from table and insert into hidden inputs
      function updateProductData() {
         // First remove any existing hidden inputs for products
         $('input[name="products[]"]').remove();

         // Loop through each product row in the table
         $('#product_table tbody tr').each(function() {
            var productName = $(this).find('td:eq(0)').text();
            var satuan = $(this).find('td:eq(1)').text();
            var qty = $(this).find('.qty-input').val();
            var harga = $(this).find('td:eq(3)').text();
            var total = $(this).find('.total-price').text();

            // Create hidden inputs for each product and append to the form
            $('form').append('<input type="hidden" name="products[]" value=\'' + JSON.stringify({
               product_name: productName,
               satuan: satuan,
               qty: qty,
               harga: harga,
               total: total
            }) + '\'>');
         });
      }

      // Update when supplier is selected
      $('#suplier_id').change(function() {
         var suplier_id = $(this).val();
         if (suplier_id != '') {
            $.ajax({
               url: "<?= site_url('master/transaksi_suplier/getProductSupplierDetail') ?>",
               method: "POST",
               data: {
                  suplier_id: suplier_id
               },
               dataType: "json",
               success: function(data) {
                  if (data.status == 'success') {
                     var productTable = $('#product_table tbody');
                     productTable.empty(); // Clear previous table rows

                     $.each(data.data, function(key, value) {
                        var row = '<tr>' +
                           '<td>' + value.product_name + '</td>' +
                           '<td>' + value.satuan + '</td>' +
                           '<td><input type="number" class="form-control qty-input" data-harga="' + value.harga + '" value="0" min="0"></td>' +
                           '<td>' + value.harga + '</td>' +
                           '<td class="total-price">0</td>' + // Set initial total to 0
                           '</tr>';
                        productTable.append(row); // Append the row to the table
                     });

                     updateTotal(); // Update total when the table is populated
                  } else {
                     $('#product_table tbody').empty(); // Clear the table if no products are found
                  }
               },
               error: function(jqXHR, textStatus, errorThrown) {
                  console.log("error jqXHR : ", jqXHR);
                  console.log("error textStatus : ", textStatus);
                  console.log("error errorThrown : ", errorThrown);
               }
            });
         } else {
            $('#product_table tbody').empty(); // Reset the table if no supplier is selected
         }
      });

      // Function to update the overall total price
      function updateTotal() {
         var totalHarga = 0;

         // Loop through each row and add up the total prices
         $('#product_table tbody tr').each(function() {
            var total = parseFloat($(this).find('.total-price').text()) || 0; // Ensure total is 0 if NaN
            totalHarga += total;
         });

         // Update the total price input
         $('#t_harga').val(totalHarga);
         calculateKembalian(); // Recalculate change whenever total is updated
      }

      // Calculate change based on total price and payment amount
      $('#bayar').on('input', function() {
         calculateKembalian();
      });

      function calculateKembalian() {
         var totalHarga = parseFloat($('#t_harga').val()) || 0; // Ensure totalHarga is 0 if NaN
         var bayar = parseFloat($('#bayar').val()) || 0; // Ensure bayar is 0 if NaN
         var kembalian = bayar - totalHarga;

         // Update the change input
         if (!isNaN(kembalian) && kembalian >= 0) {
            $('#kembalian').val(kembalian);
         } else {
            $('#kembalian').val(0); // Reset if payment is less than total
         }
      }
   });
</script>