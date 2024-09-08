<ol class="breadcrumb pull-right">
    <li class="breadcrumb-item"><a href="<?= base_url('') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a>Data Transaksi</a></li>
</ol>

<h1 class="page-header">
    <a href="<?php echo base_url() ?>kasir/transaksi/tambahTransaksi" class="btn btn-primary">Input Transaksi</a>
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
                <h4 class="panel-title">Data Transaksi</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>ID Transaksi</th>
                                <th>Status</th>
                                <th>Tanggal Input</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Konfirmasi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda akan tetap melanjutkan transaksi ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="confirmNoButton" data-dismiss="modal">Tidak</button>
                <button type="button" class="btn btn-primary" id="confirmYesButton">Ya</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        var transactionId;

        $('#confirmModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            transactionId = button.data('id'); // Extract info from data-* attributes
        });

        $('#confirmYesButton').click(function() {
            window.location.href = '<?php echo site_url('kasir/transaksi/inputBelanja/'); ?>' + transactionId;
        });

        $('#confirmNoButton').click(function() {
            $('#confirmModal').modal('hide');
            window.location.href = '<?php echo site_url('kasir/transaksi/deleteTransaction/'); ?>' + transactionId;
            // $.ajax({
            //     url: '<?php echo site_url('kasir/transaksi/deleteTransaction'); ?>', // URL untuk delete
            //     type: 'POST',
            //     data: {
            //         id: transactionId
            //     },
            //     success: function(response) {
            //         // Redirect to inputBelanja
            //         window.location.href = '<?php echo site_url('kasir/transaksi/inputBelanja/'); ?>' + transactionId;
            //     }
            // });
        });

        var table;
        table = $('#table').DataTable({
            initComplete: function() {
                var url;
                url = "<?= site_url('kasir/transaksi/') ?>";
                var input = $('#table_filter input').unbind(),
                    self = this.api(),
                    searchButton = $('<span id="btnSearch" class="btn btn-primary btn-sm" style="pull-rigth"><i class="fa fa-search"></i></span>')
                    .click(function() {
                        self.search(input.val()).draw();
                    });
                $(document).keypress(function(event) {
                    if (event.which == 13) {
                        searchButton.click();
                    }
                });
                var coba = $('#btnSearch').unbind(),
                    self = this.api(),
                    refresh = $('<span id="btnRefresh" class="btn btn-warning"><i class="fa fa-history"></i></span>')
                    .click(function() {
                        // self.search(input.val()).draw();
                        window.location.href = url;
                    });
                $('#table_filter').append(searchButton);
                // $('#table_filter').append(refresh);
            },

            "processing": true,
            "serverSide": true,
            "responsive": false,
            "order": [],

            "ajax": {
                "url": "<?php echo site_url('kasir/transaksi/getDataTransaksi/') ?>",
                "type": "POST",
                "data": function(data) {
                    // data.tgl = $('#tgl').val();
                    // data.tgl2 = $('#tgl2').val();
                }
            },

            "columnDefs": [{
                "targets": [0, 4],
                "orderable": false,
            }, ],
        });



        $('#btn-filter').click(function() {
            table.ajax.reload();
        });

        $('#btn-reset').click(function() {
            $('#form-filter')[0].reset();
            table.ajax.reload();
        });
    });
</script>