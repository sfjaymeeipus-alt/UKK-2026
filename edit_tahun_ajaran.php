
<?php
include "middleware/auth.php";
include "config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    exit("Anda tidak memiliki akses");
}

$nama_admin = $_SESSION['nama'] ?? 'Admin';
$pesan = "";

// Mengambil ID
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
} else {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
}

if ($id <= 0) {
    header("Location: data_tahun_ajaran.php");
    exit;
}

// Mengambil data tahun ajaran
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

// Data awal
$nama = $data['nama'];
$tanggal_mulai = $data['tanggal_mulai'];
$tanggal_selesai = $data['tanggal_selesai'];
$status_aktif = (int) $data['status_aktif'];

// PROSES UPDATE
if (isset($_POST['simpan'])) {

    $nama = trim($_POST['nama'] ?? '');
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';

    $status_aktif = isset($_POST['status_aktif'])
        ? (int) $_POST['status_aktif'] : -1;

    // Validasi
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

        // Memulai transaksi database
        mysqli_begin_transaction($koneksi);

        try {

            // Jika tahun ajaran dipilih aktif,
            // nonaktifkan tahun ajaran lainnya
            if ($status_aktif == 1) {

                $nonaktif = mysqli_prepare(
                    $koneksi,
                    "UPDATE t_tahun_ajaran
                     SET status_aktif = 0
                     WHERE id != ?"
                );

                if (!$nonaktif) {
                    throw new Exception("Gagal menyiapkan query status.");
                }

                mysqli_stmt_bind_param($nonaktif, "i", $id);

                if (!mysqli_stmt_execute($nonaktif)) {
                    throw new Exception("Gagal menonaktifkan tahun ajaran sebelumnya.");
                }

                mysqli_stmt_close($nonaktif);
            }

            // Update data tahun ajaran
            $stmt = mysqli_prepare(
                $koneksi,
                "UPDATE t_tahun_ajaran
                 SET nama = ?,
                     tanggal_mulai = ?,
                     tanggal_selesai = ?,
                     status_aktif = ?
                 WHERE id = ?"
            );

            if (!$stmt) {
                throw new Exception("Gagal menyiapkan query update.");
            }

            mysqli_stmt_bind_param(
                $stmt,
                "sssii",
                $nama,
                $tanggal_mulai,
                $tanggal_selesai,
                $status_aktif,
                $id
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal memperbarui data tahun ajaran.");
            }

            mysqli_stmt_close($stmt);

            // Simpan perubahan
            mysqli_commit($koneksi);

            // Notifikasi berhasil
            $_SESSION['pesan_hapus'] = "Data tahun ajaran berhasil diperbarui.";
            $_SESSION['jenis_pesan'] = "success";

            header("Location: data_tahun_ajaran.php");
            exit;

        } catch (Exception $e) {

            // Batalkan seluruh perubahan jika gagal
            mysqli_rollback($koneksi);

            $pesan = $e->getMessage();

        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Tahun Ajaran</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-8 col-lg-6">

            <div class="card border-0 shadow-sm rounded-3">

                <div class="card-header bg-white p-3">

                    <h5 class="fw-bold mb-1">
                        Edit Tahun Ajaran
                    </h5>

                    <small class="text-secondary">
                        Perbarui informasi tahun ajaran.
                    </small>

                </div>

                <div class="card-body p-4">

                    <!-- NOTIFIKASI -->
                    <?php if ($pesan != '') { ?>

                        <div class="alert alert-danger py-2 small">
                            <?= htmlspecialchars($pesan); ?>
                        </div>

                    <?php } ?>

                    <form method="POST">

                        <input type="hidden" name="id"
                               value="<?= $id; ?>">

                        <!-- NAMA -->
                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Nama Tahun Ajaran
                            </label>

                            <input type="text"
                                   name="nama"
                                   class="form-control"
                                   value="<?= htmlspecialchars($nama); ?>"
                                   placeholder="Contoh: 2026/2027"
                                   required>

                        </div>

                        <!-- TANGGAL MULAI -->
                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Tanggal Mulai
                            </label>

                            <input type="date"
                                   name="tanggal_mulai"
                                   class="form-control"
                                   value="<?= htmlspecialchars($tanggal_mulai); ?>"
                                   required>

                        </div>

                        <!-- TANGGAL SELESAI -->
                        <div class="mb-3">

                            <label class="form-label fw-semibold">
                                Tanggal Selesai
                            </label>

                            <input type="date"
                                   name="tanggal_selesai"
                                   class="form-control"
                                   value="<?= htmlspecialchars($tanggal_selesai); ?>"
                                   required>

                        </div>

                        <!-- STATUS -->
                        <div class="mb-4">

                            <label class="form-label fw-semibold">
                                Status Tahun Ajaran
                            </label>

                            <select name="status_aktif"
                                    class="form-select"
                                    required>

                                <option value="1"
                                    <?= $status_aktif == 1 ? 'selected' : ''; ?>>
                                    Aktif
                                </option>

                                <option value="0"
                                    <?= $status_aktif == 0 ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>

                            </select>

                            <small class="text-secondary">
                                Jika memilih Aktif, tahun ajaran aktif sebelumnya
                                otomatis menjadi Tidak Aktif.
                            </small>

                        </div>

                        <!-- TOMBOL -->
                        <div class="d-flex justify-content-between">

                            <a href="data_tahun_ajaran.php"
                               class="btn btn-outline-secondary">
                                Kembali
                            </a>

                            <button type="submit"
                                    name="simpan"
                                    class="btn btn-primary">
                                Simpan Perubahan
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
