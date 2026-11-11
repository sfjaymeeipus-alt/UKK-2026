
<?php
require_once __DIR__ . "/../middleware/auth.php";
require_once __DIR__ . "/../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    exit("Anda tidak memiliki akses.");
}

$user_id = (int) ($_SESSION['user_id'] ?? 0);
$nama = $_SESSION['nama'] ?? 'Wali Kelas';

/* AMBIL DATA GURU */
$query_guru = mysqli_prepare($koneksi, "
    SELECT id
    FROM t_guru
    WHERE user_id = ?
    LIMIT 1
");

mysqli_stmt_bind_param($query_guru, "i", $user_id);
mysqli_stmt_execute($query_guru);

$hasil_guru = mysqli_stmt_get_result($query_guru);
$data_guru = mysqli_fetch_assoc($hasil_guru);

if (!$data_guru) {
    exit("Data guru tidak ditemukan.");
}

$guru_id = (int) $data_guru['id'];

/* CEK STATUS WALI KELAS AKTIF */
$query_akses = mysqli_prepare($koneksi, "
    SELECT COUNT(*) AS total
    FROM t_wali_kelas wk
    INNER JOIN t_tahun_ajaran ta
        ON ta.id = wk.tahun_ajaran_id
    WHERE wk.guru_id = ?
      AND wk.status_aktif = 1
      AND ta.status_aktif = 1
");

mysqli_stmt_bind_param($query_akses, "i", $guru_id);
mysqli_stmt_execute($query_akses);

$akses = mysqli_fetch_assoc(
    mysqli_stmt_get_result($query_akses)
);

if ((int) $akses['total'] < 1) {
    exit("Anda bukan wali kelas aktif.");
}

/* AMBIL DATA SISWA */
$query_siswa = mysqli_prepare($koneksi, "
    SELECT DISTINCT
        s.id,
        s.nis,
        s.nisn,
        s.nama,
        s.jenis_kelamin,
        s.tanggal_lahir,
        s.alamat,
        k.nama AS nama_kelas,
        ta.nama AS tahun_ajaran
    FROM t_wali_kelas wk
    INNER JOIN t_tahun_ajaran ta
        ON ta.id = wk.tahun_ajaran_id
    INNER JOIN t_kelas k
        ON k.id = wk.kelas_id
    INNER JOIN t_kelas_siswa ks
        ON ks.kelas_id = wk.kelas_id
       AND ks.tahun_ajaran_id = wk.tahun_ajaran_id
    INNER JOIN t_siswa s
        ON s.id = ks.siswa_id
    WHERE wk.guru_id = ?
      AND wk.status_aktif = 1
      AND ta.status_aktif = 1
      AND ks.status_aktif = 1
    ORDER BY k.nama ASC, s.nama ASC
");

if (!$query_siswa) {
    exit("Query data siswa gagal: " . mysqli_error($koneksi));
}

mysqli_stmt_bind_param($query_siswa, "i", $guru_id);
mysqli_stmt_execute($query_siswa);

$daftar_siswa = mysqli_stmt_get_result($query_siswa);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Data Siswa - Wali Kelas</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <aside class="col-md-3 col-lg-2 bg-dark text-white min-vh-100 p-3 position-sticky top-0 vh-100 overflow-auto">

            <h5 class="fw-bold mb-1">Sistem Informasi</h5>
            <h6 class="fw-bold">Pelanggaran Siswa</h6>

            <p class="text-secondary small mb-4">
                SMK Muhammadiyah Kota Tasikmalaya
            </p>

            <p class="text-secondary small fw-bold mb-2">
                MENU NAVIGASI
            </p>

            <nav class="nav flex-column gap-1">

                <a href="../dashboard.php"
                   class="nav-link text-white rounded py-2">
                    Dashboard
                </a>

                <a href="../data_catatan_pelanggaran.php"
                    class="nav-link text-white rounded py-2">
                    Catatan Pelanggaran
                </a>

                <a href="data_siswa.php"
                   class="nav-link active bg-primary text-white rounded py-2">
                    Data Siswa
                </a>

                <a href="../data_riwayat.php"
                   class="nav-link text-white rounded py-2">
                    Riwayat
                </a>

                <a href="cetak_rekap.php"
                   class="nav-link text-white rounded py-2">
                    Rekap Poin
                </a>

                <a href="data_laporan.php"
                   class="nav-link text-white rounded py-2">
                    Laporan
                </a>

                <a href="../about_me.php"
                   class="nav-link text-white rounded py-2">
                    About Me
                </a>

                <hr class="border-secondary my-2">

                <a href="../logout.php"
                   class="nav-link text-danger rounded py-2">
                    Logout
                </a>

            </nav>

        </aside>

        <!-- KONTEN UTAMA SAMA DENGAN DATA GURU -->
        <main class="col-md-9 col-lg-10 p-0">

            <!-- HEADER -->
            <header class="bg-white border-bottom p-3 p-md-4
                           d-flex justify-content-between align-items-center">

                <div>
                    <h4 class="fw-bold text-dark mb-1">
                        Data Siswa
                    </h4>

                    <small class="text-secondary">
                        Data siswa kelas yang menjadi tanggung jawab Anda
                    </small>
                </div>

                <div class="d-flex align-items-center gap-2">

                    <div class="text-end">
                        <div class="fw-bold">
                            <?= htmlspecialchars($nama); ?>
                        </div>

                        <small class="text-secondary">
                            Wali Kelas
                        </small>
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

                <div class="card border-0 shadow-sm rounded-3">

                    <div class="card-header bg-white p-3 p-md-4">

                        <h5 class="fw-bold mb-1">
                            Daftar Siswa
                        </h5>

                        <small class="text-secondary">
                            Informasi siswa pada kelas yang Anda ampu.
                        </small>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover align-middle mb-0">

                                <thead class="table-dark">

                                    <tr>
                                        <th>No</th>
                                        <th>NIS</th>
                                        <th>NISN</th>
                                        <th>Nama Siswa</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Tanggal Lahir</th>
                                        <th>Kelas</th>
                                        <th>Tahun Ajaran</th>
                                        <th>Status</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php if (mysqli_num_rows($daftar_siswa) > 0) { ?>

                                        <?php $no = 1; ?>

                                        <?php while ($siswa = mysqli_fetch_assoc($daftar_siswa)) { ?>

                                            <tr>

                                                <td>
                                                    <?= $no++; ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($siswa['nis'] ?? ''); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($siswa['nisn'] ?? ''); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($siswa['nama'] ?? ''); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($siswa['jenis_kelamin'] ?? ''); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($siswa['tanggal_lahir'] ?? ''); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($siswa['nama_kelas'] ?? ''); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($siswa['tahun_ajaran'] ?? ''); ?>
                                                </td>

                                                <td>
                                                    <span class="badge bg-success">
                                                        Terdaftar
                                                    </span>
                                                </td>

                                            </tr>

                                        <?php } ?>

                                    <?php } else { ?>

                                        <tr>
                                            <td colspan="9" class="text-center">
                                                Belum ada siswa pada kelas yang Anda ampu.
                                            </td>
                                        </tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <!-- BAGIAN BAWAH -->
                    <div class="card-footer bg-white p-3">

                        <a href="../dashboard.php"
                           class="btn btn-outline-secondary btn-sm">
                            Kembali ke Dashboard
                        </a>

                    </div>

                </div>

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
