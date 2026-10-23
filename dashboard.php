<?php
include "middleware/auth.php";
include "config/koneksi.php";

$role = $_SESSION['role'];
$nama = $_SESSION['nama'];

// Menu admin
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

// Menu guru
$menuGuru = [
    ["Dashboard", "dashboard.php"],
    ["Catatan Pelanggaran", "data_catatan_pelanggaran.php"],
    ["Tindakan", "data_tindakan.php"],
    ["Riwayat", "data_riwayat.php"],
    ["Rekap Poin", "data_rekap_poin.php"],
    ["About Me", "about_me.php"]
];

// Pilih menu berdasarkan role
if ($role == 'admin') {
    $menu = $menuAdmin;
} elseif ($role == 'guru') {
    $menu = $menuGuru;
} else {
    die("Anda tidak memiliki akses.");
}

// MENGHITUNG JUMLAH DATA DARI DATABASE
function jumlahData($koneksi, $tabel)
{
    $hasil = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM `$tabel`");

    if ($hasil) {
        $data = mysqli_fetch_assoc($hasil);
        return (int) $data['total'];
    }

    return 0;
}

$totalSiswa = jumlahData($koneksi, 't_siswa');
$totalGuru = jumlahData($koneksi, 't_guru');
$totalKelas = jumlahData($koneksi, 't_kelas');
$totalTahunAjaran = jumlahData($koneksi, 't_tahun_ajaran');
$totalPenempatan = jumlahData($koneksi, 't_kelas_siswa');
$totalWaliKelas = jumlahData($koneksi, 't_wali_kelas');
$totalKategori = jumlahData($koneksi, 't_pelanggaran_kategori');
$totalJenisPelanggaran = jumlahData($koneksi, 't_pelanggaran');
$totalLaporan = jumlahData($koneksi, 't_pelanggaran_siswa');

// Kartu admin
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

// Kartu guru
$kartuGuru = [
    ["Catatan Pelanggaran", "Mencatat pelanggaran siswa.", "data_catatan_pelanggaran.php", $totalLaporan],
    ["Tindakan", "Mengelola tindakan atas pelanggaran.", "data_tindakan.php", $totalLaporan],
    ["Riwayat", "Melihat riwayat pelanggaran siswa.", "data_riwayat.php", $totalLaporan],
    ["Rekap Poin", "Melihat rekap poin pelanggaran siswa.", "data_rekap_poin.php", $totalLaporan]
];

if ($role == 'admin') {
    $kartu = $kartuAdmin;
} else {
    $kartu = $kartuGuru;
}

$halamanAktif = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Sistem Informasi Pelanggaran Siswa</title>

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
                SMK Muhammadiyah Kota Tasikmalaya
            </p>

            <p class="text-secondary small fw-bold mb-2">
                MENU NAVIGASI
            </p>

            <nav class="nav flex-column gap-1">

                <?php foreach ($menu as $item) { ?>

                    <a href="<?= htmlspecialchars($item[1]) ?>"
                       class="nav-link rounded py-2
                       <?= $halamanAktif == $item[1]
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
                    <h4 class="fw-bold text-dark mb-1">Dashboard</h4>

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
                            <?= htmlspecialchars(ucfirst($role)) ?>
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
                        Dashboard <?= htmlspecialchars(ucfirst($role)) ?>
                    </span>

                    <h2 class="fw-bold mb-3">
                        Selamat Datang, <?= htmlspecialchars($nama) ?>
                    </h2>

                    <p class="text-light mb-0 lh-lg">
                        Selamat datang di Sistem Informasi Pelanggaran Siswa.
                        Gunakan menu yang tersedia untuk mengelola data sekolah,
                        mencatat pelanggaran, dan melihat informasi sesuai hak
                        akses akunmu.
                    </p>

                </div>

                <!-- JUDUL MENU -->
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
                        Role: <?= htmlspecialchars(ucfirst($role)) ?>
                    </span>

                </div>

                <!-- KARTU MENU -->
                <div class="row g-3">

                    <?php
                    $nomor = 1;

                    foreach ($kartu as $item) {
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