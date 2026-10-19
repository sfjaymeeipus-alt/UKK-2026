
<?php
include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    exit("Anda tidak memiliki akses");
}

$nama = $_SESSION['nama'];
$error = "";

if (!isset($_GET['id']) || !ctype_digit($_GET['id']) || (int) $_GET['id'] <= 0) {
    exit("ID wali kelas tidak valid.");
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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $guru_id = (int) $_POST['guru_id'];
    $kelas_id = (int) $_POST['kelas_id'];
    $tahun_ajaran_id = (int) $_POST['tahun_ajaran_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = !empty($_POST['tanggal_selesai'])
        ? $_POST['tanggal_selesai'] : null;
    $status_aktif = (int) $_POST['status_aktif'];

    if (
        $guru_id <= 0 || $kelas_id <= 0 || $tahun_ajaran_id <= 0 ||
        empty($tanggal_mulai) ||
        !in_array($status_aktif, [0, 1], true)
    ) {
        $error = "Semua data wajib diisi dengan benar.";
    } elseif (
        $tanggal_selesai !== null &&
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

        mysqli_stmt_bind_param(
            $stmt,
            "iiissii",
            $guru_id,
            $kelas_id,
            $tahun_ajaran_id,
            $tanggal_mulai,
            $tanggal_selesai,
            $status_aktif,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: data_wali_kelas.php");
            exit;
        } else {
            $error = "Gagal memperbarui data: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }

    // Pertahankan isian pengguna jika validasi gagal.
    $data['guru_id'] = $guru_id;
    $data['kelas_id'] = $kelas_id;
    $data['tahun_ajaran_id'] = $tahun_ajaran_id;
    $data['tanggal_mulai'] = $tanggal_mulai;
    $data['tanggal_selesai'] = $tanggal_selesai;
    $data['status_aktif'] = $status_aktif;
}

$guru = mysqli_query($koneksi, "SELECT id, nama FROM t_guru ORDER BY nama ASC");
$kelas = mysqli_query($koneksi, "SELECT id, nama FROM t_kelas ORDER BY nama ASC");
$tahun = mysqli_query($koneksi, "SELECT id, nama FROM t_tahun_ajaran ORDER BY nama DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Wali Kelas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="mb-4">
                <h3 class="fw-bold">Edit Wali Kelas</h3>
                <p class="text-secondary mb-0">
                    Perbarui data penugasan wali kelas.
                </p>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white p-3">
                    <h5 class="mb-0">Form Edit Wali Kelas</h5>
                </div>

                <div class="card-body p-4">

                    <?php if ($error != '') { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php } ?>

                    <form method="POST"
                          action="edit_wali_kelas.php?id=<?= $id; ?>">

                        <div class="mb-3">
                            <label class="form-label">Nama Guru</label>
                            <select name="guru_id" class="form-select" required>
                                <option value="">-- Pilih Guru --</option>
                                <?php while ($g = mysqli_fetch_assoc($guru)) { ?>
                                    <option value="<?= $g['id']; ?>"
                                        <?= $data['guru_id'] == $g['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($g['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                    <option value="<?= $k['id']; ?>"
                                        <?= $data['kelas_id'] == $k['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($k['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tahun Ajaran</label>
                            <select name="tahun_ajaran_id" class="form-select" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>
                                    <option value="<?= $ta['id']; ?>"
                                        <?= $data['tahun_ajaran_id'] == $ta['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($ta['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai"
                                   class="form-control" required
                                   value="<?= htmlspecialchars($data['tanggal_mulai'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['tanggal_selesai'] ?? ''); ?>">
                            <div class="form-text">
                                Kosongkan jika penugasan masih berlangsung.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status_aktif" class="form-select" required>
                                <option value="1"
                                    <?= $data['status_aktif'] == 1 ? 'selected' : ''; ?>>
                                    Aktif
                                </option>
                                <option value="0"
                                    <?= $data['status_aktif'] == 0 ? 'selected' : ''; ?>>
                                    Tidak Aktif
                                </option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                Simpan Perubahan
                            </button>
                            <a href="data_wali_kelas.php"
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
