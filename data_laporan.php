
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include "middleware/auth.php";
include "config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'] ?? 'Admin';

$siswa_id = isset($_GET['siswa_id']) ? (int) $_GET['siswa_id'] : 0;

/* Mengambil daftar siswa untuk dropdown */
$query_siswa = "
    SELECT siswa_id, nama_siswa, MAX(nama_kelas) AS nama_kelas
    FROM t_pelanggaran_siswa
    GROUP BY siswa_id, nama_siswa
    ORDER BY nama_siswa ASC
";

$result_siswa = mysqli_query($koneksi, $query_siswa);

if (!$result_siswa) {
    die("Query siswa gagal: " . mysqli_error($koneksi));
}

/* Mengambil riwayat pelanggaran siswa yang dipilih */
$result_laporan = null;
$nama_siswa = '';
$nama_kelas = '';
$total_poin = 0;
$data_laporan = [];

if ($siswa_id > 0) {

    $query_laporan = "
        SELECT *
        FROM t_pelanggaran_siswa
        WHERE siswa_id = $siswa_id
        ORDER BY tanggal DESC, id DESC
    ";

    $result_laporan = mysqli_query($koneksi, $query_laporan);

    if (!$result_laporan) {
        die("Query laporan gagal: " . mysqli_error($koneksi));
    }

    if (mysqli_num_rows($result_laporan) > 0) {

        $data_pertama = mysqli_fetch_assoc($result_laporan);

        $nama_siswa = $data_pertama['nama_siswa'];
        $nama_kelas = $data_pertama['nama_kelas'];
        $total_poin = (int) $data_pertama['poin'];

        $data_laporan[] = $data_pertama;

        while ($data = mysqli_fetch_assoc($result_laporan)) {
            $data_laporan[] = $data;
            $total_poin += (int) $data['poin'];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pelanggaran - Sistem Informasi Pelanggaran Siswa</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
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
                   class="nav-link text-white rounded py-2">
                    Kategori Pelanggaran
                </a>

                <a href="data_jenis_pelanggaran.php"
                   class="nav-link text-white rounded py-2">
                    Jenis Pelanggaran
                </a>

                <a href="data_laporan.php"
                   class="nav-link active bg-primary text-white rounded py-2">
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
                        Laporan Pelanggaran
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

                <h3 class="fw-bold text-dark mb-2">
                    Laporan Pelanggaran
                </h3>

                <p class="text-secondary mb-4">
                    Pilih siswa untuk melihat riwayat dan total poin pelanggarannya.
                </p>

                <!-- KARTU PILIH SISWA -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">

                    <div class="card-header bg-white p-3 p-md-4">
                        <h5 class="fw-bold mb-1">
                            Pilih Siswa
                        </h5>

                        <small class="text-secondary">
                            Tentukan siswa untuk menampilkan laporan pelanggaran.
                        </small>
                    </div>

                    <div class="card-body p-3 p-md-4">

                        <form method="GET" action="data_laporan.php">

                            <div class="row align-items-end g-3">

                                <div class="col-md-9">

                                    <label for="siswa_id" class="form-label fw-semibold">
                                        Nama Siswa
                                    </label>

                                    <select name="siswa_id"
                                            id="siswa_id"
                                            class="form-select"
                                            required>

                                        <option value="">
                                            -- Pilih Siswa --
                                        </option>

                                        <?php while ($siswa = mysqli_fetch_assoc($result_siswa)) { ?>

                                            <option
                                                value="<?= (int) $siswa['siswa_id']; ?>"
                                                <?= ($siswa_id == $siswa['siswa_id']) ? 'selected' : ''; ?>>

                                                <?= htmlspecialchars($siswa['nama_siswa']); ?>

                                            </option>

                                        <?php } ?>

                                    </select>

                                </div>

                                <div class="col-md-3">

                                    <button type="submit" class="btn btn-primary w-100">
                                        Tampilkan Laporan
                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                </div>

                <!-- HASIL LAPORAN -->
                <?php if ($siswa_id > 0) { ?>

                    <?php if (!empty($data_laporan)) { ?>

                        <!-- INFORMASI SISWA -->
                        <div class="card border-0 shadow-sm rounded-3 mb-4">

                            <div class="card-header bg-white p-3 p-md-4">
                                <h5 class="fw-bold mb-1">
                                    Informasi Pelanggaran Siswa
                                </h5>

                                <small class="text-secondary">
                                    Ringkasan data pelanggaran siswa yang dipilih.
                                </small>
                            </div>

                            <div class="card-body p-3 p-md-4">

                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <div class="border rounded-3 p-3 h-100">
                                            <small class="text-secondary d-block mb-1">
                                                Nama Siswa
                                            </small>

                                            <h5 class="fw-bold mb-0">
                                                <?= htmlspecialchars($nama_siswa); ?>
                                            </h5>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="border rounded-3 p-3 h-100">
                                            <small class="text-secondary d-block mb-1">
                                                Kelas
                                            </small>

                                            <h5 class="fw-bold mb-0">
                                                <?= htmlspecialchars($nama_kelas); ?>
                                            </h5>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="bg-primary-subtle rounded-3 p-3 h-100">
                                            <small class="text-primary d-block mb-1">
                                                Total Poin
                                            </small>

                                            <h3 class="fw-bold text-primary mb-0">
                                                <?= $total_poin; ?>
                                            </h3>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- TABEL RIWAYAT PELANGGARAN -->
                        <div class="card border-0 shadow-sm rounded-3">

                            <div class="card-header bg-white p-3 p-md-4
                                        d-flex justify-content-between align-items-center
                                        flex-wrap gap-3">

                                <div>
                                    <h5 class="fw-bold mb-1">
                                        Riwayat Pelanggaran
                                    </h5>

                                    <small class="text-secondary">
                                        Daftar pelanggaran siswa berdasarkan tanggal.
                                    </small>
                                </div>

                                <div class="d-flex flex-wrap gap-2">

                                    <button type="button"
                                            class="btn btn-primary"
                                            onclick="window.print()">
                                        Cetak
                                    </button>

                                    <a href="export_pelanggaran.php?siswa_id=<?= $siswa_id; ?>"
                                       class="btn btn-success">
                                        Export
                                    </a>

                                </div>

                            </div>

                            <div class="card-body p-0">

                                <div class="table-responsive">

                                    <table class="table table-bordered table-hover
                                                  align-middle mb-0">

                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Pelanggaran</th>
                                                <th>Tanggal</th>
                                                <th>Poin</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                            <?php
                                            $no = 1;

                                            foreach ($data_laporan as $data) {
                                            ?>

                                                <tr>
                                                    <td><?= $no++; ?></td>

                                                    <td>
                                                        <?= htmlspecialchars($data['nama_pelanggaran']); ?>
                                                    </td>

                                                    <td>
                                                        <?= htmlspecialchars($data['tanggal']); ?>
                                                    </td>

                                                    <td>
                                                        <span class="badge bg-warning text-dark">
                                                            <?= (int) $data['poin']; ?> Poin
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <?php if (strtolower($data['status']) == 'selesai') { ?>

                                                            <span class="badge bg-success">
                                                                <?= htmlspecialchars($data['status']); ?>
                                                            </span>

                                                        <?php } else { ?>

                                                            <span class="badge bg-secondary">
                                                                <?= htmlspecialchars($data['status']); ?>
                                                            </span>

                                                        <?php } ?>
                                                    </td>
                                                </tr>

                                            <?php } ?>

                                            <tr class="table-light fw-bold">
                                                <th colspan="3" class="text-end">
                                                    TOTAL POIN
                                                </th>

                                                <th>
                                                    <?= $total_poin; ?>
                                                </th>

                                                <th></th>
                                            </tr>

                                        </tbody>

                                    </table>

                                </div>

                            </div>

                            <div class="card-footer bg-white p-3">

                                <a href="dashboard.php"
                                   class="btn btn-outline-secondary btn-sm">
                                    Kembali ke Dashboard
                                </a>

                            </div>

                        </div>

                    <?php } else { ?>

                        <div class="card border-0 shadow-sm rounded-3">

                            <div class="card-body p-4">

                                <div class="alert alert-warning mb-0">
                                    <h5 class="alert-heading">
                                        Belum Ada Riwayat Pelanggaran
                                    </h5>

                                    Belum ada riwayat pelanggaran untuk siswa ini.
                                </div>

                                <a href="dashboard.php"
                                   class="btn btn-outline-secondary btn-sm mt-3">
                                    Kembali ke Dashboard
                                </a>

                            </div>

                        </div>

                    <?php } ?>

                <?php } ?>

                <!-- FOOTER -->
                <footer class="text-center text-secondary small mt-4">
                    Sistem Informasi Pelanggaran Siswa &copy; 2026
                </footer>

            </section>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
