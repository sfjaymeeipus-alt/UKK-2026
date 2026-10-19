
<?php
include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    exit("Anda tidak memiliki akses");
}

if (
    !isset($_GET['id']) ||
    !ctype_digit($_GET['id']) ||
    (int) $_GET['id'] <= 0
) {
    header("Location: data_kategori_pelanggaran.php");
    exit;
}

$id = (int) $_GET['id'];
$error = "";

// Ambil data kategori berdasarkan ID
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id, nama, deskripsi, status_aktif
     FROM t_pelanggaran_kategori
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    exit("Data kategori pelanggaran tidak ditemukan.");
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kategori = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $status_aktif = isset($_POST['status_aktif'])
        ? (int) $_POST['status_aktif'] : -1;

    if ($nama_kategori == '') {
        $error = "Nama kategori wajib diisi.";
    } elseif (!in_array($status_aktif, [0, 1], true)) {
        $error = "Status kategori tidak valid.";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_pelanggaran_kategori
             SET nama = ?,
                 deskripsi = ?,
                 status_aktif = ?,
                 updated_at = NOW()
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssii",
            $nama_kategori,
            $deskripsi,
            $status_aktif,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: data_kategori_pelanggaran.php");
            exit;
        } else {
            $error = "Gagal memperbarui data kategori.";
        }

        mysqli_stmt_close($stmt);
    }

    // Tampilkan kembali isian jika validasi gagal
    $data['nama'] = $nama_kategori;
    $data['deskripsi'] = $deskripsi;
    $data['status_aktif'] = $status_aktif;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Kategori Pelanggaran</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="mb-4">
                <h3 class="fw-bold">Edit Kategori Pelanggaran</h3>
                <p class="text-secondary mb-0">
                    Perbarui informasi kategori pelanggaran siswa.
                </p>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white p-3">
                    <h5 class="mb-0">Form Edit Kategori</h5>
                </div>

                <div class="card-body p-4">

                    <?php if ($error != '') { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php } ?>

                    <form method="POST"
                          action="edit_kategori_pelanggaran.php?id=<?= $id; ?>">

                        <div class="mb-3">
                            <label class="form-label">Nama Kategori</label>
                            <input
                                type="text"
                                name="nama"
                                class="form-control"
                                maxlength="100"
                                value="<?= htmlspecialchars($data['nama'] ?? ''); ?>"
                                placeholder="Masukkan nama kategori"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea
                                name="deskripsi"
                                class="form-control"
                                rows="4"
                                placeholder="Masukkan deskripsi kategori"><?= htmlspecialchars($data['deskripsi'] ?? ''); ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
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

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                Simpan Perubahan
                            </button>

                            <a href="data_kategori_pelanggaran.php"
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

        </div>
    </div>
</div>

</body>
</html>
