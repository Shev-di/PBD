<?php
require "auth/config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus_barang'])) {
    $id_barang = $_POST['id_barang'];

    $queryHapus = "UPDATE barang SET status = 'aktif' WHERE id_barang = '$id_barang'";
    if ($conn->query($queryHapus) === TRUE) {
        echo "<script>alert('Barang berhasil dikembalikan!');</script>";
    } else {
        echo "<script>alert('Gagal mengembalikan barang: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Daftar Barang yang Dihapus</title>
</head>

<body>
    <div class="d-flex flex-column py-3 justify-content-center align-items-center text-white bg-primary">
        <h1>Daftar Barang yang Dihapus</h1>

    </div>
    <div class="alert alert-info">
        <a href="daftar_barang.php" class="btn btn-secondary ">Kembali</a>
    </div>

    <div class="container mt-4">


        <!-- Daftar Barang -->
        <h4>Daftar Barang</h4>
        <table class="table table-bordered">
            <thead class="text-center bg-secondary text-white">
                <tr>
                    <th>No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query untuk mengambil data barang
                $query = "SELECT id_barang, nama, harga, stok FROM barang WHERE status='nonaktif' ORDER BY id_barang ASC ";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    $no = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='text-center'>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $row['id_barang'] . "</td>";
                        echo "<td>" . $row['nama'] . "</td>";
                        echo "<td>
                            <form action='' method='POST' class='d-inline'>
                                <input type='hidden' name='id_barang' value='" . $row['id_barang'] . "'>
                                <input type='number' name='harga' value='" . $row['harga'] . "' class='form-control mb-2'>
                            </form>
                        </td>";
                        echo "<td>
                            <form action='' method='POST' class='d-inline'>
                                <input type='hidden' name='id_barang' value='" . $row['id_barang'] . "'>
                                <input type='number' name='stok' value='" . $row['stok'] . "' class='form-control mb-2'>
                            </form>
                        </td>";
                        echo "<td>
                            <form action='' method='POST' class='d-inline'>
                                <input type='hidden' name='id_barang' value='" . $row['id_barang'] . "'>
                                <button type='submit' name='hapus_barang' class='btn btn-primary btn-sm'>Kembalikan</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>Data barang tidak ditemukan</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>