<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];
$pesan = "";

// Mengambil data guru
$query_guru = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_guru
    ORDER BY nama ASC
");

// Mengambil data kelas
$query_kelas = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_kelas
    ORDER BY nama ASC
");

// Mengambil data tahun ajaran
$query_tahun = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_tahun_ajaran
    ORDER BY nama DESC
");

// Proses menyimpan data
if (isset($_POST['simpan'])) {

    $guru_id = $_POST['guru_id'];
    $kelas_id = $_POST['kelas_id'];
    $tahun_ajaran_id = $_POST['tahun_ajaran_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = !empty($_POST['tanggal_selesai'])
        ? $_POST['tanggal_selesai']
        : NULL;
    $status_aktif = $_POST['status_aktif'];

    $stmt = mysqli_prepare($koneksi, "
        INSERT INTO t_wali_kelas
        (
            guru_id,
            kelas_id,
            tahun_ajaran_id,
            tanggal_mulai,
            tanggal_selesai,
            status_aktif
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "iiissi",
        $guru_id,
        $kelas_id,
        $tahun_ajaran_id,
        $tanggal_mulai,
        $tanggal_selesai,
        $status_aktif
    );

    if (mysqli_stmt_execute($stmt)) {

        header("Location: data_wali_kelas.php");
        exit;

    } else {

        $pesan = "Gagal menyimpan data wali kelas: "
            . mysqli_stmt_error($stmt);

    }

    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Wali Kelas</title>

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
                    <a href="data_wali_kelas.php" class="nav-link active">
                        Wali Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kategori_pelanggaran.php" class="nav-link text-white">
                        Kategori Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_jenis_pelanggaran.php" class="nav-link text-white">
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

        <!-- KONTEN FORM TAMBAH WALI KELAS -->
        <main class="col-md-9 col-lg-10 p-4">

            <div class="card shadow-sm">
                <div class="card-body">

                    <h2 class="mb-3">Tambah Wali Kelas</h2>

                    <p class="text-muted">
                        Silakan isi formulir untuk menambahkan data wali kelas.
                    </p>

                    <?php if ($pesan != "") { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>
                    <?php } ?>

                    <form method="POST">

                        <!-- Pilih Guru -->
                        <div class="mb-3">
                            <label class="form-label">Nama Guru</label>

                            <select
                                name="guru_id"
                                class="form-select"
                                required>

                                <option value="">-- Pilih Guru --</option>

                                <?php while ($guru = mysqli_fetch_assoc($query_guru)) { ?>

                                    <option
                                        value="<?= $guru['id']; ?>"
                                        <?= (isset($_POST['guru_id']) && $_POST['guru_id'] == $guru['id']) ? 'selected' : ''; ?>>

                                        <?= htmlspecialchars($guru['nama']); ?>

                                    </option>

                                <?php } ?>

                            </select>
                        </div>

                        <!-- Pilih Kelas -->
                        <div class="mb-3">
                            <label class="form-label">Kelas</label>

                            <select
                                name="kelas_id"
                                class="form-select"
                                required>

                                <option value="">-- Pilih Kelas --</option>

                                <?php while ($kelas = mysqli_fetch_assoc($query_kelas)) { ?>

                                    <option
                                        value="<?= $kelas['id']; ?>"
                                        <?= (isset($_POST['kelas_id']) && $_POST['kelas_id'] == $kelas['id']) ? 'selected' : ''; ?>>

                                        <?= htmlspecialchars($kelas['nama']); ?>

                                    </option>

                                <?php } ?>

                            </select>
                        </div>

                        <!-- Pilih Tahun Ajaran -->
                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran</label>

                            <select
                                name="tahun_ajaran_id"
                                class="form-select"
                                required>

                                <option value="">-- Pilih Tahun Ajaran --</option>

                                <?php while ($tahun = mysqli_fetch_assoc($query_tahun)) { ?>

                                    <option
                                        value="<?= $tahun['id']; ?>"
                                        <?= (isset($_POST['tahun_ajaran_id']) && $_POST['tahun_ajaran_id'] == $tahun['id']) ? 'selected' : ''; ?>>

                                        <?= htmlspecialchars($tahun['nama']); ?>

                                    </option>

                                <?php } ?>

                            </select>
                        </div>

                        <!-- Tanggal Mulai -->
                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>

                            <input
                                type="date"
                                name="tanggal_mulai"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST['tanggal_mulai'] ?? ''); ?>"
                                required>
                        </div>

                        <!-- Tanggal Selesai -->
                        <div class="mb-3">
                            <label class="form-label">
                                Tanggal Selesai
                                <span class="text-muted">(Opsional)</span>
                            </label>

                            <input
                                type="date"
                                name="tanggal_selesai"
                                class="form-control"
                                value="<?= htmlspecialchars($_POST['tanggal_selesai'] ?? ''); ?>">
                        </div>

                        <!-- Status Aktif -->
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
                            href="data_wali_kelas.php"
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
</html