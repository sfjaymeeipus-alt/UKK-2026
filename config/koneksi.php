<?php

$koneksi = mysqli_connect("localhost", "root", "", "db_ukk_ujikom");

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>