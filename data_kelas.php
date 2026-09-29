<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$query = mysqli_query($koneksi, "SELECT * FROM t_kelas ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Kelas</title>
</head>

<body>

<h2>Data Kelas</h2>

<a href="tambah_kelas.php">+ Tambah Kelas</a>

<br><br>

<table border="1" cellpadding="10" cellspacing="0">

    <tr>
        <th>No</th>
        <th>Nama Kelas</th>
        <th>Tingkat</th>
        <th>Jurusan</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

<?php

$no = 1;

while ($data = mysqli_fetch_assoc($query)) {

?>

    <tr>

        <td><?= $no++; ?></td>

        <td><?= $data['nama']; ?></td>

        <td><?= $data['tingkat']; ?></td>

        <td><?= $data['jurusan']; ?></td>

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

            <a href="edit_kelas.php?id=<?= $data['id']; ?>">
                Edit
            </a>

            |

            <a href="hapus_kelas.php?id=<?= $data['id']; ?>"
               onclick="return confirm('Yakin ingin menghapus data ini?')">
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