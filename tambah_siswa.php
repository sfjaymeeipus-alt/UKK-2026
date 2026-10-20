<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];
$pesan = "";

if (isset($_POST['simpan'])) {

    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nisn = mysqli_real_escape_string($koneksi, $_POST['nisn']);
    $nama_siswa = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $status_aktif = (int) $_POST['status_aktif'];

    $query = "
        INSERT INTO t_siswa
        (nis, nisn, nama, jenis_kelamin, tanggal_lahir, alamat, status_aktif)
        VALUES
        ('$nis', '$nisn', '$nama_siswa', '$jenis_kelamin',
         '$tanggal_lahir', '$alamat', '$status_aktif')
    ";

    if (mysqli_query($koneksi, $query)) {

        header("Location: data_siswa.php");
        exit;

    } else {

        $pesan = "Gagal menyimpan data: " . mysqli_error($koneksi);

    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Siswa</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
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

        <a href="dashboard.php" class="nav-link text-white rounded py-2">
            Dashboard
        </a>

        <a href="data_siswa.php"
           class="nav-link active bg-primary text-white rounded py-2">
            Data Siswa
        </a>

        <a href="data_guru.php" class="nav-link text-white rounded py-2">
            Data Guru
        </a>

        <a href="data_kelas.php" class="nav-link text-white rounded py-2">
            Kelas
        </a>

        <a href="data_tahun_ajaran.php" class="nav-link text-white rounded py-2">
            Tahun Ajaran
        </a>

        <a href="data_penempatan_siswa.php" class="nav-link text-white rounded py-2">
            Penempatan Siswa
        </a>

        <a href="data_wali_kelas.php" class="nav-link text-white rounded py-2">
            Wali Kelas
        </a>

        <a href="data_kategori_pelanggaran.php" class="nav-link text-white rounded py-2">
            Kategori Pelanggaran
        </a>

        <a href="data_jenis_pelanggaran.php" class="nav-link text-white rounded py-2">
            Jenis Pelanggaran
        </a>

        <a href="data_laporan.php" class="nav-link text-white rounded py-2">
            Laporan
        </a>

        <a href="about_me.php" class="nav-link text-white rounded py-2">
            About Me
        </a>

        <hr class="border-secondary my-2">

        <a href="logout.php" class="nav-link text-danger rounded py-2">
            Logout
        </a>

    </nav>

</aside>
        <!-- KONTEN TAMBAH SISWA -->
        <main class="col-md-9 col-lg-10 p-4">

            <div class="card shadow-sm">

                <div class="card-body">

                    <h2 class="mb-3">Tambah Data Siswa</h2>

                    <p class="text-muted">
                        Silakan isi formulir berikut untuk menambahkan siswa.
                    </p>

                    <?php if ($pesan != "") { ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>

                    <?php } ?>

                    <form method="POST" action="">

                        <div class="mb-3">
                            <label for="nis" class="form-label">NIS</label>
                            <input
                                type="text"
                                name="nis"
                                id="nis"
                                class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="nisn" class="form-label">NISN</label>
                            <input
                                type="text"
                                name="nisn"
                                id="nisn"
                                class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Siswa</label>
                            <input
                                type="text"
                                name="nama"
                                id="nama"
                                class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="jenis_kelamin" class="form-label">
                                Jenis Kelamin
                            </label>

                            <select
                                name="jenis_kelamin"
                                id="jenis_kelamin"
                                class="form-select"
                                required>

                                <option value="">-- Pilih Jenis Kelamin --</option>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_lahir" class="form-label">
                                Tanggal Lahir
                            </label>

                            <input
                                type="date"
                                name="tanggal_lahir"
                                id="tanggal_lahir"
                                class="form-control"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>

                            <textarea
                                name="alamat"
                                id="alamat"
                                class="form-control"
                                rows="3"
                                required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="status_aktif" class="form-label">
                                Status
                            </label>

                            <select
                                name="status_aktif"
                                id="status_aktif"
                                class="form-select"
                                required>

                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>

                            </select>
                        </div>

                        <button
                            type="submit"
                            name="simpan"
                            class="btn btn-primary">
                            Simpan
                        </button>

                        <a href="data_siswa.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>

            </div>

        </main>

    </div>
</div>

</body>
</html>
