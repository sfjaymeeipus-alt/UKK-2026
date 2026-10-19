
<?php
include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    exit("Anda tidak memiliki akses");
}

$nama_admin = $_SESSION['nama'];
$pesan = "";

$id = isset($_GET['id']) ? (int) $_GET['id'] : (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    exit("ID penempatan siswa tidak valid.");
}

// Ambil pilihan siswa
$siswa = mysqli_query(
    $koneksi,
    "SELECT id, nis, nama FROM t_siswa ORDER BY nama ASC"
);

// Ambil pilihan kelas
$kelas = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_kelas ORDER BY nama ASC"
);

// Ambil pilihan tahun ajaran
$tahun = mysqli_query(
    $koneksi,
    "SELECT id, nama FROM t_tahun_ajaran ORDER BY id DESC"
);

if (!$siswa || !$kelas || !$tahun) {
    exit("Gagal mengambil data pilihan: " . mysqli_error($koneksi));
}

// Ambil data penempatan yang akan diedit
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT siswa_id, kelas_id, tahun_ajaran_id,
            tanggal_mulai, tanggal_selesai, status_aktif
     FROM t_kelas_siswa
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data) {
    exit("Data penempatan siswa tidak ditemukan.");
}

// Isi form dengan data lama
$siswa_id = (int) $data['siswa_id'];
$kelas_id = (int) $data['kelas_id'];
$tahun_ajaran_id = (int) $data['tahun_ajaran_id'];
$tanggal_mulai = $data['tanggal_mulai'];
$tanggal_selesai = $data['tanggal_selesai'] ?? '';
$status_aktif = (int) $data['status_aktif'];

// Proses penyimpanan perubahan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $siswa_id = (int) ($_POST['siswa_id'] ?? 0);
    $kelas_id = (int) ($_POST['kelas_id'] ?? 0);
    $tahun_ajaran_id = (int) ($_POST['tahun_ajaran_id'] ?? 0);
    $tanggal_mulai = trim($_POST['tanggal_mulai'] ?? '');
    $tanggal_selesai = trim($_POST['tanggal_selesai'] ?? '');
    $status_aktif = isset($_POST['status_aktif'])
        ? (int) $_POST['status_aktif']
        : -1;

    if (
        $siswa_id <= 0 ||
        $kelas_id <= 0 ||
        $tahun_ajaran_id <= 0 ||
        $tanggal_mulai === ''
    ) {
        $pesan = "Siswa, kelas, tahun ajaran, dan tanggal mulai wajib diisi.";
    } elseif (!in_array($status_aktif, [0, 1], true)) {
        $pesan = "Status penempatan tidak valid.";
    } elseif (
        $tanggal_selesai !== '' &&
        $tanggal_selesai < $tanggal_mulai
    ) {
        $pesan = "Tanggal selesai tidak boleh sebelum tanggal mulai.";
    } else {
        // Pastikan pilihan siswa, kelas, dan tahun ajaran tersedia
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT
                (SELECT COUNT(*) FROM t_siswa WHERE id = ?) AS jumlah_siswa,
                (SELECT COUNT(*) FROM t_kelas WHERE id = ?) AS jumlah_kelas,
                (SELECT COUNT(*) FROM t_tahun_ajaran WHERE id = ?) AS jumlah_tahun"
        );

        mysqli_stmt_bind_param(
            $cek,
            "iii",
            $siswa_id,
            $kelas_id,
            $tahun_ajaran_id
        );
        mysqli_stmt_execute($cek);
        $cek_result = mysqli_stmt_get_result($cek);
        $pilihan_valid = mysqli_fetch_assoc($cek_result);
        mysqli_stmt_close($cek);

        if (
            (int) $pilihan_valid['jumlah_siswa'] === 0 ||
            (int) $pilihan_valid['jumlah_kelas'] === 0 ||
            (int) $pilihan_valid['jumlah_tahun'] === 0
        ) {
            $pesan = "Siswa, kelas, atau tahun ajaran yang dipilih tidak ditemukan.";
        } else {
            $stmt = mysqli_prepare(
                $koneksi,
                "UPDATE t_kelas_siswa
                 SET siswa_id = ?,
                     kelas_id = ?,
                     tahun_ajaran_id = ?,
                     tanggal_mulai = ?,
                     tanggal_selesai = NULLIF(?, ''),
                     status_aktif = ?
                 WHERE id = ?"
            );

            if (!$stmt) {
                $pesan = "Gagal menyiapkan query perubahan data.";
            } else {
                mysqli_stmt_bind_param(
                    $stmt,
                    "iiissii",
                    $siswa_id,
                    $kelas_id,
                    $tahun_ajaran_id,
                    $tanggal_mulai,
                    $tanggal_selesai,
                    $status_aktif,
                    $id
                );

                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    header("Location: data_penempatan_siswa.php");
                    exit;
                } else {
                    if (mysqli_errno($koneksi) == 1062) {
                        $pesan = "Penempatan tersebut mungkin sudah terdaftar.";
                    } else {
                        $pesan = "Gagal memperbarui data penempatan siswa.";
                    }
                }

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
    <title>Edit Penempatan Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white p-3">
                    <h5 class="mb-0">Edit Penempatan Siswa</h5>
                </div>

                <div class="card-body p-4">

                    <p class="text-secondary">
                        Perbarui data siswa, kelas, dan tahun ajaran.
                    </p>

                    <?php if ($pesan !== '') { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>
                    <?php } ?>

                    <form method="POST"
                          action="edit_penempatan_siswa.php?id=<?= $id; ?>">

                        <input type="hidden" name="id" value="<?= $id; ?>">

                        <div class="mb-3">
                            <label for="siswa_id" class="form-label">Siswa</label>
                            <select name="siswa_id" id="siswa_id"
                                    class="form-select" required>
                                <option value="">-- Pilih Siswa --</option>
                                <?php while ($s = mysqli_fetch_assoc($siswa)) { ?>
                                    <option value="<?= (int) $s['id']; ?>"
                                        <?= $siswa_id === (int) $s['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="kelas_id" class="form-label">Kelas</label>
                            <select name="kelas_id" id="kelas_id"
                                    class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php while ($k = mysqli_fetch_assoc($kelas)) { ?>
                                    <option value="<?= (int) $k['id']; ?>"
                                        <?= $kelas_id === (int) $k['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($k['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tahun_ajaran_id" class="form-label">
                                Tahun Ajaran
                            </label>
                            <select name="tahun_ajaran_id" id="tahun_ajaran_id"
                                    class="form-select" required>
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                <?php while ($ta = mysqli_fetch_assoc($tahun)) { ?>
                                    <option value="<?= (int) $ta['id']; ?>"
                                        <?= $tahun_ajaran_id === (int) $ta['id'] ? 'selected' : ''; ?>>
                                        <?= htmlspecialchars($ta['nama']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tanggal_mulai" class="form-label">
                                    Tanggal Mulai
                                </label>
                                <input type="date" name="tanggal_mulai"
                                       id="tanggal_mulai" class="form-control"
                                       value="<?= htmlspecialchars($tanggal_mulai); ?>"
                                       required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tanggal_selesai" class="form-label">
                                    Tanggal Selesai
                                </label>
                                <input type="date" name="tanggal_selesai"
                                       id="tanggal_selesai" class="form-control"
                                       value="<?= htmlspecialchars($tanggal_selesai); ?>">
                                <small class="text-secondary">
                                    Kosongkan jika belum ada tanggal selesai.
                                </small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="status_aktif" class="form-label">
                                Status
                            </label>
                            <select name="status_aktif" id="status_aktif"
                                    class="form-select" required>
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
                            <button type="submit" name="simpan"
                                    class="btn btn-primary">
                                Simpan Perubahan
                            </button>
                            <a href="data_penempatan_siswa.php"
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
