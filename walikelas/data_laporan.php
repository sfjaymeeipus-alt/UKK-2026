
<?php

require_once "../middleware/auth.php";
require_once "../config/koneksi.php";

global $koneksi;

$role = $_SESSION['role'] ?? '';
$nama = $_SESSION['nama'] ?? 'Pengguna';
$user_id = (int) ($_SESSION['user_id'] ?? 0);

if (!in_array($role, ['admin', 'guru'])) {
    exit("Anda tidak memiliki akses.");
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

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
// CEK DATA GURU
// ======================================

$guru = null;
$guru_id = 0;

if ($role == 'guru') {
    $stmtGuru = mysqli_prepare(
        $koneksi,
        "SELECT id, nama FROM t_guru WHERE user_id = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($stmtGuru, "i", $user_id);
    mysqli_stmt_execute($stmtGuru);

    $resultGuru = mysqli_stmt_get_result($stmtGuru);
    $guru = mysqli_fetch_assoc($resultGuru);

    mysqli_stmt_close($stmtGuru);

    if (!$guru) {
        exit("Data guru tidak ditemukan.");
    }

    $guru_id = (int) $guru['id'];
}

// ======================================
// CEK WALI KELAS AKTIF
// ======================================

$isWaliKelas = false;
$daftarKelas = [];

if ($role == 'guru') {
    $sqlWali = "
        SELECT
            wk.id,
            wk.kelas_id,
            wk.tahun_ajaran_id,
            k.nama AS nama_kelas,
            ta.nama AS nama_tahun_ajaran
        FROM t_wali_kelas wk
        JOIN t_kelas k ON wk.kelas_id = k.id
        JOIN t_tahun_ajaran ta ON wk.tahun_ajaran_id = ta.id
        WHERE wk.guru_id = ?
        AND wk.status_aktif = 1
        AND ta.status_aktif = 1
    ";

    $stmtWali = mysqli_prepare($koneksi, $sqlWali);
    mysqli_stmt_bind_param($stmtWali, "i", $guru_id);
    mysqli_stmt_execute($stmtWali);

    $resultWali = mysqli_stmt_get_result($stmtWali);

    while ($row = mysqli_fetch_assoc($resultWali)) {
        $daftarKelas[] = $row;
    }

    mysqli_stmt_close($stmtWali);

    if (count($daftarKelas) > 0) {
        $isWaliKelas = true;
    }
}

// ======================================
// MENU SIDEBAR
// ======================================

$menuAdmin = [
    ['Dashboard', '../dashboard.php'],
    ['Data Siswa', '../data_siswa.php'],
    ['Data Guru', '../data_guru.php'],
    ['Kelas', '../data_kelas.php'],
    ['Tahun Ajaran', '../data_tahun_ajaran.php'],
    ['Penempatan Siswa', '../data_penempatan_siswa.php'],
    ['Wali Kelas', '../data_wali_kelas.php'],
    ['Kategori Pelanggaran', '../data_kategori_pelanggaran.php'],
    ['Jenis Pelanggaran', '../data_jenis_pelanggaran.php'],
    ['Laporan', '../data_laporan.php'],
    ['About Me', '../about_me.php']
];

$menuGuru = [
    ['Dashboard', '../dashboard.php'],
    ['Catatan Pelanggaran', '../data_catatan_pelanggaran.php'],
    ['Tindakan', '../data_tindakan.php'],
    ['Riwayat', '../data_riwayat.php'],
    ['Rekap Poin', '../data_rekap_poin.php'],
    ['About Me', '../about_me.php']
];

$menuWali = [
    ['Dashboard', '../dashboard.php'],
    ['Catatan Pelanggaran', '../data_catatan_pelanggaran.php'],
    ['Tindakan', '../data_tindakan.php'],
    ['Riwayat', '../data_riwayat.php'],
    ['Rekap Poin', '../data_rekap_poin.php'],
    ['Laporan', 'data_laporan.php'],
    ['About Me', '../about_me.php']
];

if ($role == 'admin') {
    $menu = $menuAdmin;
} elseif ($isWaliKelas) {
    $menu = $menuWali;
} else {
    $menu = $menuGuru;
}

$halamanAktif = basename($_SERVER['PHP_SELF']);

// ======================================
// KONDISI DATA BERDASARKAN ROLE
// ======================================

$kondisiLaporan = "1=1";
$tipeParameter = "";
$parameter = [];

if ($role == 'guru' && $isWaliKelas) {

    $kondisiKelas = [];

    foreach ($daftarKelas as $kelas) {
        $kondisiKelas[] = "(ps.kelas_id = ? AND ps.tahun_ajaran_id = ?)";

        $parameter[] = (int) $kelas['kelas_id'];
        $parameter[] = (int) $kelas['tahun_ajaran_id'];

        $tipeParameter .= "ii";
    }

    $kondisiLaporan = "(" . implode(" OR ", $kondisiKelas) . ")";

} elseif ($role == 'guru') {

    $kondisiLaporan = "ps.guru_id = ?";
    $parameter[] = $guru_id;
    $tipeParameter = "i";
}

// ======================================
// STATISTIK LAPORAN
// ======================================

$sqlStatistik = "
    SELECT
        COUNT(*) AS total_pelanggaran,
        COALESCE(SUM(ps.poin), 0) AS total_poin,
        SUM(CASE WHEN ps.status = 'Selesai' THEN 1 ELSE 0 END) AS total_selesai,
        SUM(CASE WHEN ps.status != 'Selesai' OR ps.status IS NULL THEN 1 ELSE 0 END) AS total_proses
    FROM t_pelanggaran_siswa ps
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
    WHERE $kondisiLaporan
    ORDER BY ps.nama_siswa ASC
";

$resultSiswa = queryLaporan(
    $koneksi,
    $sqlSiswa,
    $tipeParameter,
    $parameter
);

$daftarSiswa = [];

while ($row = mysqli_fetch_assoc($resultSiswa)) {
    $daftarSiswa[] = $row;
}

// ======================================
// PROSES DATA LAPORAN
// ======================================

$detailLaporan = [];
$siswaTerpilih = 0;

// WALI KELAS LANGSUNG MENAMPILKAN DATA

if ($role == 'guru' && $isWaliKelas) {

    $sqlDetail = "
        SELECT
            ps.*
        FROM t_pelanggaran_siswa ps
        WHERE $kondisiLaporan
        AND ps.status = 'Selesai'
        ORDER BY ps.tanggal DESC
    ";

    $resultDetail = queryLaporan(
        $koneksi,
        $sqlDetail,
        $tipeParameter,
        $parameter
    );

    while ($row = mysqli_fetch_assoc($resultDetail)) {
        $detailLaporan[] = $row;
    }

// ADMIN DAN GURU BIASA TETAP MENGGUNAKAN FILTER

} elseif (isset($_POST['tampilkan'])) {

    $siswaTerpilih = (int) ($_POST['siswa_id'] ?? 0);

    if ($siswaTerpilih > 0) {

        $sqlDetail = "
            SELECT
                ps.*
            FROM t_pelanggaran_siswa ps
            WHERE ps.siswa_id = ?
            AND $kondisiLaporan
            AND ps.status = 'Selesai'
            ORDER BY ps.tanggal DESC
        ";

        $tipeDetail = "i" . $tipeParameter;

        $parameterDetail = array_merge(
            [$siswaTerpilih],
            $parameter
        );

        $resultDetail = queryLaporan(
            $koneksi,
            $sqlDetail,
            $tipeDetail,
            $parameterDetail
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

    <!-- DATATABLES -->
    <link
        href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css"
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

        /* ======================================
           TAMPILAN DATATABLES
        ====================================== */

        #tabelLaporan {
            width: 100% !important;
            border-collapse: collapse;
        }

        #tabelLaporan thead th {
            background-color: #1556cf !important;
            color: white !important;
            text-align: center;
            vertical-align: middle;
            font-weight: 600;
            padding: 12px;
            border-color: #1556cf !important;
        }

        #tabelLaporan tbody td {
            padding: 11px;
            vertical-align: middle;
        }

        #tabelLaporan tbody tr:nth-child(even) {
            background-color: #f2f6ff;
        }

        #tabelLaporan tbody tr:hover {
            background-color: #e4edff !important;
        }

        #tabelLaporan tfoot td {
            background-color: #eaf1ff;
            font-weight: bold;
            padding: 12px;
        }

        .dataTables_wrapper {
            width: 100%;
        }

        .dt-search input,
        .dt-length select {
            border: 1px solid #ced4da !important;
            border-radius: 6px !important;
            padding: 6px 10px !important;
        }

        .dt-search input:focus,
        .dt-length select:focus {
            border-color: #1556cf !important;
            outline: none;
        }

        .dt-paging .dt-paging-button.current {
            background: #1556cf !important;
            color: white !important;
            border: 1px solid #1556cf !important;
            border-radius: 5px !important;
        }

        .dt-paging .dt-paging-button:hover {
            background: #dce8ff !important;
            color: #1556cf !important;
            border: 1px solid #dce8ff !important;
            border-radius: 5px !important;
        }
    </style>
</head>

<body>

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
                   class="nav-link text-white rounded py-2">
                    Data Siswa
                </a>

                <a href="../data_riwayat.php"
                   class="nav-link text-white rounded py-2">
                    Riwayat
                </a>

                <a href="../data_rekap_poin.php"
                   class="nav-link text-white rounded py-2">
                    Rekap Poin
                </a>

                <a href="data_laporan.php"
                   class="nav-link active bg-primary text-white rounded py-2">
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

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 isi-halaman">

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

                        <?php
                        if ($role == 'admin') {
                            echo "Administrator";
                        } elseif ($isWaliKelas) {
                            echo "Wali Kelas";
                        } else {
                            echo "Guru";
                        }
                        ?>

                    </small>

                </div>

            </div>

            <?php if ($role == 'guru' && $isWaliKelas) { ?>

                <div class="alert alert-primary">

                    <strong>Kelas yang diampu:</strong>

                    <?php
                    $namaKelas = [];

                    foreach ($daftarKelas as $kelas) {
                        $namaKelas[] = htmlspecialchars($kelas['nama_kelas']);
                    }

                    echo implode(", ", $namaKelas);
                    ?>

                </div>

            <?php } ?>

            <!-- STATISTIK -->
            <div class="row g-3 mb-4">

                <div class="col-md-3">
                    <div class="card card-statistik shadow-sm">
                        <div class="card-body">
                            <p class="text-muted mb-2">
                                Total Pelanggaran
                            </p>

                            <h3 class="fw-bold mb-0">
                                <?= (int) ($statistik['total_pelanggaran'] ?? 0); ?>
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
                                <?= (int) ($statistik['total_poin'] ?? 0); ?>
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
                                <?= (int) ($statistik['total_selesai'] ?? 0); ?>
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
                                <?= (int) ($statistik['total_proses'] ?? 0); ?>
                            </h3>
                        </div>
                    </div>
                </div>

            </div>

            <!-- FILTER SISWA -->
            <?php if (!($role == 'guru' && $isWaliKelas)) { ?>

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

            <?php } ?>

            <!-- HASIL LAPORAN -->
            <?php if (($role == 'guru' && $isWaliKelas) || isset($_POST['tampilkan'])) { ?>

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

                                <table id="tabelLaporan" class="table table-bordered table-striped align-middle">

                                    <thead class="table-primary">

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
                                            $totalPoinSiswa += (int) $data['poin'];
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
                                                    <?= !empty($data['tanggal'])
                                                        ? date('d-m-Y', strtotime($data['tanggal']))
                                                        : '-'; ?>
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

                                <?php if ($role == 'guru' && $isWaliKelas) { ?>

                                    Tidak ada data pelanggaran untuk kelas yang diampu.

                                <?php } else { ?>

                                    Tidak ada data pelanggaran untuk siswa yang dipilih.

                                <?php } ?>

                            </div>

                        <?php } ?>

                    </div>

                </div>

            <?php } ?>

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

<!-- JQUERY -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DATATABLES -->
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

<script>
$(document).ready(function () {

    if ($('#tabelLaporan').length) {

        $('#tabelLaporan').DataTable({
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],

            language: {
                lengthMenu: "Tampilkan _MENU_ data",
                search: "Cari data:",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                zeroRecords: "Data tidak ditemukan",
                emptyTable: "Tidak ada data tersedia",

                paginate: {
                    first: "Pertama",
                    previous: "Sebelumnya",
                    next: "Berikutnya",
                    last: "Terakhir"
                }
            }
        });

    }

});
</script>

</body>
</html>
