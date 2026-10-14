
<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];

$query = "
    SELECT
        id,
        nama,
        deskripsi,
        status_aktif,
        created_at,
        updated_at
    FROM t_pelanggaran_kategori
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Kategori Pelanggaran</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <aside class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3">

            <h5 class="fw-bold mb-1">Sistem Informasi</h5>
            <h6 class="fw-bold">Pelanggaran Siswa</h6>

            <p class="text-secondary small mb-4">
                Panel Pengelolaan Sekolah
            </p>

            <p class="text-secondary small fw-bold mb-2">
                MENU NAVIGASI
            </p>

            <nav class="nav flex-column gap-1">

                <a href="dashboard.php"
                   class="nav-link text-white rounded py-2">
                    Dashboard
                </a>

                <a href="data_siswa.php"
                   class="nav-link text-white rounded py-2">
                    Data Siswa
                </a>

                <a href="data_guru.php"
                   class="nav-link text-white rounded py-2">
                    Data Guru
                </a>

                <a href="data_kelas.php"
                   class="nav-link text-white rounded py-2">
                    Kelas
                </a>

                <a href="data_tahun_ajaran.php"
                   class="nav-link text-white rounded py-2">
                    Tahun Ajaran
                </a>

                <a href="data_penempatan_siswa.php"
                   class="nav-link text-white rounded py-2">
                    Penempatan Siswa
                </a>

                <a href="data_wali_kelas.php"
                   class="nav-link text-white rounded py-2">
                    Wali Kelas
                </a>

                <a href="data_kategori_pelanggaran.php"
                   class="nav-link active bg-primary text-white rounded py-2">
                    Kategori Pelanggaran
                </a>

                <a href="data_jenis_pelanggaran.php"
                   class="nav-link text-white rounded py-2">
                    Jenis Pelanggaran
                </a>

                <a href="data_laporan.php"
                   class="nav-link text-white rounded py-2">
                    Laporan
                </a>

                <a href="about_me.php"
                   class="nav-link text-white rounded py-2">
                    About Me
                </a>

                <hr class="border-secondary my-2">

                <a href="logout.php"
                   class="nav-link text-danger rounded py-2">
                    Logout
                </a>

            </nav>

        </aside>

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-0">

            <!-- HEADER -->
            <header class="bg-white border-bottom p-3 p-md-4
                          d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        Data Kategori Pelanggaran
                    </h5>

                    <small class="text-secondary">
                        Sistem Informasi Pelanggaran Siswa
                    </small>
                </div>

                <div class="d-flex align-items-center gap-2">

                    <div class="text-end">
                        <div class="fw-bold">
                            <?= htmlspecialchars($nama); ?>
                        </div>

                        <small class="text-secondary">Admin</small>
                    </div>

                    <div class="bg-primary-subtle text-primary rounded-circle
                                d-flex justify-content-center align-items-center
                                fw-bold flex-shrink-0"
                         style="width: 42px; height: 42px;">
                        <?= strtoupper(substr($nama, 0, 1)); ?>
                    </div>

                </div>

            </header>

            <!-- ISI HALAMAN -->
            <section class="p-3 p-md-4">

                <div class="mb-4">
                    <h3 class="fw-bold mb-1">
                        Daftar Kategori Pelanggaran
                    </h3>

                    <p class="text-secondary mb-0">
                        Kelola kategori untuk mengelompokkan pelanggaran siswa.
                    </p>
                </div>

                <!-- KARTU TABEL -->
                <div class="card border-0 shadow-sm rounded-3">

                    <!-- HEADER KARTU -->
                    <div class="card-header bg-white p-3 p-md-4
                                d-flex justify-content-between align-items-center
                                flex-wrap gap-3">

                        <div>
                            <h6 class="fw-bold mb-1">
                                Tabel Kategori Pelanggaran
                            </h6>

                            <small class="text-secondary">
                                Data kategori yang tersimpan di sistem.
                            </small>
                        </div>

                        <a href="tambah_kategori_pelanggaran.php"
                           class="btn btn-primary">
                            + Tambah Kategori
                        </a>

                    </div>

                    <!-- TABEL DATA -->
                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover
                                          align-middle mb-0">

                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Nama Kategori</th>
                                        <th>Deskripsi</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>

                                <?php
                                $no = 1;

                                if (mysqli_num_rows($result) > 0) {

                                    while ($data = mysqli_fetch_assoc($result)) {
                                ?>

                                    <tr>
                                        <td class="text-center">
                                            <?= $no++; ?>
                                        </td>

                                        <td class="fw-semibold">
                                            <?= htmlspecialchars($data['nama']); ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['deskripsi'] ?? ''); ?>
                                        </td>

                                        <td class="text-center">
                                            <?php
                                            if ($data['status_aktif'] == 1) {
                                                echo '<span class="badge bg-success">Aktif</span>';
                                            } else {
                                                echo '<span class="badge bg-danger">Tidak Aktif</span>';
                                            }
                                            ?>
                                        </td>

                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">

                                                <a
                                                    href="edit_kategori_pelanggaran.php?id=<?= urlencode($data['id']); ?>"
                                                    class="btn btn-warning btn-sm">
                                                    Edit
                                                </a>

                                                <a
                                                    href="hapus_kategori_pelanggaran.php?id=<?= urlencode($data['id']); ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Yakin ingin menghapus kategori ini?');">
                                                    Hapus
                                                </a>

                                            </div>
                                        </td>
                                    </tr>

                                <?php
                                    }

                                } else {
                                ?>

                                    <tr>
                                        <td colspan="5"
                                            class="text-center text-secondary p-4">
                                            Belum ada data kategori pelanggaran.
                                        </td>
                                    </tr>

                                <?php
                                }
                                ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <!-- FOOTER KARTU -->
                    <div class="card-footer bg-white p-3
                                d-flex justify-content-between align-items-center
                                flex-wrap gap-2">

                        <small class="text-secondary">
                            Sistem Informasi Pelanggaran Siswa
                        </small>

                        <a href="dashboard.php"
                           class="btn btn-outline-secondary btn-sm">
                            Kembali ke Dashboard
                        </a>

                    </div>

                </div>

                <!-- FOOTER HALAMAN -->
                <footer class="text-center text-secondary small mt-4">
                    Sistem Informasi Pelanggaran Siswa &copy; 2026
                </footer>

            </section>

        </main>

    </div>
</div>

</body>
</html>
