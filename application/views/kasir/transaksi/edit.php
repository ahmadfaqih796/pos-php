<script src="https://youthsforum.com/wp-content/uploads/2021/05/html5-qrcode.min_.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quagga/dist/quagga.min.js"></script>
<style>
    .result {
        background-color: green;
        color: #fff;
        padding: 20px;
    }

    .row {
        display: flex;
    }

    #previeww {
        width: 50%;
        /* Lebar video 100% dari container */
        height: auto;
        /* Tinggi video disesuaikan agar proporsional */
        max-width: 100%;
        /* Batas maksimum lebar video */
        display: block;
        /* Pastikan video ditampilkan sebagai blok */
    }
</style>
<?php
$no = 1;
?>
<ol class="breadcrumb pull-right">
    <li class="breadcrumb-item"><a href="<?= base_url('') ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?= base_url('kasir/transaksi/') ?>">Data Transaksi</a></li>
    <li class="breadcrumb-item">Input Belanja</li>
</ol>

<div class='row'>
    <div class="col-md-12">
        <h1 class="page-header">
            Form Input Belanja
        </h1>
    </div>
</div>

<?php if ($this->session->flashdata('msg')) { ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Info!</strong> <?php echo $this->session->flashdata('msg') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php } ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Scan QR Code</h4>
            </div>

            <div class="panel-body">
                <?= form_open('kasir/transaksi/inputBarangTemp/' . $id) ?>


                <div class="form-group">
                    <h4>Masukan Kode QR Code / Scan</h4>
                    <div class="d-flex justify-content-center">
                        <video id="previeww"></video>
                    </div>
                    <input type="hidden" class="form-control" id="barcode" onchange="cariDetailBarang('<?= $id ?>',this.value)">
                </div>

                <?= form_close() ?>

                <div class="form-group">
                    <label class="control-label" for="">Cari Barang : </label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="cari_barang">
                        <button class="btn btn-primary" onclick="submitSearch('<?= $id ?>')">Cari</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

    </script>

    <div class="col-md-12">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">List Belanjaan <i class="fas fa-shopping-cart"></i></h4>
            </div>

            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th>No</th>
                            <th>Barang</th>
                            <th>Harga</th>
                            <th>Jumlah Barang</th>
                            <th>Jumlah Transaksi Barang</th>
                            <th>#</th>
                        </tr>

                        <?php foreach ($barangTemp->result() as $bt) : ?>
                            <tr>
                                <td>
                                    <?= $no++ ?>
                                </td>
                                <td>
                                    <?= $bt->Nama_Barang ?>
                                </td>
                                <td>
                                    <?= "Rp." . number_format($bt->Harga_Barang, 0, '.', '.') ?>
                                </td>
                                <td>
                                    <input type="number" class="form-control" value="<?= $bt->Jumlah_Barang ?>" min="1" onchange="kalkulasi(this.value,<?= $bt->ID_Penjualan ?>,<?= $id ?>)">
                                </td>
                                <td>
                                    <?= "Rp." . number_format($bt->Jumlah_Transaksi_Barang, 0, '.', '.') ?>
                                </td>
                                <td>
                                    <a href="<?= site_url('kasir/transaksi/hapusBarangTemp/' . $bt->ID_Penjualan . '/' . $id)  ?>" class="btn btn-icon btn-danger"><i class="fa fa-times"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php if (count($barangTemp->result()) > 0) : ?>
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <h4 class="panel-title">Form Pembayaran</h4>
                </div>

                <div class="panel-body">
                    <?= form_open_multipart('kasir/transaksi/inputPembayaran/' . $id) ?>
                    <div class="form-group">
                        <label for="">TOTAL :</label>
                        <input type="hidden" name="Bill" value="<?= $totalBelanja ?>">
                        <input type="text" class="form-control" value="Rp. <?= number_format($totalBelanja, 0, '.', '.') ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="">Metode Pembayaran</label>
                        <select name="paidType" id="paidType" class="form-control" required onchange="paidTypeJs(this.value)">
                            <option value="">-- Pilih --</option>
                            <option value="Tunai">Tunai</option>
                            <option value="Transfer">Transfer</option>
                        </select>
                    </div>

                    <script>

                    </script>

                    <div id="paidTransfer">
                        <label for="">Bukti Transfer : </label>
                        <input type="file" name="buktiTf" id="buktiTf" class="form-control" onchange="validateFile(event)">
                        <div id="previewContainer" style="position: relative;">
                            <img id="preview" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQGrwXwU3LiaAHTbVPr3EX679rdj3OKpePN40Tb4B9S4g&s" alt="Preview Image" style="max-width: 200px; max-height: 200px; margin-top: 10px;">
                            <!-- <div id="zoomButtons" style="position: absolute; bottom: 10px; right: 10px;">
                                <button onclick="zoomIn()">Zoom In</button>
                                <button onclick="zoomOut()">Zoom Out</button>
                            </div> -->
                        </div>
                    </div>

                    <div id="paidTunai" class="form-group">
                        <label for="">Bayar : </label>
                        <input type="text" name="Paid" id="paidInput" class="form-control" oninput="updateKembalian()">
                    </div>

                    <div id="paidKembalian" class="form-group">
                        <label for="">Kembalian : </label>
                        <input type="text" name="Kembalian" id="kembalian" class="form-control" readonly>
                    </div>

                    <button class="btn btn-primary btn-block">Bayar <i class="fa fa-dollar"></i></button>

                    <?= form_close() ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="<?= site_url() ?>/assets/instascan/instascan.min.js"></script>



<script>
    function reload_page() {
        window.location.reload();
    }

    function updateKembalian() {
        var totalBelanja = parseFloat(document.querySelector('input[name="Bill"]').value.replace(/\./g, '').replace('Rp', ''));
        var bayar = parseFloat(document.getElementById('paidInput').value.replace(/\./g, '').replace('Rp', '')) || 0;

        // Hitung kembalian
        var kembalian = bayar - totalBelanja;

        // Format kembalian ke format Rupiah
        var formattedKembalian = formatRupiah(kembalian.toString());

        // Update nilai kembalian
        document.getElementById('kembalian').value = formattedKembalian;
    }

    function formatRupiah(angka) {
        var reverse = angka.toString().split('').reverse().join(''),
            ribuan = reverse.match(/\d{1,3}/g);
        ribuan = ribuan.join('.').split('').reverse().join('');
        return 'Rp. ' + ribuan;
    }

    function cariDetailBarang(id, barcode_id) {
        $.ajax({
            url: "<?= site_url('kasir/transaksi/cariDetailBarang/') ?>" + barcode_id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data == null) {
                    alert('Data tidak ditemukan!')
                    $("#barcode").val("")
                    $("#product_name").val("")
                    $("#price").val("")
                    $("#stock").val("")
                } else {

                    $.ajax({
                        url: "<?= site_url('kasir/transaksi/inputBarangTemp/') ?>" + id + "/" + data.product_name + "/" + data.price,
                        type: "GET",
                        dataType: "JSON",
                        success: function(data) {
                            console.log(data);
                            if (data == false) {
                                alert('Barang sudah pernah di input!')
                                $("#barcode").val("")
                            } else {
                                reload_page();
                            }
                        }
                    });
                }
            }
        });
    }

    function cariDetailBarangBySearch(id, p_name) {
        $.ajax({
            url: "<?= site_url('kasir/transaksi/cariDetailBarangBySearch/') ?>" + p_name,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                console.log("44444444444", data)
                if (data == null) {
                    alert('Data tidak ditemukan!')
                    $("#cari_barang").val("")
                    $("#product_name").val("")
                    $("#price").val("")
                    $("#stock").val("")
                } else {
                    $.ajax({
                        url: "<?= site_url('kasir/transaksi/inputBarangTemp/') ?>" + id + "/" + data.product_name + "/" + data.price,
                        type: "GET",
                        dataType: "JSON",
                        success: function(data) {
                            console.log(data);
                            if (data == false) {
                                alert('Barang sudah pernah di input!')
                                $("#cari_barang").val("")
                            } else {
                                reload_page();
                            }
                        }
                    });
                }
            }
        });
    }

    function submitSearch(id) {
        var productName = document.getElementById('cari_barang').value;
        cariDetailBarangBySearch(id, productName);
    }

    function kalkulasi(val, idPenjualan, id) {
        $.ajax({
            url: "<?= site_url('kasir/transaksi/kalkulasi/') ?>" + id + "/" + idPenjualan + "/" + val,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                console.log(data)
                if (data.data == false) {
                    alert('Gagal!');
                    reload_page();
                } else {
                    // alert('Berhasil');
                    reload_page();
                }
            }
        });
    }
</script>

<script>
    $(document).ready(function() {
        $("#paidTransfer").hide();
        $("#paidTunai").hide();
        $("#paidKembalian").hide();
    });
    var scale = 1;

    function previewImage(event) {
        var input = event.target;
        var reader = new FileReader();
        reader.onload = function() {
            var imgElement = document.getElementById('preview');
            imgElement.src = reader.result;
            imgElement.style.display = 'block';
            scale = 1;
            imgElement.style.transform = 'scale(' + scale + ')';
        }
        reader.readAsDataURL(input.files[0]);
    }

    function zoomIn() {
        var imgElement = document.getElementById('preview');
        scale += 0.1;
        imgElement.style.transform = 'scale(' + scale + ')';
    }

    function zoomOut() {
        var imgElement = document.getElementById('preview');
        scale -= 0.1;
        if (scale < 0.1) {
            scale = 0.1;
        }
        imgElement.style.transform = 'scale(' + scale + ')';
    }

    function paidTypeJs(type) {
        // alert(type)
        if (type == "Transfer") {
            $("#paidTransfer").show();
            $("#paidTunai").hide();
            $("#paidKembalian").hide();
        } else if (type == "Tunai") {
            $("#paidTransfer").hide();
            $("#paidTunai").show();
            $("#paidKembalian").show();
        } else {
            $("#paidTransfer").hide();
            $("#paidTunai").hide();
            $("#paidKembalian").hide();
        }
    }

    function validateFile(event) {
        var fileInput = event.target;
        var filePath = fileInput.value;
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i; // Ekstensi file yang diperbolehkan

        if (!allowedExtensions.exec(filePath)) {
            // Jika ekstensi file tidak sesuai dengan yang diperbolehkan
            alert('Hanya diperbolehkan file dengan ekstensi JPG, JPEG, atau PNG.');
            fileInput.value = ''; // Menghapus nilai input
            document.getElementById('preview').src = ''; // Menghapus pratinjau gambar
        } else {

            previewImage(event); // Menampilkan pratinjau gambar
        }
    }
</script>


<script>
    let scanner = new Instascan.Scanner({
        video: document.getElementById('previeww')
    });

    scanner.addListener('scan', function(content) {
        let id = "<?= $id ?>";
        $.ajax({
            url: "<?= site_url('kasir/transaksi/cariDetailBarang/') ?>" + content,
            type: "GET",
            dataType: "JSON",
            success: function(data) {
                if (data == null) {
                    alert('Data tidak ditemukan!')
                    $("#barcode").val("")
                    $("#product_name").val("")
                    $("#price").val("")
                    $("#stock").val("")
                } else {

                    $.ajax({
                        url: "<?= site_url('kasir/transaksi/inputBarangTemp/') ?>" + id + "/" + data.product_name + "/" + data.price,
                        type: "GET",
                        dataType: "JSON",
                        success: function(data) {
                            console.log(data);
                            if (data == false) {
                                alert('Barang sudah pernah di input!')
                                $("#barcode").val("")
                                reload_page();
                            } else {
                                reload_page();
                            }
                        }
                    });
                }

                scanner.stop();
            }
        });

    });


    Instascan.Camera.getCameras().then(function(cameras) {
        if (cameras.length > 0) {
            scanner.start(cameras[0]);
        } else {
            console.error('Tidak ada kamera yang ditemukan.');
        }
    }).catch(function(e) {
        console.error(e);
    });
</script>