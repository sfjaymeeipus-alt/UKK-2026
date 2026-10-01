<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$query = "
    SELECT
        id,
        nama_siswa,
        nama_kelas,
        nama_pelanggaran,
        nama_guru,
        tanggal,
        keterangan,
        poin,
        tindakan,
        status
    FROM t_pelanggaran_siswa
    ORDER BY id DESC
";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($koneksi));
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Catatan Pelanggaran Siswa</title>

</head>

<body>

    <h2>Catatan Pelanggaran Siswa</h2>

    <a href="tambah_pelanggaran_siswa.php">
        + Tambah Catatan Pelanggaran
    </a>

    <br>
    <br>

    <table border="1" cellpadding="8" cellspacing="0">

        <thead>

            <tr>

                <th>No</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Jenis Pelanggaran</th>
                <th>Guru</th>
                <th>Tanggal</th>
                <th>Poin</th>
                <th>Keterangan</th>
                <th>Tindakan</th>
                <th>Status</th>
                <th>Aksi</th>

            </tr>

        </thead>

        <tbody>

            <?php

            $no = 1;

            if (mysqli_num_rows($result) > 0) {

                while ($data = mysqli_fetch_assoc($result)) {

            ?>

                    <tr>

                        <td>
                            <?= $no++; ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['nama_siswa']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['nama_kelas']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['nama_pelanggaran']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['nama_guru']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['tanggal']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['poin']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['keterangan']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['tindakan']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['status']); ?>
                        </td>

                        <td>

                            <a href="edit_pelanggaran_siswa.php?id=<?= $data['id']; ?>">
                                Edit
                            </a>

                            |

                            <a
                                href="hapus_pelanggaran_siswa.php?id=<?= $data['id']; ?>"
                                onclick="return confirm('Yakin ingin menghapus catatan pelanggaran ini?');"
                            >
                                Hapus
                            </a>

                        </td>

                    </tr>

            <?php

                }

            } else {

            ?>

                <tr>

                    <td colspan="11">
                        Belum ada catatan pelanggaran siswa.
                    </td>

                </tr>

            <?php

            }

            ?>

        </tbody>

    </table>
<br>

    <a href="dashboard.php">Kembali ke Dashboard</a>
</body>

</html>