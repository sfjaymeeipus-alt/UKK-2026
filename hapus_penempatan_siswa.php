
<?php
include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    exit("Anda tidak memiliki akses");
}

$id = isset($_POST['id'])
    ? (int) $_POST['id']
    : (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    exit("ID penempatan siswa tidak valid.");
}

$pesan = "";

// Ambil informasi penempatan siswa
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT
        ks.id,
        s.nis,
        s.nama AS nama_siswa,
        k.nama AS nama_kelas,
        ta.nama AS tahun_ajaran,
        ks.tanggal_mulai,
        ks.tanggal_selesai
     FROM t_kelas_siswa ks
     INNER JOIN t_siswa s ON ks.siswa_id = s.id
     INNER JOIN t_kelas k ON ks.kelas_id = k.id
     INNER JOIN t_tahun_ajaran ta ON ks.tahun_ajaran_id = ta.id
     WHERE ks.id = ?"
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
    exit("Data penempatan siswa tidak ditemukan.");
}

// Proses penghapusan setelah tombol konfirmasi ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi_hapus'])) {
    $stmt = mysqli_prepare(
        $koneksi,
        "DELETE FROM t_kelas_siswa WHERE id = ?"
    );

    if (!$stmt) {
        $pesan = "Gagal menyiapkan query penghapusan.";
    } else {
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                mysqli_stmt_close($stmt);
                header("Location: data_penempatan_siswa.php");
                exit;
            } else {
                $pesan = "Data tidak ditemukan atau sudah dihapus.";
            }
        } else {
            if (mysqli_errno($koneksi) == 1451) {
                $pesan = "Penempatan siswa tidak dapat dihapus karena masih digunakan oleh data lain, misalnya riwayat pelanggaran siswa.";
            } else {
                $pesan = "Gagal menghapus data penempatan siswa.";
            }
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Penempatan Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white p-3">
                    <h5 class="mb-0">Konfirmasi Hapus Penempatan Siswa</h5>
                </div>

                <div class="card-body p-4">

                    <?php if ($pesan !== '') { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($pesan); ?>
                        </div>
                    <?php } ?>

                    <p>
                        Apakah kamu yakin ingin menghapus data penempatan berikut?
                    </p>

                    <div class="bg-light rounded p-3 mb-4">
                        <p class="mb-2">
                            <strong>NIS:</strong>
                            <?= htmlspecialchars($data['nis']); ?>
                        </p>

                        <p class="mb-2">
                            <strong>Nama Siswa:</strong>
                            <?= htmlspecialchars($data['nama_siswa']); ?>
                        </p>

                        <p class="mb-2">
                            <strong>Kelas:</strong>
                            <?= htmlspecialchars($data['nama_kelas']); ?>
                        </p>

                        <p class="mb-2">
                            <strong>Tahun Ajaran:</strong>
                            <?= htmlspecialchars($data['tahun_ajaran']); ?>
                        </p>

                        <p class="mb-2">
                            <strong>Tanggal Mulai:</strong>
                            <?= htmlspecialchars($data['tanggal_mulai']); ?>
                        </p>

                        <p class="mb-0">
                            <strong>Tanggal Selesai:</strong>
                            <?= !empty($data['tanggal_selesai'])
                                ? htmlspecialchars($data['tanggal_selesai'])
                                : '-'; ?>
                        </p>
                    </div>

                    <form method="POST"
                          action="hapus_penempatan_siswa.php"
                          onsubmit="return confirm('Yakin ingin menghapus penempatan siswa ini?');">

                        <input type="hidden" name="id" value="<?= $id; ?>">

                        <button type="submit"
                                name="konfirmasi_hapus"
                                class="btn btn-danger">
                            Ya, Hapus
                        </button>

                        <a href="data_penempatan_siswa.php"
                           class="btn btn-outline-secondary">
                            Batal
                        </a>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
