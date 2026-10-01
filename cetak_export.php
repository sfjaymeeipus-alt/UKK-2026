<?php

include "middleware/auth.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Cetak / Export</title>

</head>

<body>

    <h2>Cetak / Export</h2>

    <p>Pilih data yang ingin dicetak atau di-export.</p>

    <br>

    <a href="cetak_pelanggaran.php">
        Cetak Jenis Pelanggaran
    </a>

    <br>
    <br>

    <a href="export_pelanggaran.php">
        Export Jenis Pelanggaran
    </a>

</body>

</html>