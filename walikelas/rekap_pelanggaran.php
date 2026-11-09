<?php
require_once __DIR__ . "/../middleware/auth.php";
require_once __DIR__ . "/../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    exit("Anda tidak memiliki akses.");
}

if (!isset($koneksi) || !$koneksi) {
    exit("Koneksi database tidak tersedia.");
}

$user_id = (int) ($_SESSION['user_id'] ?? 0);
$nama = $_SESSION['nama'] ?? 'Wali Kelas';

function cekQuery($query, $koneksi, $pesan)
{
    if (!$query) {
        exit($pesan . ": " . mysqli_error($koneksi));
    }

    return $query;
}

/* =====================================
   AMBIL DATA GURU
===================================== */
$sql_guru = "
    SELECT id
    FROM t_guru
    WHERE user_id = ?
    LIMIT 1
";

$query_guru = cekQuery(
    mysqli_prepare($koneksi, $sql_guru),
    $koneksi,
    "Error mengambil data guru"
);

mysqli_stmt_bind_param($query_guru, "i", $user_id);
mysqli_stmt_execute($query_guru);

$result_guru = mysqli_stmt_get_result($query_guru);
$data_guru = mysqli_fetch_assoc($result_guru);

mysqli_stmt_close($query_guru);

if (!$data_guru) {
    exit("Data guru tidak ditemukan.");
}

$guru_id = (int) $data_guru['id'];

/* =====================================
   CEK WALI KELAS AKTIF
===================================== */
$sql_wali = "
    SELECT
        wk.kelas_id,
        wk.tahun_ajaran_id,
        k.nama AS nama_kelas,
        ta.nama AS tahun_ajaran
    FROM t_wali_kelas wk
    INNER JOIN t_kelas k
        ON k.id = wk.kelas_id
    INNER JOIN t_tahun_ajaran ta
        ON ta.id = wk.tahun_ajaran_id
    WHERE wk.guru_id = ?
      AND wk.status_aktif = 1
      AND ta.status_aktif = 1
    ORDER BY k.nama ASC
";

$query_wali = cekQuery(
    mysqli_prepare($koneksi, $sql_wali),
    $koneksi,
    "Error mengambil data wali kelas"
);

mysqli_stmt_bind_param($query_wali, "i", $guru_id);
mysqli_stmt_execute($query_wali);

$result_wali = mysqli_stmt_get_result($query_wali);

$daftar_wali = [];

while ($wali = mysqli_fetch_assoc($result_wali)) {
    $daftar_wali[] = $wali;
}

mysqli_stmt_close($query_wali);

if (count($daftar_wali) == 0) {
    exit("Anda bukan wali kelas aktif.");
}

/* =====================================
   STATISTIK PELANGGARAN
   BERDASARKAN PENEMPATAN SISWA AKTIF
===================================== */
$sql_statistik = "
    SELECT
        COUNT(*) AS total_pelanggaran,
        COALESCE(SUM(ps.poin), 0) AS total_poin,

        COALESCE(SUM(
            CASE
                WHEN ps.status = 'Selesai' THEN 1
                ELSE 0
            END
        ), 0) AS total_selesai,

        COALESCE(SUM(
            CASE
                WHEN ps.status IS NULL
                     OR ps.status <> 'Selesai'
                THEN 1
                ELSE 0
            END
        ), 0) AS total_proses

    FROM t_pelanggaran_siswa ps

    WHERE EXISTS (
        SELECT 1
        FROM t_kelas_siswa ks

        INNER JOIN t_wali_kelas wk
            ON wk.kelas_id = ks.kelas_id
           AND wk.tahun_ajaran_id = ks.tahun_ajaran_id

        INNER JOIN t_tahun_ajaran ta
            ON ta.id = ks.tahun_ajaran_id

        WHERE ks.siswa_id = ps.siswa_id
          AND ks.tahun_ajaran_id = ps.tahun_ajaran_id
          AND ks.status_aktif = 1
          AND wk.guru_id = ?
          AND wk.status_aktif = 1
          AND ta.status_aktif = 1
    )
";

$query_statistik = cekQuery(
    mysqli_prepare($koneksi, $sql_statistik),
    $koneksi,
    "Error mengambil statistik"
);

mysqli_stmt_bind_param($query_statistik, "i", $guru_id);
mysqli_stmt_execute($query_statistik);

$result_statistik = mysqli_stmt_get_result($query_statistik);
$statistik = mysqli_fetch_assoc($result_statistik);

mysqli_stmt_close($query_statistik);

$total_pelanggaran = (int) ($statistik['total_pelanggaran'] ?? 0);
$total_poin = (int) ($statistik['total_poin'] ?? 0);
$total_selesai = (int) ($statistik['total_selesai'] ?? 0);
$total_proses = (int) ($statistik['total_proses'] ?? 0);

/* =====================================
   RIWAYAT PELANGGARAN
   KELAS DIAMBIL DARI PENEMPATAN AKTIF
===================================== */
$sql_rekap = "
    SELECT
        ps.id,
        ps.nama_siswa,
        k.nama AS nama_kelas,
        ps.nama_pelanggaran,
        ps.nama_guru,
        ps.tanggal,
        ps.keterangan,
        ps.poin,
        ps.tindakan,
        ps.status,
        ta.nama AS tahun_ajaran

    FROM t_pelanggaran_siswa ps

    INNER JOIN t_kelas_siswa ks
        ON ks.siswa_id = ps.siswa_id
       AND ks.tahun_ajaran_id = ps.tahun_ajaran_id
       AND ks.status_aktif = 1

    INNER JOIN t_kelas k
        ON k.id = ks.kelas_id

    INNER JOIN t_tahun_ajaran ta
        ON ta.id = ks.tahun_ajaran_id
       AND ta.status_aktif = 1

    WHERE EXISTS (
        SELECT 1
        FROM t_wali_kelas wk
        WHERE wk.guru_id = ?
          AND wk.kelas_id = ks.kelas_id
          AND wk.tahun_ajaran_id = ks.tahun_ajaran_id
          AND wk.status_aktif = 1
    )

    ORDER BY
        ps.tanggal ASC,
        ps.id ASC
";

$query_rekap = cekQuery(
    mysqli_prepare($koneksi, $sql_rekap),
    $koneksi,
    "Error mengambil riwayat pelanggaran"
);

mysqli_stmt_bind_param($query_rekap, "i", $guru_id);
mysqli_stmt_execute($query_rekap);

$daftar_rekap = mysqli_stmt_get_result($query_rekap);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Rekap Pelanggaran - Wali Kelas</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR TETAP DIAM -->
        <aside class="col-md-3 col-lg-2 bg-dark text-white p-3 position-sticky top-0 vh-100 overflow-auto align-self-start">
            <h5 class="fw-bold mb-1">
                Sistem Informasi
            </h5>

            <h6 class="fw-bold">
                Pelanggaran Siswa
            </h6>

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

                <a href="data_siswa.php"
                   class="nav-link text-white rounded py-2">
                    Data Siswa
                </a>

                <a href="rekap_pelanggaran.php"
                   class="nav-link active bg-primary text-white rounded py-2">
                    Rekap Pelanggaran
                </a>

                <a href="cetak_rekap.php"
                   class="nav-link text-white rounded py-2">
                    Cetak Rekap
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

        <!-- KONTEN UTAMA -->
        <main class="col-9 col-lg-10 ms-auto p-0">

            <!-- HEADER -->
            <header class="bg-white border-bottom p-3 p-md-4 d-flex justify-content-between align-items-center">

                <div>
                    <h4 class="fw-bold mb-1">
                        Rekap Pelanggaran
                    </h4>

                    <small class="text-secondary">
                        Rekap pelanggaran siswa kelas yang Anda ampu
                    </small>
                </div>

                <div class="text-end">
                    <div class="fw-bold">
                        <?= htmlspecialchars($nama) ?>
                    </div>

                    <small class="text-secondary">
                        Wali Kelas
                    </small>
                </div>

            </header>

            <section class="p-3 p-md-4">

                <!-- STATISTIK -->
                <div class="row g-3 mb-4">

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-secondary small mb-2">
                                    Pelanggaran Selesai
                                </p>

                                <h4 class="fw-bold text-success">
                                    <?= number_format($total_selesai, 0, ',', '.') ?>
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-secondary small mb-2">
                                    Sedang Diproses
                                </p>

                                <h4 class="fw-bold text-warning">
                                    <?= number_format($total_proses, 0, ',', '.') ?>
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-secondary small mb-2">
                                    Total Poin
                                </p>

                                <h4 class="fw-bold text-primary">
                                    <?= number_format($total_poin, 0, ',', '.') ?>
                                </h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <p class="text-secondary small mb-2">
                                    Total Pelanggaran
                                </p>

                                <h4 class="fw-bold text-danger">
                                    <?= number_format($total_pelanggaran, 0, ',', '.') ?>
                                </h4>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- JUDUL TABEL -->
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">

                    <div>
                        <h5 class="fw-bold mb-1">
                            Riwayat Pelanggaran Siswa
                        </h5>

                        <p class="text-secondary mb-0">
                            Data hanya berasal dari kelas yang Anda ampu.
                        </p>
                    </div>

                    <a href="cetak_rekap.php"
                       class="btn btn-primary">
                        Cetak Rekap
                    </a>

                </div>

                <!-- TABEL -->
                <div class="card border-0 shadow-sm">

                    <div class="card-body p-4">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped align-middle">

                                <thead class="table-dark">

                                    <tr>
                                        <th>No</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Jenis Pelanggaran</th>
                                        <th>Guru Pencatat</th>
                                        <th>Tanggal</th>
                                        <th>Poin</th>
                                        <th>Tindakan</th>
                                        <th>Status</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php if (mysqli_num_rows($daftar_rekap) > 0) { ?>

                                        <?php $no = 1; ?>

                                        <?php while ($data = mysqli_fetch_assoc($daftar_rekap)) { ?>

                                            <tr>

                                                <td>
                                                    <?= $no++ ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($data['nama_siswa'] ?? '-') ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($data['nama_kelas'] ?? '-') ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($data['nama_pelanggaran'] ?? '-') ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($data['nama_guru'] ?? '-') ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($data['tanggal'] ?? '-') ?>
                                                </td>

                                                <td>
                                                    <?= (int) ($data['poin'] ?? 0) ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($data['tindakan'] ?? '-') ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($data['status'] ?? '-') ?>
                                                </td>

                                            </tr>

                                        <?php } ?>

                                    <?php } else { ?>

                                        <tr>
                                            <td colspan="9" class="text-center py-4">
                                                Belum ada data pelanggaran pada kelas yang Anda ampu.
                                            </td>
                                        </tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

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

<?php
mysqli_stmt_close($query_rekap);
?>