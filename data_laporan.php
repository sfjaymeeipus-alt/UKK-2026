
<?php

include "middleware/auth.php";
include "config/koneksi.php";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$role = $_SESSION['role'] ?? '';
$nama = $_SESSION['nama'] ?? 'Pengguna';

if ($role != 'admin') {
    exit("Anda tidak memiliki akses.");
}

// ======================================
// FUNGSI QUERY
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
// MENU SIDEBAR
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
// FILTER LAPORAN
// ======================================

$kelasTerpilih = $_GET['kelas'] ?? '';
$statusTerpilih = $_GET['status'] ?? '';
$tanggalAwal = $_GET['tanggal_awal'] ?? '';
$tanggalAkhir = $_GET['tanggal_akhir'] ?? '';

$where = [];
$tipe = "";
$parameter = [];

if ($kelasTerpilih != '') {
    $where[] = "ps.nama_kelas = ?";
    $tipe .= "s";
    $parameter[] = $kelasTerpilih;
}

if ($statusTerpilih == 'selesai') {
    $where[] = "ps.status = ?";
    $tipe .= "s";
    $parameter[] = "Selesai";

} elseif ($statusTerpilih == 'proses') {
    $where[] = "(ps.status != 'Selesai' OR ps.status IS NULL)";
}

if (
    $tanggalAwal != '' &&
    preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalAwal)
) {
    $where[] = "ps.tanggal >= ?";
    $tipe .= "s";
    $parameter[] = $tanggalAwal;
}

if (
    $tanggalAkhir != '' &&
    preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalAkhir)
) {
    $where[] = "ps.tanggal <= ?";
    $tipe .= "s";
    $parameter[] = $tanggalAkhir;
}

$kondisi = !empty($where)
    ? " WHERE " . implode(" AND ", $where)
    : "";

// ======================================
// STATISTIK
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
";

$resultStatistik = queryLaporan($koneksi, $sqlStatistik);
$statistik = mysqli_fetch_assoc($resultStatistik);

// ======================================
// DATA KELAS
// ======================================

$sqlKelas = "
    SELECT DISTINCT nama_kelas
    FROM t_pelanggaran_siswa
    WHERE nama_kelas IS NOT NULL
      AND nama_kelas != ''
    ORDER BY nama_kelas ASC
";

$resultKelas = queryLaporan($koneksi, $sqlKelas);

$daftarKelas = [];

while ($row = mysqli_fetch_assoc($resultKelas)) {
    $daftarKelas[] = $row['nama_kelas'];
}

// ======================================
// DATA LAPORAN
// ======================================

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

    $kondisi

    ORDER BY ps.tanggal DESC, ps.id DESC
";

$resultDetail = queryLaporan(
    $koneksi,
    $sqlDetail,
    $tipe,
    $parameter
);

$detailLaporan = [];

while ($row = mysqli_fetch_assoc($resultDetail)) {
    $detailLaporan[] = $row;
}

// ======================================
// INFORMASI PERIODE
// ======================================

$infoPeriode = 'Semua tanggal';

if ($tanggalAwal != '' && $tanggalAkhir != '') {

    $infoPeriode = date('d-m-Y', strtotime($tanggalAwal))
        . ' sampai ' . date('d-m-Y', strtotime($tanggalAkhir));

} elseif ($tanggalAwal != '') {

    $infoPeriode = 'Mulai ' . date('d-m-Y', strtotime($tanggalAwal));

} elseif ($tanggalAkhir != '') {

    $infoPeriode = 'Sampai ' . date('d-m-Y', strtotime($tanggalAkhir));
}

// ======================================
// TOTAL POIN
// ======================================

$totalPoinSiswa = 0;

foreach ($detailLaporan as $data) {
    $totalPoinSiswa += (int) ($data['poin'] ?? 0);
}

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

        /* ==================================
           CETAK LAPORAN
        ================================== */

        @media print {

            @page {
                size: A4 landscape;
                margin: 5mm;
            }

            html,
            body {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
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
                box-shadow: none !important;
                background: white !important;

                /* Memperkecil tampilan cetak */
                zoom: 0.85;
            }

            /* Hilangkan tombol */

            #areaCetak button {
                display: none !important;
            }

            /* Hilangkan pencarian dan pagination */

            #areaCetak .row {
                display: none !important;
            }

            #areaCetak #infoData,
            #areaCetak #pagination {
                display: none !important;
            }

            /* Judul */

            #areaCetak h5 {
                font-size: 12px !important;
                margin-bottom: 4px !important;
                color: black !important;
            }

            #areaCetak p {
                font-size: 8px !important;
                margin-bottom: 5px !important;
                color: black !important;
            }

            /* Tabel */

            #areaCetak .table-responsive {
                overflow: visible !important;
            }

            #areaCetak table {
                width: 100% !important;
                font-size: 7px !important;
                margin: 0 !important;
                border-collapse: collapse !important;
                table-layout: auto !important;
            }

            #areaCetak th,
            #areaCetak td {
                padding: 3px 4px !important;
                border: 1px solid black !important;
                color: black !important;
                line-height: 1.1 !important;
                vertical-align: middle !important;
            }

            #areaCetak thead th {
                background: #e5e5e5 !important;
                font-weight: bold !important;
            }

            #areaCetak tfoot td {
                font-weight: bold !important;
                background: #eeeeee !important;
            }

            #areaCetak tr {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            #areaCetak thead {
                display: table-header-group !important;
            }

            #areaCetak tfoot {
                display: table-row-group !important;
            }

            /* Hilangkan efek warna bootstrap */

            #areaCetak .badge {
                color: black !important;
                background: white !important;
                border: 1px solid black !important;
                font-size: 7px !important;
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

                <a href="logout.php" class="nav-link text-danger rounded py-2">
                    Logout
                </a>

            </nav>

        </aside>

        <!-- KONTEN UTAMA -->

        <main class="col-md-9 col-lg-10 p-4">

            <!-- HEADER -->

            <div class="d-flex justify-content-between align-items-center mb-4">

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
                        Administrator
                    </small>

                </div>

            </div>

            <!-- INFORMASI KELAS -->

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body">

                    <small class="text-muted">
                        Kelas Laporan
                    </small>

                    <h5 class="fw-bold mb-0">
                        <?= htmlspecialchars($kelasTerpilih ?: 'Semua Kelas'); ?>
                    </h5>

                </div>

            </div>

            <!-- STATISTIK -->

            <div class="row g-3 mb-4">

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

            <!-- FILTER -->

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body">

                    <h5 class="fw-bold mb-3">
                        Filter Laporan
                    </h5>

                    <form method="GET">

                        <div class="row g-3">

                            <div class="col-md-3">

                                <label class="form-label">
                                    Pilih Kelas
                                </label>

                                <select name="kelas" class="form-select">

                                    <option value="">
                                        Semua Kelas
                                    </option>

                                    <?php foreach ($daftarKelas as $kelas) { ?>

                                        <option
                                            value="<?= htmlspecialchars($kelas); ?>"
                                            <?= $kelasTerpilih == $kelas ? 'selected' : ''; ?>
                                        >
                                            <?= htmlspecialchars($kelas); ?>
                                        </option>

                                    <?php } ?>

                                </select>

                            </div>

                            <div class="col-md-3">

                                <label class="form-label">
                                    Status Pelanggaran
                                </label>

                                <select name="status" class="form-select">

                                    <option value="">
                                        Semua Status
                                    </option>

                                    <option value="selesai" <?= $statusTerpilih == 'selesai' ? 'selected' : ''; ?>>
                                        Selesai
                                    </option>

                                    <option value="proses" <?= $statusTerpilih == 'proses' ? 'selected' : ''; ?>>
                                        Dalam Proses
                                    </option>

                                </select>

                            </div>

                            <div class="col-md-3">

                                <label class="form-label">
                                    Tanggal Awal
                                </label>

                                <input
                                    type="date"
                                    name="tanggal_awal"
                                    class="form-control"
                                    value="<?= htmlspecialchars($tanggalAwal); ?>"
                                >

                            </div>

                            <div class="col-md-3">

                                <label class="form-label">
                                    Tanggal Akhir
                                </label>

                                <input
                                    type="date"
                                    name="tanggal_akhir"
                                    class="form-control"
                                    value="<?= htmlspecialchars($tanggalAkhir); ?>"
                                >

                            </div>

                        </div>

                        <div class="mt-3">

                            <button type="submit" class="btn btn-primary">
                                Terapkan Filter
                            </button>

                            <a href="data_laporan.php" class="btn btn-secondary">
                                Reset
                            </a>

                        </div>

                    </form>

                </div>

            </div>

            <!-- AREA CETAK -->

            <div class="card border-0 shadow-sm" id="areaCetak">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">

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

                    <!-- INFORMASI LAPORAN -->

                    <p class="text-muted mb-3">

                        Kelas:
                        <?= htmlspecialchars($kelasTerpilih ?: 'Semua Kelas'); ?>

                        <br>

                        Periode:
                        <?= htmlspecialchars($infoPeriode); ?>

                    </p>

                    <!-- PENCARIAN -->

                    <div class="row mb-3">

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

                    <!-- TABEL -->

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

                                <?php
                                $no = 1;
                                ?>

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

                    <!-- INFO DATA -->

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">

                        <small id="infoData" class="text-muted"></small>

                        <nav id="pagination">
                            <ul class="pagination pagination-sm mb-0" id="daftarPagination"></ul>
                        </nav>

                    </div>

                </div>

            </div>

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

<script>

// ======================================
// PENCARIAN DAN PAGINATION
// ======================================

const tabel = document.getElementById('tabelLaporan');
const jumlahData = document.getElementById('jumlahData');
const cariData = document.getElementById('cariData');
const infoData = document.getElementById('infoData');
const pagination = document.getElementById('daftarPagination');

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
        ? barisFilter.length
        : parseInt(jumlahData.value);

    const totalData = barisFilter.length;

    const totalHalaman = Math.max(
        1,
        Math.ceil(totalData / Math.max(batasData, 1))
    );

    if (halamanSekarang > totalHalaman) {
        halamanSekarang = totalHalaman;
    }

    semuaBaris.forEach(function(baris) {
        baris.style.display = 'none';
    });

    const mulai = (halamanSekarang - 1) * batasData;

    const akhir = jumlahData.value === 'all'
        ? totalData
        : mulai + batasData;

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
// PERSIAPAN CETAK
// ======================================

// Tampilkan seluruh data saat mencetak.

window.addEventListener('beforeprint', function() {

    const semuaBaris = tabel.querySelectorAll('tbody tr');

    semuaBaris.forEach(function(baris) {
        baris.style.display = 'table-row';
    });

});

// Kembalikan pagination setelah mencetak.

window.addEventListener('afterprint', function() {
    tampilkanData();
});

// Jalankan pertama kali.

tampilkanData();

</script>

</body>
</html>
