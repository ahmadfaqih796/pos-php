<title>Laporan Transaksi (<?= $q1->Nomor_Invoice ?>)</title>
<h2 align="center">Detail Laporan Transaksi (<?= $q1->Nomor_Invoice ?>)</h2>

<!-- <?= print_r($q1) ?> -->
<h3 align="center">Toko Satria Nugget</h3>
<h4 align="center"> Jl Klambir V Ruko Janetti No 4, Medan</h4>
<h4 align="center">No Telp/Wa. 082168852450</h4>

<h4 align="center">__________________________________________</h4>
<h4 align="center">Tanggal : <?= $q1->Created_Date ?></h4>
<h4 align="center">Kasir: <?= $q1->name ?></h4>
<h4 align="center">Tranksasi : <?= $q1->Paid_Type ?></h4>
<h4 align="center">__________________________________________</h4>

<script>
    window.print();
</script>
<table align="center" border="1">
    <tr>
        <th>No</th>
        <th>Item</th>
        <th>Harga</th>
        <th>Jumlah</th>
        <th>Total</th>
    </tr>

    <?php
    $no = 1;
    foreach ($q2->result() as $q_) { ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $q_->Nama_Barang ?></td>
            <td><?= number_format($q_->Harga_Barang, 0, '.', '.') ?></td>
            <td><?= number_format($q_->Jumlah_Barang, 0, '.', '.') ?></td>
            <td>Rp. <?= number_format($q_->Jumlah_Transaksi_Barang, 0, '.', '.') ?></td>
        </tr>
    <?php } ?>
    <tr>
        <td colspan="4" align="right">Sub Total</td>
        <td>Rp. <?= number_format($q1->Bill, 0, '.', '.') ?></td>
    </tr>
    <tr>
        <td colspan="4" align="right">Bayar</td>
        <td>Rp. <?= number_format($q1->Paid, 0, '.', '.') ?></td>
    </tr>
    <tr>
        <td colspan="4" align="right">Kembalian</td>
        <td>Rp. <?= number_format($q1->Paid - $q1->Bill, 0, '.', '.') ?></td>
    </tr>
</table>