<?php
// Koneksi ke database
require ("auth/config.php");

// Fungsi untuk mendapatkan kode transaksi terbaru
function generateKodeTransaksi($conn) {
    $query = "SELECT id_transaksi FROM transaksi ORDER BY id_transaksi DESC LIMIT 1";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastKode = $row['kode_transaksi'];
        $lastNumber = (int)$lastKode; // Konversi ke integer
        $newNumber = $lastNumber + 1; // Tambah 1
        return str_pad($newNumber, 3, "0", STR_PAD_LEFT); // Format jadi 3 digit
    } else {
        return "001"; // Kode pertama
    }
}

// Generate kode transaksi baru
$kodeTransaksi = generateKodeTransaksi($conn);
// Tambahkan barang ke transaksi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $kodeBarang = $_POST['id_barang'];
    $jumlah = (int)$_POST['jumlah'];

    // Ambil harga barang
    $queryBarang = "SELECT harga FROM barang WHERE id_barang = '$kodeBarang'";
    $resultBarang = $conn->query($queryBarang);

    if ($resultBarang->num_rows > 0) {
        $rowBarang = $resultBarang->fetch_assoc();
        $harga = $rowBarang['harga'];
        $subtotal = $harga * $jumlah;
        $date = date('Y-m-d');

        // Masukkan transaksi
        $queryTransaksi = "INSERT INTO transaksi (id_transaksi, id_barang, jumlah, subtotal, tanggal_transaksi) VALUES ('$kodeTransaksi', '$kodeBarang', $jumlah, $subtotal, $date)";
        if ($conn->query($queryTransaksi) === TRUE) {
            echo "Transaksi berhasil ditambahkan! Kode Transaksi: $kodeTransaksi";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Kode barang tidak ditemukan.";
    }
}

// Ketika tombol cetak nota ditekan, kode transaksi bertambah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cetak'])) {
    $kodeTransaksi = generateKodeTransaksi($conn);
    echo "Kode transaksi baru: $kodeTransaksi";
}
?>
