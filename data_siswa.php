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
    </head>
    <body>

    <h2>Data Siswa</h2>

    <a href="tambah_siswa.php"> + Tambah Siswa</a>

    <br><br>

    <table border="1" cellpadding="10" cellspacing="0">

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
    <?php
    }
    ?>
    </table>

    <br>

    <a href="dashboard.php">Kembali ke Dashboard</a>
    </body>
</html>