<ol class="breadcrumb pull-right">
    <li class="breadcrumb-item"><a href="<?= base_url('') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a>Data Suplier</a></li>
</ol>

<h1 class="page-header">
    <a href="<?php echo base_url() ?>master/suplier/tambahSuplier" class="btn btn-primary">Tambah Suplier</a>
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
                <h4 class="panel-title">Data Suplier</h4>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="table" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Perusahaan</th>
                                <th>Nama Supplier</th>
                                <!-- <th>Jenis Supplier</th>
                                <th>Harga Supplier</th> -->
                                <th>No Hp</th>
                                <th>Alamat</th>
                                <!-- <th>Kota</th>
                                <th>Provinsi</th> -->
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

<script type="text/javascript">
    $(document).ready(function() {
        var table;
        table = $('#table').DataTable({
            initComplete: function() {
                var url;
                url = "<?= site_url('master/suplier/') ?>";
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
                "url": "<?php echo site_url('master/suplier/getDataSuplier') ?>",
                "type": "POST",
                "data": function(data) {
                    // data.tgl = $('#tgl').val();
                    // data.tgl2 = $('#tgl2').val();
                }
            },

            "columnDefs": [{
                "targets": [0, 5],
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