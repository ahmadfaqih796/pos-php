<ol class="breadcrumb pull-right">
   <li class="breadcrumb-item"><a href="<?= base_url('') ?>">Dashboard</a></li>
   <li class="breadcrumb-item"><a>Data Transaksi Suplier</a></li>
</ol>

<h1 class="page-header ">
   <?= "View Transaksi Suplier" ?>
</h1>

<?php if ($this->session->flashdata('msg')) { ?>
   <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <strong>Sukses!</strong> <?php echo $this->session->flashdata('msg') ?>
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
         <span aria-hidden="true">&times;</span>
      </button>
   </div>
<?php } ?>


<div class="row">
   <div class="col-lg-12">
      <div class="panel panel-inverse">
         <div class="panel-heading">
            <h4 class="panel-title">Data View Transaksi Suplier</h4>
         </div>
         <div class="panel-body">
            <div class="table-responsive">
               <table id="table" class="table table-striped table-bordered">
                  <thead>
                     <tr>
                        <th>No.</th>
                        <th>Nama Produk</th>
                        <th>QTY</th>
                        <th>Satuan</th>
                        <th>Harga</th>
                        <th>Total</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php
                     $no = 1;
                     $total = 0;
                     foreach ($product as $key => $value) {
                     ?>
                        <tr>
                           <td><?= $no ?></td>
                           <td><?= $value->name_product ?></td>
                           <td><?= $value->qty ?></td>
                           <td><?= $value->satuan ?></td>
                           <td>Rp. <?= number_format($value->harga) ?></td>
                           <td>Rp. <?= number_format($value->total) ?></td>
                        </tr>
                     <?php
                        $no++;
                        $total += $value->total;
                     }
                     ?>
                  </tbody>
                  <tfoot>
                     <tr>
                        <th colspan="5">Sub Total</th>
                        <th>Rp. <?= number_format($total) ?></th>
                     </tr>
                     <tr>
                        <th colspan="5">Bayar</th>
                        <th>Rp. <?= number_format($transaksi->bayar) ?></th>
                     </tr>
                     <tr>
                        <th colspan="5">Kembalian</th>
                        <th>Rp. <?= number_format($transaksi->kembalian) ?></th>
                     </tr>
                  </tfoot>
               </table>
            </div>
         </div>
      </div>
   </div>
</div>

<script type="text/javascript">

</script>