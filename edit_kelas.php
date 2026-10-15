<?php
include "middleware/auth.php";
include "config/koneksi.php";

// Hanya admin yang boleh mengedit data kelas
if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];

// Ambil ID kelas dari URL
if (!isset($_GET['id']) && !isset($_POST['simpan'])) {
    header("Location: data_kelas.php");
    exit;
}

// PROSES SIMPAN PERUBAHAN
if (isset($_POST['simpan'])) {

    $id = (int) $_POST['id'];
    $nama_kelas = trim($_POST['nama']);
    $tingkat = trim($_POST['tingkat']);
    $jurusan = trim($_POST['jurusan']);
    $status_aktif = (int) $_POST['status_aktif'];

    // Validasi sederhana
    if (
        $nama_kelas == '' ||
        $tingkat == '' ||
        $jurusan == '' ||
        !in_array($status_aktif, [0, 1], true)
    ) {
        $error = "Semua data harus diisi dengan benar.";
    } else {

        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE t_kelas
             SET nama = ?, tingkat = ?, jurusan = ?, status_aktif = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sssii",
            $nama_kelas,
            $tingkat,
            $jurusan,
            $status_aktif,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: data_kelas.php?pesan=edit_berhasil");
            exit;
        } else {
            $error = "Gagal memperbarui data kelas. Pastikan data tidak digunakan secara tidak sesuai.";
        }
    }

    mysqli_stmt_close($stmt ?? null);
}

// AMBIL DATA KELAS YANG AKAN DIEDIT
if (isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT * FROM t_kelas WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($hasil);

    mysqli_stmt_close($stmt);

    if (!$data) {
        echo "Data kelas tidak ditemukan.";
        exit;
    }

} else {

    // Setelah validasi gagal, ambil kembali data kelas.
    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT * FROM t_kelas WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($hasil);

    mysqli_stmt_close($stmt);

    if (!$data) {
        echo "Data kelas tidak ditemukan.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Data Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-primary text-white p-3">
                    <h4 class="mb-1">Edit Data Kelas</h4>
                    <small>Sistem Informasi Pelanggaran Siswa</small>
                </div>

                <div class="card-body p-4">

                    <p class="text-secondary">
                        Silakan ubah informasi kelas pada formulir berikut.
                    </p>

                    <?php if (isset($error)) { ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error); ?>
                        </div>
                    <?php } ?>

                    <form method="POST" action="edit_kelas.php">

                        <input type="hidden" name="id"
                               value="<?= (int) $data['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Nama Kelas
                            </label>

                            <input type="text" name="nama"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['nama']); ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Tingkat
                            </label>

                            <input type="text" name="tingkat"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['tingkat']); ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Jurusan
                            </label>

                            <input type="text" name="jurusan"
                                   class="form-control"
                                   value="<?= htmlspecialchars($data['jurusan']); ?>"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Status Kelas
                            </label>

                            <select name="status_aktif"
                                    class="form-select" required>

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

                        <button type="submit" name="simpan"
                                class="btn btn-primary">
                            Simpan Perubahan
                        </button>

                        <a href="data_kelas.php"
                           class="btn btn-secondary">
                            Batal
                        </a>

                    </form>

                </div>
            </div>

            <p class="text-center text-secondary small mt-3">
                Sistem Informasi Pelanggaran Siswa &copy; 2026
            </p>

        </div>
    </div>

</div>

</body>
</html>