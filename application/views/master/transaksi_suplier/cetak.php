<title>Laporan Transaksi Supplier (<?= $q1->id_transaksi ?>)</title>
<style>
    table {
        border-collapse: collapse;
    }

    td,
    th {
        padding: 1rem;
    }
</style>
<h2 align="center">Detail Transaksi Supplier (<?= $q1->id_transaksi ?>)</h2>

<!-- <?= print_r($q1) ?> -->
<h3 align="center">Toko Satria Nugget</h3>
<h4 align="center"> Jl Klambir V Ruko Janetti No 4, Medan</h4>
<h4 align="center">No Telp/Wa. 082168852450</h4>

<h4 align="center">__________________________________________</h4>
<h4 align="center">Tanggal : <?= $q1->created_at ?></h4>
<h4 align="center">Kasir: Owner</h4>
<h4 align="center">Tranksasi : Cash</h4>
<h4 align="center">Supplier: <?= $supplier->name ?></h4>
<h4 align="center">__________________________________________</h4>

<script>
    window.print();
</script>
<table align="center" border="1">
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
        foreach ($q2 as $key => $value) {
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $value->name_product ?></td>
                <td><?= $value->qty ?></td>
                <td><?= $value->satuan ?></td>
                <td>Rp. <?= number_format($value->harga, 0, '.', '.') ?></td>
                <td>Rp. <?= number_format($value->total, 0, '.', '.') ?></td>
            </tr>
        <?php
            $no++;
            $total += $value->total;
        }
        ?>
    </tbody>
    <tfoot>

        <tr>
            <td colspan="5" align="right">Total</td>
            <td>Rp. <?= number_format($total, 0, '.', '.') ?></td>
        </tr>

        <tr>
            <td colspan="5" align="right">Bayar</td>
            <td>Rp. <?= number_format($q1->bayar, 0, '.', '.') ?></td>
        </tr>

        <tr>
            <td colspan="5" align="right">Kembalian</td>
            <td>Rp. <?= number_format($q1->kembalian, 0, '.', '.') ?></td>
        </tr>

    </tfoot>


    <!-- <tr>
        <td colspan="4" align="right">Sub Total</td>
        <td>Rp. <?= number_format($q1->Bill, 0, '.', '.') ?></td>
    </tr> -->
</table>