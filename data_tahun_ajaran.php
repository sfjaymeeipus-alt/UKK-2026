<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$query = mysqli_query($koneksi, "SELECT * FROM t_tahun_ajaran ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Data Tahun Ajaran</title>
        <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    </head>
    <body class="bg-light">

<div class="container py-4">

    <div class="card shadow-sm">

        <div class="card-body">

            <h2 class="mb-3">Data Tahun Ajaran</h2>

            <a href="tambah_tahun_ajaran.php" class="btn btn-primary mb-3">
                + Tambah Tahun Ajaran
            </a>

            <div class="table-responsive">

                <table class="table table-bordered table-striped table-hover">

                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal_Selesai</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

    <?php
    $no = 1;
    while ($data = mysqli_fetch_assoc($query)) {
    ?>

    <tr>

    <td><?= $no++ ?></td>
    <td><?= $data['nama']; ?></td>
    <td><?= $data['tanggal_mulai']; ?></td>
    <td><?= $data['tanggal_selesai']; ?></td>

    <td>

    <?php
    if ($data['status_aktif'] == 1) {
        echo "Aktif";
    } else {
        echo "Tidak Aktif";
    }
    ?>

    </td>

    <td>
        
    <a href="edit_tahun_ajaran.php?id=<?= $data['id']; ?>">
        Edit
    </a>

        |

    <a href="hapus_tahun_ajaran.php?id=<?php $data['id']; ?>"
    onclick="return confirm('Yakin ingin mengahapus data ini?')">
        Hapus

    </a>
    </td>
    </tr>
    <?php } ?>
    </table>

    <br>

    <a href="dashboard.php">Kembali ke Dashboard</a>
    </body>
</html>