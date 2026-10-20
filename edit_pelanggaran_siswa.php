
<?php
include "middleware/auth.php";
include "config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'guru') {
    exit("Anda tidak memiliki akses");
}

$nama_admin = $_SESSION['nama'] ?? 'guru';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header("Location: data_pelanggaran_siswa.php");
    exit;
}

// Ambil data catatan pelanggaran
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT * FROM t_pelanggaran_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    exit("Catatan pelanggaran tidak ditemukan.");
}

$error = '';

$statuses = [
    'Belum Ditindaklanjuti',
    'Diproses',
    'Selesai'
];

// Proses update
if (isset($_POST['update'])) {
    $nama_siswa = trim($_POST['nama_siswa'] ?? '');
    $nama_kelas = trim($_POST['nama_kelas'] ?? '');
    $nama_pelanggaran = trim($_POST['nama_pelanggaran'] ?? '');
    $nama_guru = trim($_POST['nama_guru'] ?? '');
    $tanggal = $_POST['tanggal'] ?? '';
    $keterangan = trim($_POST['keterangan'] ?? '');
    $poin = filter_var($_POST['poin'] ?? null, FILTER_VALIDATE_INT);
    $tindakan = trim($_POST['tindakan'] ?? '');
    $status = $_POST['status'] ?? '';

    if (
        $nama_siswa == '' ||
        $nama_kelas == '' ||
        $nama_pelanggaran == '' ||
        $nama_guru == '' ||
        $tanggal == '' ||
        $keterangan == ''
    ) {
        $error = "Data wajib diisi dengan lengkap.";
    } elseif (
        $poin === false ||
        $poin === null ||
        $poin < 0
    ) {
        $error = "Poin harus berupa angka nol atau lebih.";
    } elseif (!in_array($status, $statuses, true)) {
        $error = "Status pelanggaran tidak valid.";
    } else {
        $stmt = mysqli_prepare($koneksi, "
            UPDATE t_pelanggaran_siswa SET
                nama_siswa = ?,
                nama_kelas = ?,
                nama_pelanggaran = ?,
                nama_guru = ?,
                tanggal = ?,
                keterangan = ?,
                poin = ?,
                tindakan = ?,
                status = ?
            WHERE id = ?
        ");

        if ($stmt) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssssssissi",
                $nama_siswa,
                $nama_kelas,
                $nama_pelanggaran,
                $nama_guru,
                $tanggal,
                $keterangan,
                $poin,
                $tindakan,
                $status,
                $id
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header("Location: data_catatan_pelanggaran.php");
                exit;
            } else {
                $error = "Gagal memperbarui catatan: " .
                    mysqli_stmt_error($stmt);
            }

            mysqli_stmt_close($stmt);
        } else {
            $error = "Gagal menyiapkan query: " .
                mysqli_error($koneksi);
        }
    }

    // Pertahankan isian jika validasi gagal
    $data = array_merge($data, [
        'nama_siswa' => $nama_siswa,
        'nama_kelas' => $nama_kelas,
        'nama_pelanggaran' => $nama_pelanggaran,
        'nama_guru' => $nama_guru,
        'tanggal' => $tanggal,
        'keterangan' => $keterangan,
        'poin' => $poin,
        'tindakan' => $tindakan,
        'status' => $status
    ]);
}

function e($value) {
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Catatan Pelanggaran - Sistem Informasi Pelanggaran Siswa</title>

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

                <a href="data_catatan_pelanggaran.php"
                   class="nav-link active bg-primary text-white rounded py-2">
                    Catatan Pelanggaran
                </a>

                <a href="tindakan.php"
                   class="nav-link text-white rounded py-2">
                    Tindakan
                </a>

                <a href="riwayat.php"
                   class="nav-link text-white rounded py-2">
                    Riwayat
                </a>

                <a href="rekap_poin.php"
                   class="nav-link text-white rounded py-2">
                    Rekap Poin
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
                        Edit Catatan Pelanggaran
                    </h5>

                    <p class="text-secondary small mb-0">
                        Perbarui catatan pelanggaran siswa.
                    </p>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div class="text-end">
                        <div class="fw-semibold">
                            <?= e($nama_admin); ?>
                        </div>
                        <small class="text-secondary">Admin</small>
                    </div>

                <div class="bg-primary-subtle text-primary rounded-circle
                                d-flex justify-content-center align-items-center
                                fw-bold flex-shrink-0"
                         style="width: 42px; height: 42px;">
                        <?= strtoupper(substr($nama_admin, 0, 1)); ?>
                    </div>

            </header>

            <!-- ISI HALAMAN -->
            <section class="p-3 p-md-4">

                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-white p-3">
                        <h6 class="fw-bold mb-0">
                            Form Edit Catatan Pelanggaran
                        </h6>
                    </div>

                    <div class="card-body p-4">

                        <?php if ($error != '') { ?>
                            <div class="alert alert-danger">
                                <?= e($error); ?>
                            </div>
                        <?php } ?>

                        <form method="POST"
                              action="edit_pelanggaran_siswa.php?id=<?= $id; ?>">

                            <div class="mb-3">
                                <label class="form-label">Nama Siswa</label>
                                <input
                                    type="text"
                                    name="nama_siswa"
                                    class="form-control"
                                    value="<?= e($data['nama_siswa']); ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kelas</label>
                                <input
                                    type="text"
                                    name="nama_kelas"
                                    class="form-control"
                                    value="<?= e($data['nama_kelas']); ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Pelanggaran</label>
                                <input
                                    type="text"
                                    name="nama_pelanggaran"
                                    class="form-control"
                                    value="<?= e($data['nama_pelanggaran']); ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Guru</label>
                                <input
                                    type="text"
                                    name="nama_guru"
                                    class="form-control"
                                    value="<?= e($data['nama_guru']); ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input
                                    type="date"
                                    name="tanggal"
                                    class="form-control"
                                    value="<?= e($data['tanggal']); ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea
                                    name="keterangan"
                                    class="form-control"
                                    rows="3"
                                    required><?= e($data['keterangan']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Poin</label>
                                <input
                                    type="number"
                                    name="poin"
                                    class="form-control"
                                    min="0"
                                    step="1"
                                    value="<?= e($data['poin']); ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tindakan</label>
                                <textarea
                                    name="tindakan"
                                    class="form-control"
                                    rows="2"><?= e($data['tindakan']); ?></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Status</label>

                                <select name="status" class="form-select" required>
                                    <?php foreach ($statuses as $item) { ?>
                                        <option
                                            value="<?= e($item); ?>"
                                            <?= $data['status'] == $item ? 'selected' : ''; ?>>
                                            <?= e($item); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <button
                                    type="submit"
                                    name="update"
                                    class="btn btn-primary">
                                    Simpan Perubahan
                                </button>

                                <a
                                    href="data_catatan_pelanggaran.php"
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