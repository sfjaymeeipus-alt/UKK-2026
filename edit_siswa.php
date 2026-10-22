<?php
include "middleware/auth.php";
include "config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'] ?? 'Admin';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: data_siswa.php");
    exit;
}

$id = (int) $_GET['id'];

$query = mysqli_query($koneksi, "SELECT * FROM t_siswa WHERE id = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data siswa tidak ditemukan";
    exit;
}

$error = "";

if (isset($_POST['update'])) {
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nisn = mysqli_real_escape_string($koneksi, $_POST['nisn']);
    $nama_siswa = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $status_aktif = (int) $_POST['status_aktif'];

    $update = mysqli_query($koneksi, "
        UPDATE t_siswa SET
            nis = '$nis',
            nisn = '$nisn',
            nama = '$nama_siswa',
            jenis_kelamin = '$jenis_kelamin',
            tanggal_lahir = '$tanggal_lahir',
            alamat = '$alamat',
            status_aktif = '$status_aktif'
        WHERE id = $id
    ");

    if ($update) {
        header("Location: data_siswa.php");
        exit;
    } else {
        $error = "Gagal mengubah data: " . mysqli_error($koneksi);
    }

    // Menampilkan kembali nilai yang baru dimasukkan
    $data['nis'] = $nis;
    $data['nisn'] = $nisn;
    $data['nama'] = $nama_siswa;
    $data['jenis_kelamin'] = $jenis_kelamin;
    $data['tanggal_lahir'] = $tanggal_lahir;
    $data['alamat'] = $alamat;
    $data['status_aktif'] = $status_aktif;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Siswa - Sistem Informasi Pelanggaran Siswa</title>

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
        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-0">

            <!-- HEADER SAMA DENGAN DATA SISWA -->
            <header class="bg-white border-bottom p-3 p-md-4
                          d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="fw-bold text-dark mb-1">Edit Data Siswa</h5>
                    <small class="text-secondary">
                        Sistem Informasi Pelanggaran Siswa
                    </small>
                </div>

                <div class="d-flex align-items-center gap-2">

                    <div class="text-end">
                        <div class="fw-bold">
                            <?= htmlspecialchars($nama); ?>
                        </div>
                        <small class="text-secondary">Admin</small>
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

                <h3 class="fw-bold text-dark mb-2">Edit Data Siswa</h3>

                <p class="text-secondary mb-4">
                    Perbarui informasi identitas, alamat, dan status siswa.
                </p>

                <div class="card border-0 shadow-sm rounded-3">

                    <div class="card-header bg-white p-3 p-md-4">
                        <h5 class="fw-bold mb-1">Form Edit Siswa</h5>
                        <small class="text-secondary">
                            Ubah data siswa sesuai informasi terbaru.
                        </small>
                    </div>

                    <div class="card-body p-3 p-md-4">

                        <?php if ($error != "") { ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($error); ?>
                            </div>
                        <?php } ?>

                        <form method="POST">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">NIS</label>
                                <input type="text"
                                       name="nis"
                                       class="form-control"
                                       maxlength="10"
                                       minlength="10"
                                       pattern="[0-9]{10}"
                                       value="<?= htmlspecialchars($data['nis'] ?? ''); ?>"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">NISN</label>
                                <input type="text"
                                       name="nisn"
                                       maxlength="10"
                                       minlength="10"
                                       pattern="[0-9]{10}"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['nisn'] ?? ''); ?>"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Siswa</label>
                                <input type="text"
                                       name="nama"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['nama'] ?? ''); ?>"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Jenis Kelamin</label>
                                <select name="jenis_kelamin"
                                        class="form-select"
                                        required>
                                    <option value="L"
                                        <?= ($data['jenis_kelamin'] ?? '') == 'L' ? 'selected' : ''; ?>>
                                        Laki-laki
                                    </option>

                                    <option value="P"
                                        <?= ($data['jenis_kelamin'] ?? '') == 'P' ? 'selected' : ''; ?>>
                                        Perempuan
                                    </option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Lahir</label>
                                <input type="date"
                                       name="tanggal_lahir"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['tanggal_lahir'] ?? ''); ?>"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Alamat</label>
                                <textarea name="alamat"
                                          class="form-control"
                                          rows="3"
                                          required><?= htmlspecialchars($data['alamat'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status_aktif"
                                        class="form-select"
                                        required>
                                    <option value="1"
                                        <?= ($data['status_aktif'] ?? '') == 1 ? 'selected' : ''; ?>>
                                        Aktif
                                    </option>

                                    <option value="0"
                                        <?= ($data['status_aktif'] ?? '') == 0 ? 'selected' : ''; ?>>
                                        Tidak Aktif
                                    </option>
                                </select>
                            </div>

                            <button type="submit"
                                    name="update"
                                    class="btn btn-warning">
                                Update Data
                            </button>

                            <a href="data_siswa.php"
                               class="btn btn-secondary">
                                Kembali
                            </a>

                        </form>

                    </div>

                </div>

                <footer class="text-center text-secondary small mt-4">
                    Sistem Informasi Pelanggaran Siswa &copy; 2026
                </footer>

            </section>

        </main>

    </div>
</div>

</body>
</html>