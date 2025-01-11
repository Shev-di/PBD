<?php
require "auth/config.php";
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['id_karyawan'])) {
    header("Location: login.php");
    exit();
}

// Ambil data penjualan berdasarkan tanggal
$query = "
    SELECT DATE(t.tanggal_transaksi) AS tanggal, SUM(n.quantity*b.harga) AS total_penjualan
    FROM transaksi t
    JOIN nota n ON t.id_transaksi = n.id_transaksi
    JOIN barang b ON b.id_barang = n.id_barang
    GROUP BY DATE(t.tanggal_transaksi)
    HAVING total_penjualan > 0
    ORDER BY tanggal_transaksi ASC
";


$result = $conn->query($query);

// Siapkan data untuk grafik
$labels = [];
$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['tanggal'];
        $data[] = $row['total_penjualan'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Grafik Penjualan</title>
    <style>
        .chart-container {
            width: 80%;
            margin: auto;
        }
    </style>
</head>

<body>
    <div class="d-flex flex-column py-3 justify-content-center align-items-center text-white bg-primary">
        <h1>Grafik Penjualan</h1>
        <h4>Kasir Minimarket</h4>
    </div>

    <div class="chart-container mt-5">
        <canvas id="penjualanChart"></canvas>
    </div>

    <div class="d-flex justify-content-center mt-3">
        <a href="riwayat.php" class="btn btn-secondary">Kembali ke Riwayat</a>
    </div>

    <script>
        const ctx = document.getElementById('penjualanChart').getContext('2d');
        const penjualanChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels); ?>, // Tanggal transaksi
                datasets: [{
                    label: 'Total Penjualan (Rp)',
                    data: <?= json_encode($data); ?>, // Total penjualan
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'Rp ' + value.toLocaleString(); // Format Rupiah
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>
