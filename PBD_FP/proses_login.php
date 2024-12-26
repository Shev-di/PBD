<?php 
session_start();
require("auth/config.php");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_karyawan = $_POST['id_karyawan'];

    // Validasi input kosong
    if (empty($id_karyawan)) {
        echo "<script>alert('Id Karyawan tidak boleh kosong'); window.location.href = 'login.php';</script>";
    } else {
        // Query untuk memeriksa id_karyawan
        $query = "SELECT * FROM karyawan WHERE id_karyawan = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $id_karyawan);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Jika id_karyawan ditemukan, simpan ke sesi dan arahkan ke dashboard
            $_SESSION['id_karyawan'] = $id_karyawan;
            echo "<script>alert('Login berhasil!'); window.location.href = 'dashboard.php';</script>";
        } else {
            // Jika id_karyawan tidak ditemukan
            echo "<script>alert('Id Karyawan tidak valid'); window.location.href = 'login.php';</script>";
        }
        $stmt->close();
    }
}

?>