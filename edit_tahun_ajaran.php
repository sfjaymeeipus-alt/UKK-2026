
<?php
include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    exit("Anda tidak memiliki akses");
}

$pesan = "";
$id = 0;
$nama = "";
$tanggal_mulai = "";
$tanggal_selesai = "";
$status_aktif = 1;

// Ambil ID dari URL atau form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
} else {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
}

if ($id <= 0) {
    exit("ID tahun ajaran tidak valid.");
}

// Jika tombol simpan ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $nama = trim($_POST['nama'] ?? '');
    $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
    $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
    $status_aktif = isset($_POST['status_aktif'])
        ? (int) $_POST['status_aktif']
        : -1;

    if (
        $nama === '' ||
        $tanggal_mulai === '' ||
        $tanggal_selesai === ''
    ) {
        $pesan = "Semua kolom wajib diisi.";
    } elseif (
        !in_array($status_aktif, [0, 1], true)
    ) {
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

// Ambil data lama jika halaman baru dibuka
// atau jika proses simpan gagal
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['simpan'])) {
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT nama, tanggal_mulai, tanggal_selesai, status_aktif
         FROM t_tahun_ajaran
         WHERE id = ?"
    );

    if (!$stmt) {
        exit("Terjadi kesalahan saat mengambil data.");
    }

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
} else {
    // Pastikan data dengan ID tersebut masih tersedia
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id FROM t_tahun_ajaran WHERE id = ?"
    );
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $dataAda = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$dataAda) {
        exit("Data tahun ajaran tidak ditemukan.");
    }
}

$nama_admin = $_SESSION['nama'];
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

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white p-3">
                    <h5 class="mb-0">Edit Tahun Ajaran</h5>
                </div>

                <div class="card-body p-4">

                    <p class="text-secondary">
                        Ubah informasi tahun ajaran sesuai data yang benar.
                    </p>

                    <?php if ($pesan !== '') { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>
                    <?php } ?>

                    <form method="POST"
                          action="edit_tahun_ajaran.php?id=<?= $id; ?>">

                        <input type="hidden" name="id" value="<?= $id; ?>">

                        <div class="mb-3">
                            <label for="nama" class="form-label">
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
                            <label for="tanggal_mulai" class="form-label">
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
                            <label for="tanggal_selesai" class="form-label">
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
                            <label for="status_aktif" class="form-label">
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

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    name="simpan"
                                    class="btn btn-primary">
                                Simpan Perubahan
                            </button>

                            <a href="data_tahun_ajaran.php"
                               class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>

            <p class="text-center text-secondary small mt-3">
                Login sebagai <?= htmlspecialchars($nama_admin); ?>
            </p>

        </div>
    </div>
</div>

</body>
</html>
