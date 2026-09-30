<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}
$query = mysqli_query($koneksi, "SELECT * FROM t_guru ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Data guru</title>
        <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    </head>

    <body class="bg-light">

    <div class="container py-4">

    <div class="card shadow-sm">

        <div class="card-body">

        <h2 class="mb-3">Data Guru</h2>

        <a href="tambah_siswa.php" class="btn btn-primary mb-3">
                + Tambah Siswa
            </a>

        <div class="table-responsive">

                <table class="table table-bordered table-striped table-hover">
    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>NIP</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>

        <?php
        $no = 1;

        while ($data = mysqli_fetch_assoc($query)) {
        ?>

        <tr>
            <td><?= $no++; ?></td>
            <td><?= $data['nip']; ?></td>
            <td><?= $data['nama']; ?></td>
            <td><?= $data['email']; ?></td>

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
                <a href="edit_guru.php?id=<?= $data['id']; ?>">
                    Edit
                </a>

                |

                <a href="hapus_guru.php?id=<?= $data['id']; ?>">
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
