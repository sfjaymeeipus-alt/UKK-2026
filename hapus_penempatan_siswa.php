
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
    header("Location: data_penempatan_siswa.php");
    exit;
}

// Proses penghapusan data
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_kelas_siswa WHERE id = ?"
);

if (!$stmt) {
    exit("Gagal menyiapkan query penghapusan.");
}

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);

        header("Location: data_penempatan_siswa.php");
        exit;
    } else {
        mysqli_stmt_close($stmt);

        exit("Data penempatan siswa tidak ditemukan atau sudah dihapus.");
    }

} else {

    if (mysqli_errno($koneksi) == 1451) {
        mysqli_stmt_close($stmt);

        exit("Penempatan siswa tidak dapat dihapus karena masih digunakan oleh data lain, misalnya riwayat pelanggaran siswa.");
    } else {
        mysqli_stmt_close($stmt);

        exit("Gagal menghapus data penempatan siswa.");
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
