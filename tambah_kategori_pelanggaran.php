<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama_user = $_SESSION['nama'];
$pesan = "";

// Proses menyimpan data kategori
if (isset($_POST['simpan'])) {

    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $status_aktif = $_POST['status_aktif'];

    if ($nama == "") {

        $pesan = "Nama kategori wajib diisi.";

    } else {

        $stmt = mysqli_prepare($koneksi, "
            INSERT INTO t_pelanggaran_kategori
            (nama, deskripsi, status_aktif)
            VALUES (?, ?, ?)
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "ssi",
            $nama,
            $deskripsi,
            $status_aktif
        );

        if (mysqli_stmt_execute($stmt)) {

            header("Location: data_kategori_pelanggaran.php");
            exit;

        } else {

            $pesan = "Gagal menyimpan data: "
                . mysqli_stmt_error($stmt);

        }

        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Kategori Pelanggaran</title>

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
                   class="nav-link active bg-primary text-white rounded py-2">
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
                   class="nav-link text-white rounded py-2">
                    About Me
                </a>

                <hr class="border-secondary my-2">

                <a href="logout.php"
                   class="nav-link text-danger rounded py-2">
                    Logout
                </a>

            </nav>

        </aside>

        <!-- KONTEN FORM TAMBAH KATEGORI -->
        <main class="col-md-9 col-lg-10 p-4">

            <div class="card shadow-sm">
                <div class="card-body">

                    <h2 class="mb-3">Tambah Kategori Pelanggaran</h2>

                    <p class="text-muted">
                        Silakan isi formulir untuk menambahkan kategori pelanggaran.
                    </p>

                    <?php if ($pesan != "") { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>
                    <?php } ?>

                    <form method="POST">

                        <!-- Nama Kategori -->
                        <div class="mb-3">
                            <label class="form-label">Nama Kategori</label>

                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                placeholder="Masukkan nama kategori"
                                value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>"
                                required>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>

                            <textarea
                                name="deskripsi"
                                class="form-control"
                                rows="4"
                                placeholder="Masukkan deskripsi kategori"><?= htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label">Status</label>

                            <select
                                name="status_aktif"
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

                        <!-- Tombol -->
                        <button
                            type="submit"
                            name="simpan"
                            class="btn btn-primary">
                            Simpan
                        </button>

                        <a
                            href="data_kategori_pelanggaran.php"
                            class="btn btn-secondary">
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