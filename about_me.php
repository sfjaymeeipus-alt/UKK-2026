
<?php

include "middleware/auth.php";

$role = $_SESSION['role'];
$nama = "Puspita Dewi";

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About Me - Sistem Informasi Pelanggaran Siswa</title>

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
                Panel Pengelolaan Sekolah
            </p>

            <p class="text-secondary small fw-bold mb-2">
                MENU NAVIGASI
            </p>

            <nav class="nav flex-column gap-1">

                <a href="dashboard.php"
                   class="nav-link text-white rounded py-2">
                    Dashboard
                </a>

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
            <header class="bg-white border-bottom p-3 p-md-4
                           d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="fw-bold text-dark mb-1">
                        About Me
                    </h5>

                    <small class="text-secondary">
                        Sistem Informasi Pelanggaran Siswa
                    </small>
                </div>

                <div class="d-flex align-items-center gap-2">

                    <div class="text-end">
                        <div class="fw-bold">
                            <?= htmlspecialchars($nama); ?>
                        </div>

                        <small class="text-secondary">
                            <?= htmlspecialchars(ucfirst($role)); ?>
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

                <h3 class="fw-bold text-dark mb-2">
                    About Me
                </h3>

                <p class="text-secondary mb-4">
                    Sedikit cerita tentang saya dan proyek yang saya buat.
                </p>

                <!-- KARTU PROFIL -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">

                    <div class="card-header bg-white p-3 p-md-4">
                        <h5 class="fw-bold mb-1">
                            Profil Pengembang
                        </h5>

                        <small class="text-secondary">
                            Informasi singkat mengenai diri saya.
                        </small>
                    </div>

                    <div class="card-body p-3 p-md-4">

                        <div class="row align-items-center g-4">

                            <!-- FOTO PROFIL -->
                            <div class="col-12 col-lg-4 text-center">

                                <div class="d-inline-block p-2 rounded-circle bg-white shadow-sm">

                                    <img src="foto_profil.jpeg"
                                         alt="Foto profil Puspita Dewi"
                                         class="rounded-circle img-fluid"
                                         style="width: 220px; height: 220px; object-fit: cover;">

                                </div>

                            </div>

                            <!-- IDENTITAS -->
                            <div class="col-12 col-lg-8">

                                <p class="text-primary fw-semibold mb-2">
                                    HALO, SAYA
                                </p>

                                <h2 class="fw-bold text-dark mb-2">
                                    <?= htmlspecialchars($nama); ?>
                                </h2>

                                <h5 class="text-secondary mb-4">
                                    Siswa Rekayasa Perangkat Lunak
                                </h5>

                                <p class="text-secondary">
                                    Saya merupakan siswa kelas XII RPL 2 di
                                    SMK Muhammadiyah Tasikmalaya. Saya sedang belajar
                                    mengembangkan aplikasi berbasis web menggunakan
                                    PHP Native dan MySQL.
                                </p>

                                <a href="dashboard.php"
                                   class="btn btn-outline-secondary btn-sm mt-2">
                                    Kembali ke Dashboard
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- INFORMASI DIRI -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">

                    <div class="card-header bg-white p-3 p-md-4">
                        <h5 class="fw-bold mb-1">
                            Informasi Saya
                        </h5>

                        <small class="text-secondary">
                            Pendidikan dan bidang keahlian.
                        </small>
                    </div>

                    <div class="card-body p-3 p-md-4">

                        <div class="row g-3">

                            <div class="col-12 col-md-6">

                                <div class="border rounded-3 p-4 h-100">

                                    <p class="text-primary fw-semibold mb-2">
                                        PENDIDIKAN
                                    </p>

                                    <h6 class="fw-bold">
                                        XII RPL 2
                                    </h6>

                                    <p class="text-secondary mb-0">
                                        SMK Muhammadiyah Tasikmalaya
                                    </p>

                                </div>

                            </div>

                            <div class="col-12 col-md-6">

                                <div class="border rounded-3 p-4 h-100">

                                    <p class="text-primary fw-semibold mb-2">
                                        KEAHLIAN
                                    </p>

                                    <h6 class="fw-bold">
                                        Rekayasa Perangkat Lunak
                                    </h6>

                                    <p class="text-secondary mb-0">
                                        Pengembangan aplikasi web
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- INFORMASI PROYEK -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">

                    <div class="card-header bg-white p-3 p-md-4">
                        <h5 class="fw-bold mb-1">
                            Proyek yang Sedang Dikerjakan
                        </h5>

                        <small class="text-secondary">
                            Teknologi yang digunakan dalam pengembangan aplikasi.
                        </small>
                    </div>

                    <div class="card-body p-3 p-md-4">

                        <div class="bg-primary text-white rounded-3 p-4 p-md-5">

                            <p class="text-white-50 fw-semibold mb-2">
                                PROJECT SEKOLAH
                            </p>

                            <h3 class="fw-bold mb-3">
                                Sistem Informasi Pelanggaran Siswa
                            </h3>

                            <p class="mb-4">
                                Aplikasi berbasis web untuk membantu pengelolaan
                                data dan informasi pelanggaran siswa secara terstruktur.
                            </p>

                            <div class="d-flex flex-wrap gap-2">

                                <span class="badge bg-light text-primary px-3 py-2">
                                    PHP Native
                                </span>

                                <span class="badge bg-light text-primary px-3 py-2">
                                    MySQL
                                </span>

                                <span class="badge bg-light text-primary px-3 py-2">
                                    HTML
                                </span>

                                <span class="badge bg-light text-primary px-3 py-2">
                                    CSS
                                </span>

                                <span class="badge bg-light text-primary px-3 py-2">
                                    JavaScript
                                </span>

                            </div>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
