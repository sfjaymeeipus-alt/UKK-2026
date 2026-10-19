
<?php
include "middleware/auth.php";
include "config/koneksi.php";

if ($_SESSION['role'] != 'admin') {
    exit("Anda tidak memiliki akses");
}

// Pastikan ID tersedia dan valid
if (
    !isset($_GET['id']) ||
    !ctype_digit($_GET['id']) ||
    (int) $_GET['id'] <= 0
) {
    header("Location: data_wali_kelas.php");
    exit;
}

$id = (int) $_GET['id'];

// Periksa apakah data wali kelas tersedia
$stmt = mysqli_prepare(
    $koneksi,
    "SELECT id FROM t_wali_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$data) {
    header("Location: data_wali_kelas.php");
    exit;
}

// Hapus data wali kelas
$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_wali_kelas WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header("Location: data_wali_kelas.php");
    exit;
}

mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gagal Menghapus Wali Kelas</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 text-center">

                    <h4 class="fw-bold text-danger mb-3">
                        Data Gagal Dihapus
                    </h4>

                    <p class="text-secondary">
                        Data wali kelas tidak dapat dihapus.
                        Data mungkin masih digunakan oleh tabel lain.
                    </p>

                    <a href="data_wali_kelas.php"
                       class="btn btn-primary">
                        Kembali ke Data Wali Kelas
                    </a>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
