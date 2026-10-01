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

                    <tbody>

                    <?php

                    $no = 1;

                    while ($data = mysqli_fetch_assoc($query)) {

                    ?>

                        <tr>

                            <td>
                                <?= $no++ ?>
                            </td>

                            <td>
                                <?= $data['nis']; ?>
                            </td>

                            <td>
                                <?= $data['nisn']; ?>
                            </td>

                            <td>
                                <?= $data['nama']; ?>
                            </td>

                            <td>
                                <?= $data['jenis_kelamin']; ?>
                            </td>

                            <td>
                                <?= $data['tanggal_lahir']; ?>
                            </td>

                            <td>
                                <?= $data['alamat']; ?>
                            </td>

                            <td>

                                <?php

                                if ($data['status_aktif'] == 1) {

                                    echo '<span class="badge bg-success">Aktif</span>';

                                } else {

                                    echo '<span class="badge bg-danger">Tidak Aktif</span>';

                                }

                                ?>

                            </td>

                            <td>

                                <a
                                    href="edit_siswa.php?id=<?= $data['id']; ?>"
                                    class="btn btn-warning btn-sm">

                                    Edit

                                </a>

                                <a
                                    href="hapus_siswa.php?id=<?= $data['id']; ?>"
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')">

                                    Hapus

                                </a>

                            </td>

                        </tr>

                    <?php

                    }

                    ?>

                    </tbody>

                </table>

            </div>

            <br>

            <a href="dashboard.php" class="btn btn-secondary">
                Kembali ke Dashboard
            </a>

        </div>

    </div>

</div>

</body>

</html>