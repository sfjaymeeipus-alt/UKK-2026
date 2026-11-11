
<?php

include "middleware/auth.php";
include "config/koneksi.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$role = $_SESSION['role'] ?? '';
$nama = $_SESSION['nama'] ?? 'Pengguna';
$user_id = (int) ($_SESSION['user_id'] ?? 0);

if ($role != 'admin') {
    exit("Anda tidak memiliki akses.");
}

// ======================================
// FUNGSI QUERY LAPORAN
// ======================================

function queryLaporan($koneksi, $sql, $tipe = "", $parameter = [])
{
    $stmt = mysqli_prepare($koneksi, $sql);

    if ($tipe !== "" && !empty($parameter)) {
        $bind = [$stmt, $tipe];

        foreach ($parameter as $key => $value) {
            $parameter[$key] = $value;
            $bind[] = &$parameter[$key];
        }

        call_user_func_array('mysqli_stmt_bind_param', $bind);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    return $result;
}

// ======================================
// MENU SIDEBAR ADMIN
// ======================================

$menu = [
    ['Dashboard', 'dashboard.php'],
    ['Data Siswa', 'data_siswa.php'],
    ['Data Guru', 'data_guru.php'],
    ['Kelas', 'data_kelas.php'],
    ['Tahun Ajaran', 'data_tahun_ajaran.php'],
    ['Penempatan Siswa', 'data_penempatan_siswa.php'],
    ['Wali Kelas', 'data_wali_kelas.php'],
    ['Kategori Pelanggaran', 'data_kategori_pelanggaran.php'],
    ['Jenis Pelanggaran', 'data_jenis_pelanggaran.php'],
    ['Laporan', 'data_laporan.php'],
    ['About Me', 'about_me.php']
];

$halamanAktif = basename($_SERVER['PHP_SELF']);

// ======================================
// ADMIN MELIHAT SELURUH DATA
// ======================================

$kondisiLaporan = "1=1";
$tipeParameter = "";
$parameter = [];

// ======================================
// STATISTIK LAPORAN
// ======================================

$sqlStatistik = "
    SELECT
        COUNT(*) AS total_pelanggaran,
        COALESCE(SUM(poin), 0) AS total_poin,
        SUM(CASE
            WHEN status = 'Selesai' THEN 1
            ELSE 0
        END) AS total_selesai,
        SUM(CASE
            WHEN status != 'Selesai' OR status IS NULL THEN 1
            ELSE 0
        END) AS total_proses
    FROM t_pelanggaran_siswa
    WHERE $kondisiLaporan
";

$resultStatistik = queryLaporan(
    $koneksi,
    $sqlStatistik,
    $tipeParameter,
    $parameter
);

$statistik = mysqli_fetch_assoc($resultStatistik);

// ======================================
// DATA SISWA UNTUK DROPDOWN
// ======================================

$sqlSiswa = "
    SELECT DISTINCT
        ps.siswa_id,
        ps.nama_siswa,
        ps.nama_kelas
    FROM t_pelanggaran_siswa ps
    WHERE ps.siswa_id IS NOT NULL
    ORDER BY ps.nama_siswa ASC
";

$resultSiswa = queryLaporan($koneksi, $sqlSiswa);

$daftarSiswa = [];

while ($row = mysqli_fetch_assoc($resultSiswa)) {
    $daftarSiswa[] = $row;
}

// ======================================
// PROSES FILTER LAPORAN SISWA
// ======================================

$detailLaporan = [];
$siswaTerpilih = 0;

if (isset($_POST['tampilkan'])) {

    $siswaTerpilih = (int) ($_POST['siswa_id'] ?? 0);

    if ($siswaTerpilih > 0) {

        $sqlDetail = "
            SELECT *
            FROM t_pelanggaran_siswa
            WHERE siswa_id = ?
            ORDER BY tanggal DESC
        ";

        $resultDetail = queryLaporan(
            $koneksi,
            $sqlDetail,
            "i",
            [$siswaTerpilih]
        );

        while ($row = mysqli_fetch_assoc($resultDetail)) {
            $detailLaporan[] = $row;
        }
    }
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
        rel="stylesheet"
    >

    <style>

        body {
            background-color: #f5f7fb;
        }

        .sidebar {
            min-height: 100vh;
        }

        .sidebar .nav-link {
            color: white;
        }

        .sidebar .nav-link:hover {
            background-color: #343a40;
        }

        .card-statistik {
            border: none;
            border-radius: 12px;
        }

        .isi-halaman {
            padding: 25px;
        }

        .table th {
            white-space: nowrap;
        }

        @media print {

            .sidebar,
            .btn,
            form,
            footer {
                display: none !important;
            }

            main {
                width: 100% !important;
            }

            body {
                background: white !important;
            }

            .card {
                box-shadow: none !important;
            }

        }

    </style>

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->

        <aside class="col-md-3 col-lg-2 bg-dark text-white p-3 position-sticky top-0 vh-100 overflow-auto align-self-start">

            <div class="mb-4">

                <h5 class="fw-bold mb-1">
                    Sistem Informasi
                </h5>

                <h6 class="fw-bold">
                    Pelanggaran Siswa
                </h6>

                <small class="text-secondary">
                    SMK Muhammadiyah Kota Tasikmalaya
                </small>

            </div>

            <p class="text-secondary small fw-bold mb-2">
                MENU NAVIGASI
            </p>

            <nav class="nav flex-column gap-1">

                <?php foreach ($menu as $item) { ?>

                    <a
                        href="<?= htmlspecialchars($item[1]); ?>"
                        class="nav-link rounded py-2 <?= $halamanAktif == basename($item[1]) ? 'active bg-primary text-white' : 'text-white'; ?>"
                    >

                        <?= htmlspecialchars($item[0]); ?>

                    </a>

                <?php } ?>

                <hr class="border-secondary my-2">

                <a
                    href="logout.php"
                    class="nav-link text-danger rounded py-2"
                >
                    Logout
                </a>

            </nav>

        </aside>

        <!-- KONTEN UTAMA -->

        <main class="col-md-9 col-lg-10 isi-halaman">

            <!-- HEADER -->

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h3 class="fw-bold mb-1">
                        Laporan Pelanggaran
                    </h3>

                    <p class="text-muted mb-0">
                        Sistem Informasi Pelanggaran Siswa
                    </p>

                </div>

                <div class="text-end">

                    <div class="fw-semibold">
                        <?= htmlspecialchars($nama); ?>
                    </div>

                    <small class="text-muted">
                        Administrator
                    </small>

                </div>

            </div>

            <!-- STATISTIK -->

            <div class="row g-3 mb-4">

                <div class="col-md-3">

                    <div class="card card-statistik shadow-sm">

                        <div class="card-body">

                            <p class="text-muted mb-2">
                                Total Pelanggaran
                            </p>

                            <h3 class="fw-bold mb-0">
                                <?= number_format((int) ($statistik['total_pelanggaran'] ?? 0), 0, ',', '.'); ?>
                            </h3>

                        </div>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="card card-statistik shadow-sm">

                        <div class="card-body">

                            <p class="text-muted mb-2">
                                Total Poin
                            </p>

                            <h3 class="fw-bold mb-0">
                                <?= number_format((int) ($statistik['total_poin'] ?? 0), 0, ',', '.'); ?>
                            </h3>

                        </div>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="card card-statistik shadow-sm">

                        <div class="card-body">

                            <p class="text-muted mb-2">
                                Pelanggaran Selesai
                            </p>

                            <h3 class="fw-bold mb-0">
                                <?= number_format((int) ($statistik['total_selesai'] ?? 0), 0, ',', '.'); ?>
                            </h3>

                        </div>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="card card-statistik shadow-sm">

                        <div class="card-body">

                            <p class="text-muted mb-2">
                                Dalam Proses
                            </p>

                            <h3 class="fw-bold mb-0">
                                <?= number_format((int) ($statistik['total_proses'] ?? 0), 0, ',', '.'); ?>
                            </h3>

                        </div>

                    </div>

                </div>

            </div>

            <!-- FILTER SISWA -->

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body">

                    <h5 class="fw-bold mb-3">
                        Pilih Siswa
                    </h5>

                    <form method="POST">

                        <div class="row align-items-end">

                            <div class="col-md-8 mb-3 mb-md-0">

                                <label class="form-label">
                                    Nama Siswa
                                </label>

                                <select
                                    name="siswa_id"
                                    class="form-select"
                                    required
                                >

                                    <option value="">
                                        -- Pilih Siswa --
                                    </option>

                                    <?php foreach ($daftarSiswa as $siswa) { ?>

                                        <option
                                            value="<?= (int) $siswa['siswa_id']; ?>"
                                            <?= $siswaTerpilih == $siswa['siswa_id'] ? 'selected' : ''; ?>
                                        >

                                            <?= htmlspecialchars($siswa['nama_siswa']); ?>
                                            -
                                            <?= htmlspecialchars($siswa['nama_kelas'] ?? '-'); ?>

                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <div class="col-md-4">

                                <button
                                    type="submit"
                                    name="tampilkan"
                                    class="btn btn-primary w-100"
                                >
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

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">

                            <h5 class="fw-bold mb-0">
                                Detail Laporan Pelanggaran
                            </h5>

                            <button
                                type="button"
                                onclick="window.print()"
                                class="btn btn-outline-primary btn-sm"
                            >
                                Cetak Laporan
                            </button>

                        </div>

                        <?php if (count($detailLaporan) > 0) { ?>

                            <div class="table-responsive">

                                <table class="table table-bordered table-striped align-middle">

                                    <thead class="table-dark">

                                        <tr>
                                            <th>No</th>
                                            <th>Nama Siswa</th>
                                            <th>Kelas</th>
                                            <th>Pelanggaran</th>
                                            <th>Guru</th>
                                            <th>Tanggal</th>
                                            <th>Keterangan</th>
                                            <th>Poin</th>
                                            <th>Tindakan</th>
                                            <th>Status</th>
                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php
                                        $no = 1;
                                        $totalPoinSiswa = 0;
                                        ?>

                                        <?php foreach ($detailLaporan as $data) { ?>

                                            <?php
                                            $totalPoinSiswa += (int) ($data['poin'] ?? 0);
                                            ?>

                                            <tr>

                                                <td><?= $no++; ?></td>

                                                <td>
                                                    <?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($data['nama_pelanggaran'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($data['nama_guru'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= !empty($data['tanggal']) ? date('d-m-Y', strtotime($data['tanggal'])) : '-'; ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($data['keterangan'] ?? '-'); ?>
                                                </td>

                                                <td>
                                                    <?= (int) ($data['poin'] ?? 0); ?>
                                                </td>

                                                <td>
                                                    <?= htmlspecialchars($data['tindakan'] ?? '-'); ?>
                                                </td>

                                                <td>

                                                    <?php if (($data['status'] ?? '') == 'Selesai') { ?>

                                                        <span class="badge bg-success">
                                                            Selesai
                                                        </span>

                                                    <?php } else { ?>

                                                        <span class="badge bg-warning text-dark">
                                                            <?= htmlspecialchars($data['status'] ?? 'Diproses'); ?>
                                                        </span>

                                                    <?php } ?>

                                                </td>

                                            </tr>

                                        <?php } ?>

                                    </tbody>

                                    <tfoot>

                                        <tr class="table-light fw-bold">

                                            <td colspan="7" class="text-end">
                                                Total Poin
                                            </td>

                                            <td colspan="3">
                                                <?= $totalPoinSiswa; ?>
                                            </td>

                                        </tr>

                                    </tfoot>

                                </table>

                            </div>

                        <?php } else { ?>

                            <div class="alert alert-warning mb-0">
                                Tidak ada data pelanggaran untuk siswa yang dipilih.
                            </div>

                        <?php } ?>

                    </div>

                </div>

            <?php } ?>

            <!-- FOOTER -->

            <footer class="text-center text-muted mt-4">

                <small>
                    © <?= date('Y'); ?> Sistem Informasi Pelanggaran Siswa
                    - SMK Muhammadiyah Kota Tasikmalaya
                </small>

            </footer>

        </main>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
