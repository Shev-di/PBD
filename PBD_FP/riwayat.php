<?php
require "auth/config.php";
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['id_karyawan'])) {
    header("Location: login.php");
    exit();
}
// Ambil tanggal yang dipilih
$tanggal_dipilih = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';

// Tambahkan logika untuk tombol "Tampilkan Semua Data"
if (isset($_GET['tampilkan_semua'])) {
    $tanggal_dipilih = ''; 
}

// Query untuk menghitung jumlah transaksi 
$queryJumlahTransaksi = "
    SELECT COUNT(DISTINCT id_transaksi) AS jumlah_transaksi
    FROM transaksi
";

if ($tanggal_dipilih) {
    $queryJumlahTransaksi .= " GROUP BY tanggal_transaksi HAVING tanggal_transaksi = '$tanggal_dipilih'";
}

$resultJumlahTransaksi = $conn->query($queryJumlahTransaksi);
$jumlahTransaksi = 0;

if ($resultJumlahTransaksi->num_rows > 0) {
    $rowJumlahTransaksi = $resultJumlahTransaksi->fetch_assoc();
    $jumlahTransaksi = $rowJumlahTransaksi['jumlah_transaksi'];
}

// Ambil data riwayat transaksi berdasarkan tanggal yang dipilih
$query = "
    SELECT t.id_transaksi, t.tanggal_transaksi, k.nama AS nama_karyawan, b.nama AS nama_barang, 
           t.quantity, t.subtotal,
           SUM(t.subtotal) AS total
    FROM transaksi t
    JOIN karyawan k ON t.id_karyawan = k.id_karyawan
    JOIN barang b ON t.id_barang = b.id_barang
    GROUP BY t.id_transaksi, t.tanggal_transaksi, k.nama, b.nama, t.quantity, t.subtotal
    HAVING t.id_transaksi = t.id_transaksi
";

if ($tanggal_dipilih) {
    $query .= " WHERE t.tanggal_transaksi = '$tanggal_dipilih'";
}

$query .= " ORDER BY t.id_transaksi DESC, t.tanggal_transaksi DESC";

$result = $conn->query($query);


// Kelompokkan transaksi berdasarkan ID Transaksi
$riwayat = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id_transaksi = $row['id_transaksi'];
        $riwayat[$id_transaksi]['tanggal_transaksi'] = $row['tanggal_transaksi'];
        $riwayat[$id_transaksi]['nama_karyawan'] = $row['nama_karyawan'];
        $riwayat[$id_transaksi]['items'][] = [
            'nama_barang' => $row['nama_barang'],
            'quantity' => $row['quantity'],
            'subtotal' => $row['subtotal']
        ];

        // Hitung total transaksi
        if (!isset($riwayat[$id_transaksi]['total'])) {
            $riwayat[$id_transaksi]['total'] = 0;
        }
        $riwayat[$id_transaksi]['total'] += $row['subtotal'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        rel="stylesheet">
    <title>Riwayat Transaksi</title>
    <style>
        body,
        html {
            height: 100%;
            width: 100%;
        }

        .row {
            width: 100%;
            height: 85%;
        }

        .table-container {
            max-height: 70vh;
            overflow-y: auto;
        }

        .transaction-header {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .total-transaksi {
            text-align: right;
            font-weight: bold;
            background-color: #d4edda;
        }
    </style>
</head>

<body>
    <div class="d-flex flex-column py-3 justify-content-center align-items-center text-white bg-primary">
        <h1>Riwayat Transaksi</h1>
        <h4>Kasir Minimarket</h4>
    </div>

    <div class="row">
        <div class="col-sm-2 d-flex flex-column bg-secondary align-items-center pt-3">
            <a href="dashboard.php" class="btn btn-dark px-4 mt-2">Buat Transaksi</a>
            <a href="riwayat.php" class="btn btn-light mt-2">Riwayat Transaksi</a>
            <a href="daftar_barang.php" class="btn btn-light mt-2 px-4">Daftar Barang</a>
            <a href="login.php" class="btn btn-danger mt-2">LOGOUT</a>
        </div>

        <div class="col-sm-10 pt-4">
            <div class="mb-3">
                <form method="GET" action="riwayat.php" class="d-flex">
                    <input type="text" id="tanggal" name="tanggal" class="form-control me-2" placeholder="Pilih Tanggal"
                        value="<?= $tanggal_dipilih; ?>" autocomplete="off">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <button type="submit" name="tampilkan_semua" value="1" class="btn btn-secondary">Tampilkan Semua
                        Data</button>
                </form>
            </div>


            <div class="alert alert-info">
                <?php if ($tanggal_dipilih): ?>
                    <strong>Jumlah Transaksi pada Tanggal <?= $tanggal_dipilih; ?>:</strong> <?= $jumlahTransaksi; ?>
                    transaksi
                <?php else: ?>
                    <strong>Total Jumlah Transaksi:</strong> <?= $jumlahTransaksi; ?> transaksi
                <?php endif; ?>
            </div>


            <div class="table-container">
                <?php if (!empty($riwayat)): ?>
                    <?php foreach ($riwayat as $id_transaksi => $data): ?>
                        <table class="table table-bordered mb-4">
                            <thead>
                                <tr class="transaction-header">
                                    <td colspan="5">
                                        <strong>ID Transaksi:</strong> <?= $id_transaksi ?> |
                                        <strong>Tanggal:</strong> <?= $data['tanggal_transaksi'] ?> |
                                        <strong>Karyawan:</strong> <?= $data['nama_karyawan'] ?>
                                    </td>
                                </tr>
                                <tr class="table-primary text-center">
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; ?>
                                <?php foreach ($data['items'] as $item): ?>
                                    <tr class="text-center">
                                        <td><?= $no++ ?></td>
                                        <td><?= $item['nama_barang'] ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="3" class="total-transaksi">Total Transaksi:</td>
                                    <td class="total-transaksi">Rp <?= number_format($data['total'], 0, ',', '.') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        Tidak ada riwayat transaksi untuk tanggal <?= $tanggal_dipilih; ?>.
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="dashboard.php" class="btn btn-secondary">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script
        src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#tanggal').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
        });
    </script>
</body>

</html>