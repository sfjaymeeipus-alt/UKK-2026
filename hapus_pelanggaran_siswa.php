
<?php
include "middleware/auth.php";
include "config/koneksi.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: data_pelanggaran_siswa.php");
    exit;
}

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

if ($id <= 0) {
    header("Location: data_pelanggaran_siswa.php");
    exit;
}

$stmt = mysqli_prepare(
    $koneksi,
    "DELETE FROM t_pelanggaran_siswa WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$berhasil = mysqli_stmt_affected_rows($stmt) > 0;

mysqli_stmt_close($stmt);

if ($berhasil) {
    header("Location: data_pelanggaran_siswa.php?pesan=hapus_berhasil");
} else {
    header("Location: data_pelanggaran_siswa.php?pesan=hapus_gagal");
}
exit;
?>
