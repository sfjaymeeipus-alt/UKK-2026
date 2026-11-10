<?php
include "middleware/auth.php";
include "config/koneksi.php";

$role = $_SESSION['role'] ?? '';
$nama = $_SESSION['nama'] ?? 'Pengguna';
$user_id = (int) ($_SESSION['user_id'] ?? 0);

if (
    $role != 'admin' &&
    $role != 'guru' &&
    $role != 'wali kelas'
) {
    die("Anda tidak memiliki akses.");
}

// ======================================
// MENU ADMIN
// ======================================

$menuAdmin = [
    ["Dashboard", "dashboard.php"],
    ["Data Siswa", "data_siswa.php"],
    ["Data Guru", "data_guru.php"],
    ["Kelas", "data_kelas.php"],
    ["Tahun Ajaran", "data_tahun_ajaran.php"],
    ["Penempatan Siswa", "data_penempatan_siswa.php"],
    ["Wali Kelas", "data_wali_kelas.php"],
    ["Kategori Pelanggaran", "data_kategori_pelanggaran.php"],
    ["Jenis Pelanggaran", "data_jenis_pelanggaran.php"],
    ["Laporan", "data_laporan.php"],
    ["About Me", "about_me.php"]
];

// ======================================
// MENU GURU BIASA
// ======================================

$menuGuru = [
    ["Dashboard", "dashboard.php"],
    ["Catatan Pelanggaran", "data_catatan_pelanggaran.php"],
    ["Tindakan", "data_tindakan.php"],
    ["Riwayat", "data_riwayat.php"],
    ["Rekap Poin", "data_rekap_poin.php"],
    ["About Me", "about_me.php"]
];

// ======================================
// MENU WALI KELAS
// ======================================

$menuWali = [
    ["Dashboard", "dashboard.php"],
    ["Data Siswa", "walikelas/data_siswa.php"],
    ["Rekap Pelanggaran", "walikelas/rekap_pelanggaran.php"],
    ["Cetak Rekap", "walikelas/cetak_rekap.php"],
    ["About Me", "about_me.php"]
];

// ======================================
// FUNGSI HITUNG JUMLAH DATA
// ======================================

function jumlahData($koneksi, $tabel)
{
    $hasil = mysqli_query(
        $koneksi,
        "SELECT COUNT(*) AS total FROM `$tabel`"
    );

    if ($hasil) {
        $data = mysqli_fetch_assoc($hasil);
        return (int) $data['total'];
    }

    return 0;
}

// ======================================
// DATA ADMIN
// ======================================

$totalSiswa = jumlahData($koneksi, 't_siswa');
$totalGuru = jumlahData($koneksi, 't_guru');
$totalKelas = jumlahData($koneksi, 't_kelas');
$totalTahunAjaran = jumlahData($koneksi, 't_tahun_ajaran');
$totalPenempatan = jumlahData($koneksi, 't_kelas_siswa');
$totalWaliKelas = jumlahData($koneksi, 't_wali_kelas');
$totalKategori = jumlahData($koneksi, 't_pelanggaran_kategori');
$totalJenisPelanggaran = jumlahData($koneksi, 't_pelanggaran');
$totalLaporan = jumlahData($koneksi, 't_pelanggaran_siswa');

// ======================================
// KARTU ADMIN
// ======================================

$kartuAdmin = [
    ["Data Siswa", "Mengelola informasi data siswa.", "data_siswa.php", $totalSiswa],
    ["Data Guru", "Mengelola informasi data guru.", "data_guru.php", $totalGuru],
    ["Data Kelas", "Mengelola data kelas siswa.", "data_kelas.php", $totalKelas],
    ["Tahun Ajaran", "Mengelola periode tahun ajaran.", "data_tahun_ajaran.php", $totalTahunAjaran],
    ["Penempatan Siswa", "Mengelola penempatan siswa ke kelas.", "data_penempatan_siswa.php", $totalPenempatan],
    ["Wali Kelas", "Mengelola informasi wali kelas.", "data_wali_kelas.php", $totalWaliKelas],
    ["Kategori Pelanggaran", "Mengelola kategori pelanggaran siswa.", "data_kategori_pelanggaran.php", $totalKategori],
    ["Jenis Pelanggaran", "Mengelola jenis pelanggaran siswa.", "data_jenis_pelanggaran.php", $totalJenisPelanggaran],
    ["Laporan", "Melihat laporan pelanggaran.", "data_laporan.php", $totalLaporan]
];

// ======================================
// KARTU GURU BIASA
// ======================================

$kartuGuru = [
    ["Catatan Pelanggaran", "Mencatat pelanggaran siswa.", "data_catatan_pelanggaran.php", $totalLaporan],
    ["Tindakan", "Mengelola tindakan atas pelanggaran.", "data_tindakan.php", $totalLaporan],
    ["Riwayat", "Melihat riwayat pelanggaran siswa.", "data_riwayat.php", $totalLaporan],
    ["Rekap Poin", "Melihat rekap poin pelanggaran siswa.", "data_rekap_poin.php", $totalLaporan]
];

// ======================================
// DATA KHUSUS WALI KELAS
// ======================================

$dataGuru = null;
$daftarKelas = [];
$daftarSiswa = [];

$totalSiswaWali = 0;
$totalPelanggaranWali = 0;
$totalPoinWali = 0;

$isWaliKelas = ($role == 'wali kelas');
$guru_id = 0;

// Cari guru berdasarkan akun login
if ($role == 'guru' || $role == 'wali kelas') {

    $stmtGuru = mysqli_prepare($koneksi, "
        SELECT id, nama
        FROM t_guru
        WHERE user_id = ?
        LIMIT 1
    ");

    if ($stmtGuru) {

        mysqli_stmt_bind_param($stmtGuru, "i", $user_id);
        mysqli_stmt_execute($stmtGuru);

        $hasilGuru = mysqli_stmt_get_result($stmtGuru);
        $dataGuru = mysqli_fetch_assoc($hasilGuru);

        if ($dataGuru) {

            $guru_id = (int) $dataGuru['id'];

            // Ambil penugasan wali kelas yang masih aktif
            $stmtKelas = mysqli_prepare($koneksi, "
                SELECT
                    wk.id,
                    wk.kelas_id,
                    wk.tahun_ajaran_id,
                    k.nama AS nama_kelas,
                    ta.nama AS tahun_ajaran
                FROM t_wali_kelas wk
                JOIN t_kelas k
                    ON wk.kelas_id = k.id
                JOIN t_tahun_ajaran ta
                    ON wk.tahun_ajaran_id = ta.id
                WHERE wk.guru_id = ?
                    AND wk.status_aktif = 1
                    AND ta.status_aktif = 1
                ORDER BY k.nama ASC
            ");

            if ($stmtKelas) {

                mysqli_stmt_bind_param($stmtKelas, "i", $guru_id);
                mysqli_stmt_execute($stmtKelas);

                $hasilKelas = mysqli_stmt_get_result($stmtKelas);

                while ($barisKelas = mysqli_fetch_assoc($hasilKelas)) {
                    $daftarKelas[] = $barisKelas;
                }

                // Jika memiliki penugasan aktif, berarti wali kelas
                if (count($daftarKelas) > 0) {

                    $isWaliKelas = true;

                    // Ambil data siswa dan statistik sesuai kelas serta tahun ajaran
                    foreach ($daftarKelas as $kelas) {

                        $kelas_id = (int) $kelas['kelas_id'];
                        $tahun_ajaran_id = (int) $kelas['tahun_ajaran_id'];

                        // Hitung siswa
                        $stmtJumlahSiswa = mysqli_prepare($koneksi, "
                            SELECT COUNT(DISTINCT siswa_id) AS total
                            FROM t_kelas_siswa
                            WHERE kelas_id = ?
                                AND tahun_ajaran_id = ?
                                AND status_aktif = 1
                        ");

                        if ($stmtJumlahSiswa) {

                            mysqli_stmt_bind_param(
                                $stmtJumlahSiswa,
                                "ii",
                                $kelas_id,
                                $tahun_ajaran_id
                            );

                            mysqli_stmt_execute($stmtJumlahSiswa);

                            $hasilJumlahSiswa = mysqli_stmt_get_result($stmtJumlahSiswa);
                            $dataJumlahSiswa = mysqli_fetch_assoc($hasilJumlahSiswa);

                            $totalSiswaWali += (int) ($dataJumlahSiswa['total'] ?? 0);
                        }

                        // Ambil daftar siswa
                        $stmtDaftarSiswa = mysqli_prepare($koneksi, "
                            SELECT DISTINCT
                                s.nis,
                                s.nama,
                                k.nama AS nama_kelas
                            FROM t_kelas_siswa ks
                            JOIN t_siswa s
                                ON ks.siswa_id = s.id
                            JOIN t_kelas k
                                ON ks.kelas_id = k.id
                            WHERE ks.kelas_id = ?
                                AND ks.tahun_ajaran_id = ?
                                AND ks.status_aktif = 1
                            ORDER BY s.nama ASC
                        ");

                        if ($stmtDaftarSiswa) {

                            mysqli_stmt_bind_param(
                                $stmtDaftarSiswa,
                                "ii",
                                $kelas_id,
                                $tahun_ajaran_id
                            );

                            mysqli_stmt_execute($stmtDaftarSiswa);

                            $hasilDaftarSiswa = mysqli_stmt_get_result($stmtDaftarSiswa);

                            while ($siswa = mysqli_fetch_assoc($hasilDaftarSiswa)) {
                                $daftarSiswa[] = $siswa;
                            }
                        }

                        // Hitung pelanggaran berdasarkan kelas dan tahun ajaran
                        $stmtPelanggaran = mysqli_prepare($koneksi, "
                            SELECT
                                COUNT(*) AS total,
                                COALESCE(SUM(poin), 0) AS total_poin
                            FROM t_pelanggaran_siswa
                            WHERE kelas_id = ?
                                AND tahun_ajaran_id = ?
                        ");

                        if ($stmtPelanggaran) {

                            mysqli_stmt_bind_param(
                                $stmtPelanggaran,
                                "ii",
                                $kelas_id,
                                $tahun_ajaran_id
                            );

                            mysqli_stmt_execute($stmtPelanggaran);

                            $hasilPelanggaran = mysqli_stmt_get_result($stmtPelanggaran);
                            $dataPelanggaran = mysqli_fetch_assoc($hasilPelanggaran);

                            $totalPelanggaranWali += (int) ($dataPelanggaran['total'] ?? 0);
                            $totalPoinWali += (int) ($dataPelanggaran['total_poin'] ?? 0);
                        }
                    }
                }
            }
        }
    }
}

// ======================================
// TENTUKAN MENU BERDASARKAN HAK AKSES
// ======================================

if ($role == 'admin') {
    $menu = $menuAdmin;
} elseif ($isWaliKelas) {
    $menu = $menuWali;
} else {
    $menu = $menuGuru;
}

$halamanAktif = basename($_SERVER['PHP_SELF']);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Sistem Informasi Pelanggaran Siswa</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <aside class="col-md-3 col-lg-2 bg-dark text-white p-3 position-sticky top-0 vh-100 overflow-auto align-self-start">

            <h5 class="fw-bold mb-1">Sistem Informasi</h5>
            <h6 class="fw-bold">Pelanggaran Siswa</h6>

            <p class="text-secondary small mb-4">
                SMK Muhammadiyah Kota Tasikmalaya
            </p>

            <p class="text-secondary small fw-bold mb-2">
                MENU NAVIGASI
            </p>

            <nav class="nav flex-column gap-1">

                <?php foreach ($menu as $item) { ?>

                    <a href="<?= htmlspecialchars($item[1]) ?>"
                       class="nav-link rounded py-2
                       <?= $halamanAktif == basename($item[1])
                           ? 'active bg-primary text-white'
                           : 'text-white' ?>">

                        <?= htmlspecialchars($item[0]) ?>

                    </a>

                <?php } ?>

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
                    <h4 class="fw-bold text-dark mb-1">
                        Dashboard
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

                        <small class="text-secondary">
                            <?php
                            if ($role == 'admin') {
                                echo 'Admin';
                            } elseif ($isWaliKelas) {
                                echo 'Wali Kelas';
                            } else {
                                echo 'Guru';
                            }
                            ?>
                        </small>

                    </div>

                    <div class="bg-primary-subtle text-primary rounded-circle
                                d-flex align-items-center justify-content-center
                                fw-bold flex-shrink-0"
                         style="width: 42px; height: 42px;">

                        <?= strtoupper(substr($nama, 0, 1)) ?>

                    </div>

                </div>

            </header>

            <!-- ISI DASHBOARD -->
            <section class="p-3 p-md-4">

                <!-- SAMBUTAN -->
                <div class="bg-dark text-white rounded-3 shadow-sm p-4 p-md-5 mb-4">

                    <span class="badge border border-secondary
                                 text-white mb-3 p-2">

                        <?php
                        if ($role == 'admin') {
                            echo 'Dashboard Admin';
                        } elseif ($isWaliKelas) {
                            echo 'Dashboard Wali Kelas';
                        } else {
                            echo 'Dashboard Guru';
                        }
                        ?>

                    </span>

                    <h2 class="fw-bold mb-3">
                        Selamat Datang, <?= htmlspecialchars($nama) ?>
                    </h2>

                    <p class="text-light mb-0 lh-lg">

                        Selamat datang di Sistem Informasi Pelanggaran Siswa.
                        Gunakan menu yang tersedia untuk melihat dan mengelola
                        informasi sesuai hak akses akunmu.

                    </p>

                </div>

                <?php if ($role == 'admin') { ?>

                    <!-- DASHBOARD ADMIN -->
                    <div class="d-flex justify-content-between align-items-center
                                flex-wrap gap-2 mb-3">

                        <div>
                            <h4 class="fw-bold text-dark mb-1">
                                Menu Pengelolaan
                            </h4>

                            <p class="text-secondary mb-0">
                                Pilih halaman yang ingin kamu buka.
                            </p>
                        </div>

                        <span class="badge bg-primary-subtle text-primary p-2">
                            Role: Admin
                        </span>

                    </div>

                    <div class="row g-3">

                        <?php
                        $nomor = 1;

                        foreach ($kartuAdmin as $item) {
                        ?>

                            <div class="col-12 col-md-6 col-xl-4">

                                <div class="card h-100 border shadow-sm">

                                    <div class="card-body p-4">

                                        <p class="text-primary fw-bold small mb-3">
                                            MENU <?= str_pad($nomor, 2, "0", STR_PAD_LEFT) ?>
                                        </p>

                                        <h5 class="card-title fw-bold">
                                            <?= htmlspecialchars($item[0]) ?>
                                        </h5>

                                        <p class="card-text text-secondary small mb-2">
                                            <?= htmlspecialchars($item[1]) ?>
                                        </p>

                                        <h4 class="fw-bold mb-3">
                                            <?= (int) $item[3] ?>
                                        </h4>

                                        <a href="<?= htmlspecialchars($item[2]) ?>"
                                           class="btn btn-outline-primary btn-sm">
                                            Lihat Data
                                        </a>

                                    </div>

                                </div>

                            </div>

                        <?php
                            $nomor++;
                        }
                        ?>

                    </div>

                <?php } elseif ($isWaliKelas) { ?>

                    <!-- DASHBOARD WALI KELAS -->

                    <div class="mb-4">

                        <h4 class="fw-bold text-dark mb-1">
                            Informasi Wali Kelas
                        </h4>

                        <p class="text-secondary mb-0">
                            Informasi siswa dan pelanggaran dari kelas
                            yang menjadi tanggung jawabmu.
                        </p>

                    </div>

                    <!-- STATISTIK WALI KELAS -->
                    <div class="row g-3 mb-4">

                        <div class="col-12 col-md-6 col-xl-4">

                            <div class="card border-0 shadow-sm h-100">

                                <div class="card-body p-4">

                                    <h6 class="text-secondary">
                                        Jumlah Kelas
                                    </h6>

                                    <h2 class="fw-bold text-dark">
                                        <?= count($daftarKelas) ?>
                                    </h2>

                                </div>

                            </div>

                        </div>

                        <div class="col-12 col-md-6 col-xl-4">

                            <div class="card border-0 shadow-sm h-100">

                                <div class="card-body p-4">

                                    <h6 class="text-secondary">
                                        Jumlah Siswa
                                    </h6>

                                    <h2 class="fw-bold text-dark">
                                        <?= $totalSiswaWali ?>
                                    </h2>

                                </div>

                            </div>

                        </div>

                        <div class="col-12 col-md-6 col-xl-4">

                            <div class="card border-0 shadow-sm h-100">

                                <div class="card-body p-4">

                                    <h6 class="text-secondary">
                                        Total Pelanggaran
                                    </h6>

                                    <h2 class="fw-bold text-dark">
                                        <?= $totalPelanggaranWali ?>
                                    </h2>

                                </div>

                            </div>

                        </div>

                        <div class="col-12 col-md-6 col-xl-4">

                            <div class="card border-0 shadow-sm h-100">

                                <div class="card-body p-4">

                                    <h6 class="text-secondary">
                                        Total Poin Pelanggaran
                                    </h6>

                                    <h2 class="fw-bold text-dark">
                                        <?= $totalPoinWali ?>
                                    </h2>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- KELAS YANG DIAMPU -->
                    <div class="card border-0 shadow-sm mb-4">

                        <div class="card-body p-4">

                            <h5 class="fw-bold mb-3">
                                Kelas yang Diampu
                            </h5>

                            <div class="table-responsive">

                                <table class="table table-bordered table-striped align-middle">

                                    <thead class="table-dark">

                                        <tr>
                                            <th>No</th>
                                            <th>Nama Kelas</th>
                                            <th>Tahun Ajaran</th>
                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php if (count($daftarKelas) > 0) { ?>

                                            <?php foreach ($daftarKelas as $i => $kelas) { ?>

                                                <tr>
                                                    <td><?= $i + 1 ?></td>
                                                    <td><?= htmlspecialchars($kelas['nama_kelas']) ?></td>
                                                    <td><?= htmlspecialchars($kelas['tahun_ajaran']) ?></td>
                                                </tr>

                                            <?php } ?>

                                        <?php } else { ?>

                                            <tr>
                                                <td colspan="3" class="text-center">
                                                    Belum ada penugasan kelas aktif.
                                                </td>
                                            </tr>

                                        <?php } ?>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                    <!-- DAFTAR SISWA -->
                    <div class="card border-0 shadow-sm">

                        <div class="card-body p-4">

                            <div class="d-flex justify-content-between align-items-center
                                        flex-wrap gap-2 mb-3">

                                <h5 class="fw-bold mb-0">
                                    Daftar Siswa
                                </h5>

                                <a href="walikelas/data_siswa.php"
                                   class="btn btn-outline-primary btn-sm">
                                    Lihat Semua Siswa
                                </a>

                            </div>

                            <div class="table-responsive">

                                <table class="table table-bordered table-striped align-middle">

                                    <thead class="table-dark">

                                        <tr>
                                            <th>No</th>
                                            <th>NIS</th>
                                            <th>Nama Siswa</th>
                                            <th>Kelas</th>
                                        </tr>

                                    </thead>

                                    <tbody>

                                        <?php if (count($daftarSiswa) > 0) { ?>

                                            <?php foreach ($daftarSiswa as $i => $siswa) { ?>

                                                <tr>
                                                    <td><?= $i + 1 ?></td>
                                                    <td><?= htmlspecialchars($siswa['nis']) ?></td>
                                                    <td><?= htmlspecialchars($siswa['nama']) ?></td>
                                                    <td><?= htmlspecialchars($siswa['nama_kelas']) ?></td>
                                                </tr>

                                            <?php } ?>

                                        <?php } else { ?>

                                            <tr>
                                                <td colspan="4" class="text-center">
                                                    Belum ada data siswa.
                                                </td>
                                            </tr>

                                        <?php } ?>

                                    </tbody>

                                </table>

                            </div>

                        </div>

                    </div>

                <?php } else { ?>

                    <!-- DASHBOARD GURU BIASA -->

                    <div class="d-flex justify-content-between align-items-center
                                flex-wrap gap-2 mb-3">

                        <div>

                            <h4 class="fw-bold text-dark mb-1">
                                Menu Pengelolaan
                            </h4>

                            <p class="text-secondary mb-0">
                                Pilih halaman yang ingin kamu buka.
                            </p>

                        </div>

                        <span class="badge bg-primary-subtle text-primary p-2">
                            Role: Guru
                        </span>

                    </div>

                    <div class="row g-3">

                        <?php
                        $nomor = 1;

                        foreach ($kartuGuru as $item) {
                        ?>

                            <div class="col-12 col-md-6 col-xl-4">

                                <div class="card h-100 border shadow-sm">

                                    <div class="card-body p-4">

                                        <p class="text-primary fw-bold small mb-3">
                                            MENU <?= str_pad($nomor, 2, "0", STR_PAD_LEFT) ?>
                                        </p>

                                        <h5 class="card-title fw-bold">
                                            <?= htmlspecialchars($item[0]) ?>
                                        </h5>

                                        <p class="card-text text-secondary small mb-2">
                                            <?= htmlspecialchars($item[1]) ?>
                                        </p>

                                        <a href="<?= htmlspecialchars($item[2]) ?>"
                                           class="btn btn-outline-primary btn-sm">
                                            Lihat Data
                                        </a>

                                    </div>

                                </div>

                            </div>

                        <?php
                            $nomor++;
                        }
                        ?>

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