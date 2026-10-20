<?php
include "middleware/auth.php";
include "config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    exit("Anda tidak memiliki akses");
}

$nama_admin = $_SESSION['nama'] ?? 'Admin';

if (
    !isset($_GET['id']) ||
    !ctype_digit($_GET['id']) ||
    (int) $_GET['id'] <= 0
) {
    header("Location: data_jenis_pelanggaran.php");
    exit;
}

$id = (int) $_GET['id'];
$error = "";

// Ambil data jenis pelanggaran
$stmt = mysqli_prepare($koneksi, "
    SELECT id, kode, nama, pelanggaran_kategori_id,
           poin, deskripsi, status_aktif
    FROM t_pelanggaran
    WHERE id = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    exit("Data jenis pelanggaran tidak ditemukan.");
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode = trim($_POST['kode'] ?? '');
    $nama_pelanggaran = trim($_POST['nama'] ?? '');
    $kategori_id = (int) ($_POST['pelanggaran_kategori_id'] ?? 0);
    $poin = filter_var($_POST['poin'] ?? null, FILTER_VALIDATE_INT);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = isset($_POST['status_aktif'])
        ? (int) $_POST['status_aktif'] : -1;

    if (
        $kode == '' ||
        $nama_pelanggaran == '' ||
        $kategori_id <= 0 ||
        $poin === false ||
        $poin === null ||
        $poin < 0 ||
        !in_array($status_aktif, [0, 1], true)
    ) {
        $error = "Periksa kembali data yang kamu masukkan.";
    } else {
        // Pastikan kategori tersedia
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id FROM t_pelanggaran_kategori WHERE id = ?"
        );

        mysqli_stmt_bind_param($cek, "i", $kategori_id);
        mysqli_stmt_execute($cek);

        $kategori_result = mysqli_stmt_get_result($cek);
        $kategori_ada = mysqli_fetch_assoc($kategori_result);

        mysqli_stmt_close($cek);

        if (!$kategori_ada) {
            $error = "Kategori pelanggaran tidak ditemukan.";
        } else {
            $stmt = mysqli_prepare($koneksi, "
                UPDATE t_pelanggaran
                SET kode = ?,
                    nama = ?,
                    pelanggaran_kategori_id = ?,
                    poin = ?,
                    deskripsi = ?,
                    status_aktif = ?
                WHERE id = ?
            ");

            mysqli_stmt_bind_param(
                $stmt,
                "ssiisii",
                $kode,
                $nama_pelanggaran,
                $kategori_id,
                $poin,
                $deskripsi,
                $status_aktif,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: data_jenis_pelanggaran.php");
                exit;
            } else {
                $error = "Gagal memperbarui data. Pastikan kode pelanggaran tidak digunakan oleh data lain.";
            }

            mysqli_stmt_close($stmt);
        }
    }

    // Pertahankan isian jika validasi gagal
    $data['kode'] = $kode;
    $data['nama'] = $nama_pelanggaran;
    $data['pelanggaran_kategori_id'] = $kategori_id;
    $data['poin'] = $poin;
    $data['deskripsi'] = $deskripsi;
    $data['status_aktif'] = $status_aktif;
}

// Ambil daftar kategori
$kategori = mysqli_query(
    $koneksi,
    "SELECT id, nama
     FROM t_pelanggaran_kategori
     ORDER BY nama ASC"
);

if (!$kategori) {
    die("Gagal mengambil kategori: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Jenis Pelanggaran - Sistem Informasi Pelanggaran Siswa</title>

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
                   class="nav-link text-white rounded py-2">
                    Kategori Pelanggaran
                </a>

                <a href="data_jenis_pelanggaran.php"
                   class="nav-link active bg-primary text-white rounded py-2">
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

        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 p-0">

            <!-- HEADER -->
            <header class="bg-white border-bottom p-3 p-md-4
                           d-flex justify-content-between align-items-center">

                <div>
                    <h5 class="fw-bold mb-1">
                        Edit Jenis Pelanggaran
                    </h5>
                    <p class="text-secondary small mb-0">
                        Perbarui informasi jenis pelanggaran siswa.
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div class="text-end">
                        <div class="fw-semibold">
                            <?= htmlspecialchars($nama_admin); ?>
                        </div>
                        <small class="text-secondary">Admin</small>
                    </div>

                    <div class="rounded-circle bg-primary text-white
                                d-flex align-items-center justify-content-center fw-bold"
                         style="width:42px;height:42px;">
                        <?= strtoupper(substr($nama_admin, 0, 1)); ?>
                    </div>
                </div>

            </header>

            <!-- ISI HALAMAN -->
            <section class="p-3 p-md-4">

                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-white p-3">
                        <h6 class="fw-bold mb-0">
                            Form Edit Jenis Pelanggaran
                        </h6>
                    </div>

                    <div class="card-body p-4">

                        <?php if ($error != '') { ?>
                            <div class="alert alert-danger">
                                <?= htmlspecialchars($error); ?>
                            </div>
                        <?php } ?>

                        <form method="POST"
                              action="edit_jenis_pelanggaran.php?id=<?= $id; ?>">

                            <div class="mb-3">
                                <label class="form-label">
                                    Kode Pelanggaran
                                </label>

                                <input
                                    type="text"
                                    name="kode"
                                    class="form-control"
                                    maxlength="50"
                                    value="<?= htmlspecialchars($data['kode'] ?? ''); ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Nama Pelanggaran
                                </label>

                                <input
                                    type="text"
                                    name="nama"
                                    class="form-control"
                                    maxlength="150"
                                    value="<?= htmlspecialchars($data['nama'] ?? ''); ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Kategori Pelanggaran
                                </label>

                                <select
                                    name="pelanggaran_kategori_id"
                                    class="form-select"
                                    required>

                                    <option value="">
                                        -- Pilih Kategori --
                                    </option>

                                    <?php while ($kat = mysqli_fetch_assoc($kategori)) { ?>
                                        <option
                                            value="<?= (int) $kat['id']; ?>"
                                            <?= (string) $data['pelanggaran_kategori_id'] === (string) $kat['id'] ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($kat['nama']); ?>
                                        </option>
                                    <?php } ?>

                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Poin Pelanggaran
                                </label>

                                <input
                                    type="number"
                                    name="poin"
                                    class="form-control"
                                    min="0"
                                    step="1"
                                    value="<?= htmlspecialchars((string) ($data['poin'] ?? 0)); ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    Deskripsi
                                </label>

                                <textarea
                                    name="deskripsi"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Masukkan deskripsi pelanggaran"><?= htmlspecialchars($data['deskripsi'] ?? ''); ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">
                                    Status
                                </label>

                                <select
                                    name="status_aktif"
                                    class="form-select"
                                    required>

                                    <option value="1"
                                        <?= (string) $data['status_aktif'] === '1' ? 'selected' : ''; ?>>
                                        Aktif
                                    </option>

                                    <option value="0"
                                        <?= (string) $data['status_aktif'] === '0' ? 'selected' : ''; ?>>
                                        Tidak Aktif
                                    </option>

                                </select>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary">
                                    Simpan Perubahan
                                </button>

                                <a href="data_jenis_pelanggaran.php"
                                   class="btn btn-secondary">
                                    Kembali
                                </a>
                            </div>

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