<?php
include "middleware/auth.php";
include "config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    exit("Anda tidak memiliki akses");
}

$nama_admin = $_SESSION['nama'] ?? 'Admin';
$error = "";

if (!isset($_GET['id']) || !ctype_digit($_GET['id']) || (int) $_GET['id'] <= 0) {
    header("Location: data_wali_kelas.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = mysqli_prepare($koneksi, "
    SELECT guru_id, kelas_id, tahun_ajaran_id,
           tanggal_mulai, tanggal_selesai, status_aktif
    FROM t_wali_kelas
    WHERE id = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    exit("Data wali kelas tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guru_id = (int) ($_POST['guru_id'] ?? 0);
    $kelas_id = (int) ($_POST['kelas_id'] ?? 0);
    $tahun_ajaran_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
    $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
    $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');
    $tanggal_selesai_db = $tanggal_selesai !== ''
        ? $tanggal_selesai : null;
    $status_aktif = isset($_POST['status_aktif'])
        ? (int) $_POST['status_aktif'] : -1;

    if (
        $guru_id <= 0 ||
        $kelas_id <= 0 ||
        $tahun_ajaran_id <= 0 ||
        $tanggal_mulai === '' ||
        !in_array($status_aktif, [0, 1], true)
    ) {
        $error = "Semua data wajib diisi dengan benar.";
    } elseif (
        $tanggal_selesai !== '' &&
        $tanggal_selesai < $tanggal_mulai
    ) {
        $error = "Tanggal selesai tidak boleh sebelum tanggal mulai.";
    } else {
        $stmt = mysqli_prepare($koneksi, "
            UPDATE t_wali_kelas
            SET guru_id = ?,
                kelas_id = ?,
                tahun_ajaran_id = ?,
                tanggal_mulai = ?,
                tanggal_selesai = ?,
                status_aktif = ?
            WHERE id = ?
        ");

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "iiissii",
                $guru_id,
                $kelas_id,
                $tahun_ajaran_id,
                $tanggal_mulai,
                $tanggal_selesai_db,
                $status_aktif,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: data_wali_kelas.php");
                exit;
            } else {
                $error = "Gagal memperbarui data wali kelas.";
            }

            mysqli_stmt_close($stmt);
        } else {
            $error = "Gagal menyiapkan query perubahan data.";
        }
    }

    // Pertahankan isian jika validasi gagal.
    $data['guru_id'] = $guru_id;
    $data['kelas_id'] = $kelas_id;
    $data['tahun_ajaran_id'] = $tahun_ajaran_id;
    $data['tanggal_mulai'] = $tanggal_mulai;
    $data['tanggal_selesai'] = $tanggal_selesai;
    $data['status_aktif'] = $status_aktif;
}

$guru = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_guru ORDER BY nama ASC"
);

$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_kelas ORDER BY nama ASC"
);

$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY nama DESC"
);

if (!$guru || !$kelas || !$tahun) {
    exit("Gagal mengambil data pilihan: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Wali Kelas - Sistem Informasi Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR SAMA DENGAN DATA SISWA -->
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

                <a href="data_tahun_ajaran.php" class="nav-link text-white rounded py-2">
                    Tahun Ajaran
                </a>

                <a href="data_penempatan_siswa.php" class="nav-link text-white rounded py-2">
                    Penempatan Siswa
                </a>

                <a href="data_wali_kelas.php"
                   class="nav-link active bg-primary text-white rounded py-2">
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
                    <h5 class="fw-bold text-dark mb-1">
                        Edit Wali Kelas
                    </h5>

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

                <h3 class="fw-bold text-dark mb-2">
                    Edit Data Wali Kelas
                </h3>

                <p class="text-secondary mb-4">
                    Perbarui informasi penugasan wali kelas yang terdaftar.
                </p>

                <div class="card border-0 shadow-sm rounded-3">

                    <div class="card-header bg-white p-3 p-md-4">
                        <h5 class="fw-bold mb-1">
                            Form Edit Wali Kelas
                        </h5>

                        <small class="text-secondary">
                            Ubah guru, kelas, tahun ajaran, dan periode penugasan.
                        </small>
                    </div>

                    <div class="card-body p-3 p-md-4">

                        <?php if ($error !== '') { ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($error); ?>
                            </div>
                        <?php } ?>

                        <form method="POST"
                              action="edit_wali_kelas.php?id=<?= $id; ?>">

                            <div class="mb-3">
                                <label for="guru_id" class="form-label fw-semibold">
                                    Nama Guru
                                </label>

                                <select name="guru_id" id="guru_id"
                                        class="form-select" required>
                                    <option value="">-- Pilih Guru --</option>

                                    <?php while ($g = mysqli_fetch_assoc($guru)) { ?>
                                        <option value="<?= (int) $g['id']; ?>"
                                            <?= (int) $data['guru_id'] === (int) $g['id'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($g['nama']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="kelas_id" class="form-label fw-semibold">
                                    Kelas
                                </label>

                                <select name="kelas_id" id="kelas_id"
                                        class="form-select" required>
                                    <option value="">-- Pilih Kelas --</option>

                                    <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                        <option value="<?= (int) $k['id']; ?>"
                                            <?= (int) $data['kelas_id'] === (int) $k['id'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($k['nama']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="tahun_ajaran_id"
                                       class="form-label fw-semibold">
                                    Tahun Ajaran
                                </label>

                                <select name="tahun_ajaran_id"
                                        id="tahun_ajaran_id"
                                        class="form-select" required>
                                    <option value="">-- Pilih Tahun Ajaran --</option>

                                    <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>
                                        <option value="<?= (int) $ta['id']; ?>"
                                            <?= (int) $data['tahun_ajaran_id'] === (int) $ta['id'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($ta['nama']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_mulai"
                                           class="form-label fw-semibold">
                                        Tanggal Mulai
                                    </label>

                                    <input type="date"
                                           name="tanggal_mulai"
                                           id="tanggal_mulai"
                                           class="form-control"
                                           value="<?= htmlspecialchars($data['tanggal_mulai'] ?? ''); ?>"
                                           required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="tanggal_selesai"
                                           class="form-label fw-semibold">
                                        Tanggal Selesai
                                    </label>

                                    <input type="date"
                                           name="tanggal_selesai"
                                           id="tanggal_selesai"
                                           class="form-control"
                                           value="<?= htmlspecialchars($data['tanggal_selesai'] ?? ''); ?>">

                                    <small class="text-secondary">
                                        Kosongkan jika penugasan masih berlangsung.
                                    </small>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="status_aktif"
                                       class="form-label fw-semibold">
                                    Status
                                </label>

                                <select name="status_aktif"
                                        id="status_aktif"
                                        class="form-select" required>

                                    <option value="1"
                                        <?= (int) $data['status_aktif'] === 1 ? 'selected' : ''; ?>>
                                        Aktif
                                    </option>

                                    <option value="0"
                                        <?= (int) $data['status_aktif'] === 0 ? 'selected' : ''; ?>>
                                        Tidak Aktif
                                    </option>

                                </select>
                            </div>

                            <button type="submit" class="btn btn-warning">
                                Simpan Perubahan
                            </button>

                            <a href="data_wali_kelas.php"
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