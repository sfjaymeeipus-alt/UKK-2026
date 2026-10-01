<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$query = "
    SELECT
        p.id,
        p.kode,
        p.nama,
        p.poin,
        p.deskripsi,
        p.status_aktif,
        pk.nama AS nama_kategori
    FROM t_pelanggaran p

    INNER JOIN t_pelanggaran_kategori pk
        ON p.pelanggaran_kategori_id = pk.id

    ORDER BY p.id DESC
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

    <title>Data Jenis Pelanggaran</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body  class="bg-light">
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-body">

    <h2 class="mb-3">Data Jenis Pelanggaran</h2>

    <a href="tambah_jenis_pelanggaran.php" class="btn btn-primary mb-3">
        + Tambah Jenis Pelanggaran
    </a>
<div class="table-responsive">

     <table class="table table-bordered table-striped table-hover">

        <thead class="table-dark">

            <tr>

                <th>No</th>
                <th>Kode</th>
                <th>Nama Pelanggaran</th>
                <th>Kategori</th>
                <th>Poin</th>
                <th>Deskripsi</th>
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
                            <?= htmlspecialchars($data['kode']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['nama']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['nama_kategori']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['poin']); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($data['deskripsi']); ?>
                        </td>

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

                            <a href="edit_jenis_pelanggaran.php?id=<?= $data['id']; ?>">
                                Edit
                            </a>

                            |

                            <a
                                href="hapus_jenis_pelanggaran.php?id=<?= $data['id']; ?>"
                                onclick="return confirm('Yakin ingin menghapus jenis pelanggaran ini?');"
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

                    <td colspan="8">
                        Belum ada data jenis pelanggaran.
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