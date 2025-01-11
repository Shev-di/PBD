<?php
require "auth/config.php"; // Koneksi ke database
session_start();

// Fungsi untuk membuat kode transaksi otomatis
function generateKodeTransaksi($conn)
{
    $query = "SELECT id_transaksi FROM transaksi ORDER BY id_transaksi DESC LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastKode = (int) $row['id_transaksi'];
        $newNumber = $lastKode + 1;
        return str_pad($newNumber, 3, "0", STR_PAD_LEFT);
    } else {
        return "001";
    }
}

// Set kode transaksi di session jika belum ada
if (!isset($_SESSION['kode_transaksi'])) {
    $_SESSION['kode_transaksi'] = generateKodeTransaksi($conn);
}

// Tambahkan barang ke transaksi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $kodeBarang = $_POST['id_barang'];
    $jumlah = (int) $_POST['jumlah'];

    if (!isset($_SESSION['kode_transaksi'])) {
        echo "Kode transaksi belum dibuat.";
        exit();
    }

    $kodeTransaksi = $_SESSION['kode_transaksi'];

    // Ambil data barang dari database
    $queryBarang = "SELECT nama, harga, stok FROM barang WHERE id_barang = '$kodeBarang'";
    $resultBarang = $conn->query($queryBarang);

    if ($resultBarang->num_rows > 0) {
        $rowBarang = $resultBarang->fetch_assoc();
        $stok = $rowBarang['stok'];

        if ($stok >= $jumlah) {
            $harga = $rowBarang['harga'];
            $subtotal = $harga * $jumlah;
            $date = date('Y-m-d');
            $id_karyawan = $_SESSION['id_karyawan']; // ID karyawan dari session

            $checkQuery = "SELECT * FROM transaksi WHERE id_transaksi = '$kodeTransaksi'";
            $checkResult = $conn->query($checkQuery);

            if ($checkResult->num_rows > 0) {
                $queryTransaksi = "INSERT INTO transaksi (id_transaksi, id_karyawan, tanggal_transaksi)
                                    VALUES ('$kodeTransaksi', '$id_karyawan', '$date')";
                $queryNota = "INSERT INTO nota (id_transaksi, id_barang, quantity) 
                               VALUES ('$kodeTransaksi', '$kodeBarang', '$jumlah')";
                if($conn->query($queryTransaksi) === TRUE && $conn->query($queryNota) === TRUE) {
                    $queryUpdateStok = "UPDATE barang SET stok = stok - $jumlah WHERE id_barang = '$kodeBarang'";
                    $conn->query($queryUpdateStok);
                    echo "Barang berhasil ditambahkan ke transaksi";
                } else {
                    echo "Gagal menambahkan ke transaksi : ". $conn->error;
                }
                
            } else {
                $queryNota = "INSERT INTO nota (id_transaksi, id_barang, quantity) 
                               VALUES ('$kodeTransaksi', '$kodeBarang', $jumlah)";
                // $conn->query($queryTransaksi);
                if ($conn->query($queryNota) === TRUE) {
                    // Kurangi stok barang
                    $queryUpdateStok = "UPDATE barang SET stok = stok - $jumlah WHERE id_barang = '$kodeBarang'";
                    $conn->query($queryUpdateStok);
                    echo "Barang berhasil ditambahkan ke transaksi.";
                } else {
                    echo "Gagal menambahkan ke transaksi: " . $conn->error;
                }
            }

            // Masukkan data ke tabel transaksi
            // $queryTransaksi = "INSERT INTO nota (id_transaksi, id_barang, quantity) 
            //                    VALUES ('$kodeTransaksi', '$kodeBarang', $jumlah)";


        } else {
            echo "Stok barang tidak mencukupi.";
        }
    } else {
        echo "Kode barang tidak ditemukan.";
    }
}

// Cetak nota transaksi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cetak'])) {
    if (isset($_SESSION['kode_transaksi'])) {
        $kodeTransaksi = $_SESSION['kode_transaksi'];
        unset($_SESSION['kode_transaksi']); // Hapus session untuk transaksi baru
        header("Location: nota.php?kode_transaksi=$kodeTransaksi");
        exit();
    } else {
        echo "Kode transaksi tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Kasir MiniMarket</title>
</head>

<body>
    <div class="d-flex flex-column py-3 justify-content-center align-items-center text-white bg-primary">
        <h1>Selamat Datang</h1>
        <h4>Kasir Minimarket</h4>
    </div>

    <div class="row vh-100">
        <div class="col-sm-2 d-flex flex-column bg-secondary align-items-center pt-3">
            <a href="dashboard.php" class="btn btn-dark px-4 mt-2">Buat Transaksi</a>
            <a href="riwayat.php" class="btn btn-light mt-2">Riwayat Transaksi</a>
            <a href="daftar_barang.php" class="btn btn-light mt-2 px-4">Daftar Barang</a>
            <a href="login.php" class="btn btn-danger mt-2">LOGOUT</a>
        </div>

        <div class="col-sm-10 pt-4">
            <strong>Tanggal:</strong> <?= date('Y-m-d'); ?>
            <strong class="ms-4">Kode Transaksi Aktif:</strong> <?= $_SESSION['kode_transaksi']; ?>

            <form action="" method="POST" class="mt-2">
                <div class="mt-3 d-flex">
                    <label for="">Kode Barang:</label>
                    <input name="id_barang" class="ms-1 rounded" type="text">
                    <label for="" class="ms-3">Jumlah:</label>
                    <input name="jumlah" class="ms-2 rounded" type="number" min="1">
                    <button class="btn btn-primary ms-3" type="submit" name="tambah">Tambahkan</button>
                </div>

                <div class="mt-3">
                    <table class="table">
                        <thead class="text-center">
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Harga</th>
                                <th>Jumlah Barang</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $kodeTransaksi = $_SESSION['kode_transaksi'];
                            $query = "SELECT n.id_barang, b.nama, b.harga, n.quantity 
                                      FROM transaksi t
                                      JOIN nota n ON t.id_transaksi = n.id_transaksi
                                      JOIN barang b ON n.id_barang = b.id_barang
                                      WHERE n.id_transaksi = '$kodeTransaksi'";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                $no = 1;
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr class='text-center'>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td>" . $row['id_barang'] . "</td>";
                                    echo "<td>" . $row['nama'] . "</td>";
                                    echo "<td>" . number_format($row['harga'], 0, ',', '.') . "</td>";
                                    echo "<td>" . $row['quantity'] . "</td>";
                                    echo "<td>" . number_format($row['subtotal'], 0, ',', '.') . "</td>";
                                    echo "</tr>";
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end align-items-end">
                    <button name="cetak" type="submit" class="btn bg-primary px-3 rounded text-light">Cetak
                        Nota</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>