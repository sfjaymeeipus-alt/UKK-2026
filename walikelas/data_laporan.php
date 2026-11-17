
<?php

require_once "../middleware/auth.php";
require_once "../config/koneksi.php";

global $koneksi;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$role = $_SESSION['role'] ?? '';
$nama = $_SESSION['nama'] ?? 'Pengguna';
$user_id = (int) ($_SESSION['user_id'] ?? 0);

if (!in_array($role, ['admin', 'guru'])) {
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

        JOIN t_kelas k
            ON wk.kelas_id = k.id

        JOIN t_tahun_ajaran ta
            ON wk.tahun_ajaran_id = ta.id

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
    
];

$menuWali = [
    ['Dashboard', '../dashboard.php'],
    ['Catatan Pelanggaran', '../data_catatan_pelanggaran.php'],
    ['Tindakan', '../data_tindakan.php'],
    ['Data Siswa', 'data_siswa.php'],
    ['Riwayat', '../data_riwayat.php'],
    ['Rekap Poin', '../data_rekap_poin.php'],
    ['Laporan', 'data_laporan.php'],
   
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

        $kondisiKelas[] = "
            (ps.kelas_id = ? AND ps.tahun_ajaran_id = ?)
        ";

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

        SUM(
            CASE
                WHEN ps.status = 'Selesai' THEN 1
                ELSE 0
            END
        ) AS total_selesai,

        SUM(
            CASE
                WHEN ps.status != 'Selesai'
                OR ps.status IS NULL THEN 1
                ELSE 0
            END
        ) AS total_proses

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
// DATA LAPORAN
// ======================================

$detailLaporan = [];
$siswaTerpilih = 0;

if ($role == 'guru' && $isWaliKelas) {

    $sqlDetail = "
        SELECT
            ps.*,
            s.nis,

            COALESCE(
                NULLIF(NULLIF(TRIM(p.nama), ''), '0'),
                NULLIF(NULLIF(TRIM(pk.nama), ''), '0'),
                NULLIF(NULLIF(TRIM(ps.nama_pelanggaran), ''), '0'),
                '-'
            ) AS nama_pelanggaran_tampil

        FROM t_pelanggaran_siswa ps

        LEFT JOIN t_siswa s
            ON ps.siswa_id = s.id

        LEFT JOIN t_pelanggaran p
            ON p.id = ps.pelanggaran_id

        LEFT JOIN t_pelanggaran_kategori pk
            ON pk.id = ps.pelanggaran_kategori_id

        WHERE $kondisiLaporan

        AND ps.status = 'Selesai'

        ORDER BY ps.tanggal DESC, ps.id DESC
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

} elseif (isset($_POST['tampilkan'])) {

    $siswaTerpilih = (int) ($_POST['siswa_id'] ?? 0);

    if ($siswaTerpilih > 0) {

        $sqlDetail = "
            SELECT
                ps.*,
                s.nis,

                COALESCE(
                    NULLIF(NULLIF(TRIM(p.nama), ''), '0'),
                    NULLIF(NULLIF(TRIM(pk.nama), ''), '0'),
                    NULLIF(NULLIF(TRIM(ps.nama_pelanggaran), ''), '0'),
                    '-'
                ) AS nama_pelanggaran_tampil

            FROM t_pelanggaran_siswa ps

            LEFT JOIN t_siswa s
                ON ps.siswa_id = s.id

            LEFT JOIN t_pelanggaran p
                ON p.id = ps.pelanggaran_id

            LEFT JOIN t_pelanggaran_kategori pk
                ON pk.id = ps.pelanggaran_kategori_id

            WHERE ps.siswa_id = ?

            AND $kondisiLaporan

            AND ps.status = 'Selesai'

            ORDER BY ps.tanggal DESC, ps.id DESC
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

// ======================================
// INFORMASI PERIODE
// ======================================

$infoPeriode = 'Semua tanggal';

// ======================================
// TOTAL POIN
// ======================================

$totalPoinSiswa = 0;

foreach ($detailLaporan as $data) {
    $totalPoinSiswa += (int) ($data['poin'] ?? 0);
}

// ======================================
// NAMA KELAS DAN TAHUN
// ======================================

$namaKelas = [];
$namaTahun = [];

foreach ($daftarKelas as $kelas) {
    $namaKelas[] = $kelas['nama_kelas'];
    $namaTahun[] = $kelas['nama_tahun_ajaran'];
}

$kelasTampil = implode(', ', array_unique($namaKelas));
$tahunTampil = implode(', ', array_unique($namaTahun));

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pelanggaran Siswa</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <style>

        body {
            background: #f8f9fa;
        }

        /* =====================================
           TAMPILAN KOP LAPORAN
        ===================================== */

        .kop-cetak {
            display: none;
        }

        .logo-cetak {
            width: 70px;
            height: 75px;
            object-fit: contain;
        }

        .kop-container {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 90px;
            border-bottom: 4px solid #172554;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .logo-container {
            position: absolute;
            left: 5px;
        }

        .kop-judul {
            text-align: center;
        }

        .kop-judul h4 {
            font-size: 19px;
            font-weight: bold;
            color: #172554;
            margin-bottom: 4px;
        }

        .kop-judul h5 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .kop-judul p {
            font-size: 10px;
            margin: 0;
        }

        .periode-cetak {
            text-align: center;
            font-size: 10px;
            margin-bottom: 15px;
        }

        /* =====================================
           TABEL CETAK
        ===================================== */

        #tabelLaporan {
            width: 100%;
            border-collapse: collapse;
        }

        #tabelLaporan thead th {
            background: #172554;
            color: white;
            text-align: center;
            vertical-align: middle;
            font-size: 11px;
        }

        #tabelLaporan th,
        #tabelLaporan td {
            padding: 8px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }

        /* =====================================
           RINGKASAN CETAK
        ===================================== */

        .ringkasan-cetak {
            display: none;
        }

        .ringkasan-container {
            display: flex;
            width: 100%;
            margin-top: 15px;
            border: 1px solid #93c5fd;
            border-radius: 5px;
            background: #e0edff;
            padding: 12px;
        }

        .ringkasan-item {
            width: 50%;
            text-align: center;
        }

        .ringkasan-item:first-child {
            border-right: 1px solid #64748b;
        }

        .ringkasan-item small {
            font-weight: bold;
            font-size: 11px;
        }

        .ringkasan-item h4 {
            font-size: 19px;
            font-weight: bold;
            color: #172554;
            margin: 3px 0 0;
        }

        /* =====================================
           TANDA TANGAN
        ===================================== */

        .tanda-tangan-cetak {
            display: none;
        }

        .tanda-tangan-container {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            font-size: 10px;
        }

        .pencetak {
            width: 50%;
        }

        .ttd {
            width: 250px;
            text-align: center;
        }

        .garis-ttd {
            width: 180px;
            border-bottom: 1px solid #172554;
            margin: 65px auto 5px;
        }

        /* =====================================
           CETAK LAPORAN
        ===================================== */

        @media print {

            @page {
                size: A4 landscape;
                margin: 8mm;
            }

            html,
            body {
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                font-family: Arial, sans-serif !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            body * {
                visibility: hidden !important;
            }

            #areaCetak,
            #areaCetak * {
                visibility: visible !important;
            }

            #areaCetak {
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                background: white !important;
            }

            /* HILANGKAN TOMBOL DAN FILTER */

            .d-print-none,
            #areaCetak .row,
            #areaCetak #infoData,
            #areaCetak #pagination {
                display: none !important;
            }

            /* TAMPILKAN KOP */

            .kop-cetak {
                display: block !important;
            }

            .kop-container {
                display: flex !important;
                border-bottom: 4px solid #172554 !important;
                margin-bottom: 12px !important;
                min-height: 80px !important;
            }

            .logo-cetak {
                display: block !important;
                width: 65px !important;
                height: 70px !important;
            }

            .kop-judul h4 {
                font-size: 19px !important;
                color: #172554 !important;
                margin-bottom: 3px !important;
            }

            .kop-judul h5 {
                font-size: 13px !important;
                margin-bottom: 3px !important;
            }

            .kop-judul p {
                font-size: 9px !important;
            }

            .periode-cetak {
                display: block !important;
                font-size: 9px !important;
                margin-bottom: 10px !important;
            }

            /* TABEL */

            #areaCetak .table-responsive {
                overflow: visible !important;
                width: 100% !important;
            }

            #tabelLaporan {
                display: table !important;
                width: 100% !important;
                border-collapse: collapse !important;
                table-layout: auto !important;
                font-size: 8px !important;
                margin: 0 !important;
            }

            #tabelLaporan thead {
                display: table-header-group !important;
            }

            #tabelLaporan tbody {
                display: table-row-group !important;
            }

            #tabelLaporan tfoot {
                display: table-row-group !important;
            }

            #tabelLaporan tr {
                display: table-row !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            #tabelLaporan th,
            #tabelLaporan td {
                display: table-cell !important;
                padding: 5px 4px !important;
                border: 1px solid #64748b !important;
                color: #111827 !important;
                font-size: 8px !important;
                vertical-align: middle !important;
            }

            #tabelLaporan thead th {
                background: #172554 !important;
                color: white !important;
                font-weight: bold !important;
                text-align: center !important;
            }

            #tabelLaporan tfoot td {
                background: #e5e7eb !important;
                font-weight: bold !important;
            }

            #tabelLaporan .badge {
                background: white !important;
                color: black !important;
                border: 1px solid #333 !important;
                font-size: 7px !important;
            }

            /* TAMPILKAN RINGKASAN */

            .ringkasan-cetak {
                display: block !important;
            }

            .ringkasan-container {
                display: flex !important;
                background: #e0edff !important;
                border: 1px solid #93c5fd !important;
                padding: 10px !important;
                margin-top: 12px !important;
                break-inside: avoid !important;
            }

            .ringkasan-item {
                display: block !important;
                width: 50% !important;
                text-align: center !important;
            }

            .ringkasan-item:first-child {
                border-right: 1px solid #64748b !important;
            }

            .ringkasan-item small {
                font-size: 10px !important;
                font-weight: bold !important;
            }

            .ringkasan-item h4 {
                font-size: 17px !important;
                color: #172554 !important;
                margin: 3px 0 0 !important;
            }

            /* TANDA TANGAN */

            .tanda-tangan-cetak {
                display: block !important;
            }

            .tanda-tangan-container {
                display: flex !important;
                justify-content: space-between !important;
                margin-top: 20px !important;
                font-size: 9px !important;
                break-inside: avoid !important;
            }

            .pencetak {
                display: block !important;
                width: 50% !important;
            }

            .ttd {
                display: block !important;
                width: 230px !important;
                text-align: center !important;
            }

            .garis-ttd {
                display: block !important;
                width: 180px !important;
                border-bottom: 1px solid #172554 !important;
                margin: 55px auto 5px !important;
            }

            /* PAKSA WARNA TABEL */

            .table-dark {
                --bs-table-bg: #172554 !important;
                --bs-table-color: white !important;
            }

        }

    </style>

</head>

<body>

<div class="container-fluid">

    <div class="row">

        <!-- SIDEBAR -->

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

                <a href="../dashboard.php" class="nav-link rounded py-2 text-white">
                    Dashboard
                </a>

                <a href="../data_catatan_pelanggaran.php" class="nav-link rounded py-2 text-white">
                    Catatan Pelanggaran
                </a>

                <a href="../data_tindakan.php" class="nav-link rounded py-2 text-white">
                    Tindakan
                </a>

                <a href="data_siswa.php" class="nav-link rounded py-2 text-white">
                    Data Siswa
                </a>

                <a href="../data_riwayat.php" class="nav-link rounded py-2 text-white">
                    Riwayat
                </a>

                <a href="../data_rekap_poin.php" class="nav-link rounded py-2 text-white">
                    Rekap Poin
                </a>

                <a href="data_laporan.php" class="nav-link rounded py-2 active bg-primary text-white">
                    Laporan
                </a>

                <a href="../about_me.php" class="nav-link rounded py-2 text-white">
                    About Me
                </a>

                <hr class="border-secondary my-2">

                <a href="../logout.php" class="nav-link text-danger rounded py-2">
                    Logout
                </a>

            </nav>

        </aside>

        <!-- KONTEN UTAMA -->

        <main class="col-md-9 col-lg-10 p-4">

            <!-- HEADER -->

            <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">

                <div>

                    <h3 class="fw-bold mb-1">
                        Laporan Pelanggaran Siswa
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

            <!-- INFORMASI KELAS -->

            <?php if ($role == 'guru' && $isWaliKelas) { ?>

                <div class="card border-0 shadow-sm mb-4 d-print-none">

                    <div class="card-body">

                        <small class="text-muted">
                            Kelas Laporan
                        </small>

                        <h5 class="fw-bold mb-0">
                            <?= htmlspecialchars(implode(', ', $namaKelas)); ?>
                        </h5>

                    </div>

                </div>

            <?php } ?>

            <!-- STATISTIK -->

            <div class="row g-3 mb-4 d-print-none">

                <div class="col-md-3">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <p class="text-muted mb-2">
                                Total Pelanggaran
                            </p>

                            <h3 class="fw-bold text-primary mb-0">
                                <?= number_format((int) $statistik['total_pelanggaran'], 0, ',', '.'); ?>
                            </h3>

                        </div>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <p class="text-muted mb-2">
                                Total Poin
                            </p>

                            <h3 class="fw-bold text-info mb-0">
                                <?= number_format((int) $statistik['total_poin'], 0, ',', '.'); ?>
                            </h3>

                        </div>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <p class="text-muted mb-2">
                                Selesai
                            </p>

                            <h3 class="fw-bold text-success mb-0">
                                <?= number_format((int) $statistik['total_selesai'], 0, ',', '.'); ?>
                            </h3>

                        </div>

                    </div>

                </div>

                <div class="col-md-3">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <p class="text-muted mb-2">
                                Diproses
                            </p>

                            <h3 class="fw-bold text-warning mb-0">
                                <?= number_format((int) $statistik['total_proses'], 0, ',', '.'); ?>
                            </h3>

                        </div>

                    </div>

                </div>

            </div>

            <!-- FILTER SISWA -->

            <?php if (!($role == 'guru' && $isWaliKelas)) { ?>

                <div class="card border-0 shadow-sm mb-4 d-print-none">

                    <div class="card-body">

                        <h5 class="fw-bold mb-3">
                            Pilih Siswa
                        </h5>

                        <form method="POST">

                            <div class="row align-items-end">

                                <div class="col-md-8">

                                    <label class="form-label">
                                        Nama Siswa
                                    </label>

                                    <select name="siswa_id" class="form-select" required>

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

            <!-- AREA LAPORAN -->

            <?php if (($role == 'guru' && $isWaliKelas) || isset($_POST['tampilkan'])) { ?>

                <div class="card border-0 shadow-sm" id="areaCetak">

                    <div class="card-body">

                        <!-- =================================
                             KOP CETAK
                        ================================== -->

                        <div class="kop-cetak">

                            <div class="kop-container">

                                <div class="logo-container">

                                    <img
                                        src="/UKK-2026/logo.png?v=2"
                                        class="logo-cetak"
                                        alt="Logo Sekolah"
                                        onerror="this.style.display='none'"
                                    >

                                </div>

                                <div class="kop-judul">

                                    <h4>
                                        SMK MUHAMMADIYAH KOTA TASIKMALAYA
                                    </h4>

                                    <h5>
                                        REKAPITULASI PELANGGARAN SISWA
                                    </h5>

                                    <p>
                                        SISTEM INFORMASI PELANGGARAN SISWA
                                    </p>

                                </div>

                            </div>

                            <div class="periode-cetak">

                                <strong>
                                    Periode:
                                    <?= htmlspecialchars($tahunTampil ?: $infoPeriode); ?>
                                </strong>

                                <br>

                                Wali Kelas:
                                <?= htmlspecialchars($nama); ?>

                                |

                                Kelas:
                                <?= htmlspecialchars($kelasTampil ?: '-'); ?>

                            </div>

                        </div>

                        <!-- HEADER WEB -->

                        <div class="d-flex justify-content-between align-items-center mb-3 d-print-none">

                            <h5 class="fw-bold mb-0">
                                Data Laporan Pelanggaran
                            </h5>

                            <button
                                type="button"
                                onclick="window.print()"
                                class="btn btn-outline-primary"
                            >
                                Cetak Laporan
                            </button>

                        </div>

                        <p class="text-muted d-print-none">

                            Kelas:

                            <?= $role == 'guru' && $isWaliKelas
                                ? htmlspecialchars(implode(', ', $namaKelas))
                                : 'Siswa Terpilih'; ?>

                            <br>

                            Periode:
                            <?= htmlspecialchars($infoPeriode); ?>

                        </p>

                        <!-- PENCARIAN -->

                        <div class="row mb-3 d-print-none">

                            <div class="col-md-6 mb-2">

                                <label class="form-label">
                                    Tampilkan data
                                </label>

                                <select id="jumlahData" class="form-select">

                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="all">Semua</option>

                                </select>

                            </div>

                            <div class="col-md-6 mb-2">

                                <label class="form-label">
                                    Cari
                                </label>

                                <input
                                    type="search"
                                    id="cariData"
                                    class="form-control"
                                    placeholder="Cari laporan..."
                                >

                            </div>

                        </div>

                        <!-- TABEL LAPORAN -->

                        <div class="table-responsive">

                            <table
                                class="table table-bordered table-striped table-hover align-middle"
                                id="tabelLaporan"
                            >

                                <thead class="table-dark">

                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>NIS</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Jenis Pelanggaran</th>
                                        <th>Poin</th>
                                        <th>Keterangan</th>
                                        <th>Tindakan</th>
                                        <th>Status</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php $no = 1; ?>

                                    <?php foreach ($detailLaporan as $data) { ?>

                                        <tr>

                                            <td>
                                                <?= $no++; ?>
                                            </td>

                                            <td>
                                                <?= !empty($data['tanggal'])
                                                    ? date('d-m-Y', strtotime($data['tanggal']))
                                                    : '-'; ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nis'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_pelanggaran_tampil'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= (int) ($data['poin'] ?? 0); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['keterangan'] ?? '-'); ?>
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
                                                        <?= htmlspecialchars($data['status'] ?: 'Dalam Proses'); ?>
                                                    </span>

                                                <?php } ?>

                                            </td>

                                        </tr>

                                    <?php } ?>

                                    <?php if (count($detailLaporan) == 0) { ?>

                                        <tr>
                                            <td colspan="10" class="text-center">
                                                Tidak ada data laporan pelanggaran.
                                            </td>
                                        </tr>

                                    <?php } ?>

                                </tbody>

                                <tfoot>

                                    <tr class="table-light fw-bold">

                                        <td colspan="6" class="text-end">
                                            Total Poin
                                        </td>

                                        <td>
                                            <?= number_format($totalPoinSiswa, 0, ',', '.'); ?>
                                        </td>

                                        <td colspan="3"></td>

                                    </tr>

                                </tfoot>

                            </table>

                        </div>

                        <!-- =================================
                             RINGKASAN CETAK
                        ================================== -->

                        <div class="ringkasan-cetak">

                            <div class="ringkasan-container">

                                <div class="ringkasan-item">

                                    <small>
                                        Jumlah Kejadian
                                    </small>

                                    <h4>
                                        <?= number_format(count($detailLaporan), 0, ',', '.'); ?>
                                    </h4>

                                </div>

                                <div class="ringkasan-item">

                                    <small>
                                        Total Poin
                                    </small>

                                    <h4>
                                        <?= number_format($totalPoinSiswa, 0, ',', '.'); ?>
                                    </h4>

                                </div>

                            </div>

                        </div>

                        <!-- =================================
                             TANDA TANGAN CETAK
                        ================================== -->

                        <div class="tanda-tangan-cetak">

                            <div class="tanda-tangan-container">

                                <div class="pencetak">

                                    <p>
                                        Dicetak oleh:
                                    </p>

                                    <strong>
                                        Sistem Informasi Pelanggaran Siswa
                                    </strong>

                                    <br>

                                    <small>
                                        SMK Muhammadiyah Kota Tasikmalaya
                                    </small>

                                </div>

                                <div class="ttd">

                                    <p>
                                        Tasikmalaya, <?= date('d F Y'); ?>
                                    </p>

                                    <p>
                                        Mengetahui,
                                    </p>

                                    <p>
                                        Wali Kelas
                                    </p>

                                    <div class="garis-ttd"></div>

                                    <strong>
                                        <?= htmlspecialchars($nama); ?>
                                    </strong>

                                </div>

                            </div>

                        </div>

                        <!-- INFORMASI DATA WEB -->

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3 d-print-none">

                            <small id="infoData" class="text-muted"></small>

                            <nav>
                                <ul class="pagination pagination-sm mb-0" id="pagination"></ul>
                            </nav>

                        </div>

                    </div>

                </div>

            <?php } ?>

            <!-- FOOTER -->

            <footer class="text-center text-muted mt-4 d-print-none">

                <small>
                    © <?= date('Y'); ?> Sistem Informasi Pelanggaran Siswa
                    - SMK Muhammadiyah Kota Tasikmalaya
                </small>

            </footer>

        </main>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

// ======================================
// PENCARIAN DAN PAGINATION
// ======================================

const tabel = document.getElementById('tabelLaporan');

if (tabel) {

    const jumlahData = document.getElementById('jumlahData');
    const cariData = document.getElementById('cariData');
    const infoData = document.getElementById('infoData');
    const pagination = document.getElementById('pagination');

    let halamanSekarang = 1;

    function tampilkanData() {

        const semuaBaris = Array.from(
            tabel.querySelectorAll('tbody tr')
        ).filter(function(baris) {
            return baris.cells.length > 1;
        });

        const kataKunci = cariData.value.toLowerCase();

        const barisFilter = semuaBaris.filter(function(baris) {
            return baris.innerText.toLowerCase().includes(kataKunci);
        });

        const batasData = jumlahData.value === 'all'
            ? Math.max(barisFilter.length, 1)
            : parseInt(jumlahData.value);

        const totalData = barisFilter.length;

        const totalHalaman = Math.max(
            1,
            Math.ceil(totalData / batasData)
        );

        if (halamanSekarang > totalHalaman) {
            halamanSekarang = totalHalaman;
        }

        semuaBaris.forEach(function(baris) {
            baris.style.display = 'none';
        });

        const mulai = (halamanSekarang - 1) * batasData;
        const akhir = mulai + batasData;

        barisFilter.slice(mulai, akhir).forEach(function(baris) {
            baris.style.display = '';
        });

        if (totalData === 0) {

            infoData.innerText = 'Tidak ada data yang ditemukan.';

        } else {

            infoData.innerText =
                'Menampilkan ' +
                (mulai + 1) +
                ' sampai ' +
                Math.min(akhir, totalData) +
                ' dari ' +
                totalData +
                ' data';

        }

        pagination.innerHTML = '';

        for (let i = 1; i <= totalHalaman; i++) {

            const li = document.createElement('li');

            li.className = 'page-item ' +
                (i === halamanSekarang ? 'active' : '');

            const tombol = document.createElement('button');

            tombol.className = 'page-link';
            tombol.innerText = i;

            tombol.onclick = function() {
                halamanSekarang = i;
                tampilkanData();
            };

            li.appendChild(tombol);
            pagination.appendChild(li);

        }

    }

    jumlahData.addEventListener('change', function() {
        halamanSekarang = 1;
        tampilkanData();
    });

    cariData.addEventListener('input', function() {
        halamanSekarang = 1;
        tampilkanData();
    });

    // ======================================
    // CETAK SEMUA DATA
    // ======================================

    let kondisiBaris = [];

    window.addEventListener('beforeprint', function() {

        kondisiBaris = [];

        const semuaBaris = tabel.querySelectorAll('tbody tr');

        semuaBaris.forEach(function(baris) {

            kondisiBaris.push(baris.style.display);

            baris.style.display = 'table-row';

        });

    });

    window.addEventListener('afterprint', function() {

        const semuaBaris = tabel.querySelectorAll('tbody tr');

        semuaBaris.forEach(function(baris, index) {

            baris.style.display = kondisiBaris[index] || '';

        });

        tampilkanData();

    });

    tampilkanData();

}

</script>

</body>
</html>
