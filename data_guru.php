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
    </head>

    <body>
        <h2>Data Guru</h2>

        <a class="tambah" href="tambah_guru.php">
        + Tambah Guru </a>

        <br><br>

        <table border="1" cellpadding="10" cellspacing="0">

        <tr>
            <th>No</th>
            <th>NIP</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

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
