<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];

$total_selesai = 0;
$total_proses = 0;
$total_poin = 0;
$total_pelanggaran = 0;

$query_statistik = mysqli_query($koneksi, "
    SELECT
        COUNT(*) AS total_pelanggaran,
        COALESCE(SUM(poin), 0) AS total_poin,
        SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) AS total_selesai,
        SUM(CASE WHEN status != 'Selesai' OR status IS NULL THEN 1 ELSE 0 END) AS total_proses
    FROM t_pelanggaran_siswa
");

if ($query_statistik) {
    $statistik = mysqli_fetch_assoc($query_statistik);

    $total_selesai = (int) $statistik['total_selesai'];
    $total_proses = (int) $statistik['total_proses'];
    $total_poin = (int) $statistik['total_poin'];
    $total_pelanggaran = (int) $statistik['total_pelanggaran'];
}

$siswa = mysqli_query($koneksi, "
    SELECT DISTINCT siswa_id, nama_siswa
    FROM t_pelanggaran_siswa
    WHERE siswa_id IS NOT NULL
    ORDER BY nama_siswa ASC
");

$siswa_id = "";
$laporan = false;

if (isset($_POST['tampilkan'])) {

    $siswa_id = (int) $_POST['siswa_id'];

    $laporan = mysqli_query($koneksi, "
        SELECT *
        FROM t_pelanggaran_siswa
        WHERE siswa_id = $siswa_id
        ORDER BY tanggal DESC
    ");
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pelanggaran</title>

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
                SMK Muhammadiyah Kota Tasikmalaya
            </p>

            <p class="text-secondary small fw-bold mb-2">
                MENU NAVIGASI
            </p>

            <nav class="nav flex-column gap-1">

                <a href="dashboard.php" class="nav-link text-white rounded py-2">
                    Dashboard
                </a>

                <a href="data_siswa.php" class="nav-link text-white rounded py-2">
                    Data Siswa
                </a>

                <a href="data_guru.php" class="nav-link text-white rounded py-2">
                    Data Guru
                </a>

                <a href="data_kelas.php" class="nav-link text-white rounded py-2">
                    Kelas
                </a>

                <a href="data_tahun_ajaran.php" class="nav-link text-white rounded py-2">
                    Tahun Ajaran
                </a>

                <a href="data_penempatan_siswa.php" class="nav-link text-white rounded py-2">
                    Penempatan Siswa
                </a>

                <a href="data_wali_kelas.php" class="nav-link text-white rounded py-2">
                    Wali Kelas
                </a>

                <a href="data_kategori_pelanggaran.php" class="nav-link text-white rounded py-2">
                    Kategori Pelanggaran
                </a>

                <a href="data_jenis_pelanggaran.php" class="nav-link text-white rounded py-2">
                    Jenis Pelanggaran
                </a>

                <a href="data_laporan.php"
                   class="nav-link active bg-primary text-white rounded py-2">
                    Laporan
                </a>

                <a href="about_me.php" class="nav-link text-white rounded py-2">
                    About Me
                </a>

                <hr class="border-secondary my-2">

                <a href="logout.php" class="nav-link text-danger rounded py-2">
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
                    <h4 class="fw-bold text-dark mb-1">
                        Laporan Pelanggaran
                    </h4>

                    <small class="text-secondary">
                        Sistem Informasi Pelanggaran Siswa
                    </small>
                </div>

                <div class="d-flex align-items-center gap-2">

                    <div class="text-end">
                        <div class="fw-bold">
                            <?= htmlspecialchars($nama) ?>
                        </div>

                        <small class="text-secondary">Admin</small>
                    </div>

                    <div class="bg-primary-subtle text-primary rounded-circle
                                d-flex align-items-center justify-content-center
                                fw-bold flex-shrink-0"
                         style="width: 42px; height: 42px;">
                        <?= strtoupper(substr($nama, 0, 1)) ?>
                    </div>

                </div>

            </header>

            <!-- ISI LAPORAN -->
            <section class="p-3 p-md-4">

                <!-- KARTU STATISTIK -->
                <div class="row g-3 mb-4">

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-secondary small mb-2">
                                    Pelanggaran Selesai
                                </p>

                                <h4 class="fw-bold text-success mb-2">
                                    <?= number_format($total_selesai, 0, ',', '.') ?>
                                </h4>

                                <small class="text-secondary">
                                    Kasus selesai ditangani
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-secondary small mb-2">
                                    Sedang Diproses
                                </p>

                                <h4 class="fw-bold text-warning mb-2">
                                    <?= number_format($total_proses, 0, ',', '.') ?>
                                </h4>

                                <small class="text-secondary">
                                    Kasus belum selesai
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-secondary small mb-2">
                                    Total Poin
                                </p>

                                <h4 class="fw-bold text-primary mb-2">
                                    <?= number_format($total_poin, 0, ',', '.') ?>
                                </h4>

                                <small class="text-secondary">
                                    Akumulasi poin pelanggaran
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-secondary small mb-2">
                                    Total Pelanggaran
                                </p>

                                <h4 class="fw-bold text-danger mb-2">
                                    <?= number_format($total_pelanggaran, 0, ',', '.') ?>
                                </h4>

                                <small class="text-secondary">
                                    Seluruh kasus tercatat
                                </small>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- JUDUL LAPORAN DAN TOMBOL KEMBALI -->
                <div class="d-flex justify-content-between align-items-center
                            flex-wrap gap-2 mb-3">

                    <div>
                        <h4 class="fw-bold text-dark mb-1">
                            Laporan Pelanggaran
                        </h4>

                        <p class="text-secondary mb-0">
                            Pilih siswa untuk melihat riwayat dan total poin pelanggarannya.
                        </p>
                    </div>

                    <!-- TOMBOL KEMBALI KE DASHBOARD -->
                    <a href="dashboard.php" class="btn btn-secondary">
                        Kembali ke Dashboard
                    </a>

                </div>

                <!-- FORM PILIH SISWA -->
                <div class="card border-0 shadow-sm mb-4">

                    <div class="card-body p-4 border-bottom">

                        <h6 class="fw-bold mb-1">Pilih Siswa</h6>

                        <p class="text-secondary small mb-0">
                            Tentukan siswa untuk menampilkan laporan pelanggaran.
                        </p>

                    </div>

                    <div class="card-body p-4">

                        <form method="POST" action="">

                            <div class="row g-3 align-items-end">

                                <div class="col-12 col-md-9">

                                    <label for="siswa_id" class="form-label">
                                        Nama Siswa
                                    </label>

                                    <select
                                        name="siswa_id"
                                        id="siswa_id"
                                        class="form-select"
                                        required>

                                        <option value="">
                                            -- Pilih Siswa --
                                        </option>

                                        <?php
                                        if ($siswa) {
                                            while ($data_siswa = mysqli_fetch_assoc($siswa)) {
                                        ?>

                                            <option
                                                value="<?= (int) $data_siswa['siswa_id'] ?>"
                                                <?= ($siswa_id == $data_siswa['siswa_id']) ? 'selected' : '' ?>>

                                                <?= htmlspecialchars($data_siswa['nama_siswa']) ?>

                                            </option>

                                        <?php
                                            }
                                        }
                                        ?>

                                    </select>

                                </div>

                                <div class="col-12 col-md-3">

                                    <button
                                        type="submit"
                                        name="tampilkan"
                                        class="btn btn-primary w-100">

                                        Tampilkan Laporan

                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

                <!-- HASIL LAPORAN -->
                <?php if (isset($_POST['tampilkan'])) { ?>

                    <div class="card border-0 shadow-sm">

                        <div class="card-body p-4">

                            <h5 class="fw-bold mb-3">
                                Riwayat Pelanggaran Siswa
                            </h5>

                            <?php if ($laporan && mysqli_num_rows($laporan) > 0) { ?>

                                <div class="table-responsive">

                                    <table class="table table-bordered table-striped align-middle">

                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Siswa</th>
                                                <th>Kelas</th>
                                                <th>Jenis Pelanggaran</th>
                                                <th>Guru</th>
                                                <th>Tanggal</th>
                                                <th>Poin</th>
                                                <th>Tindakan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            <?php
                                            $no = 1;

                                            while ($data = mysqli_fetch_assoc($laporan)) {
                                            ?>

                                                <tr>
                                                    <td><?= $no++ ?></td>
                                                    <td><?= htmlspecialchars($data['nama_siswa'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($data['nama_kelas'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($data['nama_pelanggaran'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($data['nama_guru'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($data['tanggal'] ?? '-') ?></td>
                                                    <td><?= (int) ($data['poin'] ?? 0) ?></td>
                                                    <td><?= htmlspecialchars($data['tindakan'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($data['status'] ?? '-') ?></td>
                                                </tr>

                                            <?php } ?>

                                        </tbody>

                                    </table>

                                </div>

                            <?php } else { ?>

                                <div class="alert alert-info mb-0">
                                    Belum ada riwayat pelanggaran untuk siswa yang dipilih.
                                </div>

                            <?php } ?>

                        </div>

                    </div>
                    

                <?php } ?>

                <!-- FOOTER -->
                <footer class="text-center text-secondary small mt-4">
                    Sistem Informasi Pelanggaran Siswa &copy; 2026
                </footer>

            </section>

        </main>

    </div>
</div>

</body>
</html>