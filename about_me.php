<?php
include "middleware/auth.php";
include "config/koneksi.php";

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$nama = $_SESSION['nama'] ?? 'Pengguna';
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About Me - Sistem Informasi Pelanggaran Siswa</title>

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

                <a href="dashboard.php"
                   class="nav-link text-white rounded py-2">
                    Dashboard
                </a>

                <?php if ($role == 'admin') { ?>

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
                       class="nav-link text-white rounded py-2">
                        Laporan
                    </a>

                <?php } elseif ($role == 'guru') { ?>

                    <a href="data_catatan_pelanggaran.php"
                       class="nav-link text-white rounded py-2">
                        Catatan Pelanggaran
                    </a>

                    <a href="data_tindakan.php"
                       class="nav-link text-white rounded py-2">
                        Tindakan
                    </a>

                    <a href="data_riwayat.php"
                       class="nav-link text-white rounded py-2">
                        Riwayat
                    </a>

                    <a href="data_rekap_poin.php"
                       class="nav-link text-white rounded py-2">
                        Rekap Poin
                    </a>

                <?php } ?>

                <a href="about_me.php"
                   class="nav-link active bg-primary text-white rounded py-2">
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
            <header class="bg-white border-bottom px-4 py-3
                           d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="fw-bold mb-1">About Me</h5>
                    <small class="text-secondary">
                        Sistem Informasi Pelanggaran Siswa
                    </small>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="text-end">
                        <div class="fw-bold">
                            <?= htmlspecialchars($nama); ?>
                        </div>
                        <small class="text-secondary">
                            <?= htmlspecialchars(ucfirst($role)); ?>
                        </small>
                    </div>

                    <div class="rounded-circle bg-primary-subtle text-primary
                                d-flex justify-content-center align-items-center
                                fw-bold"
                         style="width: 42px; height: 42px;">
                        <?= strtoupper(substr($nama, 0, 1)); ?>
                    </div>
                </div>

            </header>

            <div class="p-3 p-md-4">

                <!-- JUDUL HALAMAN -->
                <div class="mb-4">
                    <h3 class="fw-bold mb-2">Tentang Saya</h3>
                    <p class="text-secondary mb-0">
                        Kenali profil pengembang dan proyek yang saya buat.
                    </p>
                </div>

                <!-- KARTU PROFIL -->
                <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">

<div class="bg-primary-subtle p-4 text-primary-emphasis">
    <h5 class="fw-bold mb-1">Profil Pengembang</h5>
    <p class="mb-0 small">
        Informasi singkat mengenai diri saya.
    </p>
</div>
                    <div class="card-body p-4 p-md-5">

                        <div class="row align-items-center g-4">

                            <div class="col-md-4 text-center">

                                <img src="foto_profil.jpeg"
                                     alt="Foto Puspita Dewi"
                                     class="rounded-circle img-thumbnail shadow-sm"
                                     style="width: 210px; height: 210px;
                                            object-fit: cover;">

                                <div class="mt-3">
                                    <span class="badge rounded-pill text-bg-primary px-3 py-2">
                                        XII RPL 2
                                    </span>
                                </div>

                            </div>

                            <div class="col-md-8">

                                <p class="text-primary fw-bold small mb-2">
                                    HALO, PERKENALKAN
                                </p>

                                <h1 class="fw-bold mb-2">
                                    Puspita Dewi
                                </h1>

                                <h5 class="text-secondary mb-3">
                                    Siswa Rekayasa Perangkat Lunak
                                </h5>

                                <p class="text-secondary lh-lg">
                                    Saya merupakan siswa kelas XII RPL 2
                                    di SMK Muhammadiyah Tasikmalaya.
                                    Saya memiliki minat dalam pengembangan
                                    aplikasi berbasis web dan sedang
                                    mengembangkan kemampuan pemrograman
                                    menggunakan PHP Native dan MySQL.
                                </p>

                                <div class="d-flex flex-wrap gap-2 mt-4">
                                    <span class="badge border text-dark bg-light p-2">
                                        PHP Native
                                    </span>

                                    <span class="badge border text-dark bg-light p-2">
                                        MySQL
                                    </span>

                                    <span class="badge border text-dark bg-light p-2">
                                        HTML
                                    </span>

                                    <span class="badge border text-dark bg-light p-2">
                                        JavaScript
                                    </span>

                                    <span class="badge border text-dark bg-light p-2">
                                        Bootstrap
                                    </span>
                                </div>

                                <a href="dashboard.php"
                                   class="btn btn-primary rounded-3 mt-4 px-4">
                                    Kembali ke Dashboard
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- INFORMASI PENDIDIKAN DAN KEAHLIAN -->
                <div class="row g-4 mb-4">

                    <div class="col-md-6">

                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">

                                <div class="rounded-3 bg-primary-subtle text-primary
                                            d-flex justify-content-center align-items-center mb-3"
                                     style="width: 48px; height: 48px;">
                                    <span class="fw-bold fs-5">01</span>
                                </div>

                                <h5 class="fw-bold mb-3">Pendidikan</h5>

                                <h6 class="fw-bold mb-1">XII RPL 2</h6>

                                <p class="text-secondary mb-2">
                                    SMK Muhammadiyah Tasikmalaya
                                </p>

                                <p class="small text-secondary mb-0">
                                    Kompetensi Keahlian Rekayasa Perangkat Lunak.
                                </p>

                            </div>
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="card border-0 shadow-sm rounded-4 h-100">
                            <div class="card-body p-4">

                                <div class="rounded-3 bg-success-subtle text-success
                                            d-flex justify-content-center align-items-center mb-3"
                                     style="width: 48px; height: 48px;">
                                    <span class="fw-bold fs-5">02</span>
                                </div>

                                <h5 class="fw-bold mb-3">Keahlian</h5>

                                <h6 class="fw-bold mb-1">
                                    Pengembangan Aplikasi Web
                                </h6>

                                <p class="text-secondary mb-2">
                                    Rekayasa Perangkat Lunak
                                </p>

                                <p class="small text-secondary mb-0">
                                    Mempelajari pembuatan aplikasi web,
                                    pengolahan basis data, dan desain antarmuka.
                                </p>

                            </div>
                        </div>

                    </div>

                </div>

                <!-- TENTANG PROYEK -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">

                    <div class="card-body p-4 p-md-5">

                        <div class="row align-items-center g-4">

                            <div class="col-md-8">

                                <span class="badge text-bg-primary mb-3">
                                    PROYEK SEKOLAH
                                </span>

                                <h4 class="fw-bold mb-3">
                                    Sistem Informasi Pelanggaran Siswa
                                </h4>

                                <p class="text-secondary lh-lg mb-3">
                                    Aplikasi berbasis web ini dibuat untuk
                                    membantu sekolah mengelola informasi
                                    pelanggaran siswa secara lebih terstruktur.
                                    Sistem menyediakan pengelolaan data dan
                                    informasi pelanggaran sesuai hak akses pengguna.
                                </p>

                                <p class="text-secondary mb-0">
                                    <strong>Teknologi:</strong>
                                    PHP Native, MySQL, HTML, JavaScript,
                                    dan Bootstrap.
                                </p>

                            </div>

                            <div class="col-md-4">

                                <div class="bg-light border rounded-4 p-4 text-center">
                                    <h2 class="fw-bold text-primary mb-2">
                                        RPL
                                    </h2>

                                    <p class="fw-semibold mb-1">
                                        Rekayasa Perangkat Lunak
                                    </p>

                                    <small class="text-secondary">
                                        Belajar, berkarya, dan berkembang.
                                    </small>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="text-center py-3">
                    <small class="text-secondary">
                        &copy; 2026 Sistem Informasi Pelanggaran Siswa
                        | Dibuat oleh Puspita Dewi
                    </small>
                </div>

            </div>

        </main>

    </div>
</div>

</body>
</html>