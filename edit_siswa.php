<?php

include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$id = $_GET['id'];

$query = mysqli_query(
    $koneksi,
    "SELECT * FROM t_siswa WHERE id = '$id'"
);

$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data siswa tidak ditemukan";
    exit;
}

if (isset($_POST['update'])) {

    $nis = $_POST['nis'];
    $nisn = $_POST['nisn'];
    $nama = $_POST['nama'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $status_aktif = $_POST['status_aktif'];

    $query = mysqli_query($koneksi, "
        UPDATE t_siswa SET

            nis = '$nis',
            nisn = '$nisn',
            nama = '$nama',
            jenis_kelamin = '$jenis_kelamin',
            tanggal_lahir = '$tanggal_lahir',
            alamat = '$alamat',
            status_aktif = '$status_aktif'

        WHERE id = '$id'
    ");

    if ($query) {

        header("Location: data_siswa.php");
        exit;

    } else {

        echo "Gagal mengubah data: " . mysqli_error($koneksi);

    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Edit Siswa</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

</head>

<body class="bg-light">

<div class="container py-4">

    <div class="card shadow-sm">

        <div class="card-body">

            <h2 class="mb-4">
                Edit Data Siswa
            </h2>

            <form method="POST">

                <div class="mb-3">

                    <label class="form-label">
                        NIS
                    </label>

                    <input
                        type="text"
                        name="nis"
                        class="form-control"
                        value="<?= htmlspecialchars($data['nis']); ?>"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        NISN
                    </label>

                    <input
                        type="text"
                        name="nisn"
                        class="form-control"
                        value="<?= htmlspecialchars($data['nisn']); ?>"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Nama Siswa
                    </label>

                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        value="<?= htmlspecialchars($data['nama']); ?>"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Jenis Kelamin
                    </label>

                    <select
                        name="jenis_kelamin"
                        class="form-select"
                        required>

                        <option value="L"
                            <?= $data['jenis_kelamin'] == 'L' ? 'selected' : ''; ?>>
                            Laki-laki
                        </option>

                        <option value="P"
                            <?= $data['jenis_kelamin'] == 'P' ? 'selected' : ''; ?>>
                            Perempuan
                        </option>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Tanggal Lahir
                    </label>

                    <input
                        type="date"
                        name="tanggal_lahir"
                        class="form-control"
                        value="<?= $data['tanggal_lahir']; ?>"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Alamat
                    </label>

                    <textarea
                        name="alamat"
                        class="form-control"
                        rows="3"
                        required><?= htmlspecialchars($data['alamat']); ?></textarea>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status_aktif"
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

                <button
                    type="submit"
                    name="update"
                    class="btn btn-warning">

                    Update

                </button>

                <a
                    href="data_siswa.php"
                    class="btn btn-secondary">

                    Kembali

                </a>

            </form>

        </div>

    </div>

</div>

</body>

</html>