<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];
$pesan = "";
$jenis_pesan = "danger";

if (isset($_POST['simpan'])) {

    $nama_tahun = mysqli_real_escape_string($koneksi, trim($_POST['nama']));
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = isset($_POST['status_aktif']) ? (int) $_POST['status_aktif'] : 0;

    // Validasi tanggal
    if ($tanggal_selesai <= $tanggal_mulai) {

        $pesan = "Tanggal selesai harus setelah tanggal mulai.";

    } else {

        // Cek apakah nama tahun ajaran sudah ada
        $cek = mysqli_query($koneksi, "
            SELECT id FROM t_tahun_ajaran
            WHERE LOWER(nama) = LOWER('$nama_tahun')
        ");

        if (mysqli_num_rows($cek) > 0) {

            $pesan = "Tahun ajaran ini sudah ada. Silakan masukkan tahun ajaran yang berbeda.";

        } else {

            // Cek apakah sudah ada tahun ajaran aktif
            $cek_aktif = mysqli_query($koneksi, "
                SELECT id FROM t_tahun_ajaran
                WHERE status_aktif = 1
            ");

            $jumlah_aktif = mysqli_num_rows($cek_aktif);

            // Jika memilih tidak aktif dan belum ada tahun aktif
            if ($status_aktif == 0 && $jumlah_aktif == 0) {

                $pesan = "Peringatan! Wajib ada minimal satu tahun ajaran yang aktif. Silakan aktifkan tahun ajaran ini.";

            } else {

                mysqli_begin_transaction($koneksi);

                try {

                    // Jika tahun ajaran baru aktif,
                    // nonaktifkan tahun ajaran sebelumnya
                    if ($status_aktif == 1) {

                        $update = mysqli_query($koneksi, "
                            UPDATE t_tahun_ajaran
                            SET status_aktif = 0
                        ");

                        if (!$update) {
                            throw new Exception(mysqli_error($koneksi));
                        }
                    }

                    // Simpan tahun ajaran
                    $query = mysqli_query($koneksi, "
                        INSERT INTO t_tahun_ajaran
                        (nama, tanggal_mulai, tanggal_selesai, status_aktif)
                        VALUES
                        ('$nama_tahun', '$tanggal_mulai', '$tanggal_selesai', '$status_aktif')
                    ");

                    if (!$query) {
                        throw new Exception(mysqli_error($koneksi));
                    }

                    mysqli_commit($koneksi);

                    header("Location: data_tahun_ajaran.php");
                    exit;

                } catch (Exception $e) {

                    mysqli_rollback($koneksi);

                    $pesan = "Gagal menyimpan data: " . $e->getMessage();
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Tahun Ajaran</title>

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

                <a href="data_kelas.php" class="nav-link text-white rounded py-2">
                    Kelas
                </a>

                <a href="data_tahun_ajaran.php"
                   class="nav-link active bg-primary text-white rounded py-2">
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

        <!-- KONTEN TAMBAH TAHUN AJARAN -->
        <main class="col-md-9 col-lg-10 p-4">

            <div class="card shadow-sm">
                <div class="card-body">

                    <h2 class="mb-3">Tambah Tahun Ajaran</h2>

                    <p class="text-muted">
                        Silakan isi formulir untuk menambahkan tahun ajaran.
                    </p>

                    <?php if ($pesan != "") { ?>

                        <div class="alert alert-<?php echo $jenis_pesan; ?> alert-dismissible fade show">
                            <?= htmlspecialchars($pesan); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>

                    <?php } ?>

                    <form method="POST" action="">

                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama Tahun Ajaran
                            </label>

                            <input
                                type="text"
                                name="nama"
                                id="nama"
                                class="form-control"
                                placeholder="Contoh: 2026/2027"
                                value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_mulai" class="form-label">
                                Tanggal Mulai
                            </label>

                            <input
                                type="date"
                                name="tanggal_mulai"
                                id="tanggal_mulai"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST['tanggal_mulai'] ?? ''); ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_selesai" class="form-label">
                                Tanggal Selesai
                            </label>

                            <input
                                type="date"
                                name="tanggal_selesai"
                                id="tanggal_selesai"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST['tanggal_selesai'] ?? ''); ?>"
                                required>
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

                                <option value="1"
                                    <?= (!isset($_POST['status_aktif']) || $_POST['status_aktif'] == '1') ? 'selected' : ''; ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= (isset($_POST['status_aktif']) && $_POST['status_aktif'] == '0') ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>

                            </select>
                        </div>

                        <button
                            type="submit"
                            name="simpan"
                            class="btn btn-primary">
                            Simpan
                        </button>

                        <a href="data_tahun_ajaran.php" class="btn btn-secondary">
                            Kembali
                        </a>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>