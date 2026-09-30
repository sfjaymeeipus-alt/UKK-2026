<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$query = mysqli_query($koneksi, "SELECT * FROM t_siswa ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
    <head>
        <title>Data Siswa</title>
        <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    </head>
    <body class="bg-light">

<div class="container py-4">

    <div class="card shadow-sm">

        <div class="card-body">

            <h2 class="mb-3">Data Siswa</h2>

            <a href="tambah_siswa.php" class="btn btn-primary mb-3">
                + Tambah Siswa
            </a>

            <div class="table-responsive">

                <table class="table table-bordered table-striped table-hover">

                    <thead class="table-dark">
                        <tr>
                            <th>No</th>
                            <th>NIS</th>
                            <th>NISN</th>
                            <th>Nama</th>
                            <th>Jenis Kelamin</th>
                            <th>Tanggal Lahir</th>
                            <th>Alamat</th>
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
    <td><?= $data['nis']; ?></td>
    <td><?= $data['nisn']; ?></td>
    <td><?= $data['nama']; ?></td>
    <td><?= $data['jenis_kelamin']; ?></td>
    <td><?= $data['tanggal_lahir']; ?></td>
    <td><?= $data['alamat']; ?></td>

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
        
    <a href="edit_siswa.php?id=<?= $data['id']; ?>">
        Edit
    </a>

        |

    <a href="hapus_siswa.php?id=<?php $data['id']; ?>"
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