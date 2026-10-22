
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

    $nama_kelas = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $tingkat = mysqli_real_escape_string($koneksi, $_POST['tingkat']);
    $jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
    $status_aktif = (int) $_POST['status_aktif'];

    // TAMBAHAN: CEK APAKAH NAMA KELAS SUDAH TERDATA
    $cek = mysqli_query($koneksi, "
        SELECT id
        FROM t_kelas
        WHERE nama = '$nama_kelas'
        LIMIT 1
    ");

    if (mysqli_num_rows($cek) > 0) {

        $pesan = "Data kelas sudah terdata. Silakan gunakan nama kelas yang lain.";

    } else {

        $query = "
            INSERT INTO t_kelas
            (nama, tingkat, jurusan, status_aktif)
            VALUES
            ('$nama_kelas', '$tingkat', '$jurusan', '$status_aktif')
        ";

        if (mysqli_query($koneksi, $query)) {

            header("Location: data_kelas.php");
            exit;

        } else {

            $pesan = "Gagal menyimpan data kelas: " . mysqli_error($koneksi);

        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Kelas</title>

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

                <a href="data_siswa.php" class="nav-link text-white rounded py-2">
                    Data Siswa
                </a>

                <a href="data_guru.php" class="nav-link text-white rounded py-2">
                    Data Guru
                </a>

                <a href="data_kelas.php"
                   class="nav-link active bg-primary text-white rounded py-2">
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


        <!-- KONTEN TAMBAH KELAS -->
        <main class="col-md-9 col-lg-10 p-4">

            <div class="card shadow-sm">
                <div class="card-body">

                    <h2 class="mb-3">Tambah Data Kelas</h2>

                    <p class="text-muted">
                        Silakan isi formulir untuk menambahkan data kelas.
                    </p>

                    <?php if ($pesan != "") { ?>

                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>

                    <?php } ?>

                    <form method="POST" action="">

                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Kelas
                            </label>

                            <input
                                type="text"
                                name="nama"
                                id="nama"
                                class="form-control"
                                placeholder="Contoh: Kelas X RPL 1"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="tingkat" class="form-label">
                                Tingkat
                            </label>

                            <select
                                name="tingkat"
                                id="tingkat"
                                class="form-select"
                                required>

                                <option value="">-- Pilih Tingkat --</option>
                                <option value="X">X</option>
                                <option value="XI">XI</option>
                                <option value="XII">XII</option>

                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="jurusan" class="form-label">
                                Jurusan
                            </label>

                            <select
                                name="jurusan"
                                id="jurusan"
                                class="form-select"
                                required>

                                <option value="">-- Pilih Jurusan --</option>
                                <option value="RPL">RPL</option>
                                <option value="TKJ">TKJ</option>
                                <option value="AKL">AKL</option>
                                <option value="MPLB">MPLB</option>

                            </select>
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

                        <a href="data_kelas.php" class="btn btn-secondary">
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
