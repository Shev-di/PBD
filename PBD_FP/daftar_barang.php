<?php
require "auth/config.php";
session_start();

// Tambahkan Barang Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_barang'])) {
    $id_barang = $_POST['id_barang'];
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    $queryTambah = "INSERT INTO barang (id_barang, nama, harga, stok) VALUES ('$id_barang', '$nama', $harga, $stok)";
    if ($conn->query($queryTambah) === TRUE) {
        echo "<script>alert('Barang berhasil ditambahkan!');</script>";
    } else {
        echo "<script>alert('Gagal menambahkan barang: " . $conn->error . "');</script>";
    }
}

// Edit Stok Barang
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_stok'])) {
    $id_barang = $_POST['id_barang'];
    $stok = $_POST['stok'];

    $queryEditStok = "UPDATE barang SET stok = $stok WHERE id_barang = '$id_barang'";
    if ($conn->query($queryEditStok) === TRUE) {
        echo "<script>alert('Stok berhasil diperbarui!');</script>";
    } else {
        echo "<script>alert('Gagal memperbarui stok: " . $conn->error . "');</script>";
    }
}

// Edit Harga Barang
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_harga'])) {
    $id_barang = $_POST['id_barang'];
    $harga = $_POST['harga'];

    $queryEditHarga = "UPDATE barang SET harga = $harga WHERE id_barang = '$id_barang'";
    if ($conn->query($queryEditHarga) === TRUE) {
        echo "<script>alert('Harga berhasil diperbarui!');</script>";
    } else {
        echo "<script>alert('Gagal memperbarui harga: " . $conn->error . "');</script>";
    }
}

// Hapus Barang
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['hapus_barang'])) {
    $id_barang = $_POST['id_barang'];

    $queryHapus = "DELETE FROM barang WHERE id_barang = '$id_barang'";
    if ($conn->query($queryHapus) === TRUE) {
        echo "<script>alert('Barang berhasil dihapus!');</script>";
    } else {
        echo "<script>alert('Gagal menghapus barang: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Daftar Barang</title>
</head>

<body>
    <div class="d-flex flex-column py-3 justify-content-center align-items-center text-white bg-primary">
        <h1>Daftar Barang</h1>

    </div>
    <div class="alert alert-info">
        <a href="dashboard.php" class="btn btn-secondary ">Kembali ke Dashboard</a>
        <!-- <strong>Jumlah Barang yang stoknya habis :</strong> <?php echo $jumlahBarang; ?> -->
    </div>

    <div class="container mt-4">


        <!-- Form Tambah Barang -->
        <h4>Tambah Barang Baru</h4>
        <form action="" method="POST" class="mb-4">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="id_barang" class="form-control" placeholder="Kode Barang" required>
                </div>
                <div class="col-md-3">
                    <input type="text" name="nama" class="form-control" placeholder="Nama Barang" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="harga" class="form-control" placeholder="Harga" required>
                </div>
                <div class="col-md-2">
                    <input type="number" name="stok" class="form-control" placeholder="Stok" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" name="tambah_barang" class="btn btn-success">Tambah Barang</button>
                </div>
            </div>
        </form>

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
                $query = "SELECT id_barang, nama, harga, stok FROM barang";
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
                                <button type='submit' name='edit_harga' class='btn btn-primary btn-sm'>Edit Harga</button>
                            </form>
                        </td>";
                        echo "<td>
                            <form action='' method='POST' class='d-inline'>
                                <input type='hidden' name='id_barang' value='" . $row['id_barang'] . "'>
                                <input type='number' name='stok' value='" . $row['stok'] . "' class='form-control mb-2'>
                                <button type='submit' name='edit_stok' class='btn btn-primary btn-sm'>Edit Stok</button>
                            </form>
                        </td>";
                        echo "<td>
                            <form action='' method='POST' class='d-inline'>
                                <input type='hidden' name='id_barang' value='" . $row['id_barang'] . "'>
                                <button type='submit' name='hapus_barang' class='btn btn-danger btn-sm'>Hapus</button>
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