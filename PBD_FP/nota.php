<?php
require "auth/config.php";
session_start();

// Ambil kode transaksi dari URL
if (isset($_GET['kode_transaksi'])) {
    $kodeTransaksi = $_GET['kode_transaksi'];

    // Query untuk mendapatkan data transaksi
    $query = "
        SELECT t.id_transaksi, b.nama, b.harga, t.quantity, t.subtotal, t.tanggal_transaksi
        FROM transaksi t
        JOIN barang b ON t.id_barang = b.id_barang
        WHERE t.id_transaksi = '$kodeTransaksi'
    ";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $transaksi = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "Transaksi tidak ditemukan.";
        exit();
    }

    $queryTotal = "
        SELECT SUM(t.subtotal) AS total
        FROM transaksi t 
        GROUP BY t.id_transaksi
        HAVING t.id_transaksi = '$kodeTransaksi'
    ";

    $resultTotal = $conn->query($queryTotal);
    if ($resultTotal->num_rows > 0) {
        $row = $resultTotal->fetch_assoc();
        $total = $row['total'];
    } else {
        echo "Total Kosong";
        exit();
    }
} else {
    echo "Kode transaksi tidak ditemukan.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">Nota Transaksi</h2>
        <p>Kode Transaksi: <strong><?php echo $kodeTransaksi; ?></strong></p>
        <p>Tanggal: <strong><?php echo $transaksi[0]['tanggal_transaksi']; ?></strong></p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($transaksi as $item) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . $item['nama'] . "</td>";
                    echo "<td>" . number_format($item['harga'], 0, ',', '.') . "</td>";
                    echo "<td>" . $item['quantity'] . "</td>";
                    echo "<td>" . number_format($item['subtotal'], 0, ',', '.') . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Total</th>
                    <th><?php echo number_format($total, 0, ',', '.'); ?></th>
                </tr>
            </tfoot>
        </table>
        <div class="text-center mt-3">
            <a href="dashboard.php"><button class="btn btn-danger">kembali</button></a>
            <button onclick="window.print();" class="btn btn-primary">Cetak Nota</button>
        </div>
    </div>
</body>

</html>