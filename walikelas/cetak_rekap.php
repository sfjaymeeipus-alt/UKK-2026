
<?php
require_once __DIR__ . "/../middleware/auth.php";
require_once __DIR__ . "/../config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    exit("Anda tidak memiliki akses.");
}

if (!$koneksi) {
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
   DATA GURU
===================================== */
$sql_guru = "
    SELECT id
    FROM t_guru
    WHERE user_id = ?
    LIMIT 1
";

$stmt = cekQuery(
    mysqli_prepare($koneksi, $sql_guru),
    $koneksi,
    "Error mengambil data guru"
);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$data_guru = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$data_guru) {
    exit("Data guru tidak ditemukan.");
}

$guru_id = (int) $data_guru['id'];

/* =====================================
   DATA WALI KELAS
===================================== */
$sql_wali = "
    SELECT
        wk.kelas_id,
        wk.tahun_ajaran_id,
        k.nama AS nama_kelas,
        ta.nama AS tahun_ajaran
    FROM t_wali_kelas wk
    INNER JOIN t_kelas k ON k.id = wk.kelas_id
    INNER JOIN t_tahun_ajaran ta ON ta.id = wk.tahun_ajaran_id
    WHERE wk.guru_id = ?
      AND wk.status_aktif = 1
      AND ta.status_aktif = 1
    ORDER BY k.nama ASC
";

$stmt = cekQuery(
    mysqli_prepare($koneksi, $sql_wali),
    $koneksi,
    "Error mengambil data wali kelas"
);

mysqli_stmt_bind_param($stmt, "i", $guru_id);
mysqli_stmt_execute($stmt);

$result_wali = mysqli_stmt_get_result($stmt);

$daftar_wali = [];

while ($wali = mysqli_fetch_assoc($result_wali)) {
    $daftar_wali[] = $wali;
}

mysqli_stmt_close($stmt);

if (count($daftar_wali) == 0) {
    exit("Anda bukan wali kelas aktif.");
}

/* =====================================
   DATA REKAP PELANGGARAN
===================================== */
$sql_rekap = "
    SELECT
        s.nis,
        ps.nama_siswa,
        k.nama AS nama_kelas,
        ps.nama_pelanggaran,
        ps.nama_guru,
        ps.tanggal,
        ps.poin,
        ps.tindakan,
        ps.status

    FROM t_pelanggaran_siswa ps

    INNER JOIN t_siswa s
        ON s.id = ps.siswa_id

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

    ORDER BY ps.tanggal ASC, ps.nama_siswa ASC
";

$stmt = cekQuery(
    mysqli_prepare($koneksi, $sql_rekap),
    $koneksi,
    "Error mengambil data rekap"
);

mysqli_stmt_bind_param($stmt, "i", $guru_id);
mysqli_stmt_execute($stmt);

$result_rekap = mysqli_stmt_get_result($stmt);

$rows = [];
$total_poin = 0;
$total_selesai = 0;
$total_proses = 0;

while ($data = mysqli_fetch_assoc($result_rekap)) {

    $rows[] = $data;
    $total_poin += (int) $data['poin'];

    if ($data['status'] == 'Selesai') {
        $total_selesai++;
    } else {
        $total_proses++;
    }
}

mysqli_stmt_close($stmt);

$total_pelanggaran = count($rows);

/* =====================================
   INFORMASI KELAS DAN TAHUN
===================================== */
$nama_kelas = [];
$nama_tahun = [];

foreach ($daftar_wali as $wali) {
    $nama_kelas[] = $wali['nama_kelas'];
    $nama_tahun[] = $wali['tahun_ajaran'];
}

$kelas_tampil = implode(', ', array_unique($nama_kelas));
$tahun_tampil = implode(', ', array_unique($nama_tahun));

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Rekapitulasi Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>

        body {
            background: #f4f6f9;
            font-family: Arial, sans-serif;
        }

        /* SIDEBAR */

        .sidebar {
            width: 16.666667%;
            max-width: 16.666667%;
        }

        .main-content {
            margin-left: 16.666667%;
            width: 83.333333%;
        }

        /* AREA LAPORAN */

        .print-area {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        /* HEADER LAPORAN */

        .kop-laporan {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            text-align: center;
            min-height: 85px;
            border-bottom: 4px solid #172554;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .logo-sekolah {
            position: absolute;
            left: 5px;
            width: 65px;
            height: 70px;
            object-fit: contain;
        }

        .judul-sekolah h4 {
            font-size: 20px;
            font-weight: bold;
            color: #172554;
            margin-bottom: 3px;
        }

        .judul-sekolah h5 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .judul-sekolah p {
            font-size: 10px;
            margin: 0;
        }

        .periode {
            text-align: center;
            font-size: 11px;
            margin-bottom: 12px;
        }

        /* TABEL */

        .tabel-laporan {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .tabel-laporan th {
            background: #172554;
            color: white;
            padding: 9px 5px;
            text-align: center;
            border: 1px solid #172554;
            white-space: nowrap;
        }

        .tabel-laporan td {
            padding: 7px 5px;
            border: 1px solid #9ca3af;
            vertical-align: middle;
        }

        .tabel-laporan tbody tr:nth-child(even) {
            background: #f1f5f9;
        }

        .tengah {
            text-align: center;
        }

        /* RINGKASAN */

        .ringkasan {
            display: flex;
            margin-top: 15px;
            border: 1px solid #93c5fd;
            background: #e0edff;
            border-radius: 5px;
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
            color: #172554;
            font-weight: bold;
            margin: 3px 0 0;
        }

        /* BAGIAN BAWAH */

        .bagian-bawah {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            font-size: 10px;
        }

        .tanda-tangan {
            width: 230px;
            text-align: center;
        }

        .garis-ttd {
            margin-top: 65px;
            border-bottom: 1px solid #172554;
            width: 180px;
            margin-left: auto;
            margin-right: auto;
        }

        /* CETAK */

        @media print {

            @page {
                size: A4 landscape;
                margin: 10mm;
            }

            html,
            body {
                width: 100%;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
                font-family: Arial, sans-serif;
            }

            .sidebar,
            .d-print-none {
                display: none !important;
            }

            .container-fluid,
            .container-fluid > .row {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .main-content {
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .print-area {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }

            .kop-laporan {
                min-height: 75px;
                margin-bottom: 12px;
            }

            .logo-sekolah {
                width: 60px;
                height: 65px;
            }

            .judul-sekolah h4 {
                font-size: 19px;
            }

            .judul-sekolah h5 {
                font-size: 13px;
            }

            .judul-sekolah p {
                font-size: 9px;
            }

            .tabel-laporan {
                font-size: 9px !important;
                width: 100% !important;
                page-break-inside: auto;
            }

            .tabel-laporan th,
            .tabel-laporan td {
                padding: 5px 4px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .tabel-laporan th {
                background: #172554 !important;
                color: white !important;
            }

            .tabel-laporan tbody tr:nth-child(even) {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .ringkasan {
                background: #e0edff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                padding: 9px;
            }

            .ringkasan-item h4 {
                font-size: 17px;
            }

            .bagian-bawah {
                margin-top: 20px;
            }

            tr {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .ringkasan,
            .bagian-bawah {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

        }

    </style>

</head>

<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <aside class="sidebar bg-dark text-white p-3 position-fixed top-0 start-0 vh-100 overflow-auto">

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

                <a href="../dashboard.php" class="nav-link text-white rounded py-2">
                    Dashboard
                </a>

                <a href="data_catatan_pelanggaran.php" class="nav-link text-white rounded py-2">
                    Catatan Pelanggaran
                </a>

                <a href="data_siswa.php" class="nav-link text-white rounded py-2">
                    Data Siswa
                </a>

                <a href="../data_riwayat.php" class="nav-link text-white rounded py-2">
                    Riwayat
                </a>

                <a href="cetak_rekap.php" class="nav-link active bg-primary text-white rounded py-2">
                    Rekap Poin
                </a>

                <a href="data_laporan.php" class="nav-link text-white rounded py-2">
                    Laporan
                </a>

                <a href="../about_me.php" class="nav-link text-white rounded py-2">
                    About Me
                </a>

                <hr class="border-secondary my-2">

                <a href="../logout.php" class="nav-link text-danger rounded py-2">
                    Logout
                </a>

            </nav>

        </aside>

        <!-- KONTEN -->
        <main class="main-content p-3 p-md-4">

            <!-- HEADER HALAMAN -->
            <div class="d-flex justify-content-between align-items-center mb-4 d-print-none">

                <div>
                    <h4 class="fw-bold mb-1">
                        Cetak Rekap Pelanggaran
                    </h4>

                    <p class="text-muted mb-0">
                        Wali Kelas: <?= htmlspecialchars($nama) ?>
                    </p>
                </div>

                <div class="d-flex gap-2">

                    <a href="rekap_pelanggaran.php" class="btn btn-secondary">
                        Kembali
                    </a>

                    <button onclick="window.print()" class="btn btn-primary">
                        Cetak Laporan
                    </button>

                </div>

            </div>

            <!-- LAPORAN -->
            <div class="print-area">

                <!-- KOP SEKOLAH -->
                <div class="kop-laporan">

                    <!-- GANTI LOKASI LOGO SESUAI FILE LOGO SEKOLAH -->
                    <img src="../assets/logo.png"
                         class="logo-sekolah"
                         alt="Logo Sekolah"
                         onerror="this.style.display='none'">

                    <div class="judul-sekolah">

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

                <!-- PERIODE -->
                <div class="periode">

                    <strong>
                        Periode: <?= htmlspecialchars($tahun_tampil) ?>
                    </strong>

                    <br>

                    Wali Kelas:
                    <?= htmlspecialchars($nama) ?>
                    |
                    Kelas:
                    <?= htmlspecialchars($kelas_tampil) ?>

                </div>

                <!-- TABEL -->
                <div class="table-responsive">

                    <table class="tabel-laporan">

                        <thead>

                            <tr>
                                <th width="3%">No</th>
                                <th width="10%">Tanggal</th>
                                <th width="10%">NIS</th>
                                <th width="17%">Nama Siswa</th>
                                <th width="10%">Kelas</th>
                                <th width="22%">Jenis Pelanggaran</th>
                                <th width="6%">Poin</th>
                                <th width="15%">Guru</th>
                                <th width="7%">Status</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php if (count($rows) > 0) { ?>

                                <?php foreach ($rows as $index => $data) { ?>

                                    <tr>

                                        <td class="tengah">
                                            <?= $index + 1 ?>
                                        </td>

                                        <td class="tengah">
                                            <?= date('d-m-Y', strtotime($data['tanggal'])) ?>
                                        </td>

                                        <td class="tengah">
                                            <?= htmlspecialchars($data['nis'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_siswa']) ?>
                                        </td>

                                        <td class="tengah">
                                            <?= htmlspecialchars($data['nama_kelas']) ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_pelanggaran']) ?>
                                        </td>

                                        <td class="tengah">
                                            <?= (int) $data['poin'] ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($data['nama_guru']) ?>
                                        </td>

                                        <td class="tengah">
                                            <?= htmlspecialchars($data['status'] ?? '-') ?>
                                        </td>

                                    </tr>

                                <?php } ?>

                            <?php } else { ?>

                                <tr>
                                    <td colspan="9" class="tengah">
                                        Belum ada data pelanggaran.
                                    </td>
                                </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

                <!-- RINGKASAN -->
                <div class="ringkasan">

                    <div class="ringkasan-item">

                        <small>
                            Jumlah Kejadian
                        </small>

                        <h4>
                            <?= number_format($total_pelanggaran, 0, ',', '.') ?>
                        </h4>

                    </div>

                    <div class="ringkasan-item">

                        <small>
                            Total Poin
                        </small>

                        <h4>
                            <?= number_format($total_poin, 0, ',', '.') ?>
                        </h4>

                    </div>

                </div>

                <!-- BAGIAN BAWAH -->
                <div class="bagian-bawah">

                    <div>

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

                    <div class="tanda-tangan">

                        <p>
                            Tasikmalaya, <?= date('d F Y') ?>
                        </p>

                        <p>
                            Mengetahui,
                        </p>

                        <p>
                            Wali Kelas
                        </p>

                        <div class="garis-ttd"></div>

                        <strong>
                            <?= htmlspecialchars($nama) ?>
                        </strong>

                    </div>

                </div>

            </div>

        </main>

    </div>
</div>

</body>
</html>
