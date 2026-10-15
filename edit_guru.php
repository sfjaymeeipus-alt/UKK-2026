<?php
include "middleware/auth.php";
include "config/koneksi.php";

// Hanya admin yang boleh mengedit data guru
if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama = $_SESSION['nama'];

// Ambil ID guru dari URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID guru tidak valid.";
    exit;
}

$id = (int) $_GET['id'];

$query = mysqli_query($koneksi, "SELECT * FROM t_guru WHERE id = $id");

if (!$query || mysqli_num_rows($query) == 0) {
    echo "Data guru tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($query);

// Proses ketika tombol Simpan Perubahan ditekan
if (isset($_POST['simpan'])) {

    $nip = trim($_POST['nip']);
    $nama_guru = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $status_aktif = (int) $_POST['status_aktif'];

    // Update data menggunakan prepared statement
    $update = mysqli_prepare(
        $koneksi,
        "UPDATE t_guru
         SET nip = ?, nama = ?, email = ?, status_aktif = ?
         WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $update,
        "sssii",
        $nip,
        $nama_guru,
        $email,
        $status_aktif,
        $id
    );

    if (mysqli_stmt_execute($update)) {
        header("Location: data_guru.php?pesan=berhasil_edit");
        exit;
    } else {
        $pesan_error = "Gagal mengubah data guru.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Guru - Sistem Informasi Pelanggaran Siswa</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center
                mb-4 flex-wrap gap-3">

        <div>
            <h3 class="fw-bold text-dark mb-1">Edit Data Guru</h3>
            <p class="text-secondary mb-0">
                Perbarui informasi guru yang terdaftar.
            </p>
        </div>

        <a href="data_guru.php" class="btn btn-outline-secondary">
            Kembali
        </a>

    </div>

    <!-- FORM EDIT -->
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-primary text-white p-3 rounded-top-4">
            <h5 class="mb-0 fw-bold">Formulir Edit Guru</h5>
        </div>

        <div class="card-body p-4">

            <?php if (isset($pesan_error)) { ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($pesan_error); ?>
                </div>
            <?php } ?>

            <form method="POST">

                <div class="mb-3">
                    <label for="nip" class="form-label fw-semibold">
                        NIP
                    </label>

                    <input
                        type="text"
                        name="nip"
                        id="nip"
                        class="form-control"
                        value="<?= htmlspecialchars($data['nip'] ?? ''); ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label for="nama" class="form-label fw-semibold">
                        Nama Guru
                    </label>

                    <input
                        type="text"
                        name="nama"
                        id="nama"
                        class="form-control"
                        value="<?= htmlspecialchars($data['nama'] ?? ''); ?>"
                        required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        class="form-control"
                        value="<?= htmlspecialchars($data['email'] ?? ''); ?>"
                        required>
                </div>

                <div class="mb-4">
                    <label for="status_aktif" class="form-label fw-semibold">
                        Status Guru
                    </label>

                    <select
                        name="status_aktif"
                        id="status_aktif"
                        class="form-select"
                        required>

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

                <div class="d-flex gap-2 flex-wrap">

                    <button
                        type="submit"
                        name="simpan"
                        class="btn btn-primary px-4">
                        Simpan Perubahan
                    </button>

                    <a href="data_guru.php"
                       class="btn btn-light border px-4">
                        Batal
                    </a>

                </div>

            </form>

        </div>
    </div>

    <footer class="text-center text-secondary small mt-4">
        Sistem Informasi Pelanggaran Siswa &copy; 2026
    </footer>

</div>

</body>
</html>
