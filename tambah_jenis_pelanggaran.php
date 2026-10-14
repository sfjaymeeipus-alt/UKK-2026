<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];
$pesan = "";

// Mengambil data kategori pelanggaran
$query_kategori = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_pelanggaran_kategori
    ORDER BY nama ASC
");

if (!$query_kategori) {
    die("Gagal mengambil kategori: " . mysqli_error($koneksi));
}

// Proses menyimpan data
if (isset($_POST['simpan'])) {

    $kode = trim($_POST['kode']);
    $nama_pelanggaran = trim($_POST['nama']);
    $kategori_id = (int) $_POST['pelanggaran_kategori_id'];
    $poin = (int) $_POST['poin'];
    $deskripsi = trim($_POST['deskripsi']);
    $status_aktif = (int) $_POST['status_aktif'];

    if ($kode == "" || $nama_pelanggaran == "" || $kategori_id <= 0) {

        $pesan = "Kode, nama pelanggaran, dan kategori wajib diisi.";

    } elseif ($poin < 0) {

        $pesan = "Poin tidak boleh kurang dari 0.";

    } else {

        $stmt = mysqli_prepare($koneksi, "
            INSERT INTO t_pelanggaran
            (
                kode,
                nama,
                pelanggaran_kategori_id,
                poin,
                deskripsi,
                status_aktif
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            $pesan = "Gagal menyiapkan query: " . mysqli_error($koneksi);
        } else {

            mysqli_stmt_bind_param(
                $stmt,
                "ssiisi",
                $kode,
                $nama_pelanggaran,
                $kategori_id,
                $poin,
                $deskripsi,
                $status_aktif
            );

            if (mysqli_stmt_execute($stmt)) {

                mysqli_stmt_close($stmt);

                header("Location: data_jenis_pelanggaran.php");
                exit;

            } else {

                $pesan = "Gagal menyimpan data: "
                    . mysqli_stmt_error($stmt);

                mysqli_stmt_close($stmt);
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

    <title>Tambah Jenis Pelanggaran</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <aside class="col-md-3 col-lg-2 bg-dark min-vh-100 p-3">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <p class="text-white">
                Halo, <?= htmlspecialchars($nama); ?>
            </p>

            <ul class="nav nav-pills flex-column gap-2">

                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_siswa.php" class="nav-link text-white">
                        Data Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_guru.php" class="nav-link text-white">
                        Data Guru
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kelas.php" class="nav-link text-white">
                        Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_tahun_ajaran.php" class="nav-link text-white">
                        Tahun Ajaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_penempatan_siswa.php" class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_wali_kelas.php" class="nav-link text-white">
                        Wali Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kategori_pelanggaran.php" class="nav-link text-white">
                        Kategori Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_jenis_pelanggaran.php" class="nav-link active">
                        Jenis Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_laporan.php" class="nav-link text-white">
                        Laporan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="about_me.php" class="nav-link text-white">
                        About Me
                    </a>
                </li>

                <hr class="text-secondary">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                        Logout
                    </a>
                </li>

            </ul>

        </aside>

        <!-- KONTEN FORM -->
        <main class="col-md-9 col-lg-10 p-4">

            <div class="card shadow-sm border-0">
                <div class="card-body">

                    <h2 class="mb-3">Tambah Jenis Pelanggaran</h2>

                    <p class="text-muted">
                        Isi formulir berikut untuk menambahkan jenis pelanggaran siswa.
                    </p>

                    <?php if ($pesan != "") { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>
                    <?php } ?>

                    <form method="POST">

                        <!-- Kode -->
                        <div class="mb-3">
                            <label class="form-label">Kode Pelanggaran</label>

                            <input
                                type="text"
                                name="kode"
                                class="form-control"
                                placeholder="Contoh: PL001"
                                value="<?= htmlspecialchars($_POST['kode'] ?? ''); ?>"
                                required>
                        </div>

                        <!-- Nama -->
                        <div class="mb-3">
                            <label class="form-label">Nama Pelanggaran</label>

                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                placeholder="Masukkan nama pelanggaran"
                                value="<?= htmlspecialchars($_POST['nama'] ?? ''); ?>"
                                required>
                        </div>

                        <!-- Kategori -->
                        <div class="mb-3">
                            <label class="form-label">Kategori Pelanggaran</label>

                            <select
                                name="pelanggaran_kategori_id"
                                class="form-select"
                                required>

                                <option value="">-- Pilih Kategori --</option>

                                <?php while ($kategori = mysqli_fetch_assoc($query_kategori)) { ?>

                                    <option
                                        value="<?= $kategori['id']; ?>"
                                        <?= (isset($_POST['pelanggaran_kategori_id']) && $_POST['pelanggaran_kategori_id'] == $kategori['id']) ? 'selected' : ''; ?>>

                                        <?= htmlspecialchars($kategori['nama']); ?>

                                    </option>

                                <?php } ?>

                            </select>
                        </div>

                        <!-- Poin -->
                        <div class="mb-3">
                            <label class="form-label">Poin Pelanggaran</label>

                            <input
                                type="number"
                                name="poin"
                                class="form-control"
                                min="0"
                                placeholder="Masukkan jumlah poin"
                                value="<?= htmlspecialchars($_POST['poin'] ?? '0'); ?>"
                                required>
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>

                            <textarea
                                name="deskripsi"
                                class="form-control"
                                rows="4"
                                placeholder="Masukkan deskripsi pelanggaran"><?= htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
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
                            href="data_jenis_pelanggaran.php"
                            class="btn btn-secondary">
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