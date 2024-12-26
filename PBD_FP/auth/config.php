<?php
$server = "localhost";
$user = "root";
$password = "";
$nama_database = "penjualan";

$conn = mysqli_connect($server,$user,$password,$nama_database);
if(!$conn){
    die("Ada masalah koneksi ke database : ". mysqli_connect_error());
}else {
    // echo "database terhubung";
}
?>