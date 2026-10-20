<?php
include "middleware/auth.php";
include "config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    exit("Anda tidak memiliki akses");
}

$nama_admin = $_SESSION['nama'] ?? 'Admin';
$pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
} else {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
}

if ($id <= 0) {
    header("Location: data_tahun_ajaran.php");
    exit;
}

// Ambil data tahun ajaran
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_tahun_ajaran WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    exit("Data tahun ajaran tidak ditemukan.");
}

$nama = $data['nama'];
$tanggal_mulai = $data['tanggal_mulai'];
$tanggal_selesai = $data['tanggal_selesai'];
$status_aktif = (int) $data['status_aktif'];

// Proses simpan
if (isset($_POST['simpan'])) {
    $nama = trim($_POST['nama'] ?? '');
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $status_aktif = isset($_POST['status_aktif'])
        ? (int) $_POST['status_aktif'] : -1;

    if (
        $nama === '' ||
        $tanggal_mulai === '' ||
        $tanggal_selesai === ''
    ) {
        $pesan = "Semua kolom wajib diisi.";
    } elseif (!in_array($status_aktif, [0, 1], true)) {
        $pesan = "Status tahun ajaran tidak valid.";
    } elseif ($tanggal_mulai > $tanggal_selesai) {
        $pesan = "Tanggal mulai tidak boleh melebihi tanggal selesai.";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_tahun_ajaran
             SET nama = ?, tanggal_mulai = ?,
                 tanggal_selesai = ?, status_aktif = ?
             WHERE id = ?"
        );

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "sssii",
                $nama,
                $tanggal_mulai,
                $tanggal_selesai,
                $status_aktif,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: data_tahun_ajaran.php");
                exit;
            } else {
                $pesan = "Gagal memperbarui data tahun ajaran.";
            }

            mysqli_stmt_close($stmt);
        } else {
            $pesan = "Terjadi kesalahan saat menyiapkan query.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Tahun Ajaran - Sistem Informasi Pelanggaran Siswa</title>

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

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-0">

            <!-- HEADER -->
            <header class="bg-white border-bottom p-3 p-md-4
                          d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="fw-bold text-dark mb-1">Edit Tahun Ajaran</h5>
                    <small class="text-secondary">
                        Sistem Informasi Pelanggaran Siswa
                    </small>
                </div>

                <div class="d-flex align-items-center gap-2">

                    <div class="text-end">
                        <div class="fw-bold">
                            <?= htmlspecialchars($nama_admin); ?>
                        </div>
                        <small class="text-secondary">Admin</small>
                    </div>

                    <div class="bg-primary-subtle text-primary rounded-circle
                                d-flex justify-content-center align-items-center
                                fw-bold flex-shrink-0"
                         style="width: 42px; height: 42px;">
                        <?= strtoupper(substr($nama_admin, 0, 1)); ?>
                    </div>

                </div>

            </header>

            <!-- ISI HALAMAN -->
            <section class="p-3 p-md-4">

                <div class="d-flex justify-content-between align-items-center
                            flex-wrap gap-3 mb-4">

                    <div>
                        <h3 class="fw-bold text-dark mb-2">
                            Edit Data Tahun Ajaran
                        </h3>

                        <p class="text-secondary mb-0">
                            Perbarui informasi tahun ajaran yang terdaftar.
                        </p>
                    </div>

                    <a href="data_tahun_ajaran.php"
                       class="btn btn-outline-secondary">
                        Kembali
                    </a>

                </div>

                <!-- FORM EDIT -->
                <div class="card border-0 shadow-sm rounded-3">

                    <div class="card-header bg-white p-3 p-md-4">
                        <h5 class="fw-bold mb-1">Formulir Edit Tahun Ajaran</h5>
                        <small class="text-secondary">
                            Ubah nama, periode, dan status tahun ajaran.
                        </small>
                    </div>

                    <div class="card-body p-3 p-md-4">

                        <?php if ($pesan !== '') { ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($pesan); ?>
                            </div>
                        <?php } ?>

                        <form method="POST"
                              action="edit_tahun_ajaran.php?id=<?= $id; ?>">

                            <input type="hidden" name="id" value="<?= $id; ?>">

                            <div class="mb-3">
                                <label for="nama" class="form-label fw-semibold">
                                    Nama Tahun Ajaran
                                </label>

                                <input type="text"
                                       name="nama"
                                       id="nama"
                                       class="form-control"
                                       value="<?= htmlspecialchars($nama); ?>"
                                       placeholder="Contoh: 2026/2027"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_mulai"
                                       class="form-label fw-semibold">
                                    Tanggal Mulai
                                </label>

                                <input type="date"
                                       name="tanggal_mulai"
                                       id="tanggal_mulai"
                                       class="form-control"
                                       value="<?= htmlspecialchars($tanggal_mulai); ?>"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="tanggal_selesai"
                                       class="form-label fw-semibold">
                                    Tanggal Selesai
                                </label>

                                <input type="date"
                                       name="tanggal_selesai"
                                       id="tanggal_selesai"
                                       class="form-control"
                                       value="<?= htmlspecialchars($tanggal_selesai); ?>"
                                       required>
                            </div>

                            <div class="mb-4">
                                <label for="status_aktif"
                                       class="form-label fw-semibold">
                                    Status Tahun Ajaran
                                </label>

                                <select name="status_aktif"
                                        id="status_aktif"
                                        class="form-select"
                                        required>

                                    <option value="1"
                                        <?= $status_aktif === 1 ? 'selected' : ''; ?>>
                                        Aktif
                                    </option>

                                    <option value="0"
                                        <?= $status_aktif === 0 ? 'selected' : ''; ?>>
                                        Tidak Aktif
                                    </option>

                                </select>
                            </div>

                            <button type="submit"
                                    name="simpan"
                                    class="btn btn-primary">
                                Simpan Perubahan
                            </button>

                            <a href="data_tahun_ajaran.php"
                               class="btn btn-secondary">
                                Batal
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